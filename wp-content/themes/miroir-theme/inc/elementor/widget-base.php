<?php
/**
 * Base of the Miroir Elementor widgets. A widget is a section of the lofi: it
 * renders a tree of the theme's miroir/* blocks (blocks/*), so Elementor prints
 * exactly the markup the block templates print, with the same state swaps
 * (pre_render_block in inc/block-helpers.php). Controls come from the blocks'
 * block.json "miroir.controls", plus a "show" switch per optional part and the
 * section's texts (miroir_texts() ids, see miroir_with_texts()).
 */

defined( 'ABSPATH' ) || exit;

abstract class Miroir_Widget extends \Elementor\Widget_Base {

	/**
	 * The section as a block tree. Node keys: block (name), attrs (fixed
	 * attributes), part (key of an optional part: adds a "show" switch and
	 * prefixes its controls), label (part label), controls (false: no
	 * block.json controls), children (nodes).
	 *
	 * @return array
	 */
	abstract protected function tree();

	/**
	 * Texts of the section the widget can override, grouped: label => ids.
	 *
	 * @return array
	 */
	protected function texts() {
		return array();
	}

	public function get_categories() {
		return array( 'miroir' );
	}

	public function get_keywords() {
		return array( 'miroir' );
	}

	/** The section's own markup only: no inner wrapper. */
	public function has_widget_inner_wrapper(): bool {
		return false;
	}

	/** No outer wrapper on the storefront either; the editor needs it to select the widget. */
	public function before_render() {
		if ( miroir_elementor_preview() ) {
			parent::before_render();
		}
	}

