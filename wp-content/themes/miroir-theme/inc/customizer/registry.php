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

	$color = function ( $label, $default, $var ) {
		return array( 'section' => 'miroir_colors', 'label' => $label, 'type' => 'color', 'default' => $default, 'var' => $var, 'live' => true );
	};
	$px    = function ( $section, $label, $default, $var, $max = 200 ) {
		return array( 'section' => $section, 'label' => $label, 'type' => 'number', 'default' => $default, 'unit' => 'px', 'var' => $var, 'live' => true, 'min' => 0, 'max' => $max );
	};
	$cols  = function ( $label, $default, $var ) {
		return array( 'section' => 'miroir_layout', 'label' => $label, 'type' => 'number', 'default' => $default, 'var' => $var, 'live' => true, 'min' => 1, 'max' => 8 );
	};

	$settings = array(
		// Site identity.
		'logo_width'             => array( 'section' => 'title_tagline', 'label' => __( 'Logo width (px)', 'miroir' ), 'type' => 'number', 'default' => 110, 'unit' => 'px', 'var' => '--logo-w', 'live' => true, 'min' => 20, 'max' => 400, 'description' => __( 'Applies to an uploaded logo. Without one the Miroir wordmark is shown.', 'miroir' ) ),

		// Header & footer.
		'miroir_announcement'    => array( 'section' => 'miroir_header', 'label' => __( 'Announcement text', 'miroir' ), 'type' => 'text', 'default' => __( 'free shipping across indonesia', 'miroir' ), 'live' => true, 'selector' => '.announce__text' ),
		'announcement_show'      => array( 'section' => 'miroir_header', 'label' => __( 'Show announcement bar', 'miroir' ), 'type' => 'checkbox', 'default' => true ),
		'miroir_location'        => array( 'section' => 'miroir_footer', 'label' => __( 'Location line', 'miroir' ), 'type' => 'text', 'default' => __( 'Location : Indonesia', 'miroir' ), 'live' => true, 'selector' => '.site-footer__location' ),
		'location_show'          => array( 'section' => 'miroir_footer', 'label' => __( 'Show location line', 'miroir' ), 'type' => 'checkbox', 'default' => true ),
		'copyright'              => array( 'section' => 'miroir_footer', 'label' => __( 'Copyright', 'miroir' ), 'type' => 'text', 'default' => '© {year} {site}', 'description' => __( '{year} is the current year, {site} the site title in lower case.', 'miroir' ) ),
		'instagram_url'          => array( 'section' => 'miroir_footer', 'label' => __( 'Instagram URL', 'miroir' ), 'type' => 'url', 'default' => 'https://www.instagram.com/miroir.id/', 'description' => __( 'Used on the maintenance page and in the default footer links.', 'miroir' ) ),

		// Colours.
		'color_bg'               => $color( __( 'Background', 'miroir' ), '#ffffff', '--bg' ),
		'color_ink'              => $color( __( 'Text', 'miroir' ), '#111111', '--ink' ),
		'color_ink_2'            => $color( __( 'Secondary text', 'miroir' ), '#555555', '--ink-2' ),
		'color_ink_3'            => $color( __( 'Muted text', 'miroir' ), '#9a9a9a', '--ink-3' ),
		'color_line'             => $color( __( 'Lines & borders', 'miroir' ), '#d6d6d6', '--line' ),
		'color_surface'          => $color( __( 'Surface', 'miroir' ), '#f6f6f6', '--surface' ),
		'color_on_ink'           => $color( __( 'Text on dark buttons', 'miroir' ), '#ffffff', '--on-ink' ),
		'color_ph'               => $color( __( 'Image placeholder', 'miroir' ), '#ededed', '--ph' ),
		'color_ph_ink'           => $color( __( 'Image placeholder icon', 'miroir' ), '#b8b8b8', '--ph-ink' ),
		'color_error'            => $color( __( 'Error', 'miroir' ), '#b3261e', '--error' ),
		'color_success'          => $color( __( 'Success', 'miroir' ), '#2e6b3f', '--success' ),
		'color_logo'             => $color( __( 'Logo', 'miroir' ), '#242122', '--logo' ),

		// Typography.
		'font_sans'              => array(
			'section' => 'miroir_typography',
			'label'   => __( 'Text font', 'miroir' ),
			'type'    => 'select',
			'default' => 'inter-tight',
			'choices' => array(
				'inter-tight' => 'Inter Tight',
				'system'      => __( 'System sans-serif', 'miroir' ),
				'serif'       => __( 'System serif', 'miroir' ),
				'custom'      => __( 'Custom (upload below)', 'miroir' ),
			),
		),
		'font_sans_file'         => array( 'section' => 'miroir_typography', 'label' => __( 'Custom text font file (.woff2)', 'miroir' ), 'type' => 'upload', 'default' => '' ),
		'font_sans_name'         => array( 'section' => 'miroir_typography', 'label' => __( 'Custom text font name', 'miroir' ), 'type' => 'text', 'default' => '' ),
		'font_mono'              => array(
			'section' => 'miroir_typography',
			'label'   => __( 'Label font (names, labels, buttons)', 'miroir' ),
			'type'    => 'select',
			'default' => 'dm-mono',
			'choices' => array(
				'dm-mono' => 'DM Mono',
				'system'  => __( 'System monospace', 'miroir' ),
				'sans'    => __( 'Same as text font', 'miroir' ),
				'custom'  => __( 'Custom (upload below)', 'miroir' ),
			),
		),
		'font_mono_file'         => array( 'section' => 'miroir_typography', 'label' => __( 'Custom label font file (.woff2)', 'miroir' ), 'type' => 'upload', 'default' => '' ),
		'font_mono_name'         => array( 'section' => 'miroir_typography', 'label' => __( 'Custom label font name', 'miroir' ), 'type' => 'text', 'default' => '' ),
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

		// Search.
		'popular_searches'       => array(
			'section'     => 'miroir_search',
			'label'       => __( 'Popular searches', 'miroir' ),
			'type'        => 'textarea',
			'default'     => __( 'Shirts', 'miroir' ) . " | ?s=shirt\n" . __( 'Dresses', 'miroir' ) . " | category:dress\n" . __( 'New arrivals', 'miroir' ) . ' | category:new-collection',
			'description' => __( 'One per line: label | link. A link can be a URL, "?s=word" for a search, or "category:slug" for a collection.', 'miroir' ),
		),

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
	if ( 'option' === ( $settings[ $id ]['storage'] ?? '' ) ) {
		return get_option( $id, $default );
	}
	return get_theme_mod( $id, $default );
}

