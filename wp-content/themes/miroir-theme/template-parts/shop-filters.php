<?php
/**
 * Shop sidebar (screen 02 "Filter Sidebar"): two link groups from menus and the
 * colour filter from the global Colour attribute (pa_color).
 */

defined( 'ABSPATH' ) || exit;

$groups = array(
	miroir_filter_group( 'plp-categories', miroir_text( 'shop_title' ), array( 'accessories', 'bottoms', 'dress', 'outerwear', 'tops' ) ),
	miroir_filter_group(
		'plp-featured',
		miroir_text( 'filter_featured' ),
		array(
			'best-seller'     => __( 'best sellers', 'miroir' ),
			'new-collection'  => __( 'new arrivals', 'miroir' ),
			'at-the-studio'   => __( 'studio collection', 'miroir' ),
			'miroir-x-naluri' => __( 'collaboration', 'miroir' ),
		)
	),
);
$colours = miroir_colour_filters();
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

	<?php if ( $colours ) : ?>
		<div class="filter-group">
			<p class="filter-group__title"><?php echo esc_html( miroir_text( 'filter_colour' ) ); ?></p>
			<ul class="filter-group__items">
				<?php foreach ( $colours as $colour ) : ?>
					<li>
						<a class="check" href="<?php echo esc_url( $colour['url'] ); ?>" role="checkbox" aria-checked="<?php echo $colour['checked'] ? 'true' : 'false'; ?>" rel="nofollow">
							<span class="check__box" aria-hidden="true"></span><?php echo esc_html( $colour['label'] ); ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
</aside>
