<?php
/** Automatic product-search popularity. Only daily aggregate counts are stored. */

defined( 'ABSPATH' ) || exit;

function miroir_search_popularity_setup() {
	global $wpdb;
	if ( '1' === get_option( 'miroir_search_popularity_schema' ) ) {
		return;
	}
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$table = $wpdb->prefix . 'miroir_search_counts';
	dbDelta( "CREATE TABLE {$table} (
		term_hash char(64) NOT NULL,
		search_day date NOT NULL,
		term varchar(60) NOT NULL,
		hits bigint unsigned NOT NULL DEFAULT 0,
		PRIMARY KEY  (term_hash,search_day),
		KEY search_day (search_day)
	) {$wpdb->get_charset_collate()};" );
	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table ) ) ) === $table ) {
		update_option( 'miroir_search_popularity_schema', '1', false );
	}
}
add_action( 'init', 'miroir_search_popularity_setup' );

/** Reject contact details, URLs, markup and oversized input instead of publishing them. */
function miroir_normalize_search_term( $term ) {
	if ( ! is_string( $term ) ) {
		return '';
	}
	$term = preg_replace( '/\s+/u', ' ', trim( $term ) );
	if ( ! $term || ! preg_match( "/^[\p{L}\p{M}][\p{L}\p{M} '\x{2019}-]{1,59}$/u", $term ) ) {
		return '';
	}
	return function_exists( 'mb_strtolower' ) ? mb_strtolower( $term, 'UTF-8' ) : strtolower( $term );
}

function miroir_search_popularity_cutoff() {
	return gmdate( 'Y-m-d', strtotime( '-29 days' ) );
}

/** Successful first-page storefront searches only; never count admin previews or known bots. */
function miroir_track_product_search() {
	global $wp_query, $wpdb;
	if ( is_admin() || is_preview() || is_customize_preview() || current_user_can( 'edit_posts' ) ||
		! is_search() || is_paged() || is_feed() || is_404() ||
		'product' !== get_query_var( 'post_type' ) || empty( $wp_query->found_posts ) ||
		'GET' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || headers_sent() ||
		'1' !== get_option( 'miroir_search_popularity_schema' ) ) {
		return;
	}
	$agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
	if ( ! $agent || preg_match( '/bot|crawl|spider|slurp|headless|preview/i', $agent ) ) {
		return;
	}
	$term = miroir_normalize_search_term( get_search_query( false ) );
	if ( '' === $term ) {
		return;
	}
	$day   = gmdate( 'Y-m-d' );
	$hash  = hash( 'sha256', $term );
	$token = substr( hash( 'sha256', $day . $term ), 0, 16 );
	$seen  = isset( $_COOKIE['miroir_search_seen'] ) && is_string( $_COOKIE['miroir_search_seen'] ) ? explode( '.', $_COOKIE['miroir_search_seen'] ) : array();
	if ( ( $seen[0] ?? '' ) !== $day ) {
		$seen = array( $day );
	}
	// Bound cookie size and daily contributions. The cookie contains hashes, not query text.
	if ( in_array( $token, $seen, true ) || count( $seen ) > 20 ) {
		return;
	}
	$table = $wpdb->prefix . 'miroir_search_counts';
	$added = $wpdb->query( $wpdb->prepare(
		"INSERT INTO {$table} (term_hash, search_day, term, hits) VALUES (%s, %s, %s, 1)
		ON DUPLICATE KEY UPDATE hits = hits + 1",
		$hash, $day, $term
	) );
	if ( false === $added ) {
		return;
	}
	$seen[] = $token;
	$value  = implode( '.', $seen );
	setcookie( 'miroir_search_seen', $value, array(
		'expires' => strtotime( 'tomorrow UTC' ),
		'path' => '/', 'secure' => is_ssl(), 'httponly' => true, 'samesite' => 'Lax',
	) );
	$_COOKIE['miroir_search_seen'] = $value;
	delete_transient( 'miroir_top_search_terms' );
}
add_action( 'template_redirect', 'miroir_track_product_search', 20 );

function miroir_top_search_terms() {
	global $wpdb;
	if ( '1' !== get_option( 'miroir_search_popularity_schema' ) ) {
		return array();
	}
	$terms = get_transient( 'miroir_top_search_terms' );
	if ( false !== $terms ) {
		return $terms;
	}
	$table = $wpdb->prefix . 'miroir_search_counts';
	$terms = $wpdb->get_col( $wpdb->prepare(
		"SELECT term FROM {$table} WHERE search_day >= %s GROUP BY term
		HAVING SUM(hits) >= 3 ORDER BY SUM(hits) DESC, MAX(search_day) DESC, term ASC LIMIT 3",
		miroir_search_popularity_cutoff()
	) );
	set_transient( 'miroir_top_search_terms', $terms, 5 * MINUTE_IN_SECONDS );
	return $terms;
}

function miroir_cleanup_search_counts() {
	global $wpdb;
	if ( '1' !== get_option( 'miroir_search_popularity_schema' ) ) {
		return;
	}
	$wpdb->query( $wpdb->prepare(
		"DELETE FROM {$wpdb->prefix}miroir_search_counts WHERE search_day < %s",
		miroir_search_popularity_cutoff()
	) );
	delete_transient( 'miroir_top_search_terms' );
}
add_action( 'miroir_cleanup_search_counts', 'miroir_cleanup_search_counts' );
add_action( 'init', function () {
	if ( ! wp_next_scheduled( 'miroir_cleanup_search_counts' ) ) {
		wp_schedule_event( time() + DAY_IN_SECONDS, 'daily', 'miroir_cleanup_search_counts' );
	}
} );
add_action( 'switch_theme', function () {
	wp_clear_scheduled_hook( 'miroir_cleanup_search_counts' );
} );
