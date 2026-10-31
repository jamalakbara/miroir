<?php
/**
 * Product page states drawn as a single column: sold out (38), choose a size
 * (44) and added to bag (39). Everything else uses the PDP layout (03).
 *
 * @var array $args { product: WC_Product, state: 'sold-out'|'choose-size'|'added' }
 */

defined( 'ABSPATH' ) || exit;

$product = $args['product'];
$state   = $args['state'];
$wa_url  = apply_filters( 'miroir_wa_product_url', '', $product );
$price   = wc_price( wc_get_price_to_display( $product, $product->is_type( 'variable' ) ? array( 'price' => $product->get_variation_price( 'min', true ) ) : array() ) );

/** Size label for a product: default/first variation value, or the simple product's size attribute. */
$size = (string) $product->get_attribute( 'size' );
if ( $product->is_type( 'variable' ) ) {
	$options = $product->get_variation_attributes();
	$first   = $options ? reset( $options ) : array();
	$default = $product->get_default_attributes();
	$size    = $default ? (string) reset( $default ) : (string) ( $first ? reset( $first ) : '' );
}
$size = $size ? $size : __( 'One size', 'miroir' );

$titles = array(
	'sold-out'    => __( 'Sold out', 'miroir' ),
	'choose-size' => __( 'Choose a size', 'miroir' ),
	'added'       => __( 'Added to bag', 'miroir' ),
);
?>
<div class="flow product-state">
	<div class="flow__col">
		<h1 class="flow__title"><?php echo esc_html( $titles[ $state ] ); ?></h1>

		<?php
		if ( 'added' === $state ) :
			$key  = sanitize_key( wp_unslash( $_GET['added'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$item = WC()->cart->get_cart_item( $key );
			$line = array( $product->get_name() );
			if ( $item && function_exists( 'miroir_cart_item_variant' ) && miroir_cart_item_variant( $item ) ) {
				$line[] = miroir_cart_item_variant( $item );
			}
			/* translators: %d: quantity. */
			$line[] = sprintf( __( 'Quantity %d', 'miroir' ), $item ? (int) $item['quantity'] : 1 );
			?>
			<div class="notice notice--flat" role="status">
				<p class="notice__title"><?php esc_html_e( 'Added to your bag', 'miroir' ); ?></p>
				<p class="notice__text"><?php echo esc_html( implode( ' · ', $line ) ); ?></p>
			</div>
			<?php miroir_ph( 'product-state__image product-state__image--small', $product->get_name() ); ?>
			<p class="flow__intro">
				<?php
				/* translators: 1: number of items, 2: bag subtotal. */
				echo wp_kses_post( sprintf( __( 'Bag (%1$d) · Subtotal %2$s', 'miroir' ), miroir_bag_count(), WC()->cart->get_cart_subtotal() ) );
				?>
			</p>
			<p class="flow__intro"><?php esc_html_e( 'Shipping and the final total are confirmed in WhatsApp.', 'miroir' ); ?></p>
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'View bag', 'miroir' ); ?></a>
			<a class="btn btn--block" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Continue shopping', 'miroir' ); ?></a>
		<?php else : ?>
			<?php miroir_ph( 'product-state__image', $product->get_name() ); ?>
			<h2 class="product-state__name"><?php echo esc_html( $product->get_name() ); ?></h2>
			<p class="flow__intro"><?php echo wp_kses_post( $price ); ?></p>

			<?php if ( 'sold-out' === $state ) : ?>
				<?php
				miroir_field(
					array(
						'name'    => 'size',
						'id'      => 'state-size',
						'type'    => 'select',
						'label'   => __( 'Size *', 'miroir' ),
						/* translators: %s: size. */
						'options' => array( '' => sprintf( __( '%s · unavailable', 'miroir' ), $size ) ),
						'class'   => 'field--quiet field--disabled',
					)
				);
				?>
				<div class="notice notice--flat" role="status">
					<p class="notice__title"><?php esc_html_e( 'This size is sold out', 'miroir' ); ?></p>
					<p class="notice__text"><?php esc_html_e( 'Try another size or ask our team about availability.', 'miroir' ); ?></p>
				</div>
				<p class="disabled-bar" aria-disabled="true"><?php esc_html_e( 'Out of stock', 'miroir' ); ?></p>
				<?php if ( $wa_url ) : ?>
					<a class="btn btn--block" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Ask about availability', 'miroir' ); ?></a>
				<?php endif; ?>
			<?php else : ?>
				<?php wp_enqueue_script( 'wc-add-to-cart-variation' ); ?>
				<form class="flow__form variations_form cart state-buy" action="<?php echo esc_url( $product->get_permalink() ); ?>" method="post"
					data-product_id="<?php echo absint( $product->get_id() ); ?>"
					data-product_variations="<?php echo wc_esc_json( wp_json_encode( $product->get_available_variations() ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?>">
					<?php foreach ( $product->get_variation_attributes() as $attribute => $options ) : ?>
						<div class="field field--quiet variations">
							<label class="field__label" for="<?php echo esc_attr( sanitize_title( $attribute ) ); ?>"><?php echo esc_html( wc_attribute_label( $attribute, $product ) . ' *' ); ?></label>
							<?php
							wc_dropdown_variation_attribute_options(
								array(
									'options'          => $options,
									'attribute'        => $attribute,
									'product'          => $product,
									'selected'         => '',
									'class'            => 'field__input field__input--select',
									'show_option_none' => __( 'Select a size', 'miroir' ),
								)
							);
							?>
						</div>
					<?php endforeach; ?>
					<div class="notice notice--flat" role="status">
						<p class="notice__title"><?php esc_html_e( 'Choose a size to continue', 'miroir' ); ?></p>
						<p class="notice__text"><?php esc_html_e( 'Please select a size before adding this product to your bag.', 'miroir' ); ?></p>
					</div>
					<div class="single_variation_wrap">
						<div class="woocommerce-variation single_variation screen-reader-text" aria-live="polite"></div>
						<div class="woocommerce-variation-add-to-cart variations_button">
							<input type="hidden" name="quantity" value="1">
							<button type="submit" class="single_add_to_cart_button disabled-bar disabled"><?php esc_html_e( 'Add to bag', 'miroir' ); ?></button>
							<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>">
							<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>">
							<input type="hidden" name="variation_id" class="variation_id" value="0">
						</div>
					</div>
				</form>
				<a class="btn btn--block" href="<?php echo esc_url( miroir_page_url( 'size-guide' ) ); ?>"><?php esc_html_e( 'View size guide', 'miroir' ); ?></a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
