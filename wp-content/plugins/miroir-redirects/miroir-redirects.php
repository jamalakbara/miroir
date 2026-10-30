<?php
/**
 * Plugin Name: Miroir Legacy Redirects
 * Description: 301s for legacy miroir.id URLs that have no page on the new store (docs/migration/legacy-urls.csv).
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
		return home_url( user_trailingslashit( $m[1] ) );
	}

	return null;
}
