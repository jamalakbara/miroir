<?php
/**
 * Product card (pen component "Product Card", xK7Fl): placeholder image, mono
 * name, price row. Images share a responsive 3:4 aspect ratio.
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
		<?php miroir_ph( 'card__media', $product->get_name() ); ?>
		<div class="card__meta">
			<h3 class="card__name"><?php echo esc_html( $product->get_name() ); ?></h3>
			<p class="card__price"><?php echo miroir_card_price( $product ); // phpcs:ignore WordPress.Security.EscapeOutput -- built from wc_price(). ?></p>
		</div>
	</a>
</article>
