<?php
/**
 * Appearance > Customize > Miroir: panels, sections and controls built from
 * miroir_settings() and miroir_texts(), the override CSS and the live preview.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Customizer sections of the Miroir panel.
 *
 * @return array id => [ title, description ]
 */
function miroir_customizer_sections() {
	return array(
		'miroir_header'      => array( __( 'Header', 'miroir' ), __( 'Choose whether the homepage navigation overlays the campaign video. Other pages keep the solid header.', 'miroir' ) ),
		'miroir_footer'      => array( __( 'Links', 'miroir' ), __( 'Logo, announcement, menus and footer lines are blocks: Appearance > Editor > Patterns > Header / Footer. Their links come from Appearance > Menus.', 'miroir' ) ),
		'miroir_typography'  => array( __( 'Text size', 'miroir' ), __( 'Colours and fonts: Appearance > Editor > Styles (Text = body font, Captions = label font).', 'miroir' ) ),
		'miroir_layout'      => array( __( 'Layout & spacing', 'miroir' ), '' ),
		'miroir_product'     => array( __( 'Products', 'miroir' ), __( 'Product photos come from each product\'s image and gallery in Products.', 'miroir' ) ),
		'miroir_pages'       => array( __( 'Linked pages', 'miroir' ), __( 'Pages the store links to from buttons. Leave unset to use the page with the usual address (/contact/, /size-guide/ …).', 'miroir' ) ),
		'miroir_maintenance' => array( __( 'Maintenance', 'miroir' ), __( 'The maintenance page texts are under Text & labels > Maintenance page.', 'miroir' ) ),
	);
}

/**
 * Sanitize callback for a setting type.
 *
 * @param array $s Setting definition.
 * @return callable
 */
function miroir_sanitizer( $s ) {
	switch ( $s['type'] ) {
		case 'color':
			return 'sanitize_hex_color';
		case 'number':
			return function ( $value ) use ( $s ) {
				$value = is_numeric( $value ) ? 0 + $value : $s['default'];
				return max( $s['min'] ?? PHP_INT_MIN, min( $s['max'] ?? PHP_INT_MAX, $value ) );
			};
		case 'checkbox':
			return function ( $value ) {
				return (bool) $value;
			};
		case 'select':
			return function ( $value ) use ( $s ) {
				return array_key_exists( $value, $s['choices'] ) ? $value : $s['default'];
			};
		case 'url':
		case 'upload':
			return 'esc_url_raw';
		case 'page':
			return 'absint';
		case 'textarea':
			return 'sanitize_textarea_field';
	}
	return 'sanitize_text_field';
}

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ) {
		$wp_customize->add_panel(
			'miroir',
			array(
				'title'       => __( 'Miroir theme', 'miroir' ),
				'description' => __( 'Settings the Site Editor has no place for. Templates, header, footer, colours and fonts: Appearance > Editor. Defaults match the miroir design; clear a field to go back to its default.', 'miroir' ),
				'priority'    => 30,
			)
		);
		foreach ( miroir_customizer_sections() as $id => $section ) {
			$wp_customize->add_section( $id, array( 'title' => $section[0], 'description' => $section[1], 'panel' => 'miroir' ) );
		}

		foreach ( miroir_settings() as $id => $s ) {
			$wp_customize->add_setting(
				$id,
				array(
					'type'              => 'option' === ( $s['storage'] ?? '' ) ? 'option' : 'theme_mod',
					'default'           => $s['default'],
					'transport'         => empty( $s['live'] ) ? 'refresh' : 'postMessage',
					'sanitize_callback' => miroir_sanitizer( $s ),
				)
			);
			$args = array(
				'section'     => $s['section'],
				'label'       => $s['label'],
				'description' => $s['description'] ?? '',
			);
			if ( 'color' === $s['type'] ) {
				$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, $args ) );
				continue;
			}
			if ( 'upload' === $s['type'] ) {
				$wp_customize->add_control( new WP_Customize_Upload_Control( $wp_customize, $id, $args ) );
				continue;
			}
			$args['type'] = 'page' === $s['type'] ? 'dropdown-pages' : $s['type'];
			if ( 'select' === $s['type'] ) {
				$args['choices'] = $s['choices'];
			}
			if ( 'number' === $s['type'] ) {
				$args['input_attrs'] = array( 'min' => $s['min'] ?? 0, 'max' => $s['max'] ?? 999, 'step' => 1 );
			}
			$wp_customize->add_control( $id, $args );
		}

		// Text & labels: one section per store area, every string pre-filled with its default.
		$wp_customize->add_panel(
			'miroir_text',
			array(
				'title'       => __( 'Text & labels', 'miroir' ),
				'description' => __( 'Every heading, message and button text of the store. %s, %1$s … are filled in by the store (a search term, a count, a name) and must be kept.', 'miroir' ),
				'priority'    => 31,
			)
		);
		foreach ( miroir_text_sections() as $section => $title ) {
			$wp_customize->add_section( "miroir_text_{$section}", array( 'title' => $title, 'panel' => 'miroir_text' ) );
		}
		foreach ( miroir_texts() as $id => $text ) {
			$wp_customize->add_setting(
				"miroir_text[{$id}]",
				array(
					'type'              => 'theme_mod',
					'default'           => $text[1],
					'sanitize_callback' => strlen( $text[1] ) > 90 ? 'sanitize_textarea_field' : 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				"miroir_text[{$id}]",
				array(
					'section'     => "miroir_text_{$text[0]}",
					'label'       => $text[2] ?? '',
					'type'        => strlen( $text[1] ) > 90 ? 'textarea' : 'text',
					'input_attrs' => array( 'placeholder' => $text[1] ),
				)
			);
		}
	}
);

// Override CSS right after tokens.css; empty (no tag content) while everything is default.
add_action(
	'wp_enqueue_scripts',
	function () {
		$css = miroir_custom_css();
		if ( $css ) {
			wp_add_inline_style( 'miroir-tokens', $css );
		}
	},
	21
);

add_action(
	'customize_preview_init',
	function () {
		$js   = '/assets/js/customizer-preview.js';
		$live = array();
		foreach ( miroir_settings() as $id => $s ) {
			if ( ! empty( $s['live'] ) ) {
				$live[ $id ] = array_intersect_key( $s, array_flip( array( 'var', 'unit', 'selector' ) ) );
			}
		}
		wp_enqueue_script( 'miroir-customizer-preview', MIROIR_THEME_URI . $js, array( 'customize-preview' ), filemtime( MIROIR_THEME_DIR . $js ), true );
		wp_add_inline_script( 'miroir-customizer-preview', 'window.miroirPreview = ' . wp_json_encode( $live ) . ';', 'before' );
	}
);
