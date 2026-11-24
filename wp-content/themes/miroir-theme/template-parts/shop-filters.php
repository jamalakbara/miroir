<?php
/**
 * Shop sidebar (screen 02 "Filter Sidebar"): two link groups from menus.
 */

defined( 'ABSPATH' ) || exit;

$groups = array(
	miroir_filter_group( 'plp-categories', miroir_text( 'shop_title' ), array( 'bottoms', 'dress', 'outerwear', 'tops' ) ),
	miroir_filter_group(
		'plp-featured',
		miroir_text( 'filter_featured' ),
		array(
			'new-collection' => __( 'new arrivals', 'miroir' ),
			'best-seller'    => __( 'best sellers', 'miroir' ),
			'at-the-studio'  => __( 'by miroir collection', 'miroir' ),
		)
	),
);
?>
<aside class="plp__filters" aria-label="<?php esc_attr_e( 'Filter products', 'miroir' ); ?>">
	<?php foreach ( $groups as $group ) : ?>
		<?php if ( $group['items'] ) : ?>
			<nav class="filter-group" aria-label="<?php echo esc_attr( $group['title'] ); ?>">
				<p class="filter-group__title"><?php echo esc_html( $group['title'] ); ?></p>
				<ul class="filter-group__items filter-group__items--indent">
					<?php foreach ( $group['items'] as $item ) : ?>
						<li><a href="<?php echo esc_url( $item['url'] ); ?>"<?php echo $item['current'] ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $item['label'] ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			</nav>
		<?php endif; ?>
	<?php endforeach; ?>
	<?php $sizes = miroir_listing_sizes(); ?>
	<?php if ( $sizes['terms'] ) : ?>
		<div class="filter-group" data-size-group role="group" aria-label="<?php echo esc_attr( miroir_text( 'filter_size_panel' ) ); ?>">
			<p class="filter-group__title"><?php echo esc_html( mb_strtolower( miroir_text( 'filter_size_panel' ) ) ); ?></p>
			<ul class="filter-group__items filter-group__items--indent">
				<?php foreach ( $sizes['terms'] as $term ) : ?>
					<?php
					$on   = in_array( $term->slug, $sizes['chosen'], true );
					$next = $on ? array_values( array_diff( $sizes['chosen'], array( $term->slug ) ) ) : array_merge( $sizes['chosen'], array( $term->slug ) );
					?>
					<li><a class="filter-check" href="<?php echo esc_url( miroir_listing_url( array( 'filter_size' => $next ) ) ); ?>" rel="nofollow" role="checkbox" aria-checked="<?php echo $on ? 'true' : 'false'; ?>" data-listing-link><?php echo esc_html( mb_strtolower( $term->name ) ); ?></a></li>
				<?php endforeach; ?>
			</ul>
			<?php if ( $sizes['chosen'] ) : ?>
				<a class="filter-group__clear" href="<?php echo esc_url( miroir_listing_url( array( 'filter_size' => null ) ) ); ?>" rel="nofollow" data-listing-link><?php echo esc_html( miroir_text( 'size_clear' ) ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</aside>
