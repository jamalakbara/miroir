<?php
/**
 * Elementor integration (loaded only while Elementor is active). The site is
 * edited in Elementor:
 * - Header, footer, product, collection, search, text page, 404 and
 *   maintenance screens come from Elementor templates (Templates > Saved
 *   Templates) chosen in Site Settings > Miroir > Templates (locations.php).
 * - Home, bag, checkout and account are pages built in Elementor.
 * - The sections are the Miroir widgets (widgets.php), which print the
 *   theme's blocks, so the storefront stays 1:1 with the lofi.
 * - Store-wide settings and labels live in Site Settings > Miroir (kit-tab.php).
 * Without a template the theme's block templates draw the screen as before.
 */

defined( 'ABSPATH' ) || exit;

require MIROIR_THEME_DIR . '/inc/elementor/kit-tab.php';
require MIROIR_THEME_DIR . '/inc/elementor/locations.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require MIROIR_THEME_DIR . '/inc/elementor/cli.php';
}

add_action(
	'elementor/elements/categories_registered',
	function ( $elements ) {
		$elements->add_category( 'miroir', array( 'title' => __( 'Miroir', 'miroir' ), 'icon' => 'eicon-woocommerce' ) );
	}
);

add_action(
	'elementor/widgets/register',
	function ( $widgets ) {
		require_once MIROIR_THEME_DIR . '/inc/elementor/widget-base.php';
		require_once MIROIR_THEME_DIR . '/inc/elementor/widgets.php';
		foreach ( miroir_elementor_widget_classes() as $class ) {
			$widgets->register( new $class() );
		}
	}
);

/*
 * On the storefront the document wrapper loses Elementor's "elementor" class, so
 * Elementor's base rules (.elementor img, .elementor a ...) do not restyle the
 * lofi markup; its own elementor-{id} class keeps the template's CSS working.
 */
add_filter(
	'elementor/document/wrapper_attributes',
	function ( $attributes ) {
		if ( ! miroir_elementor_preview() ) {
			$attributes['class'] = preg_replace( '/(^| )elementor( |$)/', '$1miroir-elementor$2', $attributes['class'] );
		}
		return $attributes;
	}
);

// Elementor's wrappers around Miroir sections take no space (assets/css/elementor.css).
add_action(
	'wp_enqueue_scripts',
	function () {
		$file = '/assets/css/elementor.css';
		wp_enqueue_style( 'miroir-elementor', MIROIR_THEME_URI . $file, array( 'miroir-tokens' ), filemtime( MIROIR_THEME_DIR . $file ) );
	},
	21
);

/*
 * Elementor's frontend JavaScript (~35 KB gzipped) only runs widgets and effects
 * of Elementor itself; Miroir sections need none. It is left out unless the
 * page has another widget, an entrance animation or a video/slideshow background.
 */
add_action(
	'elementor/frontend/before_render',
	function ( $element ) {
		$settings = $element->get_settings();
		if (
			( 'widget' === $element->get_type() && 0 !== strpos( $element->get_name(), 'miroir-' ) )
			|| ! empty( $settings['animation'] ) || ! empty( $settings['_animation'] )
			|| in_array( $settings['background_background'] ?? '', array( 'video', 'slideshow' ), true )
		) {
			$GLOBALS['miroir_elementor_needs_js'] = true;
		}
	}
);
add_action(
	'wp_print_footer_scripts',
	function () {
		if ( empty( $GLOBALS['miroir_elementor_needs_js'] ) && ! miroir_elementor_preview() ) {
			wp_dequeue_script( 'elementor-frontend' );
		}
	},
	1
);

/*
 * Signing in to wp-admin opens the WordPress dashboard; Elementor opens only when
 * chosen (Edit with Elementor, Templates, Site Settings). A login link that
 * pointed into the editor or Elementor's onboarding goes to the dashboard instead.
 */
