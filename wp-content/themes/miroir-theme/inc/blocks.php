<?php
/**
 * Miroir blocks (blocks/*). The block templates (templates/, parts/) are built
 * from them, so every section the Site Editor shows prints the same markup as
 * the lofi. Home sections (editorial, banner, product strip) have their own
 * editor UI in assets/js/blocks-editor.js; the site sections share the generic
 * one in assets/js/blocks-site.js, driven by the "miroir" key of block.json.
 */

defined( 'ABSPATH' ) || exit;

/** Home section blocks with a hand-written editor UI. */
const MIROIR_HOME_BLOCKS = array( 'editorial', 'banner', 'product-strip' );

add_action(
	'init',
	function () {
		$deps = array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-data', 'wp-core-data', 'wp-i18n' );
		foreach ( array( 'miroir-blocks-editor' => '/assets/js/blocks-editor.js', 'miroir-blocks-site' => '/assets/js/blocks-site.js' ) as $handle => $js ) {
			wp_register_script( $handle, MIROIR_THEME_URI . $js, $deps, filemtime( MIROIR_THEME_DIR . $js ), true );
			wp_set_script_translations( $handle, 'miroir', MIROIR_THEME_DIR . '/languages' );
		}

		$site = array();
		foreach ( glob( MIROIR_THEME_DIR . '/blocks/*/block.json' ) as $file ) {
			$type = register_block_type( dirname( $file ) );
			if ( ! $type || in_array( basename( dirname( $file ) ), MIROIR_HOME_BLOCKS, true ) ) {
				continue;
			}
			$meta   = wp_json_file_decode( $file, array( 'associative' => true ) );
			$site[] = array( 'name' => $type->name ) + ( $meta['miroir'] ?? array() );
		}
		wp_add_inline_script( 'miroir-blocks-site', 'window.miroirSiteBlocks = ' . wp_json_encode( $site ) . ';', 'before' );
	}
);

/**
 * The static front page when it is built with Miroir blocks (older content,
 * e.g. the demo seeder's cover and shortcodes, does not count).
 *
 * @return int Page id, 0 when the home falls back to miroir_home_sections().
 */
function miroir_front_page_id() {
	$front = (int) get_option( 'page_on_front' );
	if ( 'page' !== get_option( 'show_on_front' ) || ! $front ) {
		return 0;
	}
	return false !== strpos( (string) get_post_field( 'post_content', $front ), '<!-- wp:miroir/' ) ? $front : 0;
}

add_filter(
	'block_categories_all',
	function ( $categories ) {
		return array_merge( array( array( 'slug' => 'miroir', 'title' => __( 'Miroir', 'miroir' ) ) ), $categories );
	}
);

// The editor and Site Editor preview blocks with the storefront CSS.
add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'editor-styles' );
		$styles = array();
		foreach ( array_merge( miroir_styles(), array( 'editor' ) ) as $name ) {
			$styles[] = "assets/css/{$name}.css";
		}
		add_editor_style( $styles );
	}
);
