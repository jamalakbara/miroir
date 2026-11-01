<?php
/**
 * Screens 31-44: panels, empty/sold-out/size/added/stock states, notices.
 * Templates live in template-parts/panels.php and template-parts/state-*.php.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Notice title (screen 41): the lofi names the situation in mono above the
 * message. Known WooCommerce messages get the drawn titles, others a title by type.
 *
 * @param string $type    error | success | notice.
 * @param string $message Notice HTML.
 */
function miroir_notice_title( $type, $message ) {
	$text  = wp_strip_all_tags( $message );
	$known = array(
		'/password.*(incorrect|not registered)|unknown (email|username)|error: the username/i' => miroir_text( 'notice_login' ),
		'/account details changed|address changed/i'                                       => miroir_text( 'notice_saved' ),
		'/(key|link) (is )?(invalid|expired)|invalid key/i'                                => miroir_text( 'notice_reset' ),
		'/added to your cart|added to your bag/i'                                           => miroir_text( 'added_notice' ),
		'/removed/i'                                                                        => miroir_text( 'notice_removed' ),
		'/not enough|in stock|out of stock/i'                                               => miroir_text( 'stock_notice' ),
		'/choose product options|select some product options/i'                             => miroir_text( 'choose_notice' ),
	);
	foreach ( $known as $pattern => $title ) {
		if ( preg_match( $pattern, $text ) ) {
			return $title;
		}
	}
	$titles = array(
		'error'   => miroir_text( 'notice_error' ),
		'success' => miroir_text( 'notice_success' ),
		'notice'  => miroir_text( 'notice_info' ),
	);
	return $titles[ $type ] ?? $titles['notice'];
}

/*
 * Notices print inside each screen's column (below the title), so take them
 * off WooCommerce's default spots; templates call woocommerce_output_all_notices().
 */
add_action(
	'init',
	function () {
		foreach ( array(
			'woocommerce_cart_is_empty'               => 5,
			'woocommerce_before_shop_loop'            => 10,
			'woocommerce_before_single_product'       => 10,
			'woocommerce_before_cart'                 => 10,
			'woocommerce_before_checkout_form'        => 10,
			'woocommerce_before_customer_login_form'  => 10,
			'woocommerce_before_lost_password_form'   => 10,
			'woocommerce_before_reset_password_form'  => 10,
		) as $hook => $priority ) {
			remove_action( $hook, 'woocommerce_output_all_notices', $priority );
		}
	}
);

/*
 * The filter panel (33) submits checkboxes as filter_color[]=a&filter_color[]=b;
 * WooCommerce's layered nav reads comma lists, so join arrays before it runs.
 */
add_action(
	'init',
	function () {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only listing filters.
		foreach ( $_GET as $key => $value ) {
			if ( is_array( $value ) && str_starts_with( $key, 'filter_' ) ) {
				$_GET[ $key ] = implode( ',', array_map( 'sanitize_title', wp_unslash( $value ) ) );
			}
		}
		// phpcs:enable
	},
	0
);

/*
 * Added to bag (39): after a product-page add, go back to the product with
 * ?added=<cart item key>; the product block then shows the added state.
 */
add_action(
	'woocommerce_add_to_cart',
	function ( $cart_item_key ) {
		$GLOBALS['miroir_added_key'] = $cart_item_key;
	}
);
add_filter(
	'woocommerce_add_to_cart_redirect',
	function ( $url, $product ) {
		if ( empty( $GLOBALS['miroir_added_key'] ) || wp_doing_ajax() || ! $product ) {
			return $url;
		}
		$parent = $product->is_type( 'variation' ) ? wc_get_product( $product->get_parent_id() ) : $product;
		// The added state replaces WooCommerce's "added to your cart" notice.
		wc_clear_notices();
		return add_query_arg( 'added', $GLOBALS['miroir_added_key'], $parent->get_permalink() );
	},
	10,
	2
);

/**
 * Product page state for the current request: '' (normal PDP, screen 03),
 * 'added' (39), 'choose-size' (44) or 'sold-out' (38).
 *
 * @param WC_Product $product Product.
 */
function miroir_product_state( $product ) {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- display state only.
	if ( ! empty( $_GET['added'] ) && WC()->cart && WC()->cart->get_cart_item( sanitize_key( $_GET['added'] ) ) ) {
		return 'added';
	}
	if ( ! $product->is_in_stock() ) {
		return 'sold-out';
	}
	if ( isset( $_GET['choose-size'] ) && $product->is_type( 'variable' ) ) {
		return 'choose-size';
	}
	// phpcs:enable
	return '';
}

/**
 * First bag line whose quantity exceeds the stock now available (screen 40).
 *
 * @return array|null { key, item, product, requested, available }
 */
function miroir_cart_stock_problem() {
	if ( ! WC()->cart ) {
		return null;
	}
	foreach ( WC()->cart->get_cart() as $key => $item ) {
		$product = $item['data'];
		if ( ! $product->managing_stock() || $product->backorders_allowed() ) {
			continue;
		}
		$available = max( 0, (int) $product->get_stock_quantity() );
		if ( $item['quantity'] > $available ) {
			return array(
				'key'       => $key,
				'item'      => $item,
				'product'   => $product,
				'requested' => (int) $item['quantity'],
				'available' => $available,
			);
		}
	}
	return null;
}
