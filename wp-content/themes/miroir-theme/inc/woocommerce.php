<?php
/**
 * WooCommerce integration: drop WC's own CSS, keep the header bag count live.
 */

defined( 'ABSPATH' ) || exit;

// The lofi is the only visual layer; WC's general/layout/smallscreen sheets would override it.
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_dequeue_style( 'wc-blocks-style' );
		wp_dequeue_style( 'woocommerce-inline' );
	},
	20
);

// In a block theme WooCommerce also adds its block-theme sheet (enqueue_block_assets) and
// re-adds wc-blocks-style late; drop both right before styles print, in the head and footer.
foreach ( array( 'wp_print_styles', 'wp_print_footer_scripts' ) as $miroir_hook ) {
	add_action(
		$miroir_hook,
		function () {
			wp_dequeue_style( 'woocommerce-blocktheme' );
			wp_dequeue_style( 'wc-blocks-style' );
		},
		1
	);
}

/**
 * Price row of a product card: "Was" (ink-3) before the current price, or
 * "Out of stock" in the price slot, as drawn in the lofi cards.
 *
 * @param WC_Product $product Product.
 */
function miroir_card_price( $product ) {
	if ( ! $product->is_in_stock() ) {
		return '<span class="card__now">' . esc_html( miroir_text( 'out_of_stock' ) ) . '</span>';
	}

	$now = $product->is_type( 'variable' ) ? $product->get_variation_price( 'min', true ) : wc_get_price_to_display( $product );
	$out = '';
	if ( $product->is_on_sale() ) {
		$was  = $product->is_type( 'variable' ) ? $product->get_variation_regular_price( 'min', true ) : wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) );
		$out .= '<del class="card__was"><span class="screen-reader-text">' . esc_html__( 'Original price:', 'miroir' ) . ' </span>' . wc_price( $was ) . '</del>';
	}
	return $out . '<span class="card__now">' . wc_price( $now ) . '</span>';
}

/**
 * Image of a product, variation or cart item product: its own image, else the parent product's.
 * 0 while Customize > Products > "Show product photos" is off (grey placeholders).
 *
 * @param WC_Product|null $product Product.
 * @return int Attachment id, 0 when none.
 */
function miroir_product_image_id( $product ) {
	if ( ! $product instanceof WC_Product || ! miroir_mod( 'product_photos' ) ) {
		return 0;
	}
	$id = (int) $product->get_image_id();
	if ( ! $id && $product->get_parent_id() ) {
		$parent = wc_get_product( $product->get_parent_id() );
		$id     = $parent ? (int) $parent->get_image_id() : 0;
	}
	return $id;
}

/**
 * Products of a home strip, newest first.
 *
 * @param array $args {
 *     @type string $source   'category', 'sale', 'featured', 'newest' or 'ids'.
 *     @type string $category Category slug (source 'category').
 *     @type int[]  $ids      Product ids in display order (source 'ids').
 *     @type int    $limit    Maximum, -1 for all.
 * }
 * @return WC_Product[]
 */
function miroir_strip_products( $args ) {
	$query = array(
		'status'  => 'publish',
		'limit'   => (int) ( $args['limit'] ?? -1 ),
		'orderby' => 'date',
		'order'   => 'DESC',
	);
	switch ( $args['source'] ?? 'category' ) {
		case 'sale':
			$query['include'] = wc_get_product_ids_on_sale() ? wc_get_product_ids_on_sale() : array( 0 );
			break;
		case 'featured':
			$query['featured'] = true;
			break;
		case 'ids':
			$query['include'] = array_filter( array_map( 'absint', (array) ( $args['ids'] ?? array() ) ) );
			if ( ! $query['include'] ) {
				return array();
			}
			$query['orderby'] = 'include';
			break;
		case 'newest':
			break;
		default:
			if ( empty( $args['category'] ) ) {
				return array();
			}
			$query['category'] = array( $args['category'] );
	}
	return wc_get_products( $query );
}

/**
 * Render product cards for a list of products.
 *
 * @param WC_Product[] $products Products.
 */
function miroir_cards( $products ) {
	foreach ( $products as $product ) {
		get_template_part( 'template-parts/product-card', null, array( 'product' => $product ) );
	}
}

add_filter(
	'woocommerce_get_image_size_thumbnail',
	function ( $size ) {
		$size['height'] = 0;
		$size['crop']   = 0;
		return $size;
	}
);

// Listing grids are 3 x 4 on desktop; "Load more" fetches the next page (Customize > Layout).
add_filter(
	'loop_shop_per_page',
	function () {
		return max( 1, (int) miroir_mod( 'products_per_page' ) );
	}
);

