<?php
/**
 * Plugin Name: Miroir Demo Store
 * Description: WP-CLI seeder for a local demo store: store settings, legacy categories/tags/pages and template products with placeholder images. Run `wp miroir demo seed`; undo products with `wp miroir demo reset`.
 * Version:     0.1.0
 * Author:      Miroir
 * Requires Plugins: woocommerce
 */

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Seeds a browsable demo store.
 *
 * Taxonomy and page slugs come from docs/migration/legacy-urls.csv so the demo
 * already answers the legacy URLs. Product slugs are real legacy slugs too, but
 * names, prices and descriptions are placeholders. Every demo product and image
 * is marked with the `_miroir_demo` meta so `reset` removes only demo data.
 */
class Miroir_Demo_Command {

	const DEMO_META = '_miroir_demo';

	/** Legacy product categories: slug => [name, parent slug]. Parents first. */
	const CATEGORIES = array(
		'shop'               => array( 'Shop', '' ),
		'baju'               => array( 'Baju', '' ),
		'women'              => array( 'Women', 'baju' ),
		'new-collection'     => array( 'New Collection', '' ),
		'sale'               => array( 'Sale', '' ),
		'best-seller'        => array( 'Best Seller', 'shop' ),
		'shop-by-miroir'     => array( 'Shop by Miroir', 'shop' ),
		'shop-by-collection' => array( 'Shop by Collection', 'shop' ),
		'textura'            => array( 'Textura', 'shop-by-collection' ),
		'riviera'            => array( 'Riviera', 'shop-by-collection' ),
		'ligne'              => array( 'Ligne', 'shop-by-collection' ),
		'holiday-edit'       => array( 'Holiday Edit', 'shop-by-collection' ),
		'miroir-x-naluri'    => array( 'Miroir x Naluri', 'shop-by-collection' ),
		'at-the-studio'      => array( 'At the Studio', 'shop-by-collection' ),
	);

	const TAGS = array(
		'vintage'          => 'Vintage',
		'casual'           => 'Casual',
		'mini'             => 'Mini',
		'casual-to-formal' => 'Casual to Formal',
	);

