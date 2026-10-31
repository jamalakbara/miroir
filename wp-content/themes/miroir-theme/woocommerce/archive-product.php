<?php
/**
 * Product listing: category/shop (screen 02) and product search (screen 10).
 *
 * Replaces WooCommerce's archive template outright; the lofi has no breadcrumb,
 * result count or sort dropdown on the listing (sorting lives in the filter panel).
 */

defined( 'ABSPATH' ) || exit;

get_header();

$is_search = is_search();

// Empty listings get their own screens (35 search, 36 collection).
if ( ! have_posts() ) {
	get_template_part( 'template-parts/empty-results', null, array( 'context' => $is_search ? 'search' : 'collection' ) );
	get_footer();
	return;
}

$next_url  = get_next_posts_page_link();
$has_more  = $next_url && get_query_var( 'paged', 1 ) < $GLOBALS['wp_query']->max_num_pages;
?>
<?php if ( $is_search ) : ?>
	<div class="search-body">
		<form class="search-bar" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="search-query"><?php esc_html_e( 'Search products', 'miroir' ); ?></label>
			<input class="search-bar__input" id="search-query" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off">
			<input type="hidden" name="post_type" value="product">
			<a class="search-bar__clear" href="<?php echo esc_url( add_query_arg( array( 's' => '', 'post_type' => 'product' ), home_url( '/' ) ) ); ?>" aria-label="<?php esc_attr_e( 'Clear search', 'miroir' ); ?>"><?php echo miroir_icon( 'x', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></a>
		</form>
		<?php if ( have_posts() ) : ?>
			<p class="search-count">
				<?php
				/* translators: 1: number of results, 2: search query. */
				printf( esc_html( _n( '%1$d result for “%2$s”', '%1$d results for “%2$s”', (int) $GLOBALS['wp_query']->found_posts, 'miroir' ) ), (int) $GLOBALS['wp_query']->found_posts, esc_html( get_search_query() ) );
				?>
			</p>
			<div class="grid search-grid" data-load-more-list>
				<?php
				while ( have_posts() ) {
					the_post();
					get_template_part( 'template-parts/product-card', null, array( 'product' => wc_get_product( get_the_ID() ) ) );
				}
				?>
			</div>
			<?php if ( $has_more ) : ?>
				<a class="btn listing-more" href="<?php echo esc_url( $next_url ); ?>" data-load-more><?php esc_html_e( 'Load more', 'miroir' ); ?></a>
			<?php endif; ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/empty-results', null, array( 'context' => 'search' ) ); ?>
		<?php endif; ?>
	</div>
<?php else : ?>
	<div class="plp">
		<?php get_template_part( 'template-parts/shop-filters' ); ?>

		<div class="plp__bar">
			<p class="plp__bar-title"><?php echo esc_html( miroir_listing_title() ); ?></p>
			<a class="plp__bar-filter" href="<?php echo esc_url( add_query_arg( 'panel', 'filters' ) ); ?>" data-panel-open="filters" aria-controls="filter-panel" aria-expanded="false"><?php esc_html_e( 'Filter / sort +', 'miroir' ); ?></a>
		</div>

		<div class="plp__main">
			<?php woocommerce_output_all_notices(); ?>
			<?php if ( woocommerce_product_loop() && have_posts() ) : ?>
				<div class="grid plp__grid" data-load-more-list>
					<?php
					while ( have_posts() ) {
						the_post();
						get_template_part( 'template-parts/product-card', null, array( 'product' => wc_get_product( get_the_ID() ) ) );
					}
					?>
				</div>
				<?php if ( $has_more ) : ?>
					<a class="btn listing-more" href="<?php echo esc_url( $next_url ); ?>" data-load-more><?php esc_html_e( 'Load more', 'miroir' ); ?></a>
				<?php endif; ?>
			<?php else : ?>
				<?php get_template_part( 'template-parts/empty-results', null, array( 'context' => 'collection' ) ); ?>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
<?php
get_footer();
