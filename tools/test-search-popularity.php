<?php
/** Run from the repo, replacing the host path as needed:
 * docker compose run --rm -T -v F:/projects/miroir/tools:/tmp/miroir-tests:ro wpcli eval-file /tmp/miroir-tests/test-search-popularity.php
 * Test data is rolled back; real search counts are preserved.
 */

global $wpdb, $wp_query;
$check = function ( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
};
$original_query = $wp_query;
$original_cookie = $_COOKIE;
$original_server = $_SERVER;
$wpdb->query( 'START TRANSACTION' );
try {
	$table = $wpdb->prefix . 'miroir_search_counts';
	$wpdb->query( "DELETE FROM {$table}" );
	delete_transient( 'miroir_top_search_terms' );
	$check( array() === miroir_popular_searches(), 'Empty history must not show manual defaults.' );
	$check( ! isset( miroir_settings()['popular_searches'] ), 'Manual control must be removed.' );
	$check( 'linen shirt' === miroir_normalize_search_term( "  LINEN   Shirt  " ), 'Normalize case and whitespace.' );
	foreach ( array( '', 'a', '<script>alert</script>', 'me@example.test', '08123456', 'https://example.test', str_repeat( 'a', 61 ), array() ) as $invalid ) {
		$check( '' === miroir_normalize_search_term( $invalid ), 'Reject invalid input.' );
	}
	$_SERVER['REQUEST_METHOD'] = 'GET';
	$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0';
	$wp_query = new WP_Query();
	$wp_query->is_search = true;
	$wp_query->found_posts = 1;
	$wp_query->set( 'post_type', 'product' );
	$wp_query->set( 's', 'Shirt' );
	unset( $_COOKIE['miroir_search_seen'] );
	miroir_track_product_search();
	miroir_track_product_search();
	$check( '1' === $wpdb->get_var( "SELECT SUM(hits) FROM {$table}" ), 'Refresh must not count twice.' );
	$check( array() === miroir_top_search_terms(), 'Do not publish terms below three hits.' );
	for ( $i = 0; $i < 2; $i++ ) {
		unset( $_COOKIE['miroir_search_seen'] );
		miroir_track_product_search();
	}
	$check( array( 'shirt' ) === miroir_top_search_terms(), 'Three browsers must qualify a term.' );
	$check( false !== strpos( miroir_popular_searches()['shirt'], 'post_type=product' ), 'Suggestions must link to product search.' );
	unset( $_COOKIE['miroir_search_seen'] );
	$wp_query->found_posts = 0;
	miroir_track_product_search();
	$wp_query->found_posts = 1;
	$wp_query->is_paged = true;
	miroir_track_product_search();
	$wp_query->is_paged = false;
	$_SERVER['HTTP_USER_AGENT'] = 'Googlebot';
	miroir_track_product_search();
	$check( '3' === $wpdb->get_var( "SELECT SUM(hits) FROM {$table}" ), 'Exclude empty searches, pagination and bots.' );
	foreach ( array( 'dress' => 8, 'linen' => 5, 'pants' => 4 ) as $term => $hits ) {
		$wpdb->insert( $table, array( 'term_hash' => hash( 'sha256', $term ), 'search_day' => gmdate( 'Y-m-d' ), 'term' => $term, 'hits' => $hits ) );
	}
	$wpdb->insert( $table, array( 'term_hash' => hash( 'sha256', 'expired' ), 'search_day' => gmdate( 'Y-m-d', strtotime( '-30 days' ) ), 'term' => 'expired', 'hits' => 100 ) );
	delete_transient( 'miroir_top_search_terms' );
	$check( array( 'dress', 'linen', 'pants' ) === miroir_top_search_terms(), 'Rank top three by frequency and exclude expired counts.' );
	miroir_cleanup_search_counts();
	$check( '0' === $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE term = 'expired'" ), 'Cleanup must expire old counts.' );
} finally {
	$wpdb->query( 'ROLLBACK' );
	delete_transient( 'miroir_top_search_terms' );
	$wp_query = $original_query;
	$_COOKIE = $original_cookie;
	$_SERVER = $original_server;
}
WP_CLI::success( 'Search popularity: normalization, empty state, tracking, deduplication, threshold, exclusions, ranking, expiry and links passed.' );
