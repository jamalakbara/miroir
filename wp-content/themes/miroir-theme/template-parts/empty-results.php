<?php
/**
 * Empty listings: search with no results (screen 35) and a collection with no
 * matching products (screen 36). Rendered instead of the listing layout.
 *
 * @var array $args { context: 'search'|'collection' }
 */

defined( 'ABSPATH' ) || exit;

$shop = wc_get_page_permalink( 'shop' );

if ( 'search' === ( $args['context'] ?? '' ) ) :
	$query = get_search_query();
	?>
	<div class="flow">
		<div class="flow__col">
			<h1 class="flow__title"><?php esc_html_e( 'No results', 'miroir' ); ?></h1>
			<form class="flow__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php miroir_field( array( 'name' => 's', 'id' => 'empty-search', 'type' => 'search', 'label' => __( 'Search products', 'miroir' ), 'value' => $query, 'class' => 'field--quiet' ) ); ?>
				<input type="hidden" name="post_type" value="product">
				<?php /* translators: %s: search query. */ ?>
				<p class="flow__heading"><?php echo esc_html( sprintf( __( 'No results for “%s”.', 'miroir' ), $query ) ); ?></p>
				<p class="flow__intro"><?php esc_html_e( 'Try a shorter search or check your spelling.', 'miroir' ); ?></p>
				<button class="btn btn--primary btn--block" type="submit"><?php esc_html_e( 'Search again', 'miroir' ); ?></button>
			</form>
			<a class="btn btn--block" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Explore all products', 'miroir' ); ?></a>
		</div>
	</div>
	<?php
	return;
endif;

$term     = is_tax() ? get_queried_object() : null;
$path     = array();
$base_url = $shop;
if ( $term instanceof WP_Term ) {
	foreach ( array_reverse( get_ancestors( $term->term_id, $term->taxonomy ) ) as $ancestor_id ) {
		$path[] = get_term( $ancestor_id )->name;
	}
	$path[]   = $term->name;
	$base_url = get_term_link( $term );
}
// Active layered-nav filters (filter_*), e.g. "Black · Size S".
$active = array();
foreach ( wc_get_attribute_taxonomies() as $attribute ) {
	$param = 'filter_' . $attribute->attribute_name;
	foreach ( array_filter( explode( ',', wc_clean( wp_unslash( $_GET[ $param ] ?? '' ) ) ) ) as $slug ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$value    = get_term_by( 'slug', $slug, wc_attribute_taxonomy_name( $attribute->attribute_name ) );
		$name     = $value ? $value->name : $slug;
		$active[] = 'size' === $attribute->attribute_name ? sprintf( /* translators: %s: size. */ __( 'Size %s', 'miroir' ), $name ) : ucfirst( $name );
	}
}
?>
<div class="flow">
	<div class="flow__col">
		<h1 class="flow__title"><?php esc_html_e( 'No matches', 'miroir' ); ?></h1>
		<?php if ( $path ) : ?>
			<p class="flow__label"><?php echo esc_html( implode( ' / ', $path ) ); ?></p>
		<?php endif; ?>
		<?php if ( $active ) : ?>
			<p class="flow__intro"><?php echo esc_html( implode( ' · ', $active ) ); ?> · <a href="<?php echo esc_url( $base_url ); ?>"><?php esc_html_e( 'Clear all', 'miroir' ); ?></a></p>
			<p class="flow__heading"><?php esc_html_e( 'No products match your filters.', 'miroir' ); ?></p>
			<p class="flow__intro"><?php esc_html_e( 'Remove a filter or browse the full collection.', 'miroir' ); ?></p>
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( $base_url ); ?>"><?php esc_html_e( 'Clear filters', 'miroir' ); ?></a>
		<?php else : ?>
			<p class="flow__heading"><?php esc_html_e( 'Nothing in this collection yet.', 'miroir' ); ?></p>
			<p class="flow__intro"><?php esc_html_e( 'Browse the full collection in the meantime.', 'miroir' ); ?></p>
		<?php endif; ?>
		<a class="btn btn--block" href="<?php echo esc_url( $shop ); ?>"><?php esc_html_e( 'Back to collection', 'miroir' ); ?></a>
	</div>
</div>
