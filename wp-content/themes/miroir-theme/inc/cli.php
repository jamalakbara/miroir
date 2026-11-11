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
		$faq = self::faq_blocks();

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
			'faq'                  => array( 'FAQ', $faq, 'page-faq' ),
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
				'Returns & Exchanges',
				self::returns_blocks(),
			),
			'terms-of-use'         => array(
				'Terms of Use',
				self::terms_blocks(),
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
			'studio-location'      => array(
				'Studio location',
				self::blocks(
					'Visit the Miroir studio in Bandung, by appointment only.',
					array(
						'Address'       => 'Jl. Nayaga no 6, Bandung',
						'Opening hours' => "By appointment only\nMonday–Friday: 9AM–2PM\nSaturday: 9AM–12PM\nSunday: Off",
					),
					'Book an appointment on WhatsApp',
					'#whatsapp'
				),
			),
			'payment-confirmation' => array(
				'Payment confirmation',
				self::blocks(
					'Only pay after our team confirms stock, shipping and the final total. Use the bank or PayPal details supplied in your order conversation.',
					array( 'Confirm your payment' => 'Send your payment receipt and order reference in the same WhatsApp chat. Our team will verify the payment and reply.' ),
					'Send receipt via WhatsApp',
					'#whatsapp',
					'',
					'WhatsApp-first version · no onsite receipt upload or carrier integration.'
				),
			),
			'size-guide'           => array( 'Size guide', self::size_guide(), 'page-guide' ),
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

	/** FAQ page as core Details blocks (first one open). */
	private static function faq_blocks() {
		$faq = '';
		foreach ( self::faq_items() as $i => $item ) {
			$open        = 0 === $i ? ' open' : '';
			$paragraphs = '';
			foreach ( $item[1] as $paragraph ) {
				$paragraphs .= sprintf( "<!-- wp:paragraph -->\n<p>%s</p>\n<!-- /wp:paragraph -->", esc_html( $paragraph ) );
			}
			$faq .= sprintf(
				"<!-- wp:details%s -->\n<details class=\"wp-block-details\"%s><summary>%s</summary>%s</details>\n<!-- /wp:details -->\n\n",
				$open ? ' {"showContent":true}' : '',
				$open,
				esc_html( $item[0] ),
				$paragraphs
			);
		}
		return $faq;
	}

	/** Returns & Exchanges page copy. */
	private static function returns_blocks() {
		return self::blocks(
			'Need to return or exchange an item? Simply reach out to our customer care team via WhatsApp within 3 days of receiving your order. We’re happy to assist you.',
			array(
				'Return eligibility & window' => array(
					'Return or exchange requests must be made within 3 days of receiving your parcel. Items must be unused and in their original condition, with all tags, labels, and packaging intact.',
				),
				'Return & exchange process'   => array(
					'Please contact our customer care team via WhatsApp with your order details before sending any items back. Once your request has been reviewed and confirmed, you may ship your parcel to:',
					"Miroir Studio\nJl. Nayaga No. 6\nBandung, Indonesia",
					'Please wait for our team’s confirmation before arranging the return shipment.',
				),
				'Refund timing & return costs' => array(
					'For return shipping arrangements and refund enquiries, please contact our customer care team via WhatsApp. Our team will guide you through the process and provide further assistance.',
				),
			),
			'Request help with a return',
			'/contact/'
		);
	}

	/** Terms of Use page copy. */
	private static function terms_blocks() {
		return self::blocks(
			'By browsing or placing an order through Miroir, you agree to the following terms and conditions. Please take a moment to read them before making a purchase.',
			array(
				'Prices & payment'                => array(
					'All prices displayed on our website are listed in Indonesian Rupiah (IDR), unless stated otherwise.',
					'A WhatsApp message is considered an order request. Our team will confirm product availability and the final order total, including applicable shipping costs, before proceeding with payment.',
					'Once the final total has been confirmed, payment must be completed within 24 hours. Prices confirmed by our team will remain valid for your order, even if website prices change afterward.',
				),
				'Order acceptance & cancellations' => array(
					'All orders are subject to product availability and confirmation by our team.',
					'Need to cancel your order or change your mind about a selected item? No worries! You may request a cancellation or product change by contacting our team via WhatsApp first.',
					'All cancellation and modification requests must be confirmed by our team before any changes are made. Product changes are subject to stock availability and any applicable price differences.',
					'Once your order is confirmed, we will begin preparing your parcel for shipment.',
				),
				'Shipping & delivery'             => array(
					'Orders are dispatched within 24 hours after order confirmation on weekdays and within 2 days for orders placed over the weekend.',
					'Domestic shipments within Indonesia are handled by J&T Express. International shipping is available through TLX and DHL, depending on the destination.',
					'Delivery times may vary depending on the courier and destination.',
				),
				'Liability & applicable law'      => array(
					'Miroir strives to ensure that all product descriptions, images, prices, and information on our website are accurate and up to date.',
					'Please note that slight colour variations may occur due to lighting conditions and individual screen settings.',
					'All purchases and transactions made through Miroir are subject to the applicable laws and regulations of the Republic of Indonesia.',
					'For any questions or assistance, please contact our customer care team via WhatsApp.',
				),
			),
			'Contact customer care',
			'/contact/'
		);
	}

	/**
	 * FAQ copy (client-approved): question => paragraphs. Used by the page seed and
	 * by `wp eval` when refreshing an existing FAQ page's stored content.
	 *
	 * @return array<int, array{0:string,1:string[]}>
	 */
	public static function faq_items() {
		return array(
			array(
				'How long does shipping take?',
				array(
					'We aim to dispatch your order within 24 hours after it is received on weekdays, and within 2 days for orders placed over the weekend.',
					'For domestic deliveries within Indonesia, we partner with J&T Express to ensure your pieces reach you safely.',
				),
			),
			array(
				'Do you ship outside Indonesia?',
				array(
					'Yes, we do! Miroir offers international shipping to selected destinations, including Singapore and Malaysia through TLX.',
					'For other international destinations, we also offer DHL shipping. Shipping rates and delivery times may vary depending on your location. Feel free to contact us via WhatsApp for more details.',
				),
			),
			array(
				'How do I confirm my order and payment?',
				array(
					'Once you’ve placed your order, please contact us via WhatsApp to confirm your order and payment.',
					'Our team will assist you with the confirmation process and make sure everything is in order before your parcel is on its way.',
				),
			),
			array(
				'Can I return or exchange an item?',
				array(
					'We accept exchange requests within 3 days of receiving your order.',
					'To be eligible for an exchange, the item must be in its original condition, with all tags, labels, and packaging kept intact. Please contact our team via WhatsApp for assistance.',
				),
			),
			array(
				'How do I find my size?',
				array(
					'Each Miroir piece is thoughtfully designed with comfort and fit in mind. You can find detailed measurements and sizing information on each product page.',
					'If you’re unsure about the fit, feel free to reach out to us. We’d be happy to help you find your perfect fit.',
				),
			),
			array(
				'How do I track my order?',
				array(
					'Once your order has been shipped, you can track your parcel using the tracking number provided by our team or through your selected courier’s tracking service.',
					'You can also contact us via WhatsApp if you need any assistance with your delivery.',
				),
			),
		);
	}

	/**
	 * Page body as blocks: intro paragraph, ruled sections (Group blocks with the
	 * "section" class: heading + paragraph), one full-width button. The lofi's
	 * draft/scope notes are ordinary paragraphs ("note-draft", "note-scope") that
	 * editors delete once approved copy is in.
	 *
	 * @param string $intro    Intro paragraph.
	 * @param array  $sections heading => copy (a string, or a list of paragraphs; line breaks kept).
	 * @param string $button   Button label.
	 * @param string $url      Button URL ("#whatsapp" opens the shop's WhatsApp).
	 * @param string $before   Draft note shown above the button.
	 * @param string $after    Scope note shown below the button.
	 */
	private static function blocks( $intro, $sections, $button, $url, $before = '', $after = '' ) {
		$out       = "<!-- wp:paragraph -->\n<p>" . esc_html( $intro ) . "</p>\n<!-- /wp:paragraph -->\n\n";
		$paragraph = function ( $text ) {
			return "<!-- wp:paragraph -->\n<p>" . nl2br( esc_html( $text ), false ) . "</p>\n<!-- /wp:paragraph -->";
		};
		foreach ( $sections as $heading => $copy ) {
			$out .= '<!-- wp:group {"className":"section","layout":{"type":"default"}} -->' . "\n"
				. '<div class="wp-block-group section"><!-- wp:heading -->' . "\n"
				. '<h2 class="wp-block-heading">' . esc_html( $heading ) . "</h2>\n<!-- /wp:heading -->\n\n"
				. implode( "\n\n", array_map( $paragraph, (array) $copy ) ) . "</div>\n<!-- /wp:group -->\n\n";
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
				case 'video-hero':
					$attrs = array( 'caption' => $section['caption'], 'url' => wp_make_link_relative( $section['url'] ) );
					break;
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
	/**
	 * Undo Site Editor changes: delete stored templates, template parts and
	 * global styles of this theme, so the theme files (templates/, parts/,
	 * theme.json) apply again. Page content and menus are not touched.
	 *
	 * ## OPTIONS
	 *
	 * [--yes]
	 * : Skip the confirmation.
	 *
	 * @subcommand reset-templates
	 * @when after_wp_load
	 */
	public function reset_templates( $args, $assoc ) {
		$posts = get_posts(
			array(
				'post_type'      => array( 'wp_template', 'wp_template_part', 'wp_global_styles' ),
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'tax_query'      => array( array( 'taxonomy' => 'wp_theme', 'field' => 'name', 'terms' => get_stylesheet() ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		if ( ! $posts ) {
			WP_CLI::success( 'No Site Editor changes stored: every template comes from the theme files.' );
			return;
		}
		foreach ( $posts as $post ) {
			WP_CLI::log( sprintf( '%-17s #%d %s', $post->post_type, $post->ID, $post->post_name ) );
		}
		if ( empty( $assoc['yes'] ) ) {
			WP_CLI::confirm( sprintf( 'Delete these %d Site Editor changes?', count( $posts ) ) );
		}
		foreach ( $posts as $post ) {
			wp_delete_post( $post->ID, true );
		}
		WP_CLI::success( 'Templates, parts and styles are back to the theme files.' );
	}

	/**
	 * Put every on-sale product in the "sale" category and take the rest out.
	 * Saves keep it in step afterwards; run once for products saved before that.
	 *
	 * @subcommand sync-sale
	 * @when after_wp_load
	 */
	public function sync_sale() {
		if ( ! get_term_by( 'slug', 'sale', 'product_cat' ) ) {
			WP_CLI::error( 'No product category with the slug "sale".' );
		}
		$ids     = wc_get_products(
			array(
				'status' => array( 'publish', 'private', 'draft', 'pending', 'future' ),
				'type'   => array_keys( wc_get_product_types() ),
				'limit'  => -1,
				'return' => 'ids',
			)
		);
		$changed = 0;
		foreach ( $ids as $id ) {
			$product = wc_get_product( $id );
			if ( $product && miroir_sync_sale_category( $product ) ) {
				WP_CLI::log( sprintf( '#%d %s: %s', $id, $product->get_name(), $product->is_on_sale() ? 'added' : 'removed' ) );
				++$changed;
			}
		}
		WP_CLI::success( sprintf( '%d of %d products changed.', $changed, count( $ids ) ) );
	}

	public function maintenance( $args ) {
		$state = 'on' === ( $args[0] ?? '' ) ? 'on' : 'off';
		update_option( 'miroir_maintenance', $state );
		WP_CLI::success( "Maintenance mode $state." );
	}

	/**
	 * Print the secret link that shows the store during maintenance.
	 *
	 * ## OPTIONS
	 *
	 * [--rotate]
	 * : Replace the secret. Every existing preview stops working.
	 *
	 * @subcommand preview-link
	 * @when after_wp_load
	 */
	public function preview_link( $args, $assoc ) {
		$token = (string) get_option( 'miroir_preview_token', '' );
		if ( '' === $token || ! empty( $assoc['rotate'] ) ) {
			$token = wp_generate_password( 32, false );
			update_option( 'miroir_preview_token', $token, false );
		}
		WP_CLI::log( add_query_arg( 'miroir-preview', $token, home_url( '/' ) ) );
	}

	/**
	 * Apply the 2026-11-11 client copy to a site that already has its pages, menus
	 * and home page: FAQ, Returns & Exchanges and Terms of Use pages, the policy
	 * footer labels, the shop sidebar menus and the home captions. Page content
	 * lives in the database, so deploying the theme alone does not change it.
	 * Safe to re-run: it sets the same copy again.
	 *
	 * ## EXAMPLES
	 *
	 *     wp miroir theme refresh-copy
	 *
	 * @subcommand refresh-copy
	 * @when after_wp_load
	 */
	public function refresh_copy() {
		$pages = array(
			'faq'               => array( 'FAQ', self::faq_blocks() ),
			'returns-exchanges' => array( 'Returns & Exchanges', self::returns_blocks() ),
			'terms-of-use'      => array( 'Terms of Use', self::terms_blocks() ),
		);
		foreach ( $pages as $slug => $page ) {
			$this->refresh_page( $slug, $page[0], $page[1] );
		}

		// Menus: rename, remove and reorder by item title.
		$this->refresh_menu( 'footer-policies', array( 'Return policy' => 'Returns & Exchanges', 'Condition of use' => 'Terms of Use' ), array() );
		$this->refresh_menu( 'shop-women', array(), array( 'accessories' ) );
		$this->refresh_menu(
			'featured',
			array( 'studio collection' => 'by miroir collection' ),
			array( 'collaboration' ),
			array( 'new arrivals', 'best sellers', 'by miroir collection' )
		);

		// Home captions and labels, stored in the home page's Elementor data, block backup and content.
		$home = (int) get_option( 'page_on_front' );
		if ( ! $home ) {
			$page = get_page_by_path( 'home' );
			$home = $page ? $page->ID : 0;
		}
		if ( $home ) {
			$swaps = array(
				'new arrivals: browse the collection'      => 'new arrivals: a little something new, made to be loved.',
				'shop women: relaxed by design'            => 'shop women: everyday femininity, effortlessly.',
				'shop women: browse the collection'        => 'shop women: everyday femininity, effortlessly.',
				'the collection: everyday, in good company' => 'where everyday meets elegance',
				'"label":"new arrivals"'                   => '"label":"discover what’s new"',
				'"label":"shop studio collection"'         => '"label":"shop by miroir"',
			);
			foreach ( array( '_elementor_data', '_miroir_blocks_before_elementor' ) as $key ) {
				$value = get_post_meta( $home, $key, true );
				if ( is_string( $value ) && '' !== $value ) {
					$new = strtr( $value, $swaps );
					if ( $new !== $value ) {
						update_post_meta( $home, $key, wp_slash( $new ) );
						WP_CLI::log( "home   #$home $key updated" );
					}
				}
			}
			$content = get_post_field( 'post_content', $home );
			$new     = strtr( $content, $swaps );
			if ( $new !== $content ) {
				wp_update_post( array( 'ID' => $home, 'post_content' => wp_slash( $new ) ) );
				WP_CLI::log( "home   #$home post_content updated" );
			}
			delete_post_meta( $home, '_elementor_element_cache' );
		} else {
			WP_CLI::warning( 'No home page found; captions not touched.' );
		}

		if ( class_exists( '\\Elementor\\Plugin' ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}
		wp_cache_flush();
		WP_CLI::success( 'Copy refreshed.' );
	}

	/**
	 * Set one info page's title and content. Elementor pages keep their text in a
	 * miroir-text-page widget, so that is updated too; the block copy and the
	 * stored content follow the page's existing shape.
	 *
	 * @param string $slug   Page slug.
	 * @param string $title  Page title.
	 * @param string $blocks Block markup.
	 */
	private function refresh_page( $slug, $title, $blocks ) {
		$page = get_page_by_path( $slug );
		if ( ! $page ) {
			WP_CLI::warning( "page   $slug not found, skipped" );
			return;
		}
		$id   = $page->ID;
		$html = do_blocks( $blocks );

		$raw = get_post_meta( $id, '_elementor_data', true );
		$set = false;
		if ( is_string( $raw ) && '' !== $raw ) {
			$data   = json_decode( $raw, true );
			$walk   = function ( &$nodes ) use ( &$walk, $html, &$set ) {
				foreach ( $nodes as &$node ) {
					if ( ! $set && 'miroir-text-page' === ( $node['widgetType'] ?? '' ) ) {
						$node['settings']['text'] = "\n" . $html . "\n";
						$set                      = true;
					}
					if ( ! empty( $node['elements'] ) ) {
						$walk( $node['elements'] );
					}
				}
			};
			if ( is_array( $data ) ) {
				$walk( $data );
			}
			if ( $set ) {
				update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
				delete_post_meta( $id, '_elementor_element_cache' );
			}
		}
		if ( metadata_exists( 'post', $id, '_miroir_blocks_before_elementor' ) ) {
			update_post_meta( $id, '_miroir_blocks_before_elementor', wp_slash( $blocks ) );
		}

		$current = get_post_field( 'post_content', $id );
		if ( 0 === strpos( ltrim( $current ), '<article' ) ) {
			$content = "<article>\n<h1>" . esc_html( $title ) . "</h1>\n" . $html . "\n\t\t</article>";
		} else {
			$content = $blocks;
		}
		wp_update_post( array( 'ID' => $id, 'post_title' => $title, 'post_content' => wp_slash( $content ) ) );
		WP_CLI::log( "page   $slug updated" . ( $set ? ' (Elementor widget too)' : '' ) );
	}

	/**
	 * Rename, remove and reorder items of a nav menu, matched by title.
	 *
	 * @param string   $slug   Menu slug.
	 * @param string[] $rename Old title => new title.
	 * @param string[] $remove Titles to delete.
	 * @param string[] $order  Titles in the wanted order (optional).
	 */
	private function refresh_menu( $slug, $rename, $remove, $order = array() ) {
		$menu = wp_get_nav_menu_object( $slug );
		if ( ! $menu ) {
			WP_CLI::warning( "menu   $slug not found, skipped" );
			return;
		}
		foreach ( (array) wp_get_nav_menu_items( $menu->term_id ) as $item ) {
			if ( in_array( $item->title, $remove, true ) ) {
				wp_delete_post( $item->ID, true );
				WP_CLI::log( "menu   $slug removed {$item->title}" );
			} elseif ( isset( $rename[ $item->title ] ) ) {
				wp_update_post( array( 'ID' => $item->ID, 'post_title' => $rename[ $item->title ] ) );
				WP_CLI::log( "menu   $slug renamed {$item->title} -> {$rename[ $item->title ]}" );
			}
		}
		if ( $order ) {
			foreach ( (array) wp_get_nav_menu_items( $menu->term_id ) as $item ) {
				$position = array_search( $item->title, $order, true );
				if ( false !== $position && (int) $item->menu_order !== $position + 1 ) {
					wp_update_post( array( 'ID' => $item->ID, 'menu_order' => $position + 1 ) );
				}
			}
		}
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
					'new collection' => $cat( 'new-collection' ),
					'shop'           => $shop,
					'sale'           => $cat( 'sale' ),
				),
			),
			'plp-categories' => array(
				'name'  => 'shop women',
				'items' => array(
					'bottoms'     => $cat( 'bottoms' ),
					'dress'       => $cat( 'dress' ),
					'outerwear'   => $cat( 'outerwear' ),
					'tops'        => $cat( 'tops' ),
				),
			),
			'plp-featured'   => array(
				'name'  => 'featured',
				'items' => array(
					'new arrivals'         => $cat( 'new-collection' ),
					'best sellers'         => $cat( 'best-seller' ),
					'by miroir collection' => $cat( 'at-the-studio' ),
				),
			),
			'panel-nav'      => array(
				'name'  => 'Menu panel',
				'items' => array(
					'New arrivals'  => $cat( 'new-collection' ),
					'Shop women'    => $shop,
					'Collections'   => $cat( 'shop-by-collection' ),
					'Sale'          => $cat( 'sale' ),
					'Account'       => $account,
					'Customer care' => $page( 'contact' ),
				),
			),
			'footer-account' => array(
				'name'  => 'Footer: account',
				'items' => array(
					'Account'              => $account,
					'Payment confirmation' => $page( 'payment-confirmation' ),
					'Shipping information' => $page( 'shipping-information' ),
				),
			),
			'footer-policy'  => array(
				'name'  => 'Footer: policies',
				'items' => array(
					'FAQ'              => $page( 'faq' ),
					'Returns & Exchanges' => $page( 'returns-exchanges' ),
					'Terms of Use'        => $page( 'terms-of-use' ),
				),
			),
			'footer-contact' => array(
				'name'  => 'Footer: contact',
				'items' => array(
					'About us'              => $page( 'about' ),
					'Studio location'       => $page( 'studio-location' ),
					'Contact customer care' => $page( 'contact' ),
					'Instagram'             => 'https://www.instagram.com/miroirstore/',
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
