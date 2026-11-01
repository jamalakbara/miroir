<?php
/**
 * Home product strip: section link, carousel arrows and an overflow row of cards.
 *
 * @var array $args { label, url, products: WC_Product[], columns: 5|6 }
 */
defined( 'ABSPATH' ) || exit;

if ( empty( $args['products'] ) ) {
	return;
}
$strip_id = wp_unique_id( 'home-products-' );
$columns  = 6 === (int) $args['columns'] ? 6 : 5;
?>
<section class="home-strip home-strip--<?php echo (int) $columns; ?>" data-product-carousel aria-label="<?php echo esc_attr( $args['label'] ); ?>">
	<div class="home-strip__header">
		<?php miroir_section_link( $args['label'], $args['url'] ); ?>
		<div class="home-strip__controls" data-carousel-controls hidden>
			<button type="button" class="home-strip__button" data-carousel-prev aria-controls="<?php echo esc_attr( $strip_id ); ?>" aria-label="<?php esc_attr_e( 'Previous products', 'miroir' ); ?>"><svg width="24" height="12" viewBox="0 0 24 12" fill="none" stroke="currentColor" aria-hidden="true"><path d="M24 6H1m5-5L1 6l5 5"/></svg></button>
			<button type="button" class="home-strip__button" data-carousel-next aria-controls="<?php echo esc_attr( $strip_id ); ?>" aria-label="<?php esc_attr_e( 'Next products', 'miroir' ); ?>"><svg width="24" height="12" viewBox="0 0 24 12" fill="none" stroke="currentColor" aria-hidden="true"><path d="M0 6h23m-5-5l5 5-5 5"/></svg></button>
		</div>
	</div>
	<div class="grid home-strip__grid" id="<?php echo esc_attr( $strip_id ); ?>" data-carousel-track tabindex="0" role="group" aria-label="<?php echo esc_attr( $args['label'] ); ?>"><?php miroir_cards( $args['products'] ); ?></div>
</section>
