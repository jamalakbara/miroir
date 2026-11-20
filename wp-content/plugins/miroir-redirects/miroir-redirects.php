<?php
/**
 * Plugin Name: Miroir Legacy Redirects
 * Description: 301s for legacy miroir.id URLs that have no page on the new store, and for the shop category that duplicates the shop page (docs/migration/legacy-urls.csv).
 * Version:     0.2.0
 * Author:      Miroir
 * Requires Plugins: woocommerce
 */

defined( 'ABSPATH' ) || exit;

/**
 * Only acts on requests WordPress is about to 404, so live pages, products and
 * in-range pagination are never redirected. Runs before redirect_canonical().
 */
add_action(
	'template_redirect',
	function () {
		if ( ! is_404() ) {
			return;
		}

		$path = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH );
		if ( ! $path ) {
			return;
		}

		$target = miroir_legacy_redirect_target( $path );
		if ( $target ) {
			wp_safe_redirect( $target, 301, 'Miroir legacy redirects' );
			exit;
		}
	},
	1
);

/**
 * Product category that lists exactly what the shop page lists. Its archive
 * (and its pages) 301 to the shop page so search engines see one listing; its
 * subcategories (Best Seller, Shop by collection, ...) are untouched.
 */
const MIROIR_SHOP_DUPLICATE_CATEGORY = 'shop';

add_action(
	'template_redirect',
	function () {
		if ( ! is_product_category( MIROIR_SHOP_DUPLICATE_CATEGORY ) ) {
			return;
		}
		$target = wc_get_page_permalink( 'shop' );
		$paged  = (int) get_query_var( 'paged' );
		if ( $paged > 1 ) {
			$target = user_trailingslashit( trailingslashit( $target ) . 'page/' . $paged, 'paged' );
		}
		$query = wp_parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		wp_safe_redirect( $query ? $target . '?' . $query : $target, 301, 'Miroir legacy redirects' );
		exit;
	},
	1
);

// Breadcrumbs of its subcategories (and their JSON-LD) link the shop page, not the redirect.
add_filter(
	'woocommerce_get_breadcrumb',
	function ( $crumbs ) {
		$term = get_term_by( 'slug', MIROIR_SHOP_DUPLICATE_CATEGORY, 'product_cat' );
		$from = $term ? get_term_link( $term ) : '';
		foreach ( $crumbs as &$crumb ) {
			if ( $from && ! is_wp_error( $from ) && isset( $crumb[1] ) && $from === $crumb[1] ) {
				$crumb[1] = wc_get_page_permalink( 'shop' );
			}
		}
		return $crumbs;
	}
);

// Keep the redirected category out of the core sitemap.
add_filter(
	'wp_sitemaps_taxonomies_query_args',
	function ( $args, $taxonomy ) {
		$term = 'product_cat' === $taxonomy ? get_term_by( 'slug', MIROIR_SHOP_DUPLICATE_CATEGORY, 'product_cat' ) : null;
		if ( $term ) {
			$args['exclude'] = array_merge( (array) ( $args['exclude'] ?? array() ), array( $term->term_id ) );
		}
		return $args;
	},
	10,
	2
);

/**
 * Legacy product slug => title of its product in the new catalog (first published
 * match wins). Matched by title, not slug, so it holds whatever slug the product
 * has on each site. Only used when the legacy URL 404s.
 */
const MIROIR_LEGACY_PRODUCTS = array(
	// Same product, different slug.
	'tara-green-top'              => 'Tara Green',
	'lui-stripe-green-top'        => 'Lui Stripe Green',
	'lui-stripe-yellow-top'       => 'Lui Stripe Yellow',
	'kala-stripe-yellow-top'      => 'Kala Stripe Yellow',
	'davon-cream-pants'           => 'Devon Cream Pants',
	'sabine-ballon-pants'         => 'Sabine White Pants',
	// Colour or variant no longer sold: the same model in the new catalog.
	'tara-white-top'              => 'Tara Green',
	'tara-cream-top'              => 'Tara Green',
	'tempo-polka-top'             => 'Tempo Stripe White',
	'tempo-2-way-stripe-wrap-top' => 'Tempo Stripe White',
	'isley-stripe-yellow-top'     => 'Isley Stripe Navy',
	'kala-stripe-green-top'       => 'Kala Stripe Yellow',
	// Same name as the old slug; only needed if the new product got a different slug.
	'lumi-pants'                  => 'Lumi Pants',
	'vibe-stripe-white'           => 'Vibe Stripe White',
	'flow-stripe-navy'            => 'Flow Stripe Navy',
	'tara-stripe-blue'            => 'Tara Stripe Blue',
	'emme-stripe-green'           => 'Emme Stripe Green',
	'emme-stripe-brown'           => 'Emme Stripe Brown',
	'morty-stripe-brown'          => 'Morty Stripe Brown',
	'tempo-stripe-white'          => 'Tempo Stripe White',
	'isley-stripe-navy'           => 'Isley Stripe Navy',
	'devon-black-pants'           => 'Devon Black Pants',
);

/**
 * Permalink of the first published product with one of these titles.
 *
 * @param string[] $titles Product titles, in order of preference.
 * @return string|null
 */
function miroir_product_link_by_title( array $titles ) {
	foreach ( $titles as $title ) {
		$ids = get_posts(
			array(
				'post_type'      => 'product',
				'post_status'    => 'publish',
				'title'          => $title,
				'fields'         => 'ids',
				'posts_per_page' => 1,
				'orderby'        => 'ID',
				'order'          => 'ASC',
			)
		);
		if ( $ids ) {
			return get_permalink( $ids[0] );
		}
	}
	return null;
}

/**
 * Map a legacy path that 404s on the new store to its replacement URL.
 *
 * @param string $path Request path, e.g. /shop-2/page/12/.
 * @return string|null Absolute URL to redirect to, or null to leave the 404.
 */
function miroir_legacy_redirect_target( $path ) {
	$shop = wc_get_page_permalink( 'shop' );

	// Old-store products: the same or closest product in the new catalog when there is
	// one, else the shop page (also covers numeric or trashed slugs like /product/9163/).
	if ( preg_match( '#^/product/([^/]+)/?$#', $path, $m ) ) {
		$titles = MIROIR_LEGACY_PRODUCTS[ $m[1] ] ?? null;
		$link   = $titles ? miroir_product_link_by_title( (array) $titles ) : null;
		return $link ? $link : $shop;
	}

	// Listing pages past the end of the new catalog go to page 1 of the same listing,
	// since page counts differ from the old store. Listings that no longer exist go to
	// the closest parent category that does, else the shop page. Resolved here in one
	// step so every legacy URL is a single 301 hop.
	if ( preg_match( '#^/shop-2(?:/page/\d+)?/?$#', $path ) ) {
		return $shop;
	}
	if ( preg_match( '#^/(product-category|product-tag)/(.+?)(?:/page/\d+)?/?$#', $path, $m ) ) {
		$taxonomy = 'product-category' === $m[1] ? 'product_cat' : 'product_tag';
		$slugs    = array_reverse( explode( '/', $m[2] ) );
		foreach ( $slugs as $slug ) {
			if ( MIROIR_SHOP_DUPLICATE_CATEGORY === $slug && 'product_cat' === $taxonomy ) {
				return $shop;
			}
			$term = get_term_by( 'slug', sanitize_title( $slug ), $taxonomy );
			if ( $term ) {
				$link = get_term_link( $term );
				return is_wp_error( $link ) ? $shop : $link;
			}
			if ( 'product_tag' === $taxonomy ) {
				break;
			}
		}
		return $shop;
	}

	return null;
}