	public function after_render() {
		if ( miroir_elementor_preview() ) {
			parent::after_render();
		}
	}

	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => $this->get_title() ) );
		$this->add_tree_controls( $this->tree(), '' );
		$this->end_controls_section();

		foreach ( $this->texts() as $label => $ids ) {
			$this->start_controls_section( 'texts_' . sanitize_key( $label ), array( 'label' => $label ) );
			$this->add_text_controls( $ids );
			$this->end_controls_section();
		}
	}

	/**
	 * Controls of a node and its parts.
	 *
	 * @param array  $node   Tree node.
	 * @param string $prefix Setting name prefix.
	 */
	protected function add_tree_controls( $node, $prefix ) {
		if ( isset( $node['part'] ) ) {
			$prefix = $node['part'] . '_';
			$this->add_control(
				$prefix . 'show',
				array(
					'label'        => $node['label'] ?? $node['part'],
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'default'      => 'yes',
					'return_value' => 'yes',
					'separator'    => 'before',
				)
			);
		}
		if ( $node['block'] && false !== ( $node['controls'] ?? true ) ) {
			foreach ( self::block_controls( $node['block'] ) as $control ) {
				if ( array_key_exists( $control['attr'], $node['attrs'] ?? array() ) ) {
					continue; // Fixed by the tree.
				}
				$args = array(
					'label'       => $control['label'],
					'description' => $control['help'] ?? '',
					'default'     => $control['default'],
				);
				if ( isset( $node['part'] ) ) {
					$args['condition'] = array( $node['part'] . '_show' => 'yes' );
				}
				switch ( $control['type'] ) {
					case 'toggle':
						$args['type']         = \Elementor\Controls_Manager::SWITCHER;
						$args['return_value'] = 'yes';
						$args['default']      = $control['default'] ? 'yes' : '';
						break;
					case 'number':
						$args['type'] = \Elementor\Controls_Manager::NUMBER;
						break;
					case 'select':
						$args['type']    = \Elementor\Controls_Manager::SELECT;
						$args['options'] = array_column( $control['options'] ?? array(), 1, 0 );
						break;
					default:
						$args['type'] = \Elementor\Controls_Manager::TEXT;
				}
				$this->add_control( $prefix . $control['attr'], $args );
			}
		}
		foreach ( $node['children'] ?? array() as $child ) {
			$this->add_tree_controls( $child, $prefix );
		}
	}

	/**
	 * Text controls: empty = the text from Site Settings > Miroir > Labels.
	 *
	 * @param string[] $ids miroir_texts() ids.
	 */
	protected function add_text_controls( $ids ) {
		$texts = miroir_texts();
		foreach ( $ids as $id ) {
			if ( ! isset( $texts[ $id ] ) ) {
				continue;
			}
			$this->add_control(
				'text_' . $id,
				array(
					'label'       => $texts[ $id ][2] ?? $id,
					'type'        => strlen( $texts[ $id ][1] ) > 60 ? \Elementor\Controls_Manager::TEXTAREA : \Elementor\Controls_Manager::TEXT,
					'placeholder' => miroir_text( $id ),
					'label_block' => true,
				)
			);
		}
	}

	/**
	 * block.json "miroir.controls" of a block, with each attribute's default.
	 *
	 * @param string $name Block name.
	 * @return array
	 */
	protected static function block_controls( $name ) {
		static $cache = array();
		if ( ! isset( $cache[ $name ] ) ) {
			$type     = WP_Block_Type_Registry::get_instance()->get_registered( $name );
			$file     = MIROIR_THEME_DIR . '/blocks/' . substr( $name, strlen( 'miroir/' ) ) . '/block.json';
			$meta     = file_exists( $file ) ? wp_json_file_decode( $file, array( 'associative' => true ) ) : array();
			$controls = array();
			foreach ( $meta['miroir']['controls'] ?? array() as $control ) {
				$control['default'] = $type->attributes[ $control['attr'] ]['default'] ?? '';
				$controls[]         = $control;
			}
			$cache[ $name ] = $controls;
		}
		return $cache[ $name ];
	}

	/**
	 * The parsed block of a tree node for the current settings; null when its
	 * part is switched off.
	 *
	 * @param array  $node     Tree node.
	 * @param array  $settings Widget settings.
	 * @param string $prefix   Setting name prefix.
	 * @return array|null
	 */
	protected function parsed_block( $node, $settings, $prefix = '' ) {
		if ( isset( $node['part'] ) ) {
			$prefix = $node['part'] . '_';
			if ( 'yes' !== ( $settings[ $prefix . 'show' ] ?? 'yes' ) ) {
				return null;
			}
		}
		$attrs = $node['attrs'] ?? array();
		if ( false !== ( $node['controls'] ?? true ) ) {
			foreach ( self::block_controls( $node['block'] ) as $control ) {
				$key = $prefix . $control['attr'];
				if ( array_key_exists( $control['attr'], $attrs ) || ! array_key_exists( $key, $settings ) ) {
					continue;
				}
				$value = $settings[ $key ];
				switch ( $control['type'] ) {
					case 'toggle':
						$value = 'yes' === $value;
						break;
					case 'number':
						$value = is_numeric( $value ) ? 0 + $value : $control['default'];
						break;
					default:
						$value = (string) $value;
				}
				$attrs[ $control['attr'] ] = $value;
			}
		}
		$inner = array();
		foreach ( $node['children'] ?? array() as $child ) {
			$block = $this->parsed_block( $child, $settings, $prefix );
			if ( $block ) {
				$inner[] = $block;
			}
		}
		// Same whitespace between inner blocks as parsed template files.
		$content = array();
		foreach ( $inner as $i => $unused ) {
			$content[] = $i ? "\n\n" : "\n";
			$content[] = null;
		}
		if ( $inner ) {
			$content[] = "\n";
		}
		return array(
			'blockName'    => $node['block'],
			'attrs'        => $attrs,
			'innerBlocks'  => $inner,
			'innerHTML'    => implode( '', array_filter( $content ) ),
			'innerContent' => $content,
		);
	}

	/**
	 * Texts set on the widget, by miroir_texts() id.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function text_values( $settings ) {
		$values = array();
		foreach ( $this->texts() as $ids ) {
			foreach ( $ids as $id ) {
				$values[ $id ] = (string) ( $settings[ 'text_' . $id ] ?? '' );
			}
		}
		return $values;
	}

	/**
	 * The section's markup.
	 *
	 * @param array $settings Widget settings.
	 * @return string
	 */
	protected function markup( $settings ) {
		$tree = $this->tree();
		if ( null === $tree['block'] ) {
			// A run of sections without a container, e.g. the footer part.
			$html = array();
			foreach ( $tree['children'] ?? array() as $child ) {
				$block = $this->parsed_block( $child, $settings );
				if ( $block ) {
					$html[] = render_block( $block );
				}
			}
			return implode( "\n\n", $html );
		}
		$block = $this->parsed_block( $tree, $settings );
		return $block ? render_block( $block ) : '';
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		echo miroir_with_texts( $this->text_values( $settings ), fn() => $this->markup( $settings ) ); // phpcs:ignore WordPress.Security.EscapeOutput -- block markup.
	}
}
