<?php
/**
 * The Miroir section widgets (Elementor > Miroir). Each prints one section of
 * the lofi through the theme's blocks; see Miroir_Widget.
 */

defined( 'ABSPATH' ) || exit;

/** Scrolling announcement above the menu. */
class Miroir_Widget_Announcement extends Miroir_Widget {
	public function get_name() {
		return 'miroir-announcement';
	}
	public function get_title() {
		return __( 'Announcement bar', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-animated-headline';
	}
	protected function tree() {
		return array( 'block' => 'miroir/announcement' );
	}
}

/** Logo, menu and shop links; the menu button opens the menu panel below 1024px. */
class Miroir_Widget_Navigation extends Miroir_Widget {
	public function get_name() {
		return 'miroir-navigation';
	}
	public function get_title() {
		return __( 'Main navigation', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-nav-menu';
	}
	protected function tree() {
		// Only the search and account links print their label; the others draw a count, code or icon.
		$item = fn( $kind, $label ) => array(
			'block'    => 'miroir/nav-item',
			'attrs'    => array( 'kind' => $kind ),
			'part'     => $kind,
			'label'    => $label,
			'controls' => in_array( $kind, array( 'search', 'account' ), true ),
		);
		return array(
			'block'    => 'miroir/site-nav',
			'children' => array(
				array( 'block' => 'miroir/logo', 'part' => 'logo', 'label' => __( 'Logo', 'miroir' ) ),
				array(
					'block'    => 'miroir/nav-group',
					'attrs'    => array( 'slot' => 'primary' ),
					'children' => array(
						array( 'block' => 'miroir/menu', 'attrs' => array( 'location' => 'primary' ), 'part' => 'menu', 'label' => __( 'Menu (Appearance > Menus > Primary)', 'miroir' ) ),
					),
				),
				array(
					'block'    => 'miroir/nav-group',
					'attrs'    => array( 'slot' => 'utility' ),
					'children' => array(
						$item( 'currency', __( 'Currency', 'miroir' ) ),
						$item( 'search', __( 'Search link', 'miroir' ) ),
						$item( 'account', __( 'Account link', 'miroir' ) ),
						$item( 'bag', __( 'Bag link', 'miroir' ) ),
					),
				),
				$item( 'menu-toggle', __( 'Menu button (tablet and mobile)', 'miroir' ) ),
			),
		);
	}
}

/** Footer line, the three footer menus and the copyright. */
class Miroir_Widget_Footer extends Miroir_Widget {
	public function get_name() {
		return 'miroir-footer';
	}
	public function get_title() {
		return __( 'Footer', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-footer';
	}
	protected function tree() {
		$menu = fn( $location, $label ) => array(
			'block'    => 'miroir/menu',
			'attrs'    => array( 'location' => $location ),
			'part'     => str_replace( '-', '_', $location ),
			'label'    => $label,
			'controls' => false,
		);
		return array(
			'block'    => null,
			'children' => array(
				array( 'block' => 'miroir/footer-text', 'part' => 'line', 'label' => __( 'Footer line', 'miroir' ) ),
				$menu( 'footer-account', __( 'Menu: Footer account', 'miroir' ) ),
				$menu( 'footer-policy', __( 'Menu: Footer policy', 'miroir' ) ),
				$menu( 'footer-contact', __( 'Menu: Footer contact', 'miroir' ) ),
				array( 'block' => 'miroir/copyright', 'part' => 'copyright', 'label' => __( 'Copyright', 'miroir' ) ),
			),
		);
	}
}

/** Full-bleed campaign film with responsive posters and no-video fallback. */
class Miroir_Widget_Video_Hero extends Miroir_Widget {
	public function get_name() { return 'miroir-video-hero'; }
	public function get_title() { return __( 'Campaign video hero', 'miroir' ); }
	public function get_icon() { return 'eicon-video-camera'; }
	protected function tree() { return array( 'block' => 'miroir/video-hero' ); }
	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => $this->get_title() ) );
		foreach ( array( 'video' => __( 'Campaign video (MP4 or WebM)', 'miroir' ), 'videoMobile' => __( 'Mobile video (optional portrait version)', 'miroir' ) ) as $key => $label ) {
			$this->add_control( $key, array( 'label' => $label, 'type' => \Elementor\Controls_Manager::MEDIA, 'media_types' => array( 'video' ), 'description' => __( 'Muted loop. Empty: show the poster. Reduced motion and data saver always use the poster.', 'miroir' ) ) );
		}
		foreach ( array( 'poster' => __( 'Desktop poster (9:4)', 'miroir' ), 'posterTablet' => __( 'Tablet poster (16:9)', 'miroir' ), 'posterMobile' => __( 'Mobile poster (3:4)', 'miroir' ) ) as $key => $label ) {
			miroir_elementor_image_control( $this, $key, $label );
		}
		miroir_elementor_link_controls( $this, __( 'Empty: new arrivals.', 'miroir' ) );
		$this->end_controls_section();
	}
	protected function markup( $settings ) {
		return render_block( miroir_parsed_block( 'miroir/video-hero', array(
			'video' => (string) ( $settings['video']['url'] ?? '' ),
			'videoMobile' => (string) ( $settings['videoMobile']['url'] ?? '' ),
			'poster' => miroir_elementor_image_id( $settings['poster'] ?? null ),
			'posterTablet' => miroir_elementor_image_id( $settings['posterTablet'] ?? null ),
			'posterMobile' => miroir_elementor_image_id( $settings['posterMobile'] ?? null ),
			'caption' => (string) ( $settings['caption'] ?? 'new arrivals: a little something new, made to be loved.' ),
			'url' => (string) ( $settings['url']['url'] ?? '' ),
		) ) );
	}
}

