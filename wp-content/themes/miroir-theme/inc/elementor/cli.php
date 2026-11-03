<?php
/**
 * WP-CLI: `wp miroir elementor setup|reset`. Setup hands the site to Elementor
 * with the current design: one template per screen and the home, bag,
 * checkout and account pages, all built from Miroir widgets, plus Elementor's
 * lean output options. Reset returns every screen to the block templates.
 */

defined( 'ABSPATH' ) || exit;

class Miroir_Elementor_Command {

	/** Sections of each location's template, top to bottom. */
	const TEMPLATES = array(
		'header'      => array( 'miroir-announcement', 'miroir-navigation' ),
		'footer'      => array( 'miroir-footer' ),
		'product'     => array( 'miroir-product' ),
		'collection'  => array( 'miroir-collection' ),
		'search'      => array( 'miroir-search-results' ),
		'page'        => array( 'miroir-page' ),
		'404'         => array( 'miroir-not-found' ),
		'maintenance' => array( 'miroir-maintenance' ),
	);

	/** WooCommerce pages built with Elementor: page option => widget. */
	const STORE_PAGES = array(
		'woocommerce_cart_page_id'      => 'miroir-bag',
		'woocommerce_checkout_page_id'  => 'miroir-checkout',
		'woocommerce_myaccount_page_id' => 'miroir-account',
	);

	/**
	 * Build the Elementor templates and pages from the current design and use them.
	 *
	 * ## OPTIONS
	 *
	 * [--force]
	 * : Rebuild templates and pages that already exist (their Elementor edits are lost).
	 *
	 * @when after_wp_load
	 */
	public function setup( $args = array(), $assoc = array() ) {
		$force = ! empty( $assoc['force'] );
		self::as_admin();
		$this->options();

		$kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();
		if ( ! $kit->get_id() ) {
			\Elementor\Plugin::$instance->kits_manager->create_default();
			$kit = \Elementor\Plugin::$instance->kits_manager->get_active_kit();
		}
		$settings = (array) $kit->get_meta( '_elementor_page_settings' );
		$settings = self::globals( $settings );

		foreach ( self::TEMPLATES as $location => $widgets ) {
			$key   = 'miroir_template_' . $location;
			$title = sprintf( 'Miroir: %s', miroir_elementor_locations()[ $location ] );
			$id    = (int) ( $settings[ $key ] ?? 0 );
			if ( ! $id || ! get_post( $id ) ) {
				// Unassigned (e.g. after `reset`): reuse the template setup made before.
				$found = get_posts( array( 'post_type' => 'elementor_library', 'title' => $title, 'post_status' => 'any', 'numberposts' => 1, 'fields' => 'ids' ) );
				$id    = $found ? (int) $found[0] : 0;
				if ( $id && ! $force ) {
					$settings[ $key ] = (string) $id;
					WP_CLI::log( "template {$location}: reassigned #{$id}" );
					continue;
				}
			} elseif ( ! $force ) {
				WP_CLI::log( "template {$location}: kept #{$id}" );
				continue;
			}
			$elements = self::stack( array_map( fn( $widget ) => self::widget( $widget ), $widgets ) );
			$id       = $this->save_template( $id, $title, $elements );
			$settings[ $key ] = (string) $id;
			WP_CLI::log( "template {$location}: #{$id}" );
		}
		$this->save_kit( $kit, $settings );

		$home = (int) get_option( 'page_on_front' );
		if ( $home && ( $force || ! miroir_elementor_built( $home ) ) ) {
			$this->save_page( $home, self::stack( self::home_widgets( $home ) ) );
			WP_CLI::log( "page home: #{$home}" );
		}
		foreach ( self::STORE_PAGES as $option => $widget ) {
			$page = (int) get_option( $option );
			if ( $page && ( $force || ! miroir_elementor_built( $page ) ) ) {
				$this->save_page( $page, self::stack( array( self::widget( $widget ) ) ) );
				WP_CLI::log( "page {$widget}: #{$page}" );
			}
		}
		// Every other page becomes a Text page widget holding the text it shows today.
		foreach ( self::text_pages() as $page ) {
			if ( $force || ! miroir_elementor_built( $page ) ) {
				$this->save_page( $page, self::stack( array( self::widget( 'miroir-text-page', array( 'text' => self::page_html( $page ) ) ) ) ) );
				WP_CLI::log( "page text: #{$page} " . get_the_title( $page ) );
			}
		}

		\Elementor\Plugin::$instance->files_manager->clear_cache();
		WP_CLI::success( 'The site is managed in Elementor.' );
	}

