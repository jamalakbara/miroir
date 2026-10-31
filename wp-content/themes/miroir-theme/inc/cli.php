<?php
/**
 * WP-CLI: `wp miroir theme setup` puts the site in the state the theme's
 * templates expect. Safe to re-run.
 */

defined( 'ABSPATH' ) || exit;

class Miroir_Theme_Command {

	/** Classic WooCommerce pages: option => shortcode. The theme overrides their PHP templates. */
	const WC_SHORTCODES = array(
		'woocommerce_cart_page_id'      => '[woocommerce_cart]',
		'woocommerce_checkout_page_id'  => '[woocommerce_checkout]',
		'woocommerce_myaccount_page_id' => '[woocommerce_my_account]',
	);

	/**
	 * Switch WooCommerce pages to shortcodes, create info pages and the theme's menus.
	 *
	 * ## OPTIONS
	 *
	 * [--force-pages]
	 * : Overwrite the content of info pages that already exist (e.g. the legacy "contact" page).
	 *
	 * @when after_wp_load
	 */
	public function setup( $args = array(), $assoc = array() ) {
		foreach ( self::WC_SHORTCODES as $option => $shortcode ) {
			$page_id = (int) get_option( $option );
			if ( ! $page_id ) {
				WP_CLI::warning( "No page set in $option" );
				continue;
			}
			$content = '<!-- wp:shortcode -->' . $shortcode . '<!-- /wp:shortcode -->';
			if ( get_post_field( 'post_content', $page_id ) !== $content ) {
				wp_update_post( array( 'ID' => $page_id, 'post_content' => $content ) );
				WP_CLI::log( "page   #$page_id now uses $shortcode" );
			}
		}

		// Registration (screen 12): own email + password, username generated from the email.
		foreach ( array(
			'woocommerce_enable_myaccount_registration' => 'yes',
			'woocommerce_registration_generate_username' => 'yes',
			'woocommerce_registration_generate_password' => 'no',
		) as $option => $value ) {
			update_option( $option, $value );
		}

		$this->pages( ! empty( $assoc['force-pages'] ) );
		$this->home( ! empty( $assoc['force-pages'] ) );
		$this->menus();
		WP_CLI::success( 'Theme setup done.' );
	}

