<?php
/**
 * Elementor Site Settings > Miroir: which template draws each screen, the
 * store-wide settings of miroir_settings() and the labels of miroir_texts().
 * Values are stored in the active kit; miroir_mod() and miroir_text() read
 * them before the Customizer values (filters below). Empty = the default.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Saved Site Settings of the active kit (raw, as stored).
 *
 * @return array
 */
function miroir_elementor_kit_settings() {
	$id       = (int) get_option( 'elementor_active_kit' );
	$settings = $id ? get_post_meta( $id, '_elementor_page_settings', true ) : array();
	return is_array( $settings ) ? $settings : array();
}

/**
 * One saved Site Setting.
 *
 * @param string $key     Control id.
 * @param mixed  $default Value when unset.
 * @return mixed
 */
function miroir_elementor_setting( $key, $default = null ) {
	$settings = miroir_elementor_kit_settings();
	return $settings[ $key ] ?? $default;
}

add_filter(
	'miroir_mod',
	function ( $value, $id, $default ) {
		$saved   = miroir_elementor_setting( 'miroir_set_' . $id );
		$setting = miroir_settings()[ $id ];
		// Option settings (maintenance) are copied to their option on save; the option is current.
		if ( null === $saved || 'option' === ( $setting['storage'] ?? '' ) ) {
			return $value;
		}
		switch ( $setting['type'] ) {
			case 'checkbox':
				return 'yes' === $saved;
			case 'number':
				return is_numeric( $saved ) ? 0 + $saved : $default;
			case 'page':
				return (int) $saved;
		}
		return '' !== trim( (string) $saved ) ? $saved : $default;
	},
	10,
	3
);

add_filter(
	'miroir_saved_texts',
	function ( $texts ) {
		foreach ( miroir_elementor_kit_settings() as $key => $value ) {
			if ( 0 === strpos( $key, 'miroir_text_' ) && is_string( $value ) && '' !== trim( $value ) ) {
				$texts[ substr( $key, strlen( 'miroir_text_' ) ) ] = $value;
			}
		}
		return $texts;
	}
);

/**
 * The design's colours and fonts as Elementor Global Colors / Fonts: the
 * theme.json palette (tokens.css reads it) and the two font roles.
 *
 * @return array { colors: id => [ slug, title, default ], fonts: id => [ var, title, family, fallback ] }
 */
function miroir_elementor_globals() {
	$colors = array();
	foreach ( wp_get_global_settings( array( 'color', 'palette', 'theme' ) ) as $color ) {
		$colors[ 'miroir_' . str_replace( '-', '_', $color['slug'] ) ] = array( $color['slug'], $color['name'], strtolower( $color['color'] ) );
	}
	return array(
		'colors' => $colors,
		'fonts'  => array(
			'miroir_sans' => array( '--font-sans', __( 'Text (Avenir)', 'miroir' ), 'Avenir', 'system-ui, sans-serif' ),
			'miroir_mono' => array( '--font-mono', __( 'Labels (DM Mono)', 'miroir' ), 'DM Mono', 'ui-monospace, monospace' ),
		),
	);
}

/**
 * Global Colors / Fonts of Site Settings that differ from the design.
 *
 * @return array { colors: slug => colour, fonts: var => family }
 */
function miroir_elementor_global_changes() {
	$settings = miroir_elementor_kit_settings();
	$globals  = miroir_elementor_globals();
	$changes  = array( 'colors' => array(), 'fonts' => array() );
	foreach ( (array) ( $settings['custom_colors'] ?? array() ) as $item ) {
		$global = $globals['colors'][ $item['_id'] ?? '' ] ?? null;
		$color  = strtolower( (string) ( $item['color'] ?? '' ) );
		if ( $global && preg_match( '/^#[0-9a-f]{3,8}$|^rgba?\([0-9., ]+\)$/', $color ) && $color !== $global[2] ) {
			$changes['colors'][ $global[0] ] = $color;
		}
	}
	foreach ( (array) ( $settings['custom_typography'] ?? array() ) as $item ) {
		$global = $globals['fonts'][ $item['_id'] ?? '' ] ?? null;
		$family = trim( (string) ( $item['typography_font_family'] ?? '' ) );
		if ( $global && '' !== $family && $family !== $global[2] && ! preg_match( '/[;{}"\\\\]/', $family ) ) {
			$changes['fonts'][ $global[0] ] = sprintf( '"%s", %s', $family, $global[3] );
		}
	}
	return $changes;
}

add_filter(
	'miroir_global_styles_css',
	function ( $css ) {
		$changes = miroir_elementor_global_changes();
		$vars    = '';
		foreach ( $changes['colors'] as $slug => $color ) {
			$vars .= "--wp--preset--color--{$slug}:{$color};";
		}
		foreach ( $changes['fonts'] as $var => $family ) {
			$vars .= "{$var}:{$family};";
		}
		return $vars ? $css . ":root{{$vars}}" : $css;
	}
);

// No preload for a bundled font that Global Fonts replaced.
add_filter(
	'miroir_bundled_fonts',
	function ( $fonts ) {
		$changes = miroir_elementor_global_changes()['fonts'];
		$fonts['inter-tight-var-latin'] = $fonts['inter-tight-var-latin'] && ! isset( $changes['--font-sans'] );
		$fonts['dm-mono-400-latin']     = $fonts['dm-mono-400-latin'] && ! isset( $changes['--font-mono'] );
		return $fonts;
	}
);