	/**
	 * Template products: slug => [garment, regular price, sale price, categories, tags, extra].
	 * Garment drives the placeholder silhouette and whether sizes are variations.
	 * Extra flags: 'featured', 'out_of_stock'.
	 */
	const PRODUCTS = array(
		'vale-dress'                    => array( 'dress', 389000, 0, array( 'new-collection', 'riviera' ), array( 'casual' ), array( 'featured' ) ),
		'mara-dress'                    => array( 'dress', 359000, 0, array( 'best-seller', 'riviera' ), array( 'casual-to-formal' ), array() ),
		'revie-top'                     => array( 'top', 229000, 0, array( 'new-collection', 'ligne' ), array( 'casual' ), array() ),
		'lumie-top'                     => array( 'top', 219000, 0, array( 'best-seller', 'ligne' ), array( 'casual' ), array( 'featured' ) ),
		'liora-top'                     => array( 'top', 239000, 179000, array( 'sale', 'ligne' ), array( 'vintage' ), array() ),
		'lumi-pants'                    => array( 'pants', 289000, 0, array( 'shop-by-miroir' ), array( 'casual' ), array() ),
		'dale-cream-dress'              => array( 'dress', 399000, 0, array( 'new-collection', 'textura' ), array( 'casual-to-formal' ), array( 'featured' ) ),
		'zee-white-top'                 => array( 'top', 199000, 0, array( 'best-seller', 'shop-by-miroir' ), array( 'casual' ), array() ),
		'vibe-stripe-white'             => array( 'top', 219000, 0, array( 'riviera' ), array( 'casual' ), array() ),
		'davon-cream-pants'             => array( 'pants', 299000, 249000, array( 'sale', 'shop-by-miroir' ), array( 'casual-to-formal' ), array() ),
		'heir-white-dress'              => array( 'dress', 429000, 0, array( 'holiday-edit' ), array( 'casual-to-formal' ), array() ),
		'nora-blue-electric'            => array( 'top', 229000, 0, array( 'new-collection', 'at-the-studio' ), array( 'casual' ), array( 'featured' ) ),
		'flow-stripe-navy'              => array( 'top', 219000, 0, array( 'riviera' ), array( 'casual' ), array() ),
		'kana-brown-top'                => array( 'top', 209000, 159000, array( 'sale', 'textura' ), array( 'vintage' ), array() ),
		'barli-long-dress'              => array( 'dress', 449000, 0, array( 'holiday-edit' ), array( 'casual-to-formal' ), array() ),
		'muse-brown-skirt'              => array( 'skirt', 259000, 0, array( 'textura' ), array( 'vintage' ), array() ),
		'shane-organza-outer'           => array( 'outer', 479000, 0, array( 'new-collection', 'at-the-studio' ), array( 'casual-to-formal' ), array( 'featured' ) ),
		'zaline-organza-set'            => array( 'set', 529000, 0, array( 'at-the-studio' ), array( 'casual-to-formal' ), array() ),
		'day-plaid-dress'               => array( 'dress', 379000, 299000, array( 'sale', 'holiday-edit' ), array( 'vintage', 'mini' ), array() ),
		'elara-black-skirt'             => array( 'skirt', 269000, 0, array( 'best-seller', 'ligne' ), array( 'mini' ), array() ),
		'sabine-ballon-pants'           => array( 'pants', 319000, 0, array( 'at-the-studio' ), array( 'casual' ), array( 'out_of_stock' ) ),
		'seren-polka-top'               => array( 'top', 229000, 0, array( 'miroir-x-naluri' ), array( 'vintage' ), array() ),
		'tempo-2-way-stripe-wrap-top'   => array( 'top', 249000, 0, array( 'best-seller', 'miroir-x-naluri' ), array( 'casual-to-formal' ), array() ),
		'eliya-organza-dress'           => array( 'dress', 469000, 0, array( 'holiday-edit' ), array( 'casual-to-formal' ), array() ),
		'kiko-white-embroidery-dress'   => array( 'dress', 419000, 0, array( 'miroir-x-naluri' ), array( 'vintage' ), array() ),
		'como-black-polka-pants'        => array( 'pants', 299000, 0, array( 'miroir-x-naluri' ), array( 'vintage' ), array() ),
		'mara-white-long-sleeve-dress'  => array( 'dress', 399000, 0, array( 'new-collection', 'riviera' ), array( 'casual-to-formal' ), array() ),
		'arlene-outer-dress'            => array( 'outer', 459000, 0, array( 'best-seller', 'textura' ), array( 'casual-to-formal' ), array() ),
	);

	/** Garments sold in S/M/L as variations; the rest are simple "All Size" products. */
	const SIZED_GARMENTS = array( 'dress', 'pants', 'skirt', 'set' );

	/** Slug word => [color name, RGB]. First match wins, so specific words go first. */
	const COLORS = array(
		'electric' => array( 'Electric Blue', array( 47, 75, 216 ) ),
		'navy'     => array( 'Navy', array( 31, 42, 68 ) ),
		'blue'     => array( 'Blue', array( 92, 128, 182 ) ),
		'black'    => array( 'Black', array( 38, 36, 36 ) ),
		'brown'    => array( 'Brown', array( 132, 90, 62 ) ),
		'cream'    => array( 'Cream', array( 226, 211, 184 ) ),
		'white'    => array( 'White', array( 246, 243, 236 ) ),
		'green'    => array( 'Green', array( 94, 125, 90 ) ),
		'plaid'    => array( 'Plaid', array( 140, 112, 88 ) ),
		'organza'  => array( 'Ivory', array( 236, 226, 214 ) ),
	);

	const DEFAULT_COLORS = array(
		array( 'Sand', array( 214, 190, 160 ) ),
		array( 'Sage', array( 158, 172, 140 ) ),
		array( 'Dusty Rose', array( 205, 156, 150 ) ),
		array( 'Taupe', array( 160, 140, 124 ) ),
	);

	/**
	 * Configure the store and create demo taxonomy, pages and products. Safe to re-run:
	 * anything that already exists (matched by slug) is left alone.
	 *
	 * @when after_wp_load
	 */
	public function seed() {
		$this->configure_store();
		$this->seed_taxonomy();
		$this->seed_pages();

		$created = 0;
		foreach ( self::PRODUCTS as $slug => $spec ) {
			if ( get_page_by_path( $slug, OBJECT, 'product' ) ) {
				WP_CLI::log( "skip   product $slug (exists)" );
				continue;
			}
			$id = $this->create_product( $slug, $spec );
			WP_CLI::log( "create product $slug (#$id)" );
			++$created;
		}

		$this->seed_front_page();
		$this->seed_navigation();
		flush_rewrite_rules( false );
		WP_CLI::success( "Demo store ready: $created new products. Visit " . home_url( '/' ) );
	}

