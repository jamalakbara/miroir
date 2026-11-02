<?php
/**
 * Product images: lead image with the zoom button, then the other views. A
 * product without photos keeps the four placeholder views of the lofi.
 */

defined( 'ABSPATH' ) || exit;

$product = miroir_block_product();
if ( ! $product ) {
	return;
}
$lead    = miroir_product_image_id( $product );
$gallery = miroir_mod( 'product_photos' ) ? array_map( 'intval', $product->get_gallery_image_ids() ) : array();
$views   = $lead ? $gallery : array_pad( $gallery, 3, 0 );
?>
<div class="pdp__spacer" aria-hidden="true"></div>

<div class="pdp__gallery" data-pdp-gallery>
	<div class="pdp__track" data-pdp-track tabindex="0" role="region" aria-label="<?php esc_attr_e( 'Product photos', 'miroir' ); ?>">
	<div class="pdp__lead" data-pdp-slide>
		<?php miroir_image( $lead, 'woocommerce_single', 'pdp__view', $product->get_name() ); ?>
		<button class="pdp__zoom" type="button" data-zoom aria-label="<?php esc_attr_e( 'Enlarge images', 'miroir' ); ?>"><?php echo miroir_icon( 'maximize-2', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></button>
	</div>
	<div class="pdp__more">
		<?php foreach ( $views as $view ) : ?>
			<div data-pdp-slide><?php miroir_image( $view, 'woocommerce_single', 'pdp__view', $view ? $product->get_name() : '' ); ?></div>
		<?php endforeach; ?>
	</div>
	</div>
	<div class="pdp__navigation"<?php echo count( $views ) ? '' : ' hidden'; ?>>
		<span data-pdp-counter aria-live="polite" aria-atomic="true">1 / <?php echo count( $views ) + 1; ?></span>
		<div class="pdp__arrows">
			<button type="button" data-pdp-prev aria-label="<?php esc_attr_e( 'Previous photo', 'miroir' ); ?>" disabled><svg width="24" height="12" viewBox="0 0 24 12" fill="none" stroke="currentColor" aria-hidden="true"><path d="M24 6H1m5-5L1 6l5 5"/></svg></button>
			<button type="button" data-pdp-next aria-label="<?php esc_attr_e( 'Next photo', 'miroir' ); ?>"><svg width="24" height="12" viewBox="0 0 24 12" fill="none" stroke="currentColor" aria-hidden="true"><path d="M0 6h23m-5-5l5 5-5 5"/></svg></button>
		</div>
	</div>
</div>