	/**
	 * Info pages (screens 11, 22-26, 28, 29) as editable blocks. Copy is the
	 * lofi's placeholder text: policy copy still needs client approval. Existing
	 * pages are left alone unless --force-pages is passed (overwrites their content).
	 *
	 * @param bool $force Overwrite existing pages.
	 */
	private function pages( $force = false ) {
		$answer    = 'Answer copy goes here. Two to four lines of plain text explaining the policy, timelines and any exceptions, with a link to the related page if needed.';
		$questions = array(
			'How long does shipping take?',
			'Do you ship outside Indonesia?',
			'How do I confirm my bank transfer?',
			'Can I return or exchange an item?',
			'How do I find my size?',
			'How do I track my order?',
		);
		$faq = '';
		foreach ( $questions as $i => $question ) {
			$open = 0 === $i ? ' open' : '';
			$faq .= sprintf(
				"<!-- wp:details%s -->\n<details class=\"wp-block-details\"%s><summary>%s</summary><!-- wp:paragraph -->\n<p>%s</p>\n<!-- /wp:paragraph --></details>\n<!-- /wp:details -->\n\n",
				$open ? ' {"showContent":true}' : '',
				$open,
				esc_html( $question ),
				esc_html( $answer )
			);
		}

		$contact = '/contact/';
		// Section heading => "[Approved <heading> content to be supplied before launch.]".
		$drafts  = function ( ...$headings ) {
			$sections = array();
			foreach ( $headings as $heading ) {
				$sections[ $heading ] = '[Approved ' . strtolower( $heading ) . ' content to be supplied before launch.]';
			}
			return $sections;
		};
		$pages   = array(
			'faq'                  => array( 'FAQ', $faq, 'page-templates/faq.php' ),
			'shipping-information' => array(
				'Shipping information',
				self::blocks(
					'Our team confirms stock, delivery availability and the final shipping cost in WhatsApp before you pay.',
					$drafts( 'Destinations & couriers', 'Delivery estimates', 'International duties' ),
					'Contact customer care',
					$contact,
					'DRAFT · Policy copy requires approval before launch.'
				),
			),
			'returns-exchanges'    => array(
				'Returns & exchanges',
				self::blocks( 'Contact customer care with your order reference and a description of the issue.', $drafts( 'Return eligibility & window', 'Exchange process', 'Refund timing & return costs' ), 'Request help with a return', $contact, 'DRAFT · Policy copy requires approval before launch.' ),
			),
			'terms-of-use'         => array(
				'Terms of use',
				self::blocks( 'A WhatsApp message is a request to order. Our team confirms availability and the final total before payment.', $drafts( 'Prices & payment', 'Order acceptance & cancellations', 'Liability & applicable law' ), 'Contact customer care', $contact, 'DRAFT · Policy copy requires approval before launch.' ),
			),
			'privacy-policy'       => array(
				'Privacy policy',
				self::blocks( 'Learn how miroir handles account, order and delivery information.', $drafts( 'Data collected & purposes', 'Retention & third-party services', 'Access, correction & deletion' ), 'Contact customer care', $contact, 'DRAFT · Policy copy requires approval before launch.' ),
			),
			'contact'              => array(
				'Contact customer care',
				self::blocks(
					'We’re here to help. Have your order reference ready, if you have one.',
					array(
						'Order enquiries'      => 'Message us in your existing WhatsApp conversation. Please do not share passwords or full payment-card details.',
						'Support availability' => '[Support hours and business contact details to be confirmed.]',
					),
					'Contact us on WhatsApp',
					'#whatsapp'
				),
			),
			'payment-confirmation' => array(
				'Payment confirmation',
				self::blocks(
					'Only pay after our team confirms stock, shipping and the final total. Use the bank details supplied in your order conversation.',
					array( 'Confirm your payment' => 'Send your transfer receipt and order reference in the same WhatsApp chat. Our team will verify the payment and reply.' ),
					'Send receipt via WhatsApp',
					'#whatsapp',
					'',
					'WhatsApp-first version · no onsite receipt upload or carrier integration.'
				),
			),
			'size-guide'           => array( 'Size guide', self::size_guide(), 'page-templates/guide.php' ),
			'track-your-order'     => array(
				'Track your order',
				self::blocks( 'Tracking information is shared in your WhatsApp conversation once your order has shipped. Have your order reference ready.', array(), 'Ask about my delivery', '#whatsapp', '', 'WhatsApp-first version · no onsite receipt upload or carrier integration.' ),
			),
		);
		foreach ( $pages as $slug => $page ) {
			list( $title, $content ) = $page;
			$template = $page[2] ?? '';
			$existing = get_page_by_path( $slug );
			if ( $existing && ! $force ) {
				continue;
			}
			$id = wp_insert_post(
				array(
					'ID'           => $existing ? $existing->ID : 0,
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $title,
					'post_content' => $content,
				)
			);
			if ( $template ) {
				update_post_meta( $id, '_wp_page_template', $template );
			}
			WP_CLI::log( 'page   ' . $slug . ( $existing ? ' overwritten' : ' created' ) );
		}
		$privacy = get_page_by_path( 'privacy-policy' );
		if ( $privacy ) {
			update_option( 'wp_page_for_privacy_policy', $privacy->ID );
		}
	}

	/**
	 * Page body as blocks: intro paragraph, ruled sections (Group blocks with the
	 * "section" class: heading + paragraph), one full-width button. The lofi's
	 * draft/scope notes are ordinary paragraphs ("note-draft", "note-scope") that
	 * editors delete once approved copy is in.
	 *
	 * @param string $intro    Intro paragraph.
	 * @param array  $sections heading => copy.
	 * @param string $button   Button label.
	 * @param string $url      Button URL ("#whatsapp" opens the shop's WhatsApp).
	 * @param string $before   Draft note shown above the button.
	 * @param string $after    Scope note shown below the button.
	 */
	private static function blocks( $intro, $sections, $button, $url, $before = '', $after = '' ) {
		$out = "<!-- wp:paragraph -->\n<p>" . esc_html( $intro ) . "</p>\n<!-- /wp:paragraph -->\n\n";
		foreach ( $sections as $heading => $copy ) {
			$out .= '<!-- wp:group {"className":"section","layout":{"type":"default"}} -->' . "\n"
				. '<div class="wp-block-group section"><!-- wp:heading -->' . "\n"
				. '<h2 class="wp-block-heading">' . esc_html( $heading ) . "</h2>\n<!-- /wp:heading -->\n\n"
				. "<!-- wp:paragraph -->\n<p>" . esc_html( $copy ) . "</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->\n\n";
		}
		if ( $before ) {
			$out .= self::note( $before, 'note-draft' );
		}
		$out .= "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button {\"width\":100} -->\n"
			. '<div class="wp-block-button has-custom-width wp-block-button__width-100"><a class="wp-block-button__link wp-element-button" href="' . esc_attr( $url ) . '">' . esc_html( $button ) . "</a></div>\n"
			. "<!-- /wp:button --></div>\n<!-- /wp:buttons -->";
		if ( $after ) {
			$out .= "\n\n" . self::note( $after, 'note-scope' );
		}
		return $out;
	}