/** Two images (one small, one large) with a caption, linking to a collection or page. */
class Miroir_Widget_Editorial extends Miroir_Widget {
	public function get_name() {
		return 'miroir-editorial';
	}
	public function get_title() {
		return __( 'Editorial pair', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-gallery-grid';
	}
	protected function tree() {
		return array( 'block' => 'miroir/editorial' );
	}
	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => $this->get_title() ) );
		$this->add_control(
			'layout',
			array(
				'label'   => __( 'Image order', 'miroir' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'small-first',
				'options' => array(
					'small-first' => __( 'Small image first', 'miroir' ),
					'large-first' => __( 'Large image first', 'miroir' ),
				),
			)
		);
		miroir_elementor_image_control( $this, 'smallImage', __( 'Small image (portrait 3:4)', 'miroir' ) );
		miroir_elementor_image_control( $this, 'largeImage', __( 'Large image (portrait 3:4)', 'miroir' ) );
		miroir_elementor_link_controls( $this, __( 'Empty: the shop.', 'miroir' ) );
		$this->end_controls_section();
	}
	protected function markup( $settings ) {
		return render_block(
			miroir_parsed_block(
				'miroir/editorial',
				array(
					'layout'     => (string) $settings['layout'],
					'smallImage' => miroir_elementor_image_id( $settings['smallImage'] ?? null ),
					'largeImage' => miroir_elementor_image_id( $settings['largeImage'] ?? null ),
					'caption'    => (string) $settings['caption'],
					'url'        => (string) ( $settings['url']['url'] ?? '' ),
				)
			)
		);
	}
}

