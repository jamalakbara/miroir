<?php
/**
 * Search engine and link-preview markup. The site runs without an SEO plugin,
 * so the theme prints it: meta description, canonical on shop and collection
 * pages, Open Graph / Twitter cards, JSON-LD (WooCommerce's Product and
 * BreadcrumbList generators, plus Organization and WebSite on the home page),
 * a favicon fallback, product and collection titles, noindex on panel, filter
 * and empty collection URLs, and a core sitemap without users or bag pages.
 *
 * Head output stands down when an SEO plugin is active; that plugin owns it.
 */

defined( 'ABSPATH' ) || exit;

/** An SEO plugin is handling titles, descriptions, social cards and schema. */
function miroir_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/** Shop page or a product category / tag archive. */
function miroir_seo_is_listing() {
	return function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() );
}

/** Collection name as written (the design's listing title is lowercased). */
function miroir_seo_listing_name() {
	return is_shop() ? get_the_title( wc_get_page_id( 'shop' ) ) : single_term_title( '', false );
}

/** The product on a single product page, or null. */
function miroir_seo_product() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return null;
	}
	$product = wc_get_product( get_queried_object_id() );
	return $product ? $product : null;
}

/**
 * Plain text cut at a word boundary, for descriptions.
 *
 * @param string $text HTML or text.
 * @param int    $max  Characters.
 */
function miroir_seo_trim( $text, $max = 155 ) {
	$text = trim( preg_replace( '/\s+/u', ' ', wp_strip_all_tags( strip_shortcodes( excerpt_remove_blocks( (string) $text ) ) ) ) );
	if ( mb_strlen( $text ) <= $max ) {
		return $text;
	}
	$cut = mb_substr( $text, 0, $max - 1 );
	$cut = preg_replace( '/\s+\S*$/u', '', $cut );
	return rtrim( $cut, " ,;:.–—-" ) . '…';
}

/** Meta description for the current screen; '' when there is nothing to say. */
function miroir_seo_description() {
	$text    = '';
	$product = miroir_seo_product();
	if ( is_front_page() ) {
		$text = miroir_text( 'seo_home_description' );
	} elseif ( $product ) {
		$text = $product->get_short_description() ? $product->get_short_description() : $product->get_description();
		if ( '' === miroir_seo_trim( $text ) ) {
			$text = miroir_text( 'seo_product_description', $product->get_name() );
		}
	} elseif ( miroir_seo_is_listing() ) {
		$term = is_product_taxonomy() ? get_queried_object() : null;
		$text = $term && '' !== trim( $term->description ) ? $term->description : miroir_text( 'seo_listing_description', miroir_seo_listing_name() );
		$paged = (int) get_query_var( 'paged' );
		if ( $paged > 1 ) {
			/* translators: %d: page number. */
			$text = sprintf( __( 'Page %d.', 'miroir' ), $paged ) . ' ' . miroir_seo_trim( $text, 145 );
		}
	} elseif ( is_singular() ) {
		$post = get_queried_object();
		$text = has_excerpt( $post ) ? $post->post_excerpt : $post->post_content;
	}
	return miroir_seo_trim( $text );
}

/** Canonical URL of the current screen (core prints it for singular pages only). */
function miroir_seo_canonical() {
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_singular() ) {
		return (string) wp_get_canonical_url();
	}
	if ( ! miroir_seo_is_listing() ) {
		return '';
	}
	$url = is_shop() ? get_permalink( wc_get_page_id( 'shop' ) ) : get_term_link( get_queried_object() );
	if ( ! $url || is_wp_error( $url ) ) {
		return '';
	}
	$paged = (int) get_query_var( 'paged' );
	if ( $paged > 1 ) {
		$url = user_trailingslashit( trailingslashit( $url ) . 'page/' . $paged, 'paged' );
	}
	return $url;
}

/**
 * Share image: product photo, collection thumbnail, featured image, else the campaign poster.
 *
 * @return array{0:string,1:int,2:int} URL, width, height (0 when unknown).
 */