	/**
	 * Size guide (screen 37): lofi's illustrative measurements in a Table block.
	 */
	private static function size_guide() {
		$rows = array( array( 'Size', 'Chest', 'Length' ), array( 'S', '88', '62' ), array( 'M', '92', '64' ), array( 'L', '96', '66' ) );
		$head = '<thead><tr><th>' . implode( '</th><th>', $rows[0] ) . '</th></tr></thead>';
		$body = '';
		foreach ( array_slice( $rows, 1 ) as $row ) {
			$body .= '<tr><td>' . implode( '</td><td>', $row ) . '</td></tr>';
		}
		$button = function ( $label, $url ) {
			return '<!-- wp:button {"width":100,"className":"is-style-outline"} -->' . "\n"
				. '<div class="wp-block-button has-custom-width wp-block-button__width-100 is-style-outline"><a class="wp-block-button__link wp-element-button" href="' . esc_attr( $url ) . '">' . esc_html( $label ) . "</a></div>\n<!-- /wp:button -->";
		};
		return "<!-- wp:paragraph -->\n<p>Garment measurements · centimetres</p>\n<!-- /wp:paragraph -->\n\n"
			. self::note( 'Illustrative values only. Replace with the measurements for this product before launch.', 'note-help' )
			. "<!-- wp:table -->\n<figure class=\"wp-block-table\"><table>" . $head . '<tbody>' . $body . "</tbody></table></figure>\n<!-- /wp:table -->\n\n"
			. "<!-- wp:paragraph -->\n<p>Measure a similar garment laid flat. Compare its chest circumference and shoulder-to-hem length.</p>\n<!-- /wp:paragraph -->\n\n"
			. "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\">" . $button( 'Ask about sizing', '#whatsapp' ) . "\n\n" . $button( 'Back to product', '#back' ) . "</div>\n<!-- /wp:buttons -->";
	}

	/** Paragraph block with a class. */
	private static function note( $text, $class ) {
		return '<!-- wp:paragraph {"className":"' . $class . '"} -->' . "\n" . '<p class="' . $class . '">' . esc_html( $text ) . "</p>\n<!-- /wp:paragraph -->\n\n";
	}

	/**
	 * Front page as Miroir blocks, one per section of miroir_home_sections(), so
	 * the client edits the home page in the page editor. A front page that
	 * already has Miroir blocks is left alone unless $force; other content
	 * (the demo seeder's cover and shortcodes, never shown by this theme) is replaced.
	 *
	 * @param bool $force Overwrite existing Miroir blocks.
	 */
	private function home( $force = false ) {
		$front = (int) get_option( 'page_on_front' );
		if ( 'page' !== get_option( 'show_on_front' ) || ! $front ) {
			$front = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Home', 'post_name' => 'home' ) );
			update_option( 'page_on_front', $front );
			update_option( 'show_on_front', 'page' );
			WP_CLI::log( "page   #$front is the front page" );
		}
		if ( miroir_front_page_id() && ! $force ) {
			return;
		}

		$blocks = array();
		foreach ( miroir_home_sections() as $section ) {
			switch ( $section['type'] ) {
				case 'editorial':
					$attrs = array( 'layout' => $section['layout'], 'caption' => $section['caption'], 'url' => wp_make_link_relative( $section['url'] ) );
					break;
				case 'banner':
					$attrs = array( 'caption' => $section['caption'], 'url' => wp_make_link_relative( $section['url'] ) );
					break;
				case 'strip':
					$attrs = array(
						'label'    => $section['label'],
						'category' => $section['category'],
						'limit'    => (int) $section['limit'],
						'columns'  => (int) ( $section['columns'] ?? 5 ),
					);
					break;
				default:
					continue 2;
			}
			$name     = 'strip' === $section['type'] ? 'product-strip' : $section['type'];
			$blocks[] = serialize_block( array( 'blockName' => "miroir/{$name}", 'attrs' => $attrs, 'innerBlocks' => array(), 'innerContent' => array() ) );
		}
		wp_update_post( array( 'ID' => $front, 'post_content' => implode( "\n\n", $blocks ) ) );
		WP_CLI::log( "page   #$front home sections as blocks" );
	}

