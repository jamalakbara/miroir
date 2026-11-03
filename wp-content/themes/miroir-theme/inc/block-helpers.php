<?php
/**
 * Helpers for the Miroir site blocks (blocks/*): the header, footer, page,
 * product and listing sections of the block templates. Each block prints the
 * markup the classic templates printed, so these helpers hold the bits that
 * several blocks share.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Whether the block is rendered for an editor preview (ServerSideRender)
 * rather than for the storefront.
 */
function miroir_block_preview() {
	return ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || miroir_elementor_preview();
}

/**
 * Whether Elementor is rendering for its editor: the preview frame, or a
 * widget re-rendered while its settings change.
 */
function miroir_elementor_preview() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		return false;
	}
	$elementor = \Elementor\Plugin::$instance;
	return ( $elementor->editor && $elementor->editor->is_edit_mode() ) || ( $elementor->preview && $elementor->preview->is_preview_mode() );
}

/**
 * Whether a post is built with Elementor (its content is Elementor's).
 *
 * @param int $post_id Post id.
 */
function miroir_elementor_built( $post_id ) {
	if ( ! $post_id || ! did_action( 'elementor/loaded' ) ) {
		return false;
	}
	$document = \Elementor\Plugin::$instance->documents->get( $post_id );
	return $document && $document->is_built_with_elementor();
}

/**
 * Product a product section shows: the current product, or the newest
 * product while the template is previewed in the Site Editor.
 *
 * @return WC_Product|null
 */
function miroir_block_product() {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return null;
	}
	$product = 'product' === get_post_type() ? wc_get_product( get_the_ID() ) : null;
	if ( ! $product && miroir_block_preview() ) {
		$ids     = wc_get_products( array( 'limit' => 1, 'status' => 'publish', 'return' => 'ids' ) );
		$product = $ids ? wc_get_product( $ids[0] ) : null;
	}
	return $product ?: null;
}

/**
 * Products of the main query for listing sections; a sample page of products
 * in the Site Editor, where there is no shop query.
 *
 * @return WC_Product[]
 */
function miroir_block_listing_products() {
	if ( miroir_block_preview() ) {
		return function_exists( 'wc_get_products' ) ? wc_get_products( array( 'limit' => 8, 'status' => 'publish' ) ) : array();
	}
	$products = array();
	while ( have_posts() ) {
		the_post();
		$products[] = wc_get_product( get_the_ID() );
	}
	rewind_posts();
	return array_filter( $products );
}

/**
 * "Load more" link of the main query, empty on the last page.
 */
function miroir_block_load_more() {
	$next_url = get_next_posts_page_link();
	if ( miroir_block_preview() || ! $next_url || get_query_var( 'paged', 1 ) >= $GLOBALS['wp_query']->max_num_pages ) {
		return '';
	}
	return sprintf( '<a class="btn listing-more" href="%s" data-load-more>%s</a>', esc_url( $next_url ), esc_html( miroir_text( 'load_more' ) ) );
}

/**
 * Links a menu block shows while no menu is assigned to its location
 * (Appearance > Menus), as in the classic header and footer.
 *
 * @param string $location Menu location.
 * @return array Label => URL.
 */
function miroir_menu_fallback( $location ) {
	$shop    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
	$account = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
	switch ( $location ) {
		case 'primary':
			$sale = get_term_by( 'slug', 'sale', 'product_cat' );
			return array(
				__( 'sale', 'miroir' )       => $sale && ! is_wp_error( $sale ) ? get_term_link( $sale ) : $shop,
				__( 'shop women', 'miroir' ) => $shop,
			);
		case 'footer-account':
			return array(
				__( 'My account', 'miroir' )           => $account,
				__( 'Payment confirmation', 'miroir' ) => miroir_page_url( 'payment-confirmation' ),
				__( 'Shipping information', 'miroir' ) => miroir_page_url( 'shipping-information' ),
			);
		case 'footer-policy':
			return array(
				__( 'FAQ', 'miroir' )              => miroir_page_url( 'faq' ),
				__( 'Return policy', 'miroir' )    => miroir_page_url( 'returns-exchanges' ),
				__( 'Condition of use', 'miroir' ) => miroir_page_url( 'terms-of-use' ),
			);
		case 'footer-contact':
			return array(
				__( 'Contact customer care', 'miroir' ) => miroir_page_url( 'contact' ),
				__( 'Instagram', 'miroir' )             => miroir_mod( 'instagram_url' ),
			);
	}
	return array();
}

/**
 * Content of the current page through the_content filters (blocks, shortcodes,
 * #whatsapp links). Guards against a page that shows itself.
 */
function miroir_block_page_content() {
	static $rendering = array();
	$id = get_the_ID();
	if ( ! $id || isset( $rendering[ $id ] ) ) {
		return '';
	}
	$rendering[ $id ] = true;
	ob_start();
	the_content();
	unset( $rendering[ $id ] );
	return ob_get_clean();
}

/**
 * The home sections of miroir_home_sections(), shown while the front page
 * has no Miroir blocks yet.
 */
function miroir_home_fallback() {
	ob_start();
	foreach ( miroir_home_sections() as $section ) {
		switch ( $section['type'] ) {
			case 'video-hero':
				get_template_part( 'template-parts/home-video-hero', null, $section );
				break;

			case 'editorial':
				get_template_part( 'template-parts/home-editorial', null, $section );
				break;

			case 'banner':
				get_template_part( 'template-parts/home-banner', null, $section );
				break;

			case 'strip':
				$section['products'] = function_exists( 'wc_get_products' ) ? miroir_strip_products( $section ) : array();
				$section['columns']  = $section['columns'] ?? ( 6 === (int) $section['limit'] ? 6 : 5 );
				get_template_part( 'template-parts/home-strip', null, $section );
				break;
		}
	}
	return ob_get_clean();
}

/*
 * Screens that replace a container's sections: bag, checkout and account pages
 * (their woocommerce/ templates draw everything), product states (sold out,
 * choose size, added) and empty listings. WordPress renders inner blocks before
 * their container, so this is decided here, before them: otherwise the
 * WooCommerce shortcode and notices would run twice and the notices print in
 * the discarded copy.
 */
add_filter(
	'pre_render_block',
	function ( $pre, $parsed ) {
		if ( null !== $pre || miroir_block_preview() || ! function_exists( 'is_woocommerce' ) ) {
			return $pre;
		}
		switch ( $parsed['blockName'] ) {
			case 'miroir/page':
				return is_cart() || is_checkout() || is_account_page() ? miroir_block_page_content() : null;

			case 'miroir/product':
				$product = miroir_block_product();
				$state   = $product ? miroir_product_state( $product ) : '';
				if ( ! $state ) {
					return null;
				}
				ob_start();
				get_template_part( 'template-parts/product-state', null, array( 'product' => $product, 'state' => $state ) );
				return ob_get_clean();

			case 'miroir/listing':
				if ( have_posts() ) {
					return null;
				}
				ob_start();
				get_template_part( 'template-parts/empty-results', null, array( 'context' => is_search() ? 'search' : 'collection' ) );
				return ob_get_clean();
		}
		return null;
	},
	10,
	2
);

/**
 * Notices printed inline by the product and listing sections (inc/states.php
 * removes WooCommerce's default notice spots).
 */
function miroir_block_notices() {
	if ( miroir_block_preview() || ! function_exists( 'woocommerce_output_all_notices' ) ) {
		return '';
	}
	ob_start();
	woocommerce_output_all_notices();
	return ob_get_clean();
}
