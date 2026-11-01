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

<div class="pdp__gallery">
	<div class="pdp__lead">
		<?php miroir_image( $lead, 'woocommerce_single', 'pdp__view', $product->get_name() ); ?>
		<button class="pdp__zoom" type="button" data-zoom aria-label="<?php esc_attr_e( 'Enlarge images', 'miroir' ); ?>"><?php echo miroir_icon( 'maximize-2', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></button>
	</div>
	<div class="pdp__more">
		<?php foreach ( $views as $view ) : ?>
			<?php miroir_image( $view, 'woocommerce_single', 'pdp__view', $view ? $product->get_name() : '' ); ?>
		<?php endforeach; ?>
	</div>
</div>