/** Font sizes of tokens.css, scaled by the "Text size" setting. */
const MIROIR_TEXT_SIZES = array( 'xs' => 11, 'sm' => 12, 'base' => 13, 'md' => 16, 'lg' => 19, 'xl' => 28, '2xl' => 32, 'logo' => 26 );

/** Line-box tokens of tokens.css (font size => px). */
const MIROIR_LINE_HEIGHTS = array(
	'sans' => array( 11 => 13, 12 => 15, 13 => 16, 14 => 17, 16 => 19, 19 => 23, 20 => 24, 22 => 27, 26 => 31, 28 => 34, 32 => 39 ),
	'mono' => array( 11 => 14, 12 => 16, 13 => 17, 14 => 18, 16 => 21, 22 => 29, 24 => 31, 26 => 34, 28 => 36, 36 => 47 ),
);

/**
 * Font stacks for the font settings.
 *
 * @param string $role 'sans' or 'mono'.
 * @return string|null CSS font-family value, or null for the bundled default.
 */
function miroir_font_stack( $role ) {
	$choice = miroir_mod( "font_{$role}" );
	$custom = trim( (string) miroir_mod( "font_{$role}_name" ) );
	if ( 'custom' === $choice && $custom && miroir_mod( "font_{$role}_file" ) ) {
		return '"' . str_replace( '"', '', $custom ) . '", ' . ( 'mono' === $role ? 'ui-monospace, monospace' : 'system-ui, sans-serif' );
	}
	switch ( $choice ) {
		case 'system':
			return 'mono' === $role ? 'ui-monospace, SFMono-Regular, Menlo, Consolas, monospace' : 'system-ui, -apple-system, "Segoe UI", Roboto, sans-serif';
		case 'serif':
			return 'Georgia, "Times New Roman", serif';
		case 'sans':
			return 'var(--font-sans)';
	}
	return null;
}

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

	$faces = '';
	foreach ( array( 'sans', 'mono' ) as $role ) {
		$stack = miroir_font_stack( $role );
		if ( ! $stack ) {
			continue;
		}
		$vars[ "--font-{$role}" ] = $stack;
		$file                     = miroir_mod( "font_{$role}_file" );
		if ( 'custom' === miroir_mod( "font_{$role}" ) && $file ) {
			$faces .= sprintf( '@font-face{font-family:"%s";font-style:normal;font-weight:100 900;font-display:swap;src:url(%s) format("woff2")}', esc_attr( str_replace( '"', '', trim( miroir_mod( "font_{$role}_name" ) ) ) ), esc_url( $file ) );
		}
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

	if ( ! $vars && ! $faces ) {
		return '';
	}
	$css = '';
	foreach ( $vars as $name => $value ) {
		$css .= $name . ':' . wp_strip_all_tags( (string) $value ) . ';';
	}
	return $faces . ( $css ? ':root{' . $css . '}' : '' );
}
