<?php
/**
 * PDP buy box (screen 03 "Buy Box"): size select, Add to bag, Order via
 * WhatsApp and its hint. Variable products keep WooCommerce's variation form
 * contract (classes, data-product_variations, hidden ids) so wc-add-to-cart-variation works.
 *
 * @var array $args { product: WC_Product }
 */

defined( 'ABSPATH' ) || exit;

$product  = $args['product'];
$wa_url   = apply_filters( 'miroir_wa_product_url', '', $product );
$chevron  = miroir_icon( 'chevron-down', 14, 'field__icon' );
$disabled = ! $product->is_purchasable() || ! $product->is_in_stock();

if ( $product->is_type( 'variable' ) ) :
	wp_enqueue_script( 'wc-add-to-cart-variation' );
	$variations = $product->get_available_variations();
	?>
	<form class="variations_form cart buy-form" action="<?php echo esc_url( $product->get_permalink() ); ?>" method="post" enctype="multipart/form-data"
		data-product_id="<?php echo absint( $product->get_id() ); ?>"
		data-product_variations="<?php echo wc_esc_json( wp_json_encode( $variations ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">
		<?php foreach ( $product->get_variation_attributes() as $attribute => $options ) : ?>
			<div class="field buy-form__size variations">
				<label class="screen-reader-text" for="<?php echo esc_attr( sanitize_title( $attribute ) ); ?>"><?php echo esc_html( wc_attribute_label( $attribute, $product ) ); ?></label>
				<div class="field__control">
					<?php
					wc_dropdown_variation_attribute_options(
						array(
							'options'   => $options,
							'attribute' => $attribute,
							'product'   => $product,
							'class'     => 'field__input field__input--select',
						)
					);
					echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG.
					?>
				</div>
			</div>
		<?php endforeach; ?>
		<div class="single_variation_wrap">
			<div class="woocommerce-variation single_variation screen-reader-text" aria-live="polite"></div>
			<div class="woocommerce-variation-add-to-cart variations_button">
				<input type="hidden" name="quantity" value="1">
				<button type="submit" class="single_add_to_cart_button btn btn--block"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
				<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>">
				<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>">
				<input type="hidden" name="variation_id" class="variation_id" value="0">
			</div>
		</div>
	</form>
<?php else : ?>
	<form class="cart buy-form" action="<?php echo esc_url( $product->get_permalink() ); ?>" method="post" enctype="multipart/form-data">
		<div class="field buy-form__size">
			<label class="screen-reader-text" for="size-static"><?php esc_html_e( 'Size', 'miroir' ); ?></label>
			<div class="field__control">
				<select class="field__input field__input--select" id="size-static" disabled>
					<option><?php echo esc_html( $product->get_attribute( 'size' ) ? $product->get_attribute( 'size' ) : __( 'One size', 'miroir' ) ); ?></option>
				</select>
				<?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?>
			</div>
		</div>
		<input type="hidden" name="quantity" value="1">
		<button type="submit" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>" class="single_add_to_cart_button btn btn--block"<?php disabled( $disabled ); ?>><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
	</form>
<?php endif; ?>

<?php if ( $wa_url ) : ?>
	<a class="btn btn--whatsapp btn--block" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener" data-wa-product="<?php echo absint( $product->get_id() ); ?>">
		<?php echo miroir_icon( 'message-circle', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?>
		<?php esc_html_e( 'Order via WhatsApp', 'miroir' ); ?>
	</a>
	<p class="buy-hint"><?php esc_html_e( 'Opens WhatsApp with this product, size and price filled in.', 'miroir' ); ?></p>
<?php endif; ?>