/**
 * A shop sidebar link group. Uses the menu at $location (its name is the group
 * title) or, until one is assigned, the product categories in $fallback.
 *
 * @param string $location Menu location.
 * @param string $title    Fallback title.
 * @param array  $fallback Category slugs, or slug => label.
 * @return array { title, items: [ {label, url, current} ] }
 */
function miroir_filter_group( $location, $title, $fallback ) {
	$current = is_tax( 'product_cat' ) ? get_queried_object() : null;
	$items   = array();

	$locations = get_nav_menu_locations();
	if ( ! empty( $locations[ $location ] ) && ( $menu = wp_get_nav_menu_object( $locations[ $location ] ) ) ) { // phpcs:ignore Generic.CodeAnalysis.AssignmentInCondition
		$title = $menu->name;
		foreach ( wp_get_nav_menu_items( $menu ) ?: array() as $item ) {
			$items[] = array(
				'label'   => $item->title,
				'url'     => $item->url,
				'current' => $current && 'product_cat' === $item->object && (int) $item->object_id === $current->term_id,
			);
		}
		return compact( 'title', 'items' );
	}

	foreach ( $fallback as $slug => $label ) {
		if ( is_int( $slug ) ) {
			$slug  = $label;
			$label = null;
		}
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term ) {
			continue;
		}
		$items[] = array(
			'label'   => $label ?? strtolower( $term->name ),
			'url'     => get_term_link( $term ),
			'current' => $current && $current->term_id === $term->term_id,
		);
	}
	return compact( 'title', 'items' );
}

/**
 * Colour filter checkboxes from the global Colour attribute (pa_color). Each
 * links to the current listing with that colour toggled in WooCommerce's
 * layered-nav query (?filter_color=a,b&query_type_color=or).
 *
 * @return array [ {label, url, checked} ]
 */
