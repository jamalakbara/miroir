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
			<h1 class="flow__title"><?php echo esc_html( miroir_text( 'no_results' ) ); ?></h1>
			<form class="flow__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php miroir_field( array( 'name' => 's', 'id' => 'empty-search', 'type' => 'search', 'label' => miroir_text( 'search_label' ), 'value' => $query, 'class' => 'field--quiet' ) ); ?>
				<input type="hidden" name="post_type" value="product">
				<?php /* translators: %s: search query. */ ?>
				<p class="flow__heading"><?php echo esc_html( miroir_text( 'no_results_for', $query ) ); ?></p>
				<p class="flow__intro"><?php echo esc_html( miroir_text( 'no_results_text' ) ); ?></p>
				<button class="btn btn--primary btn--block" type="submit"><?php echo esc_html( miroir_text( 'search_again' ) ); ?></button>
			</form>
			<a class="btn btn--block" href="<?php echo esc_url( $shop ); ?>"><?php echo esc_html( miroir_text( 'explore_all' ) ); ?></a>
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
		<h1 class="flow__title"><?php echo esc_html( miroir_text( 'no_matches' ) ); ?></h1>
		<?php if ( $path ) : ?>
			<p class="flow__label"><?php echo esc_html( implode( ' / ', $path ) ); ?></p>
		<?php endif; ?>
		<?php if ( $active ) : ?>
			<p class="flow__intro"><?php echo esc_html( implode( ' · ', $active ) ); ?> · <a href="<?php echo esc_url( $base_url ); ?>"><?php echo esc_html( miroir_text( 'clear_all' ) ); ?></a></p>
			<p class="flow__heading"><?php echo esc_html( miroir_text( 'no_matches_heading' ) ); ?></p>
			<p class="flow__intro"><?php echo esc_html( miroir_text( 'no_matches_text' ) ); ?></p>
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( $base_url ); ?>"><?php echo esc_html( miroir_text( 'clear_filters' ) ); ?></a>
		<?php else : ?>
			<p class="flow__heading"><?php echo esc_html( miroir_text( 'empty_collection' ) ); ?></p>
			<p class="flow__intro"><?php echo esc_html( miroir_text( 'empty_collection_text' ) ); ?></p>
		<?php endif; ?>
		<a class="btn btn--block" href="<?php echo esc_url( $shop ); ?>"><?php echo esc_html( miroir_text( 'back_to_collection' ) ); ?></a>
	</div>
</div>