	/**
	 * Draw every screen with the block templates again. Templates and page
	 * contents stay in the database (the pages get their blocks back).
	 *
	 * @when after_wp_load
	 */
	public function reset() {
		self::as_admin();
		$kit      = \Elementor\Plugin::$instance->kits_manager->get_active_kit();
		$settings = (array) $kit->get_meta( '_elementor_page_settings' );
		foreach ( array_keys( self::TEMPLATES ) as $location ) {
			unset( $settings[ 'miroir_template_' . $location ] );
		}
		$this->save_kit( $kit, $settings );

		$pages = array();
		foreach ( get_posts( array( 'post_type' => 'page', 'post_status' => 'any', 'numberposts' => -1, 'meta_key' => '_miroir_blocks_before_elementor', 'fields' => 'ids' ) ) as $page ) { // phpcs:ignore WordPress.DB.SlowDBQuery
			$pages[ $page ] = get_post_meta( $page, '_miroir_blocks_before_elementor', true );
		}
		foreach ( array_filter( $pages ) as $page => $content ) {
			delete_post_meta( $page, '_elementor_edit_mode' );
			wp_update_post( array( 'ID' => $page, 'post_content' => wp_slash( $content ) ) );
			WP_CLI::log( "page #{$page}: blocks restored" );
		}
		\Elementor\Plugin::$instance->files_manager->clear_cache();
		WP_CLI::success( 'The block templates draw the site again.' );
	}

	/**
	 * Add the design's colours and fonts to Global Colors / Fonts (kept when
	 * already there), and copy Customizer values into Site Settings > Miroir.
	 *
	 * @param array $settings Kit settings.
	 * @return array
	 */
	private static function globals( $settings ) {
		$globals = miroir_elementor_globals();
		$colors  = array_column( (array) ( $settings['custom_colors'] ?? array() ), null, '_id' );
		foreach ( $globals['colors'] as $id => list( $slug, $title, $color ) ) {
			$colors[ $id ] = $colors[ $id ] ?? array( '_id' => $id, 'title' => $title, 'color' => $color );
		}
		$settings['custom_colors'] = array_values( $colors );

		$fonts = array_column( (array) ( $settings['custom_typography'] ?? array() ), null, '_id' );
		foreach ( $globals['fonts'] as $id => list( $var, $title, $family ) ) {
			$fonts[ $id ] = $fonts[ $id ] ?? array( '_id' => $id, 'title' => $title, 'typography_typography' => 'custom', 'typography_font_family' => $family );
		}
		$settings['custom_typography'] = array_values( $fonts );

		foreach ( miroir_settings() as $id => $s ) {
			$key = 'miroir_set_' . $id;
			if ( isset( $settings[ $key ] ) || 'option' === ( $s['storage'] ?? '' ) ) {
				continue;
			}
			$value = get_theme_mod( $id, null );
			if ( null !== $value && $value !== $s['default'] ) {
				$settings[ $key ] = 'checkbox' === $s['type'] ? ( $value ? 'yes' : '' ) : (string) $value;
			}
		}
		foreach ( (array) get_theme_mod( 'miroir_text', array() ) as $id => $text ) {
			if ( '' !== trim( (string) $text ) && ! isset( $settings[ 'miroir_text_' . $id ] ) ) {
				$settings[ 'miroir_text_' . $id ] = (string) $text;
			}
		}
		return $settings;
	}

	/** Elementor saves documents only for a user who may edit them (none under WP-CLI unless --user). */
	private static function as_admin() {
		if ( get_current_user_id() ) {
			return;
		}
		$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
		if ( ! $admins ) {
			WP_CLI::error( 'No administrator to save Elementor documents as; pass --user.' );
		}
		wp_set_current_user( (int) $admins[0] );
	}

