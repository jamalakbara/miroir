<?php
/**
 * Product card (pen component "Product Card", xK7Fl): product image (grey
 * placeholder without one), mono name, price row. Images share one responsive
 * aspect ratio (Customize > Products, default 3:4).
 *
 * @var array $args { product: WC_Product }
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'] ?? wc_get_product();
if ( ! $product ) {
	return;
}
?>
<article class="card">
	<a class="card__link" href="<?php echo esc_url( $product->get_permalink() ); ?>">
		<?php
		$gallery = miroir_mod( 'product_photos' ) && miroir_mod( 'card_hover_image' ) ? $product->get_gallery_image_ids() : array();
		miroir_image( miroir_product_image_id( $product ), 'woocommerce_thumbnail', 'card__media', $product->get_name(), 28, $gallery ? (int) $gallery[0] : 0 );
		?>
		<div class="card__meta">
			<h3 class="card__name"><?php echo esc_html( $product->get_name() ); ?></h3>
			<p class="card__price"><?php echo miroir_card_price( $product ); // phpcs:ignore WordPress.Security.EscapeOutput -- built from wc_price(). ?></p>
		</div>
	</a>
</article>