	/**
	 * Delete every demo product, its variations and its placeholder images.
	 * Categories, tags, pages and store settings are kept.
	 *
	 * @when after_wp_load
	 */
	public function reset() {
		$ids = get_posts(
			array(
				'post_type'   => 'product',
				'post_status' => 'any',
				'numberposts' => -1,
				'fields'      => 'ids',
				'meta_key'    => self::DEMO_META,
			)
		);
		foreach ( $ids as $id ) {
			$product = wc_get_product( $id );
			foreach ( $product->get_children() as $child_id ) {
				wp_delete_post( $child_id, true );
			}
			$product->delete( true );
		}

		$images = get_posts(
			array(
				'post_type'   => 'attachment',
				'post_status' => 'any',
				'numberposts' => -1,
				'fields'      => 'ids',
				'meta_key'    => self::DEMO_META,
			)
		);
		foreach ( $images as $id ) {
			wp_delete_attachment( $id, true );
		}

		WP_CLI::success( sprintf( 'Removed %d demo products and %d images.', count( $ids ), count( $images ) ) );
	}

	private function configure_store() {
		$options = array(
			'blogname'                                 => 'Miroir',
			'blogdescription'                          => 'Everyday womenswear',
			'woocommerce_default_country'              => 'ID:JK',
			'woocommerce_currency'                     => 'IDR',
			'woocommerce_currency_pos'                 => 'left_space',
			'woocommerce_price_thousand_sep'           => '.',
			'woocommerce_price_decimal_sep'            => ',',
			'woocommerce_price_num_decimals'           => 0,
			'woocommerce_calc_taxes'                   => 'no',
			// Product photos are portrait; match the catalog crop to 4:5.
			'woocommerce_thumbnail_cropping'           => 'custom',
			'woocommerce_thumbnail_cropping_custom_width'  => 4,
			'woocommerce_thumbnail_cropping_custom_height' => 5,
			// New stores start in "coming soon" mode, which hides the shop from logged-out visitors.
			'woocommerce_coming_soon'                  => 'no',
			'woocommerce_store_pages_only'             => 'no',
			// Offline payment methods so test orders can be placed locally.
			'woocommerce_cod_settings'                 => array(
				'enabled'     => 'yes',
				'title'       => 'Cash on delivery',
				'description' => 'Pay with cash upon delivery.',
			),
			'woocommerce_bacs_settings'                => array(
				'enabled'     => 'yes',
				'title'       => 'Bank transfer',
				'description' => 'Transfer to our bank account. Your order ships once payment is confirmed.',
			),
		);
		foreach ( $options as $name => $value ) {
			update_option( $name, $value );
		}

		// The legacy shop listing lived at /shop-2/; keep the same URL.
		$shop_id = wc_get_page_id( 'shop' );
		if ( $shop_id > 0 && 'shop-2' !== get_post_field( 'post_name', $shop_id ) ) {
			wp_update_post( array( 'ID' => $shop_id, 'post_name' => 'shop-2' ) );
		}

		$this->seed_shipping();
		WP_CLI::log( 'store  settings: IDR, Indonesia, COD + bank transfer, flat-rate shipping, shop at /shop-2/' );
	}

	/**
	 * Flat Rp 20.000, free from Rp 500.000. WooCommerce onboarding may already have
	 * created an "Indonesia" zone with unconditional free shipping, so reuse and fix it.
	 */
	private function seed_shipping() {
		$zone = null;
		foreach ( WC_Shipping_Zones::get_zones() as $data ) {
			if ( 'Indonesia' === $data['zone_name'] ) {
				$zone = new WC_Shipping_Zone( $data['id'] );
			}
		}
		if ( ! $zone ) {
			$zone = new WC_Shipping_Zone();
			$zone->set_zone_name( 'Indonesia' );
			$zone->add_location( 'ID', 'country' );
			$zone->save();
		}

		$settings = array(
			'flat_rate'     => array( 'title' => 'Regular', 'tax_status' => 'none', 'cost' => '20000' ),
			'free_shipping' => array( 'title' => 'Free shipping', 'requires' => 'min_amount', 'min_amount' => '500000', 'ignore_discounts' => 'no' ),
		);
		$existing = array();
		foreach ( $zone->get_shipping_methods() as $method ) {
			$existing[ $method->id ] = $method->instance_id;
		}
		foreach ( $settings as $method_id => $values ) {
			$instance_id = $existing[ $method_id ] ?? $zone->add_shipping_method( $method_id );
			update_option( WC_Shipping_Zones::get_shipping_method( $instance_id )->get_instance_option_key(), $values );
		}
		WC_Cache_Helper::get_transient_version( 'shipping', true );
	}

