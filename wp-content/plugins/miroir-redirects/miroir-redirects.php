<?php
/**
 * Plugin Name: Miroir Legacy Redirects
 * Description: 301s for legacy miroir.id URLs that have no page on the new store, and for the shop category that duplicates the shop page (docs/migration/legacy-urls.csv).
 * Version:     0.1.0
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
 * Map a legacy path that 404s on the new store to its replacement URL.
 *
 * @param string $path Request path, e.g. /shop-2/page/12/.
 * @return string|null Absolute URL to redirect to, or null to leave the 404.
 */
function miroir_legacy_redirect_target( $path ) {
	// Leftover numeric or trashed product slugs from the old store: /product/9163/, /product/__trashed/.
	if ( preg_match( '#^/product/(\d+|__trashed)/?$#', $path ) ) {
		return wc_get_page_permalink( 'shop' );
	}

	// Listing pages past the end of the new catalog: send to page 1 of the same listing,
	// since page counts differ from the old store.
	if ( preg_match( '#^(/(?:product-category|product-tag)/.+?|/shop-2)/page/\d+/?$#', $path, $m ) ) {
		// The consolidated shop category goes straight to the shop page, not via a second 301.
		return '/product-category/' . MIROIR_SHOP_DUPLICATE_CATEGORY === $m[1] ? wc_get_page_permalink( 'shop' ) : home_url( user_trailingslashit( $m[1] ) );
	}

	return null;
}
