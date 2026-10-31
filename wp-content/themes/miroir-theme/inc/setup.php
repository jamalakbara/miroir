<?php
/**
 * Theme supports, menus and assets.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	function () {
		load_theme_textdomain( 'miroir', MIROIR_THEME_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		// Photos are cover-cropped by CSS into the lofi's boxes, so WooCommerce keeps them uncropped.
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width'         => 600,
				'single_image_width'            => 1200,
				'gallery_thumbnail_image_width' => 200,
			)
		);
		add_theme_support( 'custom-logo', array( 'flex-width' => true, 'flex-height' => true ) );

		register_nav_menus(
			array(
				'primary'        => __( 'Header: primary links', 'miroir' ),
				'footer-account' => __( 'Footer: account links', 'miroir' ),
				'footer-policy'  => __( 'Footer: policy links', 'miroir' ),
				'footer-contact' => __( 'Footer: contact links', 'miroir' ),
				'plp-categories' => __( 'Shop sidebar: categories', 'miroir' ),
				'plp-featured'   => __( 'Shop sidebar: featured', 'miroir' ),
				'panel-nav'      => __( 'Menu panel', 'miroir' ),
			)
		);
	}
);

/**
 * Stylesheets in cascade order. Page CSS files are small and always loaded:
 * one request per file is cheaper to maintain than conditional loading while
 * the lofi is still moving.
 */
function miroir_styles() {
	return array( 'fonts', 'tokens', 'base', 'components', 'header', 'footer', 'home', 'shop', 'product', 'cart', 'checkout', 'account', 'flow', 'info', 'maintenance' );
}

add_action(
	'wp_enqueue_scripts',
	function () {
		$deps = array();
		foreach ( miroir_styles() as $name ) {
			$file = "/assets/css/{$name}.css";
			if ( ! file_exists( MIROIR_THEME_DIR . $file ) ) {
				continue;
			}
			wp_enqueue_style( "miroir-{$name}", MIROIR_THEME_URI . $file, $deps, filemtime( MIROIR_THEME_DIR . $file ) );
			$deps = array( "miroir-{$name}" );
		}

		$js = '/assets/js/theme.js';
		wp_enqueue_script( 'miroir-theme', MIROIR_THEME_URI . $js, array(), filemtime( MIROIR_THEME_DIR . $js ), array( 'strategy' => 'defer' ) );

		// The lofi is drawn without any block or global styles; they only fight the 1:1 CSS.
		foreach ( array( 'wp-block-library', 'wp-block-library-theme', 'classic-theme-styles', 'global-styles', 'core-block-supports' ) as $handle ) {
			wp_dequeue_style( $handle );
		}
	},
	20
);

add_action(
	'wp_head',
	function () {
		$fonts = array(
			'inter-tight-var-latin' => 'inter-tight' === miroir_mod( 'font_sans' ),
			'dm-mono-400-latin'     => 'dm-mono' === miroir_mod( 'font_mono' ),
		);
		foreach ( array_keys( array_filter( $fonts ) ) as $font ) {
			printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( MIROIR_THEME_URI . "/assets/fonts/{$font}.woff2" ) );
		}
	},
	1
);

// Load core block CSS as one stylesheet (wp-block-library, dequeued above) instead of per-block inline styles.
add_filter( 'should_load_separate_core_block_assets', '__return_false' );

// No emoji polyfill: it injects inline CSS and an extra script on every page.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
