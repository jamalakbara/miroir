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
$size = $size ? $size : miroir_text( 'one_size' );

$titles = array(
	'sold-out'    => miroir_text( 'state_sold_out' ),
	'choose-size' => miroir_text( 'state_choose' ),
	'added'       => miroir_text( 'state_added' ),
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
			$line[] = miroir_text( 'state_quantity', $item ? (int) $item['quantity'] : 1 );
			?>
			<div class="notice notice--flat" role="status">
				<p class="notice__title"><?php echo esc_html( miroir_text( 'added_notice' ) ); ?></p>
				<p class="notice__text"><?php echo esc_html( implode( ' · ', $line ) ); ?></p>
			</div>
			<?php miroir_image( miroir_product_image_id( $item ? $item['data'] : $product ), 'woocommerce_single', 'product-state__image product-state__image--small', $product->get_name() ); ?>
			<p class="flow__intro">
				<?php
				/* translators: 1: number of items, 2: bag subtotal. */
				echo wp_kses_post( miroir_text( 'state_bag_line', miroir_bag_count(), WC()->cart->get_cart_subtotal() ) );
				?>
			</p>
			<p class="flow__intro"><?php echo esc_html( miroir_text( 'state_added_note' ) ); ?></p>
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php echo esc_html( miroir_text( 'view_bag' ) ); ?></a>
			<a class="btn btn--block" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php echo esc_html( miroir_text( 'continue_shopping' ) ); ?></a>
		<?php else : ?>
			<?php miroir_image( miroir_product_image_id( $product ), 'woocommerce_single', 'product-state__image', $product->get_name() ); ?>
			<h2 class="product-state__name"><?php echo esc_html( $product->get_name() ); ?></h2>
			<p class="flow__intro"><?php echo wp_kses_post( $price ); ?></p>

			<?php if ( 'sold-out' === $state ) : ?>
				<?php
				miroir_field(
					array(
						'name'    => 'size',
						'id'      => 'state-size',
						'type'    => 'select',
						'label'   => miroir_text( 'size_label' ),
						/* translators: %s: size. */
						'options' => array( '' => miroir_text( 'size_unavailable', $size ) ),
						'class'   => 'field--quiet field--disabled',
					)
				);
				?>
				<div class="notice notice--flat" role="status">
					<p class="notice__title"><?php echo esc_html( miroir_text( 'sold_out_notice' ) ); ?></p>
					<p class="notice__text"><?php echo esc_html( miroir_text( 'sold_out_text' ) ); ?></p>
				</div>
				<p class="disabled-bar" aria-disabled="true"><?php echo esc_html( miroir_text( 'out_of_stock' ) ); ?></p>
				<?php if ( $wa_url ) : ?>
					<a class="btn btn--block" href="<?php echo esc_attr( $wa_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( miroir_text( 'ask_availability' ) ); ?></a>
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
									'show_option_none' => miroir_text( 'select_size' ),
								)
							);
							?>
						</div>
					<?php endforeach; ?>
					<div class="notice notice--flat" role="status">
						<p class="notice__title"><?php echo esc_html( miroir_text( 'choose_notice' ) ); ?></p>
						<p class="notice__text"><?php echo esc_html( miroir_text( 'choose_text' ) ); ?></p>
					</div>
					<div class="single_variation_wrap">
						<div class="woocommerce-variation single_variation screen-reader-text" aria-live="polite"></div>
						<div class="woocommerce-variation-add-to-cart variations_button">
							<?php // Keeps the quantity chosen on the PDP stepper before the size was missing. ?>
							<input type="hidden" name="quantity" value="<?php echo esc_attr( max( 1, absint( $_GET['quantity'] ?? 1 ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only; WooCommerce validates on add. ?>">
							<button type="submit" class="single_add_to_cart_button disabled-bar disabled"><?php echo esc_html( miroir_text( 'add_to_bag' ) ); ?></button>
							<input type="hidden" name="add-to-cart" value="<?php echo absint( $product->get_id() ); ?>">
							<input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>">
							<input type="hidden" name="variation_id" class="variation_id" value="0">
						</div>
					</div>
				</form>
				<a class="btn btn--block" href="<?php echo esc_url( miroir_page_url( 'size-guide' ) ); ?>"><?php echo esc_html( miroir_text( 'size_guide_link' ) ); ?></a>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</div>