function miroir_colour_filters() {
	$taxonomy = apply_filters( 'miroir_colour_taxonomy', 'pa_color' );
	if ( ! taxonomy_exists( $taxonomy ) ) {
		return array();
	}
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'orderby'    => 'menu_order',
		)
	);
	if ( is_wp_error( $terms ) || ! $terms ) {
		return array();
	}

	$param   = 'filter_' . wc_attribute_taxonomy_slug( $taxonomy );
	$chosen  = array_filter( explode( ',', wc_clean( wp_unslash( $_GET[ $param ] ?? '' ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification
	$base    = remove_query_arg( array( 'paged', 'product-page' ) );
	$base    = preg_replace( '#/page/\d+/?#', '/', $base );
	$filters = array();
	foreach ( $terms as $term ) {
		$checked = in_array( $term->slug, $chosen, true );
		$next    = $checked ? array_diff( $chosen, array( $term->slug ) ) : array_merge( $chosen, array( $term->slug ) );
		$url     = $next
			? add_query_arg( array( $param => implode( ',', $next ), 'query_type_' . wc_attribute_taxonomy_slug( $taxonomy ) => 'or' ), $base )
			: remove_query_arg( array( $param, 'query_type_' . wc_attribute_taxonomy_slug( $taxonomy ) ), $base );
		$filters[] = array(
			'label'   => $term->name,
			'url'     => $url,
			'checked' => $checked,
		);
	}
	return $filters;
}

/**
 * Listing heading for the mobile bar: the category name, or "shop women" on the shop page.
 */
function miroir_listing_title() {
	if ( is_tax() ) {
		return strtolower( single_term_title( '', false ) );
	}
	return miroir_text( 'shop_title' );
}

// Header search and the search panel look in products only, so results use the listing layout.
add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( ! is_admin() && $query->is_main_query() && $query->is_search() && ! $query->get( 'post_type' ) ) {
			$query->set( 'post_type', 'product' );
		}
	}
);

/**
 * Variant line under a bag item: "Size M" for variations, the size attribute
 * (e.g. "All Size") for simple products.
 *
 * @param array $cart_item Cart item.
 */
function miroir_cart_item_variant( $cart_item ) {
	$product = $cart_item['data'];
	if ( $product->is_type( 'variation' ) ) {
		$parts = array();
		foreach ( $cart_item['variation'] as $name => $value ) {
			$taxonomy = wc_attribute_taxonomy_slug( str_replace( 'attribute_', '', $name ) );
			$term     = taxonomy_exists( 'pa_' . $taxonomy ) ? get_term_by( 'slug', $value, 'pa_' . $taxonomy ) : null;
			$parts[]  = wc_attribute_label( str_replace( 'attribute_', '', $name ), $product ) . ' ' . ( $term ? $term->name : $value );
		}
		return implode( ', ', $parts );
	}
	return (string) $product->get_attribute( 'size' );
}

// Account menu as drawn: dashboard, orders, addresses, account details, log out.
add_filter(
	'woocommerce_account_menu_items',
	function ( $items ) {
		unset( $items['downloads'], $items['payment-methods'] );
		return $items;
	}
);

/*
 * Registration (screen 12) asks for first and last name; passwords follow the
 * lofi rule "at least 12 characters" instead of WooCommerce's strength meter,
 * whose inline widget is not part of the design.
 */
const MIROIR_MIN_PASSWORD = 12;

add_action(
	'woocommerce_register_post',
	function ( $username, $email, $errors ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- WooCommerce verified the register nonce.
		foreach ( array( 'first_name' => __( 'First name', 'miroir' ), 'last_name' => __( 'Last name', 'miroir' ) ) as $key => $label ) {
			if ( '' === trim( wc_clean( wp_unslash( $_POST[ $key ] ?? '' ) ) ) ) {
				/* translators: %s: field label. */
				$errors->add( $key, sprintf( __( '%s is required.', 'miroir' ), $label ) );
			}
		}
		if ( isset( $_POST['password'] ) && strlen( wp_unslash( $_POST['password'] ) ) < MIROIR_MIN_PASSWORD ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- length check only.
			/* translators: %d: minimum length. */
			$errors->add( 'password', sprintf( __( 'Use at least %d characters for your password.', 'miroir' ), MIROIR_MIN_PASSWORD ) );
		}
		// phpcs:enable
	},
	10,
	3
);

add_action(
	'woocommerce_created_customer',
	function ( $customer_id ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- WooCommerce verified the register nonce.
		$customer = new WC_Customer( $customer_id );
		$first    = wc_clean( wp_unslash( $_POST['first_name'] ?? '' ) );
		$last     = wc_clean( wp_unslash( $_POST['last_name'] ?? '' ) );
		// phpcs:enable
		$customer->set_first_name( $first );
		$customer->set_last_name( $last );
		$customer->set_billing_first_name( $first );
		$customer->set_billing_last_name( $last );
		$customer->set_display_name( trim( $first . ' ' . $last ) );
		$customer->save();
	}
);

add_action(
	'validate_password_reset',
	function ( $errors, $user ) {
		$pass = wp_unslash( $_POST['password_1'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- WooCommerce verified the reset nonce; length check only.
		if ( $user instanceof WP_User && '' !== $pass && strlen( $pass ) < MIROIR_MIN_PASSWORD ) {
			/* translators: %d: minimum length. */
			$errors->add( 'password_too_short', sprintf( __( 'Use at least %d characters for your password.', 'miroir' ), MIROIR_MIN_PASSWORD ) );
		}
	},
	10,
	2
);

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_dequeue_script( 'wc-password-strength-meter' );
	},
	20
);

// Address forms (screen 20): no company; shipping also asks for a phone, as drawn.
add_filter(
	'woocommerce_billing_fields',
	function ( $fields ) {
		unset( $fields['billing_company'] );
		return $fields;
	}
);
add_filter(
	'woocommerce_shipping_fields',
	function ( $fields ) {
		unset( $fields['shipping_company'] );
		$fields['shipping_phone'] = array_merge(
			$fields['shipping_phone'] ?? array(),
			array(
				'label'        => __( 'Phone', 'miroir' ),
				'type'         => 'tel',
				'required'     => true,
				'autocomplete' => 'tel',
				'validate'     => array( 'phone' ),
				'priority'     => 100,
			)
		);
		return $fields;
	}
);

// After registering, land on the dashboard rather than back on ?action=register.
add_filter(
	'woocommerce_registration_redirect',
	function () {
		return wc_get_page_permalink( 'myaccount' );
	}
);

// The register form carries its own privacy line (screen 12 help text).
remove_action( 'woocommerce_register_form', 'wc_registration_privacy_policy_text', 20 );

// The lofi calls the cart a bag everywhere.
add_filter(
	'woocommerce_product_single_add_to_cart_text',
	function () {
		return miroir_text( 'add_to_bag' );
	}
);

// Refresh "bag (n)" after AJAX add-to-cart.
add_filter(
	'woocommerce_add_to_cart_fragments',
	function ( $fragments ) {
		$fragments['a.site-nav__bag'] = miroir_header_bag();
		return $fragments;
	}
);

// Fragments cached in sessionStorage would keep an old "bag (n)" label after it is edited in the Customizer.
add_filter(
	'woocommerce_cart_fragment_name',
	function ( $name ) {
		$label = function_exists( 'miroir_text' ) ? miroir_text( 'nav_bag', 0 ) : __( 'bag (%d)', 'miroir' );
		return $name . '_' . substr( md5( 'compact-header-v1_' . $label ), 0, 8 );
	}
);