add_filter(
	'login_redirect',
	function ( $redirect_to, $requested, $user ) {
		if ( $user instanceof WP_User && $user->has_cap( 'edit_posts' ) && preg_match( '/[?&](action=elementor|page=elementor-app)\b/', (string) $requested ) ) {
			return admin_url();
		}
		return $redirect_to;
	},
	10,
	3
);
// No jump to Elementor's onboarding after the plugin is activated or updated.
add_action(
	'admin_init',
	function () {
		delete_transient( 'elementor_activation_redirect' );
	},
	1
);

// One place to edit: the Customizer's Miroir panels and Site Editor > Styles move to Site Settings.
add_action(
	'customize_register',
	function ( $wp_customize ) {
		$wp_customize->remove_panel( 'miroir' );
		$wp_customize->remove_panel( 'miroir_text' );
	},
	1000
);
add_action(
	'admin_menu',
	function () {
		remove_submenu_page( 'themes.php', 'site-editor.php' );
	},
	1000
);

/**
 * A parsed block for render_block().
 *
 * @param string $name  Block name.
 * @param array  $attrs Attributes.
 * @return array
 */
function miroir_parsed_block( $name, $attrs = array() ) {
	return array(
		'blockName'    => $name,
		'attrs'        => $attrs,
		'innerBlocks'  => array(),
		'innerHTML'    => '',
		'innerContent' => array(),
	);
}

/**
 * Image control (media library).
 *
 * @param \Elementor\Widget_Base $widget Widget.
 * @param string                 $name   Setting name.
 * @param string                 $label  Label.
 */
function miroir_elementor_image_control( $widget, $name, $label ) {
	$widget->add_control(
		$name,
		array(
			'label'       => $label,
			'type'        => \Elementor\Controls_Manager::MEDIA,
			'default'     => array( 'id' => 0, 'url' => '' ),
			'description' => __( 'Empty: grey placeholder.', 'miroir' ),
		)
	);
}

/**
 * Attachment id of an image control value.
 *
 * @param mixed $value MEDIA control value.
 * @return int
 */
function miroir_elementor_image_id( $value ) {
	return is_array( $value ) ? (int) ( $value['id'] ?? 0 ) : 0;
}

/**
 * Caption and link controls of the home sections.
 *
 * @param \Elementor\Widget_Base $widget Widget.
 * @param string                 $help   What an empty link goes to.
 */
function miroir_elementor_link_controls( $widget, $help ) {
	$widget->add_control( 'caption', array( 'label' => __( 'Caption', 'miroir' ), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => true ) );
	$widget->add_control(
		'url',
		array(
			'label'       => __( 'Link', 'miroir' ),
			'type'        => \Elementor\Controls_Manager::URL,
			'description' => $help,
			'options'     => false,
		)
	);
}

/**
 * Collections for a select: slug => name (count).
 *
 * @return array
 */
function miroir_elementor_category_options() {
	$terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) );
	$items = array();
	foreach ( is_wp_error( $terms ) ? array() : $terms as $term ) {
		$items[ $term->slug ] = sprintf( '%s (%d)', $term->name, $term->count );
	}
	asort( $items );
	return $items;
}

/**
 * Products for a select: id => name.
 *
 * @return array
 */
function miroir_elementor_product_options() {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}
	$items = array();
	foreach ( wc_get_products( array( 'limit' => 500, 'status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ) ) as $product ) {
		$items[ $product->get_id() ] = $product->get_name();
	}
	return $items;
}

/**
 * Text ids of a Text & labels section.
 *
 * @param string $section Section key of miroir_text_sections().
 * @return string[]
 */
function miroir_text_ids( $section ) {
	return array_keys( array_filter( miroir_texts(), fn( $text ) => $section === $text[0] ) );
}

/**
 * Layout of a text page from its page template: FAQ = info, guide = guide.
 *
 * @return string loose | guide | info
 */
function miroir_page_variant() {
	switch ( get_page_template_slug() ) {
		case 'page-faq':
			return 'info';
		case 'page-guide':
			return 'guide';
	}
	return 'loose';
}
