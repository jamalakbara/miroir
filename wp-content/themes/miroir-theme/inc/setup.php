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

		miroir_dequeue_core_styles();

		// What Site Editor > Styles controls: preset variables (tokens.css reads them), the
		// font roles and Additional CSS. No layout or element styles.
		wp_add_inline_style( 'miroir-tokens', miroir_global_styles_css() );
	},
	20
);

/**
 * The lofi is drawn without WordPress's block and global styles; they only fight the 1:1 CSS.
 * Blocks the theme styles itself in page content lose their core CSS too; other core blocks
 * added in the editor keep theirs (core loads them per block). Some are enqueued while the
 * content renders (e.g. a block style variation), so this runs again right before printing.
 */
function miroir_dequeue_core_styles() {
	$handles = array( 'wp-block-library', 'wp-block-library-theme', 'classic-theme-styles', 'global-styles', 'core-block-supports', 'block-style-variation-styles' );
	foreach ( array( 'paragraph', 'heading', 'list', 'group', 'buttons', 'button', 'details', 'table', 'separator', 'template-part' ) as $block ) {
		$handles[] = "wp-block-{$block}";
	}
	foreach ( $handles as $handle ) {
		wp_dequeue_style( $handle );
	}
}
add_action( 'wp_print_styles', 'miroir_dequeue_core_styles', 1 );
add_action( 'wp_print_footer_scripts', 'miroir_dequeue_core_styles', 1 );

/**
 * CSS for the Site Editor's Styles: the palette and font family presets as
 * --wp--preset--* variables (tokens.css maps --bg, --ink, ... onto them), the
 * fonts chosen for Text (--font-sans) and Captions (--font-mono, the label
 * font), and Styles > Additional CSS.
 */
function miroir_global_styles_css() {
	$css    = wp_get_global_stylesheet( array( 'variables' ) );
	$styles = wp_get_global_styles();
	$roles  = array(
		'--font-sans' => $styles['typography']['fontFamily'] ?? '',
		'--font-mono' => $styles['elements']['caption']['typography']['fontFamily'] ?? '',
		'--bg'        => $styles['color']['background'] ?? '',
		'--ink'       => $styles['color']['text'] ?? '',
	);
	$vars = '';
	foreach ( array_filter( $roles, 'is_string' ) as $var => $value ) {
		// Preset references come back as var:preset|color|ink.
		$value = preg_replace( '/^var:preset\|([a-z-]+)\|([a-z0-9-]+)$/', 'var(--wp--preset--$1--$2)', $value );
		if ( '' !== $value && false === strpos( $value, '{' ) && false === strpos( $value, ';' ) ) {
			$vars .= "{$var}:{$value};";
		}
	}
	if ( $vars ) {
		$css .= ":root{{$vars}}";
	}
	return $css . wp_get_global_stylesheet( array( 'custom-css' ) );
}

add_action(
	'wp_head',
	function () {
		// The bundled fonts, unless Styles picked other fonts for their role.
		$styles = wp_get_global_styles();
		$fonts  = array(
			'inter-tight-var-latin' => empty( $styles['typography']['fontFamily'] ),
			'dm-mono-400-latin'     => empty( $styles['elements']['caption']['typography']['fontFamily'] ),
		);
		foreach ( array_keys( array_filter( $fonts ) ) as $font ) {
			printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( MIROIR_THEME_URI . "/assets/fonts/{$font}.woff2" ) );
		}
	},
	1
);

// No emoji polyfill: it injects inline CSS and an extra script on every page.
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