	/** Elementor output options: containers, lean markup, no default colours/fonts or Google Fonts. */
	private function options() {
		$options = array(
			'elementor_experiment-container'          => 'active',
			'elementor_experiment-e_optimized_markup' => 'active',
			'elementor_experiment-e_font_icon_svg'    => 'active',
			'elementor_disable_color_schemes'         => 'yes',
			'elementor_disable_typography_schemes'    => 'yes',
			'elementor_google_font'                   => '0',
		);
		foreach ( $options as $name => $value ) {
			update_option( $name, $value );
		}
		$types = (array) get_option( 'elementor_cpt_support', array( 'page', 'post' ) );
		update_option( 'elementor_cpt_support', array_values( array_unique( array_merge( $types, array( 'page' ) ) ) ) );
	}

	/**
	 * Elements of a page or template: one full-width container holding the sections.
	 *
	 * @param array $widgets Widget elements.
	 * @return array
	 */
	private static function stack( $widgets ) {
		$zero = array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true );
		return array(
			array(
				'id'       => self::element_id(),
				'elType'   => 'container',
				'isInner'  => false,
				'settings' => array(
					'content_width' => 'full',
					'padding'       => $zero,
					'margin'        => $zero,
					'flex_gap'      => array( 'column' => '0', 'row' => '0', 'isLinked' => true, 'unit' => 'px', 'size' => 0 ),
					'css_classes'   => 'miroir-stack',
				),
				'elements' => $widgets,
			),
		);
	}

	/**
	 * A widget element.
	 *
	 * @param string $type     Widget name.
	 * @param array  $settings Settings.
	 * @return array
	 */
	private static function widget( $type, $settings = array() ) {
		return array(
			'id'         => self::element_id(),
			'elType'     => 'widget',
			'widgetType' => $type,
			'isInner'    => false,
			'settings'   => $settings,
			'elements'   => array(),
		);
	}

	private static function element_id() {
		return substr( md5( wp_generate_uuid4() ), 0, 7 );
	}

	/**
	 * The home page's Miroir blocks as widgets, with the same settings.
	 *
	 * @param int $page Front page id.
	 * @return array
	 */
	private static function home_widgets( $page ) {
		$content = (string) get_post_meta( $page, '_miroir_blocks_before_elementor', true );
		if ( '' === $content ) {
			$content = (string) get_post_field( 'post_content', $page );
		}
		$link    = fn( $url ) => array( 'url' => (string) $url, 'is_external' => '', 'nofollow' => '' );
		$image   = fn( $id ) => array( 'id' => (int) $id, 'url' => $id ? (string) wp_get_attachment_url( $id ) : '' );
		$widgets = array();
		foreach ( parse_blocks( $content ) as $block ) {
			$type = $block['blockName'] ? WP_Block_Type_Registry::get_instance()->get_registered( $block['blockName'] ) : null;
			if ( ! $type ) {
				continue;
			}
			$a = $type->prepare_attributes_for_render( $block['attrs'] );
			switch ( $block['blockName'] ) {
				case 'miroir/video-hero':
					$widgets[] = self::widget( 'miroir-video-hero', array( 'video' => array( 'url' => $a['video'] ), 'videoMobile' => array( 'url' => $a['videoMobile'] ), 'poster' => $image( $a['poster'] ), 'posterTablet' => $image( $a['posterTablet'] ), 'posterMobile' => $image( $a['posterMobile'] ), 'caption' => $a['caption'], 'url' => $link( $a['url'] ) ) );
					break;
				case 'miroir/editorial':
					$widgets[] = self::widget( 'miroir-editorial', array( 'layout' => $a['layout'], 'smallImage' => $image( $a['smallImage'] ), 'largeImage' => $image( $a['largeImage'] ), 'caption' => $a['caption'], 'url' => $link( $a['url'] ) ) );
					break;
				case 'miroir/banner':
					$widgets[] = self::widget( 'miroir-banner', array( 'image' => $image( $a['image'] ), 'imageTablet' => $image( $a['imageTablet'] ), 'imageMobile' => $image( $a['imageMobile'] ), 'caption' => $a['caption'], 'url' => $link( $a['url'] ) ) );
					break;
				case 'miroir/product-strip':
					$widgets[] = self::widget(
						'miroir-product-row',
						array(
							'label'    => $a['label'],
							'source'   => $a['source'],
							'category' => $a['category'],
							'ids'      => array_map( 'strval', $a['ids'] ),
							'limit'    => (string) $a['limit'],
							'columns'  => (string) $a['columns'],
							'url'      => $link( $a['url'] ),
						)
					);
					break;
			}
		}
		if ( ! $widgets ) {
			WP_CLI::warning( 'The front page has no Miroir blocks: run `wp miroir theme setup` first for the default home sections.' );
		}
		return $widgets;
	}

	/**
	 * Pages other than home, bag, checkout, account and the shop (a product listing, not text).
	 *
	 * @return int[]
	 */
	private static function text_pages() {
		$skip = array( (int) get_option( 'page_on_front' ), function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'shop' ) : 0 );
		foreach ( array_keys( self::STORE_PAGES ) as $option ) {
			$skip[] = (int) get_option( $option );
		}
		$pages = get_posts( array( 'post_type' => 'page', 'post_status' => array( 'publish', 'draft', 'pending', 'private' ), 'numberposts' => -1, 'fields' => 'ids' ) );
		return array_values( array_diff( array_map( 'intval', $pages ), $skip ) );
	}

	/**
	 * What a page's text column shows today: its blocks through the_content
	 * filters (as miroir/page-content prints them), from the copy kept before
	 * Elementor took over when there is one.
	 *
	 * @param int $page Page id.
	 * @return string
	 */
	private static function page_html( $page ) {
		$content = (string) get_post_meta( $page, '_miroir_blocks_before_elementor', true );
		if ( '' === $content ) {
			$content = (string) get_post_field( 'post_content', $page );
		}
		$frontend = \Elementor\Plugin::$instance->frontend;
		$priority = has_filter( 'the_content', array( $frontend, 'apply_builder_in_content' ) );
		if ( false !== $priority ) {
			remove_filter( 'the_content', array( $frontend, 'apply_builder_in_content' ), $priority );
		}
		$GLOBALS['post'] = get_post( $page ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
		setup_postdata( $GLOBALS['post'] );
		$html = str_replace( ']]>', ']]&gt;', apply_filters( 'the_content', $content ) );
		wp_reset_postdata();
		if ( false !== $priority ) {
			add_filter( 'the_content', array( $frontend, 'apply_builder_in_content' ), $priority );
		}
		return $html;
	}

	/**
	 * Save a library template.
	 *
	 * @param int    $id       Existing template id or 0.
	 * @param string $title    Title.
	 * @param array  $elements Elements.
	 * @return int
	 */
	private function save_template( $id, $title, $elements ) {
		$documents = \Elementor\Plugin::$instance->documents;
		$document  = $id ? $documents->get( $id, false ) : $documents->create( 'page', array( 'post_title' => $title, 'post_status' => 'publish', 'post_type' => 'elementor_library' ) );
		if ( is_wp_error( $document ) || ! $document ) {
			WP_CLI::error( "Could not create the template {$title}." );
		}
		$document->save( array( 'elements' => $elements ) );
		return $document->get_main_id();
	}

	/**
	 * Build a page with Elementor; its blocks are kept for `reset`.
	 *
	 * @param int   $page     Page id.
	 * @param array $elements Elements.
	 */
	private function save_page( $page, $elements ) {
		if ( ! get_post_meta( $page, '_miroir_blocks_before_elementor', true ) && ! miroir_elementor_built( $page ) ) {
			update_post_meta( $page, '_miroir_blocks_before_elementor', wp_slash( get_post_field( 'post_content', $page ) ) );
		}
		update_post_meta( $page, '_elementor_edit_mode', 'builder' );
		update_post_meta( $page, '_elementor_template_type', 'wp-page' );
		$document = \Elementor\Plugin::$instance->documents->get( $page, false );
		$document->save( array( 'elements' => $elements ) );
	}

	/**
	 * Save the kit's Site Settings.
	 *
	 * @param \Elementor\Core\Kits\Documents\Kit $kit      Active kit.
	 * @param array                              $settings Settings.
	 */
	private function save_kit( $kit, $settings ) {
		update_post_meta( $kit->get_id(), '_elementor_page_settings', $settings );
	}
}

WP_CLI::add_command( 'miroir elementor', 'Miroir_Elementor_Command' );
