<?php
/**
 * Filter & sort panel (screen 33) for the shop and category listings, a modal
 * dialog like search and currency, shown below 600px. There is no apply button:
 * theme.js reloads the listing as soon as a control changes. Without JavaScript
 * the form still submits (orderby and filter_size[], joined for WooCommerce in
 * inc/states.php) through a button inside <noscript>.
 *
 * @var array $args { open: bool }
 */

defined( 'ABSPATH' ) || exit;

$base   = miroir_listing_url( array( 'orderby' => null, 'filter_size' => null ) );
$base   = strtok( $base, '?' );
$sizes  = miroir_listing_sizes();
$sort   = miroir_listing_sort();
$groups = $sizes['terms'] ? array( array( 'label' => miroir_text( 'filter_size_panel' ), 'terms' => $sizes['terms'], 'chosen' => $sizes['chosen'] ) ) : array();
?>
<dialog class="flow panel panel--modal" id="filter-panel" data-panel="filters" aria-labelledby="filter-panel-title"<?php echo empty( $args['open'] ) ? '' : ' open'; ?>>
	<form class="flow__col" method="get" action="<?php echo esc_url( $base ); ?>" data-filter-form>
		<div class="panel__header">
			<h2 class="flow__title" id="filter-panel-title"><?php echo esc_html( miroir_text( 'filters_title' ) ); ?></h2>
			<button class="panel__close" type="button" data-panel-close aria-label="<?php echo esc_attr( miroir_text( 'filters_close' ) ); ?>"><?php echo miroir_icon( 'x', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></button>
		</div>
		<?php miroir_field( array( 'name' => 'orderby', 'id' => 'panel-orderby', 'type' => 'select', 'label' => miroir_text( 'sort_label' ), 'options' => miroir_listing_sorts(), 'value' => $sort, 'class' => 'field--quiet' ) ); ?>
		<?php foreach ( $groups as $group ) : ?>
			<p class="flow__label"><?php echo esc_html( $group['label'] ); ?></p>
			<?php foreach ( $group['terms'] as $term ) : ?>
				<label class="check panel-check">
					<input type="checkbox" name="filter_size[]" value="<?php echo esc_attr( $term->slug ); ?>"<?php checked( in_array( $term->slug, $group['chosen'], true ) ); ?>>
					<?php echo esc_html( ucfirst( $term->name ) ); ?>
				</label>
			<?php endforeach; ?>
			<input type="hidden" name="query_type_size" value="or">
		<?php endforeach; ?>
		<noscript><button class="btn btn--primary btn--block" type="submit"><?php esc_html_e( 'Apply', 'miroir' ); ?></button></noscript>
		<a class="btn btn--block" href="<?php echo esc_url( $base ); ?>" rel="nofollow" data-listing-link data-filter-clear<?php echo ( $sort || $sizes['chosen'] ) ? '' : ' hidden'; ?>><?php echo esc_html( miroir_text( 'clear_all' ) ); ?></a>
	</form>
</dialog>