	private function seed_taxonomy() {
		foreach ( self::CATEGORIES as $slug => list( $name, $parent ) ) {
			$this->ensure_term( 'product_cat', $slug, $name, $parent );
		}
		foreach ( self::TAGS as $slug => $name ) {
			$this->ensure_term( 'product_tag', $slug, $name );
		}
		// WooCommerce's default "Uncategorized" category has no legacy counterpart.
		update_option( 'default_product_cat', get_term_by( 'slug', 'baju', 'product_cat' )->term_id );
		WP_CLI::log( sprintf( 'terms  %d categories, %d tags', count( self::CATEGORIES ), count( self::TAGS ) ) );
	}

	private function ensure_term( $taxonomy, $slug, $name, $parent_slug = '' ) {
		$term = get_term_by( 'slug', $slug, $taxonomy );
		if ( $term ) {
			return (int) $term->term_id;
		}
		$args = array( 'slug' => $slug );
		if ( $parent_slug ) {
			$args['parent'] = get_term_by( 'slug', $parent_slug, $taxonomy )->term_id;
		}
		$result = wp_insert_term( $name, $taxonomy, $args );
		if ( is_wp_error( $result ) ) {
			WP_CLI::error( "Could not create $taxonomy $slug: " . $result->get_error_message() );
		}
		return (int) $result['term_id'];
	}