/** Full-width banner with separate desktop, tablet and mobile images. */
class Miroir_Widget_Banner extends Miroir_Widget {
	public function get_name() {
		return 'miroir-banner';
	}
	public function get_title() {
		return __( 'Banner', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-image';
	}
	protected function tree() {
		return array( 'block' => 'miroir/banner' );
	}
	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => $this->get_title() ) );
		miroir_elementor_image_control( $this, 'image', __( 'Desktop image', 'miroir' ) );
		miroir_elementor_image_control( $this, 'imageTablet', __( 'Tablet image (empty: desktop image)', 'miroir' ) );
		miroir_elementor_image_control( $this, 'imageMobile', __( 'Mobile image (empty: tablet image)', 'miroir' ) );
		miroir_elementor_link_controls( $this, __( 'Empty: the sale collection.', 'miroir' ) );
		$this->end_controls_section();
	}
	protected function markup( $settings ) {
		return render_block(
			miroir_parsed_block(
				'miroir/banner',
				array(
					'image'       => miroir_elementor_image_id( $settings['image'] ?? null ),
					'imageTablet' => miroir_elementor_image_id( $settings['imageTablet'] ?? null ),
					'imageMobile' => miroir_elementor_image_id( $settings['imageMobile'] ?? null ),
					'caption'     => (string) $settings['caption'],
					'url'         => (string) ( $settings['url']['url'] ?? '' ),
				)
			)
		);
	}
}

