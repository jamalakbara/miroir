<?php
/**
 * Miroir blocks (blocks/*): home sections the client can add, reorder and edit
 * in the page editor. Each block renders the same template part as the PHP
 * fallback in front-page.php, so the markup matches the lofi exactly.
 */

defined( 'ABSPATH' ) || exit;

/** Block folders under blocks/. */
const MIROIR_BLOCKS = array( 'editorial', 'banner', 'product-strip' );

add_action(
	'init',
	function () {
		$js = '/assets/js/blocks-editor.js';
		wp_register_script(
			'miroir-blocks-editor',
			MIROIR_THEME_URI . $js,
			array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-server-side-render', 'wp-data', 'wp-core-data', 'wp-i18n' ),
			filemtime( MIROIR_THEME_DIR . $js ),
			true
		);
		wp_set_script_translations( 'miroir-blocks-editor', 'miroir', MIROIR_THEME_DIR . '/languages' );

		foreach ( MIROIR_BLOCKS as $block ) {
			register_block_type( MIROIR_THEME_DIR . "/blocks/{$block}" );
		}
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

// The editor previews blocks with the storefront CSS.
add_action(
	'after_setup_theme',
	function () {
		add_theme_support( 'editor-styles' );
		add_editor_style( array( 'assets/css/fonts.css', 'assets/css/tokens.css', 'assets/css/base.css', 'assets/css/components.css', 'assets/css/home.css', 'assets/css/editor.css' ) );
	}
);
