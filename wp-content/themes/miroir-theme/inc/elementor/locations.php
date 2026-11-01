<?php
/**
 * Elementor templates in place of the block templates' sections. The block
 * templates stay untouched (also their Site Editor copies): the header and
 * footer template parts and the <main> section are swapped while they render.
 * Pages built with Elementor fill <main> on their own.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Screens an Elementor template can draw.
 *
 * @return array Location => label.
 */
function miroir_elementor_locations() {
	return array(
		'header'      => __( 'Header', 'miroir' ),
		'footer'      => __( 'Footer', 'miroir' ),
		'product'     => __( 'Product page', 'miroir' ),
		'collection'  => __( 'Shop and collections', 'miroir' ),
		'search'      => __( 'Search results', 'miroir' ),
		'page'        => __( 'Text pages (not built with Elementor)', 'miroir' ),
		'404'         => __( 'Page not found', 'miroir' ),
		'maintenance' => __( 'Maintenance page', 'miroir' ),
	);
}

/**
 * Template chosen for a location in Site Settings > Miroir > Templates.
 *
 * @param string $location Key of miroir_elementor_locations().
 * @return int Published template id, 0 for the theme's own design.
 */
function miroir_elementor_template( $location ) {
	$id = (int) miroir_elementor_setting( 'miroir_template_' . $location );
	return $id && 'publish' === get_post_status( $id ) ? $id : 0;
}

/**
 * HTML of an Elementor template.
 *
 * @param int $id Template id.
 * @return string
 */
function miroir_elementor_render( $id ) {
	return \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $id );
}

/**
 * Location of <main> on the current screen; '' when the block template draws it.
 *
 * @return string
 */
function miroir_elementor_main_location() {
	if ( is_singular( 'product' ) ) {
		return 'product';
	}
	if ( is_search() ) {
		return 'search';
	}
	if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) {
		return 'collection';
	}
	if ( is_404() ) {
		return '404';
	}
	if ( is_page() && ! is_front_page() && ! ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) ) {
		return 'page';
	}
	return '';
}

add_filter(
	'pre_render_block',
	function ( $pre, $parsed ) {
		// Site Editor previews show the block templates themselves.
		if ( null !== $pre || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return $pre;
		}
		switch ( $parsed['blockName'] ) {
			case 'core/template-part':
				$slug = $parsed['attrs']['slug'] ?? '';
				$id   = in_array( $slug, array( 'header', 'footer' ), true ) ? miroir_elementor_template( $slug ) : 0;
				if ( ! $id ) {
					return null;
				}
				$tag = $parsed['attrs']['tagName'] ?? $slug;
				return sprintf( '<%1$s class="%2$s wp-block-template-part">%3$s</%1$s>', tag_escape( $tag ), esc_attr( trim( ( $parsed['attrs']['className'] ?? '' ) ) ), miroir_elementor_render( $id ) );

			case 'miroir/main':
				if ( is_singular() && miroir_elementor_built( get_queried_object_id() ) ) {
					$content = miroir_block_page_content();
				} else {
					$location = miroir_elementor_main_location();
					$id       = $location ? miroir_elementor_template( $location ) : 0;
					if ( ! $id ) {
						return null;
					}
					$content = miroir_elementor_render( $id );
				}
				ob_start();
				require MIROIR_THEME_DIR . '/blocks/main/render.php';
				return ob_get_clean();
		}
		return null;
	},
	// After core's _wp_add_block_level_preset_styles (10), which returns null and would drop the swap.
	20,
	2
);
