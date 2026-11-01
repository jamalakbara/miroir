<?php
/**
 * Theme settings registry. Every admin-editable value is declared once here;
 * the Customizer is built from it and templates read it with miroir_mod().
 * Defaults are the lofi values, so an untouched install renders exactly as
 * the pen and emits no override CSS.
 */

defined( 'ABSPATH' ) || exit;

/**
 * All settings, keyed by theme_mod name (or option name for 'option' storage).
 *
 * Keys per entry: section, label, type (text, textarea, color, number, select,
 * checkbox, url, page, upload, image), default, choices (select), unit and var
 * (CSS custom property written when the value differs from the default),
 * storage ('option' for values shared with WP-CLI), live (postMessage preview:
 * sets var, or the text of selector), description.
 *
 * @return array
 */
function miroir_settings() {
	static $settings = null;
	if ( null !== $settings ) {
		return $settings;
	}

	$px    = function ( $section, $label, $default, $var, $max = 200 ) {
		return array( 'section' => $section, 'label' => $label, 'type' => 'number', 'default' => $default, 'unit' => 'px', 'var' => $var, 'live' => true, 'min' => 0, 'max' => $max );
	};
	$cols  = function ( $label, $default, $var ) {
		return array( 'section' => 'miroir_layout', 'label' => $label, 'type' => 'number', 'default' => $default, 'var' => $var, 'live' => true, 'min' => 1, 'max' => 8 );
	};

	$settings = array(
		// Links (header and footer texts are blocks in Appearance > Editor).
		'instagram_url'          => array( 'section' => 'miroir_footer', 'label' => __( 'Instagram URL', 'miroir' ), 'type' => 'url', 'default' => 'https://www.instagram.com/miroir.id/', 'description' => __( 'Used on the maintenance page and in the default footer links.', 'miroir' ) ),

		// Typography.
		'type_scale'             => array( 'section' => 'miroir_typography', 'label' => __( 'Text size (%)', 'miroir' ), 'type' => 'number', 'default' => 100, 'min' => 80, 'max' => 140, 'description' => __( 'Scales every font size and line height.', 'miroir' ) ),

		// Layout & spacing.
		'gutter_d'               => $px( 'miroir_layout', __( 'Page margin · desktop (px)', 'miroir' ), 56, '--gutter-d' ),
		'gutter_t'               => $px( 'miroir_layout', __( 'Page margin · tablet (px)', 'miroir' ), 32, '--gutter-t' ),
		'gutter_m'               => $px( 'miroir_layout', __( 'Page margin · mobile (px)', 'miroir' ), 20, '--gutter-m' ),
		'home_space_d'           => $px( 'miroir_layout', __( 'Home section spacing · desktop (px)', 'miroir' ), 96, '--home-space-d', 300 ),
		'home_strip_space_d'     => $px( 'miroir_layout', __( 'Home product row spacing · desktop (px)', 'miroir' ), 120, '--home-strip-space-d', 300 ),
		'home_space_t'           => $px( 'miroir_layout', __( 'Home section spacing · tablet (px)', 'miroir' ), 40, '--home-space-t', 300 ),
		'home_space_m'           => $px( 'miroir_layout', __( 'Home section spacing · mobile (px)', 'miroir' ), 32, '--home-space-m', 300 ),
		'shop_cols_d'            => $cols( __( 'Shop columns · desktop', 'miroir' ), 4, '--shop-cols-d' ),
		'shop_cols_t'            => $cols( __( 'Shop columns · tablet', 'miroir' ), 3, '--shop-cols-t' ),
		'shop_cols_m'            => $cols( __( 'Shop columns · mobile', 'miroir' ), 2, '--shop-cols-m' ),
		'shop_col_gap_d'         => $px( 'miroir_layout', __( 'Shop column gap · desktop (px)', 'miroir' ), 32, '--shop-col-gap-d' ),
		'shop_row_gap_d'         => $px( 'miroir_layout', __( 'Shop row gap · desktop (px)', 'miroir' ), 64, '--shop-row-gap-d' ),
		'search_cols_d'          => $cols( __( 'Search columns · desktop', 'miroir' ), 4, '--search-cols-d' ),
		'search_cols_t'          => $cols( __( 'Search columns · tablet', 'miroir' ), 3, '--search-cols-t' ),
		'search_cols_m'          => $cols( __( 'Search columns · mobile', 'miroir' ), 2, '--search-cols-m' ),
		'products_per_page'      => array( 'section' => 'miroir_layout', 'label' => __( 'Products per page', 'miroir' ), 'type' => 'number', 'default' => 12, 'min' => 1, 'max' => 96 ),

		// Product page & cards.
		'image_ratio'            => array(
			'section' => 'miroir_product',
			'label'   => __( 'Product image shape', 'miroir' ),
			'type'    => 'select',
			'default' => '3 / 4',
			'var'     => '--image-ratio',
			'live'    => true,
			'choices' => array(
				'3 / 4' => __( 'Portrait 3:4', 'miroir' ),
				'2 / 3' => __( 'Tall 2:3', 'miroir' ),
				'4 / 5' => __( 'Portrait 4:5', 'miroir' ),
				'1 / 1' => __( 'Square 1:1', 'miroir' ),
			),
		),
		'product_photos'         => array( 'section' => 'miroir_product', 'label' => __( 'Show product photos (off: grey placeholders)', 'miroir' ), 'type' => 'checkbox', 'default' => false ),
		'card_hover_image'       => array( 'section' => 'miroir_product', 'label' => __( 'Show second gallery image on hover', 'miroir' ), 'type' => 'checkbox', 'default' => false ),
		'related_count'          => array( 'section' => 'miroir_product', 'label' => __( 'Similar products shown', 'miroir' ), 'type' => 'number', 'default' => 7, 'min' => 0, 'max' => 24 ),
		'similar_cols_d'         => $cols( __( 'Similar products columns · desktop', 'miroir' ), 7, '--similar-cols-d' ),
		'pdp_size_guide'         => array( 'section' => 'miroir_product', 'label' => __( 'Show size guide link on product pages', 'miroir' ), 'type' => 'checkbox', 'default' => false ),

		// Pages the theme links to.
		'page_contact'              => array( 'section' => 'miroir_pages', 'label' => __( 'Customer care page', 'miroir' ), 'type' => 'page', 'default' => 0 ),
		'page_size_guide'           => array( 'section' => 'miroir_pages', 'label' => __( 'Size guide page', 'miroir' ), 'type' => 'page', 'default' => 0 ),
		'page_payment_confirmation' => array( 'section' => 'miroir_pages', 'label' => __( 'Payment confirmation page', 'miroir' ), 'type' => 'page', 'default' => 0 ),
		'page_shipping_information' => array( 'section' => 'miroir_pages', 'label' => __( 'Shipping information page', 'miroir' ), 'type' => 'page', 'default' => 0 ),

		// Maintenance (option shared with `wp miroir theme maintenance on|off`).
		'miroir_maintenance'     => array(
			'section'     => 'miroir_maintenance',
			'label'       => __( 'Maintenance mode', 'miroir' ),
			'type'        => 'select',
			'storage'     => 'option',
			'default'     => 'off',
			'choices'     => array(
				'off' => __( 'Off: store is open', 'miroir' ),
				'on'  => __( 'On: visitors see the maintenance page', 'miroir' ),
			),
			'description' => __( 'Administrators keep browsing the store normally.', 'miroir' ),
		),
	);

	return $settings;
}

