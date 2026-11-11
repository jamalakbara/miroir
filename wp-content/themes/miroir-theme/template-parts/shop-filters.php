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
</aside>