	/**
	 * Turn maintenance mode (screen 30) on or off.
	 *
	 * ## OPTIONS
	 *
	 * <state>
	 * : on or off
	 *
	 * @when after_wp_load
	 */
	public function maintenance( $args ) {
		$state = 'on' === ( $args[0] ?? '' ) ? 'on' : 'off';
		update_option( 'miroir_maintenance', $state );
		WP_CLI::success( "Maintenance mode $state." );
	}

	/**
	 * Menus for each theme location, built from the lofi's links. Existing menus
	 * (matched by name) are left as they are so client edits survive a re-run.
	 */
	private function menus() {
		$shop      = wc_get_page_permalink( 'shop' );
		$account   = wc_get_page_permalink( 'myaccount' );
		$cat       = function ( $slug ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			return $term ? array( 'object' => 'product_cat', 'id' => $term->term_id ) : null;
		};
		$page      = function ( $slug ) {
			$post = get_page_by_path( $slug );
			return $post ? array( 'object' => 'page', 'id' => $post->ID ) : null;
		};
		$menus = array(
			'primary'        => array(
				'name'  => 'Header',
				'items' => array(
					'sale'       => $cat( 'sale' ),
					'shop women' => $shop,
				),
			),
			'plp-categories' => array(
				'name'  => 'shop women',
				'items' => array(
					'accessories' => $cat( 'accessories' ),
					'bottoms'     => $cat( 'bottoms' ),
					'dress'       => $cat( 'dress' ),
					'outerwear'   => $cat( 'outerwear' ),
					'tops'        => $cat( 'tops' ),
				),
			),
			'plp-featured'   => array(
				'name'  => 'featured',
				'items' => array(
					'best sellers'      => $cat( 'best-seller' ),
					'new arrivals'      => $cat( 'new-collection' ),
					'studio collection' => $cat( 'at-the-studio' ),
					'collaboration'     => $cat( 'miroir-x-naluri' ),
				),
			),
			'panel-nav'      => array(
				'name'  => 'Menu panel',
				'items' => array(
					'New arrivals'  => $cat( 'new-collection' ),
					'Shop women'    => $shop,
					'Collections'   => $cat( 'shop-by-collection' ),
					'Sale'          => $cat( 'sale' ),
					'My account'    => $account,
					'Customer care' => $page( 'contact' ),
				),
			),
			'footer-account' => array(
				'name'  => 'Footer: account',
				'items' => array(
					'My account'           => $account,
					'Payment confirmation' => $page( 'payment-confirmation' ),
					'Shipping information' => $page( 'shipping-information' ),
				),
			),
			'footer-policy'  => array(
				'name'  => 'Footer: policies',
				'items' => array(
					'FAQ'              => $page( 'faq' ),
					'Return policy'    => $page( 'returns-exchanges' ),
					'Condition of use' => $page( 'terms-of-use' ),
				),
			),
			'footer-contact' => array(
				'name'  => 'Footer: contact',
				'items' => array(
					'Contact customer care' => $page( 'contact' ),
					'Instagram'             => 'https://www.instagram.com/miroir.id/',
				),
			),
		);

		$locations = get_theme_mod( 'nav_menu_locations', array() );
		foreach ( $menus as $location => $menu ) {
			$existing = wp_get_nav_menu_object( $menu['name'] );
			if ( $existing ) {
				$locations[ $location ] = $existing->term_id;
				continue;
			}
			$missing = array_keys( array_filter( $menu['items'], 'is_null' ) );
			if ( $missing ) {
				// The theme falls back to the same links in code; build the menu once every target exists.
				WP_CLI::warning( "menu   {$menu['name']} not created yet, missing: " . implode( ', ', $missing ) );
				continue;
			}
			$menu_id = wp_create_nav_menu( $menu['name'] );
			foreach ( $menu['items'] as $label => $target ) {
				$item = is_array( $target )
					? array( 'menu-item-type' => 'page' === $target['object'] ? 'post_type' : 'taxonomy', 'menu-item-object' => $target['object'], 'menu-item-object-id' => $target['id'] )
					: array( 'menu-item-type' => 'custom', 'menu-item-url' => $target );
				wp_update_nav_menu_item( $menu_id, 0, $item + array( 'menu-item-title' => $label, 'menu-item-status' => 'publish' ) );
			}
			$locations[ $location ] = $menu_id;
			WP_CLI::log( "menu   {$menu['name']} -> $location" );
		}
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}

WP_CLI::add_command( 'miroir theme', 'Miroir_Theme_Command' );