class Miroir_Kit_Tab extends \Elementor\Core\Kits\Documents\Tabs\Tab_Base {

	public function get_id() {
		return 'miroir-settings';
	}

	public function get_title() {
		return __( 'Miroir', 'miroir' );
	}

	public function get_group() {
		return 'settings';
	}

	public function get_icon() {
		return 'eicon-woocommerce';
	}

	protected function register_tab_controls() {
		$this->start_controls_section( 'miroir_templates', array( 'label' => __( 'Templates', 'miroir' ), 'tab' => $this->get_id() ) );
		$templates = array( '' => __( 'Miroir design (built in)', 'miroir' ) );
		foreach ( get_posts( array( 'post_type' => 'elementor_library', 'numberposts' => -1, 'post_status' => 'publish', 'orderby' => 'title', 'order' => 'ASC' ) ) as $post ) {
			$templates[ (string) $post->ID ] = $post->post_title;
		}
		foreach ( miroir_elementor_locations() as $location => $label ) {
			$this->add_control(
				'miroir_template_' . $location,
				array(
					'label'   => $label,
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => $templates,
					'default' => '',
				)
			);
		}
		$this->add_control(
			'miroir_templates_note',
			array(
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Edit templates in Templates > Saved Templates. Home, bag, checkout and account are pages: edit them in Pages with Elementor.', 'miroir' ),
			)
		);
		$this->end_controls_section();

		foreach ( miroir_customizer_sections() as $section => list( $label ) ) {
			$settings = array_filter( miroir_settings(), fn( $s ) => $section === $s['section'] );
			if ( ! $settings ) {
				continue;
			}
			$this->start_controls_section( 'miroir_' . $section, array( 'label' => $label, 'tab' => $this->get_id() ) );
			foreach ( $settings as $id => $s ) {
				$this->add_control( 'miroir_set_' . $id, $this->setting_control( $s + array( 'id' => $id ) ) );
			}
			$this->end_controls_section();
		}

		$texts = miroir_texts();
		foreach ( miroir_text_sections() as $section => $label ) {
			/* translators: %s: area of the store, e.g. Bag. */
			$this->start_controls_section( 'miroir_labels_' . $section, array( 'label' => sprintf( __( 'Labels: %s', 'miroir' ), $label ), 'tab' => $this->get_id() ) );
			foreach ( $texts as $id => $text ) {
				if ( $section !== $text[0] ) {
					continue;
				}
				$this->add_control(
					'miroir_text_' . $id,
					array(
						'label'       => $text[2] ?? $id,
						'type'        => strlen( $text[1] ) > 60 ? \Elementor\Controls_Manager::TEXTAREA : \Elementor\Controls_Manager::TEXT,
						'placeholder' => $text[1],
						'label_block' => true,
					)
				);
			}
			$this->end_controls_section();
		}
	}

	/**
	 * Elementor control for a miroir_settings() entry.
	 *
	 * @param array $s Setting definition.
	 * @return array
	 */
	private function setting_control( $s ) {
		$args = array(
			'label'       => $s['label'],
			'description' => $s['description'] ?? '',
			'label_block' => in_array( $s['type'], array( 'textarea', 'url', 'page' ), true ),
		);
		switch ( $s['type'] ) {
			case 'checkbox':
				return $args + array( 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => $s['default'] ? 'yes' : '' );
			case 'number':
				return $args + array( 'type' => \Elementor\Controls_Manager::NUMBER, 'min' => $s['min'] ?? null, 'max' => $s['max'] ?? null, 'placeholder' => (string) $s['default'] );
			case 'select':
				// An option setting starts from its option, so saving other settings keeps it.
				$default = 'option' === ( $s['storage'] ?? '' ) ? get_option( $s['id'], $s['default'] ) : $s['default'];
				return $args + array( 'type' => \Elementor\Controls_Manager::SELECT, 'options' => $s['choices'], 'default' => $default );
			case 'textarea':
				return $args + array( 'type' => \Elementor\Controls_Manager::TEXTAREA, 'placeholder' => $s['default'], 'rows' => 4 );
			case 'page':
				$pages = array( '' => __( 'The page with the usual address', 'miroir' ) );
				foreach ( get_pages() as $page ) {
					$pages[ (string) $page->ID ] = $page->post_title;
				}
				return $args + array( 'type' => \Elementor\Controls_Manager::SELECT, 'options' => $pages, 'default' => '' );
		}
		return $args + array( 'type' => \Elementor\Controls_Manager::TEXT, 'placeholder' => (string) $s['default'] );
	}
}

add_action(
	'elementor/kit/register_tabs',
	function ( $kit ) {
		$kit->register_tab( 'miroir-settings', Miroir_Kit_Tab::class );
	}
);

// Maintenance stays an option too: `wp miroir theme maintenance` and inc/maintenance.php read it.
add_action(
	'elementor/document/after_save',
	function ( $document ) {
		if ( ! $document instanceof \Elementor\Core\Kits\Documents\Kit ) {
			return;
		}
		$mode = $document->get_settings( 'miroir_set_miroir_maintenance' );
		if ( in_array( $mode, array( 'on', 'off' ), true ) ) {
			update_option( 'miroir_maintenance', $mode );
		}
	}
);