/** Scrolling row of product cards: a collection, the sale, featured, newest or hand-picked. */
class Miroir_Widget_Product_Row extends Miroir_Widget {
	public function get_name() {
		return 'miroir-product-row';
	}
	public function get_title() {
		return __( 'Product row', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-slider-push';
	}
	protected function tree() {
		return array( 'block' => 'miroir/product-strip' );
	}
	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => $this->get_title() ) );
		$this->add_control( 'label', array( 'label' => __( 'Title', 'miroir' ), 'type' => \Elementor\Controls_Manager::TEXT, 'label_block' => true ) );
		$this->add_control(
			'source',
			array(
				'label'   => __( 'Show', 'miroir' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'category',
				'options' => array(
					'category' => __( 'A collection', 'miroir' ),
					'sale'     => __( 'Products on sale', 'miroir' ),
					'featured' => __( 'Featured products', 'miroir' ),
					'newest'   => __( 'Newest products', 'miroir' ),
					'ids'      => __( 'Hand-picked products', 'miroir' ),
				),
			)
		);
		$this->add_control(
			'category',
			array(
				'label'     => __( 'Collection', 'miroir' ),
				'type'      => \Elementor\Controls_Manager::SELECT2,
				'options'   => miroir_elementor_category_options(),
				'condition' => array( 'source' => 'category' ),
			)
		);
		$this->add_control(
			'ids',
			array(
				'label'       => __( 'Products', 'miroir' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => miroir_elementor_product_options(),
				'condition'   => array( 'source' => 'ids' ),
			)
		);
		$this->add_control(
			'limit',
			array(
				'label'     => __( 'How many', 'miroir' ),
				'type'      => \Elementor\Controls_Manager::SELECT,
				'default'   => '-1',
				'options'   => array( '-1' => __( 'All', 'miroir' ) ) + array_combine( array( '4', '5', '6', '8', '10', '12', '16', '20', '24' ), array( '4', '5', '6', '8', '10', '12', '16', '20', '24' ) ),
				'condition' => array( 'source!' => 'ids' ),
			)
		);
		$this->add_control(
			'columns',
			array(
				'label'   => __( 'Cards per row (desktop)', 'miroir' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '5',
				'options' => array( '5' => '5', '6' => '6' ),
			)
		);
		$this->add_control(
			'url',
			array(
				'label'       => __( '"View all" link', 'miroir' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'description' => __( 'Empty: the collection page.', 'miroir' ),
				'options'     => false,
			)
		);
		$this->end_controls_section();
	}
	protected function markup( $settings ) {
		return render_block(
			miroir_parsed_block(
				'miroir/product-strip',
				array(
					'label'    => (string) $settings['label'],
					'source'   => (string) $settings['source'],
					'category' => (string) $settings['category'],
					'ids'      => array_map( 'intval', (array) ( $settings['ids'] ?: array() ) ),
					'limit'    => (int) $settings['limit'],
					'columns'  => (int) $settings['columns'],
					'url'      => (string) ( $settings['url']['url'] ?? '' ),
				)
			)
		);
	}
}

/** Shop and collection pages: filters, title bar and product grid. */
class Miroir_Widget_Collection extends Miroir_Widget {
	public function get_name() {
		return 'miroir-collection';
	}
	public function get_title() {
		return __( 'Collection page', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-products';
	}
	protected function tree() {
		return array(
			'block'    => 'miroir/listing',
			'attrs'    => array( 'layout' => 'plp' ),
			'children' => array(
				array( 'block' => 'miroir/shop-filters', 'part' => 'filters', 'label' => __( 'Sidebar (desktop)', 'miroir' ) ),
				array( 'block' => 'miroir/listing-bar', 'part' => 'bar', 'label' => __( 'Title bar', 'miroir' ) ),
				array( 'block' => 'miroir/product-grid' ),
			),
		);
	}
	protected function texts() {
		return array(
			__( 'Texts', 'miroir' )       => array( 'filter_sort_link', 'filter_featured', 'load_more' ),
			__( 'Empty states', 'miroir' ) => array( 'no_matches', 'no_matches_heading', 'no_matches_text', 'clear_filters', 'empty_collection', 'empty_collection_text', 'back_to_collection' ),
		);
	}
}

/** Search results: search field and results grid. */
class Miroir_Widget_Search_Results extends Miroir_Widget {
	public function get_name() {
		return 'miroir-search-results';
	}
	public function get_title() {
		return __( 'Search results', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-search-results';
	}
	protected function tree() {
		return array(
			'block'    => 'miroir/listing',
			'attrs'    => array( 'layout' => 'search' ),
			'children' => array(
				array( 'block' => 'miroir/search-bar', 'part' => 'bar', 'label' => __( 'Search field', 'miroir' ) ),
				array( 'block' => 'miroir/search-results' ),
			),
		);
	}
	protected function texts() {
		return array( __( 'No results', 'miroir' ) => array( 'no_results', 'no_results_for', 'no_results_text', 'search_again', 'explore_all' ) );
	}
}

/** Product page: details, buy box, previous/next, images and similar products. */
class Miroir_Widget_Product extends Miroir_Widget {
	public function get_name() {
		return 'miroir-product';
	}
	public function get_title() {
		return __( 'Product page', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-single-product';
	}
	protected function tree() {
		return array(
			'block'    => 'miroir/product',
			'children' => array(
				array(
					'block'    => 'miroir/product-main',
					'children' => array(
						array(
							'block'    => 'miroir/product-details',
							'children' => array(
								array( 'block' => 'miroir/product-info', 'part' => 'info', 'label' => __( 'Name, price and description', 'miroir' ) ),
								array( 'block' => 'miroir/product-buy', 'part' => 'buy', 'label' => __( 'Size and buy buttons', 'miroir' ) ),
								array( 'block' => 'miroir/product-pager', 'part' => 'pager', 'label' => __( 'Previous / next product', 'miroir' ) ),
							),
						),
						array( 'block' => 'miroir/product-gallery', 'part' => 'gallery', 'label' => __( 'Images', 'miroir' ) ),
					),
				),
				array( 'block' => 'miroir/similar-products', 'part' => 'similar', 'label' => __( 'Similar products', 'miroir' ) ),
			),
		);
	}
	protected function texts() {
		return array(
			__( 'Texts', 'miroir' )          => array( 'prev_product', 'next_product', 'similar_title', 'buy_hint', 'out_of_stock' ),
			__( 'Sold out and sizes', 'miroir' ) => array( 'state_sold_out', 'sold_out_notice', 'sold_out_text', 'ask_availability', 'state_choose', 'choose_notice', 'choose_text', 'select_size', 'size_label', 'size_unavailable' ),
			__( 'Added to bag', 'miroir' )   => array( 'state_added', 'added_notice', 'added_close', 'added_error', 'state_added_note', 'view_bag' ),
		);
	}
}

/** Text pages (info, guides, FAQ) written in the WordPress editor: title and that text. */
class Miroir_Widget_Page extends Miroir_Widget {
	public function get_name() {
		return 'miroir-page';
	}
	public function get_title() {
		return __( 'Page layout (text from WordPress editor)', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-post-content';
	}
	protected function tree() {
		return array(
			'block'    => 'miroir/page',
			'controls' => false,
			'children' => array(
				array( 'block' => 'miroir/page-title', 'part' => 'title', 'label' => __( 'Title', 'miroir' ) ),
				array( 'block' => 'miroir/page-content' ),
			),
		);
	}
	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => $this->get_title() ) );
		$this->add_control(
			'variant',
			array(
				'label'       => __( 'Layout', 'miroir' ),
				'type'        => \Elementor\Controls_Manager::SELECT,
				'default'     => 'auto',
				'options'     => array(
					'auto'  => __( 'From the page template', 'miroir' ),
					'loose' => __( 'Text column', 'miroir' ),
					'guide' => __( 'Guide', 'miroir' ),
					'info'  => __( 'Information', 'miroir' ),
				),
				'description' => __( 'The page text is edited in Pages.', 'miroir' ),
			)
		);
		$this->add_tree_controls( $this->tree(), '' );
		$this->end_controls_section();
	}
	protected function markup( $settings ) {
		return render_block( $this->page_block( $settings ) );
	}
	/**
	 * The page layout block for the settings.
	 *
	 * @param array $settings Widget settings.
	 * @return array
	 */
	protected function page_block( $settings ) {
		$block = $this->parsed_block( $this->tree(), $settings );
		$block['attrs']['variant'] = 'auto' === $settings['variant'] ? miroir_page_variant() : (string) $settings['variant'];
		return $block;
	}
}

/** Text pages written in Elementor: title and text in the page's column (info, guide or plain). */
class Miroir_Widget_Text_Page extends Miroir_Widget_Page {
	public function get_name() {
		return 'miroir-text-page';
	}
	public function get_title() {
		return __( 'Text page', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-text';
	}
	protected function tree() {
		return array(
			'block'    => 'miroir/page',
			'controls' => false,
			'children' => array(
				array( 'block' => 'miroir/page-title', 'part' => 'title', 'label' => __( 'Title (the page title)', 'miroir' ) ),
				array( 'block' => 'miroir/page-text', 'controls' => false ),
			),
		);
	}
	protected function register_controls() {
		parent::register_controls();
		$this->update_control( 'variant', array( 'description' => '' ) );
		$this->start_injection( array( 'of' => 'title_show' ) );
		$this->add_control(
			'text',
			array(
				'label'     => __( 'Text', 'miroir' ),
				'type'      => \Elementor\Controls_Manager::WYSIWYG,
				'separator' => 'before',
			)
		);
		$this->end_injection();
	}
	protected function page_block( $settings ) {
		$block = parent::page_block( $settings );
		foreach ( $block['innerBlocks'] as $i => $inner ) {
			if ( 'miroir/page-text' === $inner['blockName'] ) {
				$block['innerBlocks'][ $i ]['attrs']['html'] = (string) $settings['text'];
			}
		}
		return $block;
	}
}

/** WooCommerce screens drawn by the theme's woocommerce/ templates, with their texts. */
abstract class Miroir_Widget_Store_Page extends Miroir_Widget {
	/** Shortcode of the screen. */
	abstract protected function shortcode();
	protected function tree() {
		return array( 'block' => null );
	}
	protected function register_controls() {
		$this->start_controls_section( 'section_content', array( 'label' => $this->get_title() ) );
		$this->add_control(
			'note',
			array(
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw'  => esc_html__( 'Layout follows the Miroir design. Change the texts below; colours and spacing are in Site Settings.', 'miroir' ),
			)
		);
		$this->end_controls_section();
		foreach ( $this->texts() as $label => $ids ) {
			$this->start_controls_section( 'texts_' . sanitize_key( $label ), array( 'label' => $label ) );
			$this->add_text_controls( $ids );
			$this->end_controls_section();
		}
	}
	protected function markup( $settings ) {
		return do_shortcode( '[' . $this->shortcode() . ']' );
	}
}

/** Bag (screens 04, 05, 40). */
class Miroir_Widget_Bag extends Miroir_Widget_Store_Page {
	public function get_name() {
		return 'miroir-bag';
	}
	public function get_title() {
		return __( 'Bag', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-cart';
	}
	protected function shortcode() {
		return 'woocommerce_cart';
	}
	protected function texts() {
		return array( __( 'Texts', 'miroir' ) => miroir_text_ids( 'bag' ) );
	}
}

/** Review before WhatsApp (screen 06) and order sent (07, 43). */
class Miroir_Widget_Checkout extends Miroir_Widget_Store_Page {
	public function get_name() {
		return 'miroir-checkout';
	}
	public function get_title() {
		return __( 'Checkout', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-checkout';
	}
	protected function shortcode() {
		return 'woocommerce_checkout';
	}
	protected function texts() {
		return array(
			__( 'Review', 'miroir' )     => miroir_text_ids( 'checkout' ),
			__( 'Order sent', 'miroir' ) => miroir_text_ids( 'sent' ),
		);
	}
}

/** Account, login, registration and password screens. */
class Miroir_Widget_Account extends Miroir_Widget_Store_Page {
	public function get_name() {
		return 'miroir-account';
	}
	public function get_title() {
		return __( 'Account', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-lock-user';
	}
	protected function shortcode() {
		return 'woocommerce_my_account';
	}
	protected function texts() {
		return array( __( 'Texts', 'miroir' ) => miroir_text_ids( 'account' ) );
	}
}

/** Page not found (screen 44). */
class Miroir_Widget_Not_Found extends Miroir_Widget {
	public function get_name() {
		return 'miroir-not-found';
	}
	public function get_title() {
		return __( 'Page not found', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-warning';
	}
	protected function tree() {
		return array( 'block' => 'miroir/not-found' );
	}
	protected function texts() {
		return array( __( 'Texts', 'miroir' ) => miroir_text_ids( 'notfound' ) );
	}
}

/** Maintenance page (screen 30). */
class Miroir_Widget_Maintenance extends Miroir_Widget {
	public function get_name() {
		return 'miroir-maintenance';
	}
	public function get_title() {
		return __( 'Maintenance message', 'miroir' );
	}
	public function get_icon() {
		return 'eicon-tools';
	}
	protected function tree() {
		return array( 'block' => null );
	}
	protected function texts() {
		return array( __( 'Texts', 'miroir' ) => miroir_text_ids( 'maintenance' ) );
	}
	protected function markup( $settings ) {
		ob_start();
		get_template_part( 'template-parts/maintenance-body' );
		return ob_get_clean();
	}
}

/**
 * Widget classes by template location they appear in by default.
 *
 * @return string[]
 */
function miroir_elementor_widget_classes() {
	return array(
		'Miroir_Widget_Announcement',
		'Miroir_Widget_Navigation',
		'Miroir_Widget_Footer',
		'Miroir_Widget_Video_Hero',
		'Miroir_Widget_Editorial',
		'Miroir_Widget_Banner',
		'Miroir_Widget_Product_Row',
		'Miroir_Widget_Collection',
		'Miroir_Widget_Search_Results',
		'Miroir_Widget_Product',
		'Miroir_Widget_Page',
		'Miroir_Widget_Text_Page',
		'Miroir_Widget_Bag',
		'Miroir_Widget_Checkout',
		'Miroir_Widget_Account',
		'Miroir_Widget_Not_Found',
		'Miroir_Widget_Maintenance',
	);
}