	private function seed_pages() {
		$placeholder = '<!-- wp:paragraph --><p><em>Placeholder text: replace with the real copy from miroir.id.</em></p><!-- /wp:paragraph -->';
		$pages       = array(
			'about'                => array( 'About', '<!-- wp:paragraph --><p>Miroir makes easy, considered pieces for everyday wear: breathable fabrics, relaxed cuts and details that go from casual to formal.</p><!-- /wp:paragraph -->' ),
			'contact'              => array( 'Contact', '<!-- wp:paragraph --><p>Questions about sizing, orders or collaborations? Reach us by email or WhatsApp and we will reply within one working day.</p><!-- /wp:paragraph -->' ),
			'delivery-and-returns' => array( 'Delivery and Returns', '<!-- wp:paragraph --><p>Orders ship within 1 to 2 working days. Flat-rate shipping across Indonesia, free above Rp 500.000. Items can be exchanged within 7 days of delivery if unworn and with tags attached.</p><!-- /wp:paragraph -->' ),
			'terms-and-conditions' => array( 'Terms and Conditions', '<!-- wp:paragraph --><p>By placing an order you agree to these terms. Prices are in Indonesian Rupiah and include all applicable taxes.</p><!-- /wp:paragraph -->' ),
		);
		foreach ( $pages as $slug => list( $title, $body ) ) {
			if ( get_page_by_path( $slug ) ) {
				continue;
			}
			wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => $slug,
					'post_title'   => $title,
					'post_content' => $body . $placeholder,
				)
			);
		}
		$terms_page = get_page_by_path( 'terms-and-conditions' );
		update_option( 'woocommerce_terms_page_id', $terms_page->ID );
		WP_CLI::log( 'pages  about, contact, delivery-and-returns, terms-and-conditions' );
	}

	private function seed_front_page() {
		$home = get_page_by_path( 'home' );
		if ( ! $home ) {
			$content = <<<'HTML'
<!-- wp:cover {"customOverlayColor":"#e2d3b8","isUserOverlayColor":true,"minHeight":420,"align":"full"} -->
<div class="wp-block-cover alignfull" style="min-height:420px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-100 has-background-dim" style="background-color:#e2d3b8"></span><div class="wp-block-cover__inner-container">
<!-- wp:heading {"textAlign":"center","level":1,"style":{"color":{"text":"#2a2420"}}} -->
<h1 class="wp-block-heading has-text-align-center has-text-color" style="color:#2a2420">Miroir</h1>
<!-- /wp:heading -->
<!-- wp:paragraph {"align":"center","style":{"color":{"text":"#2a2420"}}} -->
<p class="has-text-align-center has-text-color" style="color:#2a2420">Easy pieces, from casual to formal.</p>
<!-- /wp:paragraph -->
<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="/shop-2/">Shop now</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
</div></div>
<!-- /wp:cover -->

<!-- wp:heading -->
<h2 class="wp-block-heading">New Collection</h2>
<!-- /wp:heading -->
<!-- wp:shortcode -->
[products category="new-collection" limit="8" columns="4"]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Best Seller</h2>
<!-- /wp:heading -->
<!-- wp:shortcode -->
[products category="best-seller" limit="8" columns="4"]
<!-- /wp:shortcode -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Sale</h2>
<!-- /wp:heading -->
<!-- wp:shortcode -->
[products on_sale="true" limit="4" columns="4"]
<!-- /wp:shortcode -->
HTML;
			$home_id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_name'    => 'home',
					'post_title'   => 'Home',
					'post_content' => $content,
				)
			);
		} else {
			$home_id = $home->ID;
		}
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );

		// The theme's page template prints the page title; the front page should open on the hero.
		$theme = get_stylesheet();
		if ( ! get_block_template( $theme . '//front-page' ) || 'theme' === get_block_template( $theme . '//front-page' )->source ) {
			$template_id = wp_insert_post(
				array(
					'post_type'    => 'wp_template',
					'post_status'  => 'publish',
					'post_name'    => 'front-page',
					'post_title'   => 'Front Page',
					'post_content' => '<!-- wp:template-part {"slug":"header"} /-->' . "\n"
						. '<!-- wp:group {"tagName":"main","layout":{"type":"constrained"}} --><main class="wp-block-group"><!-- wp:post-content {"layout":{"type":"constrained"}} /--></main><!-- /wp:group -->' . "\n"
						. '<!-- wp:template-part {"slug":"footer"} /-->',
				)
			);
			wp_set_object_terms( $template_id, $theme, 'wp_theme' );
		}
		WP_CLI::log( 'page   home set as front page' );
	}

	/**
	 * The header's navigation block falls back to the newest wp_navigation post,
	 * so creating one replaces the default list of every page.
	 */
	private function seed_navigation() {
		if ( get_posts( array( 'post_type' => 'wp_navigation', 'name' => 'miroir-main', 'post_status' => 'publish' ) ) ) {
			return;
		}
		$links = array(
			'Shop'           => '/shop-2/',
			'New Collection' => '/product-category/new-collection/',
			'Best Seller'    => '/product-category/shop/best-seller/',
			'Sale'           => '/product-category/sale/',
			'About'          => '/about/',
			'Contact'        => '/contact/',
		);
		$blocks = '';
		foreach ( $links as $label => $url ) {
			$blocks .= sprintf( '<!-- wp:navigation-link {"label":"%s","url":"%s","kind":"custom"} /-->', $label, $url );
		}
		wp_insert_post(
			array(
				'post_type'    => 'wp_navigation',
				'post_status'  => 'publish',
				'post_name'    => 'miroir-main',
				'post_title'   => 'Main menu',
				'post_content' => $blocks,
			)
		);
		WP_CLI::log( 'menu   main navigation' );
	}

	private function create_product( $slug, $spec ) {
		list( $garment, $regular, $sale, $cats, $tags, $extra ) = $spec;

		$name  = ucwords( str_replace( '-', ' ', $slug ) );
		$color = $this->color_for( $slug );
		$sized = in_array( $garment, self::SIZED_GARMENTS, true );

		$product = $sized ? new WC_Product_Variable() : new WC_Product_Simple();
		$product->set_name( $name );
		$product->set_slug( $slug );
		$product->set_sku( 'MRR-' . strtoupper( $slug ) );
		$product->set_status( 'publish' );
		$product->set_featured( in_array( 'featured', $extra, true ) );
		$product->set_short_description( sprintf( '%s %s in a relaxed fit. Placeholder copy for the demo store.', $color[0], $garment ) );
		$product->set_description( $this->description( $name, $garment, $color[0] ) );

		// Everything lives under "Baju" and "Shop" as on the legacy site, plus its own categories.
		$cat_ids = array();
		foreach ( array_merge( array( 'baju', 'shop' ), $cats ) as $cat ) {
			$cat_ids[] = get_term_by( 'slug', $cat, 'product_cat' )->term_id;
		}
		$product->set_category_ids( $cat_ids );
		$product->set_tag_ids(
			array_map(
				function ( $tag ) {
					return get_term_by( 'slug', $tag, 'product_tag' )->term_id;
				},
				$tags
			)
		);

		$color_attr = new WC_Product_Attribute();
		$color_attr->set_name( 'Color' );
		$color_attr->set_options( array( $color[0] ) );
		$color_attr->set_visible( true );
		$color_attr->set_variation( false );

		$size_attr = new WC_Product_Attribute();
		$size_attr->set_name( 'Size' );
		$size_attr->set_options( $sized ? array( 'S', 'M', 'L' ) : array( 'All Size' ) );
		$size_attr->set_visible( true );
		$size_attr->set_variation( $sized );
		$size_attr->set_position( 1 );

		$product->set_attributes( array( $color_attr, $size_attr ) );

		if ( ! $sized ) {
			$product->set_regular_price( (string) $regular );
			if ( $sale ) {
				$product->set_sale_price( (string) $sale );
			}
			$product->set_manage_stock( true );
			$product->set_stock_quantity( in_array( 'out_of_stock', $extra, true ) ? 0 : 12 );
		} else {
			$product->set_default_attributes( array( 'size' => 'M' ) );
		}

		$product->add_meta_data( self::DEMO_META, 1, true );
		$product_id = $product->save();

		$product->set_image_id( $this->placeholder_image( $product_id, $slug, $name, $garment, $color[1] ) );
		$product->save();

		if ( $sized ) {
			foreach ( array( 'S', 'M', 'L' ) as $size ) {
				$variation = new WC_Product_Variation();
				$variation->set_parent_id( $product_id );
				$variation->set_attributes( array( 'size' => $size ) );
				$variation->set_sku( 'MRR-' . strtoupper( $slug ) . '-' . $size );
				$variation->set_regular_price( (string) $regular );
				if ( $sale ) {
					$variation->set_sale_price( (string) $sale );
				}
				$variation->set_manage_stock( true );
				$variation->set_stock_quantity( in_array( 'out_of_stock', $extra, true ) ? 0 : 5 );
				$variation->save();
			}
			WC_Product_Variable::sync( $product_id );
		}

		return $product_id;
	}

	private function description( $name, $garment, $color ) {
		return sprintf(
			"<p>The %s is a %s %s cut from a soft, breathable woven fabric that keeps its shape through the day.</p>\n" .
			"<ul><li>Relaxed fit, true to size</li><li>Lightweight and non-sheer</li><li>Cold hand wash, hang dry</li></ul>\n" .
			'<p><em>Placeholder description for the demo store.</em></p>',
			esc_html( $name ),
			strtolower( $color ),
			$garment
		);
	}

	private function color_for( $slug ) {
		foreach ( self::COLORS as $word => $color ) {
			if ( false !== strpos( $slug, $word ) ) {
				return $color;
			}
		}
		return self::DEFAULT_COLORS[ crc32( $slug ) % count( self::DEFAULT_COLORS ) ];
	}

	/**
	 * Draw a flat garment silhouette (with stripes or dots when the slug says so)
	 * and import it as the product image.
	 */
	private function placeholder_image( $product_id, $slug, $name, $garment, $rgb ) {
		$w   = 800;
		$h   = 1000;
		$img = imagecreatetruecolor( $w, $h );

		// Garment layer: base color plus pattern across the whole canvas.
		$base = imagecolorallocate( $img, ...$rgb );
		imagefill( $img, 0, 0, $base );
		$light   = array_sum( $rgb ) > 600;
		$pattern = $light ? imagecolorallocate( $img, 70, 80, 120 ) : imagecolorallocate( $img, 245, 242, 235 );
		if ( false !== strpos( $slug, 'stripe' ) ) {
			for ( $y = 0; $y < $h; $y += 36 ) {
				imagefilledrectangle( $img, 0, $y, $w, $y + 12, $pattern );
			}
		} elseif ( false !== strpos( $slug, 'polka' ) ) {
			for ( $y = 0; $y < $h; $y += 60 ) {
				for ( $x = ( $y / 60 ) % 2 ? 30 : 0; $x < $w; $x += 60 ) {
					imagefilledellipse( $img, $x, $y, 16, 16, $pattern );
				}
			}
		} elseif ( false !== strpos( $slug, 'plaid' ) ) {
			for ( $i = 0; $i < $w; $i += 80 ) {
				imagefilledrectangle( $img, $i, 0, $i + 14, $h, $pattern );
				imagefilledrectangle( $img, 0, $i, $w, $i + 14, $pattern );
			}
		}

		// Mask layer: background everywhere except a transparent silhouette hole,
		// so copying it over the garment layer leaves the pattern only inside the shape.
		$mask = imagecreatetruecolor( $w, $h );
		$bg   = $light ? imagecolorallocate( $mask, 214, 205, 192 ) : imagecolorallocate( $mask, 240, 235, 226 );
		imagefill( $mask, 0, 0, $bg );
		$hole = imagecolorallocate( $mask, 255, 0, 255 );
		foreach ( $this->silhouette( $garment ) as $points ) {
			imagefilledpolygon( $mask, $points, $hole );
		}
		imagecolortransparent( $mask, $hole );
		imagecopy( $img, $mask, 0, 0, 0, 0, $w, $h );
		imagedestroy( $mask );

		$ink  = imagecolorallocate( $img, 42, 36, 32 );
		$font = WP_CONTENT_DIR . '/themes/twentytwentythree/assets/fonts/inter/Inter-VariableFont_slnt,wght.ttf';
		if ( file_exists( $font ) ) {
			imagettftext( $img, 16, 0, 40, 60, $ink, $font, 'MIROIR' );
			$box = imagettfbbox( 30, 0, $font, $name );
			imagettftext( $img, 30, 0, (int) ( ( $w - ( $box[2] - $box[0] ) ) / 2 ), 940, $ink, $font, $name );
		} else {
			imagestring( $img, 5, 40, 940, $name, $ink );
		}

		$tmp = wp_tempnam( $slug . '.jpg' );
		imagejpeg( $img, $tmp, 86 );
		imagedestroy( $img );

		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		$file          = array(
			'name'     => $slug . '.jpg',
			'tmp_name' => $tmp,
		);
		$attachment_id = media_handle_sideload( $file, $product_id, $name );
		if ( is_wp_error( $attachment_id ) ) {
			WP_CLI::warning( "Image for $slug failed: " . $attachment_id->get_error_message() );
			return 0;
		}
		update_post_meta( $attachment_id, self::DEMO_META, 1 );
		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $name );
		return $attachment_id;
	}

	/** Polygons (flat x,y lists on an 800x1000 canvas) for each garment type. */
	private function silhouette( $garment ) {
		$top   = array( 250, 230, 340, 200, 460, 200, 550, 230, 650, 380, 575, 425, 530, 350, 530, 660, 270, 660, 270, 350, 225, 425, 150, 380 );
		$dress = array( 320, 180, 360, 180, 400, 220, 440, 180, 480, 180, 500, 260, 480, 420, 610, 840, 190, 840, 320, 420, 300, 260 );
		$pants = array( 290, 190, 510, 190, 545, 850, 425, 850, 400, 400, 375, 850, 255, 850 );
		$skirt = array( 300, 260, 500, 260, 610, 800, 190, 800 );
		$outer = array( 250, 200, 340, 170, 460, 170, 550, 200, 660, 620, 590, 640, 540, 360, 545, 840, 255, 840, 260, 360, 210, 640, 140, 620 );

		switch ( $garment ) {
			case 'dress':
				return array( $dress );
			case 'pants':
				return array( $pants );
			case 'skirt':
				return array( $skirt );
			case 'outer':
				return array( $outer );
			case 'set':
				// Cropped top above a skirt.
				return array(
					array( 250, 150, 340, 120, 460, 120, 550, 150, 650, 300, 575, 345, 530, 270, 530, 450, 270, 450, 270, 270, 225, 345, 150, 300 ),
					array( 300, 480, 500, 480, 600, 860, 200, 860 ),
				);
			default:
				return array( $top );
		}
	}
}

WP_CLI::add_command( 'miroir demo', 'Miroir_Demo_Command' );