/**
 * Current value of a registered setting (theme_mod or option), falling back to its default.
 *
 * @param string $id Setting id from miroir_settings().
 * @return mixed
 */
function miroir_mod( $id ) {
	$settings = miroir_settings();
	if ( ! isset( $settings[ $id ] ) ) {
		return null;
	}
	$default = $settings[ $id ]['default'];
	$value   = 'option' === ( $settings[ $id ]['storage'] ?? '' ) ? get_option( $id, $default ) : get_theme_mod( $id, $default );
	// Elementor Site Settings > Miroir replaces the Customizer value (inc/elementor/kit-tab.php).
	return apply_filters( 'miroir_mod', $value, $id, $default );
}

/** Font sizes of tokens.css, scaled by the "Text size" setting. */
const MIROIR_TEXT_SIZES = array( 'xs' => 11, 'sm' => 12, 'base' => 13, 'md' => 16, 'lg' => 19, 'xl' => 28, '2xl' => 32, 'logo' => 26 );

/** Line-box tokens of tokens.css (font size => px). */
const MIROIR_LINE_HEIGHTS = array(
	'sans' => array( 11 => 13, 12 => 15, 13 => 16, 14 => 17, 16 => 19, 19 => 23, 20 => 24, 22 => 27, 26 => 31, 28 => 34, 32 => 39 ),
	'mono' => array( 11 => 14, 12 => 16, 13 => 17, 14 => 18, 16 => 21, 22 => 29, 24 => 31, 26 => 34, 28 => 36, 36 => 47 ),
);


/**
 * CSS overriding tokens.css with the values changed in the Customizer. Empty
 * when nothing differs from the defaults.
 *
 * @return string
 */
function miroir_custom_css() {
	$vars = array();
	foreach ( miroir_settings() as $id => $s ) {
		if ( empty( $s['var'] ) ) {
			continue;
		}
		$value = miroir_mod( $id );
		if ( '' === $value || null === $value || (string) $value === (string) $s['default'] ) {
			continue;
		}
		$vars[ $s['var'] ] = $value . ( $s['unit'] ?? '' );
	}

	$scale = (int) miroir_mod( 'type_scale' );
	if ( $scale && 100 !== $scale ) {
		$k = $scale / 100;
		foreach ( MIROIR_TEXT_SIZES as $name => $size ) {
			$vars[ "--text-{$name}" ] = round( $size * $k, 2 ) . 'px';
		}
		foreach ( MIROIR_LINE_HEIGHTS as $family => $heights ) {
			foreach ( $heights as $size => $height ) {
				$vars[ "--lh-{$family}-{$size}" ] = round( $height * $k ) . 'px';
			}
		}
	}

	if ( ! $vars ) {
		return '';
	}
	$css = '';
	foreach ( $vars as $name => $value ) {
		$css .= $name . ':' . wp_strip_all_tags( (string) $value ) . ';';
	}
	return ':root{' . $css . '}';
}