function miroir_seo_image() {
	$id      = 0;
	$product = miroir_seo_product();
	if ( $product ) {
		$id = (int) $product->get_image_id();
	} elseif ( is_product_taxonomy() ) {
		$id = (int) get_term_meta( get_queried_object_id(), 'thumbnail_id', true );
	} elseif ( is_singular() && ! is_front_page() ) {
		$id = (int) get_post_thumbnail_id();
	}
	if ( $id ) {
		$src = wp_get_attachment_image_src( $id, 'full' );
		if ( $src ) {
			return array( $src[0], (int) $src[1], (int) $src[2] );
		}
	}
	return array( MIROIR_THEME_URI . '/assets/images/hero-video-poster-desktop.webp', 1440, 640 );
}

/** Variant URLs of a page: header panels, sort and filters, currency switch. */
function miroir_seo_is_variant_request() {
	foreach ( array_keys( $_GET ) as $key ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( in_array( $key, array( 'panel', 'orderby', 'min_price', 'max_price', 'rating_filter', 'miroir_currency' ), true ) || str_starts_with( $key, 'filter_' ) || str_starts_with( $key, 'query_type_' ) ) {
			return true;
		}
	}
	return false;
}

/**
 * Visually hidden h1 for screens whose design shows no page title: the home
 * page and collections on desktop. '' elsewhere.
 */
function miroir_seo_hidden_h1() {
	if ( is_front_page() ) {
		$tagline = get_bloginfo( 'description' );
		return get_bloginfo( 'name' ) . ( $tagline ? ' – ' . $tagline : '' );
	}
	// An empty collection prints its own "No matches" h1.
	if ( miroir_seo_is_listing() && $GLOBALS['wp_query']->found_posts > 0 ) {
		return miroir_seo_listing_name();
	}
	return '';
}

// Noindex variant URLs (their canonical is the clean page) and empty collections.
add_filter(
	'wp_robots',
	function ( $robots ) {
		$empty_listing = miroir_seo_is_listing() && ! is_shop() && 0 === (int) $GLOBALS['wp_query']->found_posts;
		if ( miroir_seo_is_variant_request() || $empty_listing ) {
			unset( $robots['index'] );
			$robots['noindex'] = true;
			$robots['follow']  = true;
		}
		return $robots;
	}
);

// Product pages read "Name – Category – Miroir"; collections add the tagline.
add_filter(
	'document_title_parts',
	function ( $parts ) {
		if ( miroir_seo_plugin_active() ) {
			return $parts;
		}
		$middle  = '';
		$product = miroir_seo_product();
		if ( $product ) {
			$middle = miroir_seo_product_category( $product );
		} elseif ( miroir_seo_is_listing() ) {
			$middle = get_bloginfo( 'description' );
		}
		if ( '' === $middle || empty( $parts['site'] ) ) {
			return $parts;
		}
		$site = $parts['site'];
		unset( $parts['site'], $parts['tagline'] );
		$parts['tagline'] = $middle;
		$parts['site']    = $site;
		return $parts;
	}
);

/**
 * The product's most specific category (deepest in the tree), e.g. Tops rather
 * than Women or New Collection.
 *
 * @param WC_Product $product Product.
 */
function miroir_seo_product_category( $product ) {
	$best  = '';
	$depth = -1;
	foreach ( wc_get_product_terms( $product->get_id(), 'product_cat' ) as $term ) {
		if ( (int) get_option( 'default_product_cat' ) === $term->term_id ) {
			continue;
		}
		$d = count( get_ancestors( $term->term_id, 'product_cat', 'taxonomy' ) );
		if ( $d > $depth ) {
			$best  = $term->name;
			$depth = $d;
		}
	}
	return $best;
}

