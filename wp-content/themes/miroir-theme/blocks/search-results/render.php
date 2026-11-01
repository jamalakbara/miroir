<?php
/**
 * Result count, product cards and load more of the search results page.
 */

defined( 'ABSPATH' ) || exit;

$products = miroir_block_listing_products();
if ( ! $products ) {
	get_template_part( 'template-parts/empty-results', null, array( 'context' => 'search' ) );
	return;
}
$found = miroir_block_preview() ? count( $products ) : (int) $GLOBALS['wp_query']->found_posts;
?>
<p class="search-count">
	<?php
	/* translators: 1: number of results, 2: search query. */
	printf( esc_html( _n( '%1$d result for “%2$s”', '%1$d results for “%2$s”', $found, 'miroir' ) ), (int) $found, esc_html( get_search_query() ) );
	?>
</p>
<div class="grid search-grid" data-load-more-list>
	<?php miroir_cards( $products ); ?>
</div>
<?php
echo miroir_block_load_more(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped helper markup.
