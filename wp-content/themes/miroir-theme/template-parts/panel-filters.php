<?php
/**
 * Filter & sort panel (screen 33) for the shop and category listings. A GET
 * form back to the listing: orderby, colour and size checkboxes
 * (filter_color[] / filter_size[], joined for WooCommerce in inc/states.php).
 *
 * @var array $args { open: bool }
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$base   = preg_replace( '#/page/\d+/?#', '/', strtok( (string) wp_unslash( $_SERVER['REQUEST_URI'] ?? '' ), '?' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- path only, escaped on output.
$orders = array(
	'date'       => miroir_text( 'sort_date' ),
	'popularity' => miroir_text( 'sort_popularity' ),
	'price'      => miroir_text( 'sort_price' ),
	'price-desc' => miroir_text( 'sort_price_desc' ),
);
// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only listing filters.
$current = sanitize_key( wp_unslash( $_GET['orderby'] ?? 'date' ) );

$groups = array();
foreach ( array( 'pa_color' => miroir_text( 'filter_colour_panel' ), 'pa_size' => miroir_text( 'filter_size_panel' ) ) as $taxonomy => $label ) {
	if ( ! taxonomy_exists( $taxonomy ) ) {
		continue;
	}
	$terms = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => true,
			'orderby'    => 'menu_order',
		)
	);
	if ( is_wp_error( $terms ) || ! $terms ) {
		continue;
	}
	$param  = 'filter_' . wc_attribute_taxonomy_slug( $taxonomy );
	$chosen = array_filter( explode( ',', wc_clean( wp_unslash( $_GET[ $param ] ?? '' ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$groups[] = compact( 'taxonomy', 'label', 'terms', 'param', 'chosen' );
}
?>
<div class="flow panel" id="filter-panel" data-panel="filters"<?php echo empty( $args['open'] ) ? ' hidden' : ''; ?>>
	<form class="flow__col" method="get" action="<?php echo esc_url( home_url( $base ) ); ?>">
		<h2 class="flow__title"><?php echo esc_html( miroir_text( 'filters_title' ) ); ?></h2>
		<button class="btn btn--block" type="button" data-panel-close><?php echo esc_html( miroir_text( 'filters_close' ) ); ?></button>
		<?php miroir_field( array( 'name' => 'orderby', 'id' => 'panel-orderby', 'type' => 'select', 'label' => miroir_text( 'sort_label' ), 'options' => $orders, 'value' => $current, 'class' => 'field--quiet' ) ); ?>
		<?php foreach ( $groups as $group ) : ?>
			<p class="flow__label"><?php echo esc_html( $group['label'] ); ?></p>
			<?php foreach ( $group['terms'] as $term ) : ?>
				<label class="check panel-check">
					<input type="checkbox" name="<?php echo esc_attr( $group['param'] ); ?>[]" value="<?php echo esc_attr( $term->slug ); ?>"<?php checked( in_array( $term->slug, $group['chosen'], true ) ); ?>>
					<?php echo esc_html( ucfirst( $term->name ) ); ?>
				</label>
			<?php endforeach; ?>
			<input type="hidden" name="query_type_<?php echo esc_attr( wc_attribute_taxonomy_slug( $group['taxonomy'] ) ); ?>" value="or">
		<?php endforeach; ?>
		<button class="btn btn--primary btn--block" type="submit">
			<?php
			/* translators: %d: number of products in the current listing. */
			echo esc_html( sprintf( _n( 'Show %d product', 'Show %d products', (int) $wp_query->found_posts, 'miroir' ), (int) $wp_query->found_posts ) );
			?>
		</button>
		<a class="btn btn--block" href="<?php echo esc_url( home_url( $base ) ); ?>"><?php echo esc_html( miroir_text( 'clear_all' ) ); ?></a>
	</form>
</div>