add_action(
	'wp_head',
	function () {
		if ( ! has_site_icon() ) {
			printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url( MIROIR_THEME_URI . '/assets/images/favicon.svg' ) );
		}
		if ( miroir_seo_plugin_active() || is_404() || is_search() ) {
			return;
		}

		$description = miroir_seo_description();
		$canonical   = miroir_seo_canonical();
		$product     = miroir_seo_product();
		list( $image, $width, $height ) = miroir_seo_image();

		if ( $description ) {
			printf( '<meta name="description" content="%s">' . "\n", esc_attr( $description ) );
		}
		if ( $canonical && ! is_singular() ) {
			printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $canonical ) );
		}

		$og = array(
			'og:site_name'   => get_bloginfo( 'name' ),
			'og:locale'      => get_locale(),
			'og:type'        => $product ? 'product' : 'website',
			'og:title'       => wp_get_document_title(),
			'og:description' => $description,
			'og:url'         => $canonical,
			'og:image'       => $image,
			'og:image:width' => $width ? (string) $width : '',
			'og:image:height' => $height ? (string) $height : '',
		);
		if ( $product && '' !== $product->get_price() ) {
			$og['product:price:amount']   = wc_format_decimal( $product->get_price(), wc_get_price_decimals() );
			$og['product:price:currency'] = get_woocommerce_currency();
			$og['product:availability']   = $product->is_in_stock() ? 'in stock' : 'out of stock';
		}
		foreach ( array_filter( $og, 'strlen' ) as $property => $content ) {
			printf( '<meta property="%s" content="%s">' . "\n", esc_attr( $property ), esc_attr( $content ) );
		}
		echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

		if ( is_front_page() ) {
			miroir_seo_print_home_schema();
		}
	},
	2
);

/** Organization and WebSite (with product search) on the home page. */
function miroir_seo_print_home_schema() {
	$home = home_url( '/' );
	$org  = array(
		'@type' => 'Organization',
		'@id'   => $home . '#organization',
		'name'  => get_bloginfo( 'name' ),
		'url'   => $home,
	);
	$logo = (int) get_theme_mod( 'custom_logo' ) ? wp_get_attachment_image_url( (int) get_theme_mod( 'custom_logo' ), 'full' ) : get_site_icon_url( 512 );
	if ( $logo ) {
		$org['logo'] = $logo;
	}
	$instagram = miroir_mod( 'instagram_url' );
	if ( $instagram ) {
		$org['sameAs'] = array( $instagram );
	}
	$graph = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			$org,
			array(
				'@type'           => 'WebSite',
				'@id'             => $home . '#website',
				'name'            => get_bloginfo( 'name' ),
				'url'             => $home,
				'publisher'       => array( '@id' => $home . '#organization' ),
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => home_url( '/?s={search_term_string}&post_type=product' ),
					'query-input' => 'required name=search_term_string',
				),
			),
		),
	);
	echo '<script type="application/ld+json">' . wp_json_encode( $graph, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES ) . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

// The theme's blocks skip WooCommerce's single-product and breadcrumb hooks, which is
// where its JSON-LD is generated. Generate it here; WooCommerce prints it at wp_footer 10.
add_action(
	'wp_footer',
	function () {
		if ( miroir_seo_plugin_active() || ! function_exists( 'WC' ) || ! WC()->structured_data instanceof WC_Structured_Data ) {
			return;
		}
		$product = miroir_seo_product();
		if ( $product ) {
			WC()->structured_data->generate_product_data( $product );
		}
		if ( $product || ( miroir_seo_is_listing() && ! is_shop() ) ) {
			$crumbs = new WC_Breadcrumb();
			$crumbs->add_crumb( get_bloginfo( 'name' ), home_url( '/' ) );
			$crumbs->generate();
			WC()->structured_data->generate_breadcrumblist_data( $crumbs );
		}
	},
	5
);

// Sitemap: no author archives, no bag, checkout or account pages.
add_filter(
	'wp_sitemaps_add_provider',
	function ( $provider, $name ) {
		return 'users' === $name ? false : $provider;
	},
	10,
	2
);

add_filter(
	'wp_sitemaps_posts_query_args',
	function ( $args, $post_type ) {
		if ( 'page' === $post_type && function_exists( 'wc_get_page_id' ) ) {
			$skip                  = array_filter( array( wc_get_page_id( 'cart' ), wc_get_page_id( 'checkout' ), wc_get_page_id( 'myaccount' ) ), fn( $id ) => $id > 0 );
			$args['post__not_in'] = array_merge( $args['post__not_in'] ?? array(), $skip );
		}
		return $args;
	},
	10,
	2
);
