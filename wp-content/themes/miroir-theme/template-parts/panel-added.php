<?php
/** Added-item confirmation; also usable after a normal, non-JavaScript POST. */
defined( 'ABSPATH' ) || exit;
$key  = sanitize_key( wp_unslash( $_GET['added'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
$item = $key ? WC()->cart->get_cart_item( $key ) : null;
$back = remove_query_arg( array( 'added', 'choose-size', 'quantity', 'add-to-cart' ) );
?>
<dialog class="panel panel--modal panel--bag" id="added-panel" data-panel="added" aria-labelledby="added-panel-title" data-title="<?php echo esc_attr( miroir_text( 'bag_title' ) ); ?>" data-updated="<?php echo esc_attr( miroir_text( 'bag_updated' ) ); ?>" data-error="<?php echo esc_attr( miroir_text( 'added_error' ) ); ?>" data-bag-url="<?php echo esc_url( wc_get_cart_url() ); ?>" data-bag-label="<?php echo esc_attr( miroir_text( 'view_bag' ) ); ?>"<?php echo $item ? ' open data-added-item' : ''; ?>>
		<div class="panel__header">
			<h2 class="flow__heading" id="added-panel-title" tabindex="-1"><?php echo esc_html( miroir_text( $item ? 'added_notice' : 'bag_title' ) ); ?></h2>
			<a class="panel__close" href="<?php echo esc_url( $back ); ?>" data-panel-close aria-label="<?php echo esc_attr( miroir_text( 'added_close' ) ); ?>"><?php echo miroir_icon( 'x', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
		</div>
		<form class="mini-bag__form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post" data-mini-bag-form>
		<div class="mini-bag__items">
		<?php foreach ( WC()->cart->get_cart() as $cart_key => $cart_item ) : ?>
			<?php
			$product = $cart_item['data'];
			if ( ! $product || ! $product->exists() || $cart_item['quantity'] <= 0 ) {
				continue;
			}
			$max_qty = $product->get_max_purchase_quantity();
			?>
		<div class="mini-bag__item" data-mini-bag-item="<?php echo esc_attr( $cart_key ); ?>">
			<?php miroir_image( miroir_product_image_id( $product ), 'woocommerce_thumbnail', 'mini-bag__image', $product->get_name(), 20 ); ?>
			<div class="mini-bag__details">
				<p class="flow__label"><?php echo esc_html( $product->get_name() ); ?></p>
				<?php $variant = miroir_cart_item_variant( $cart_item ); ?>
				<?php if ( $variant ) : ?><p class="flow__intro"><?php echo esc_html( $variant ); ?></p><?php endif; ?>
				<p class="flow__intro"><?php echo wp_kses_post( WC()->cart->get_product_price( $product ) ); ?></p>
				<?php if ( $product->is_sold_individually() ) : ?>
					<input type="hidden" name="cart[<?php echo esc_attr( $cart_key ); ?>][qty]" value="1">
					<p class="flow__intro"><?php echo esc_html( miroir_text( 'state_quantity', 1 ) ); ?></p>
				<?php else : ?>
					<div class="qty" data-qty>
						<button class="qty__btn" type="button" data-qty-step="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'miroir' ); ?>"<?php disabled( $cart_item['quantity'] <= 0 ); ?>>−</button>
						<input class="qty__input" type="number" inputmode="numeric" name="cart[<?php echo esc_attr( $cart_key ); ?>][qty]" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" min="0" step="1"<?php echo $max_qty > 0 ? ' max="' . esc_attr( $max_qty ) . '"' : ''; ?> aria-label="<?php echo esc_attr( sprintf( __( '%s quantity', 'miroir' ), $product->get_name() ) ); ?>">
						<button class="qty__btn" type="button" data-qty-step="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'miroir' ); ?>"<?php disabled( $max_qty > 0 && $cart_item['quantity'] >= $max_qty ); ?>>+</button>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<?php endforeach; ?>
		<?php if ( WC()->cart->is_empty() ) : ?><p class="flow__intro"><?php echo esc_html( miroir_text( 'bag_empty' ) ); ?></p><?php endif; ?>
		</div>
		<input type="hidden" name="update_cart" value="1">
		<input type="hidden" name="miroir_mini_bag" value="1">
		<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce', false ); ?>
		<noscript><button class="btn btn--block" type="submit"><?php esc_html_e( 'Update bag', 'miroir' ); ?></button></noscript>
		</form>
		<div class="mini-bag__feedback flow__intro" role="status" aria-live="polite"></div>
		<p class="mini-bag__subtotal flow__intro"><?php echo wp_kses_post( miroir_text( 'state_bag_line', miroir_bag_count(), WC()->cart->get_cart_subtotal() ) ); ?></p>
		<div class="mini-bag__actions">
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php echo esc_html( miroir_text( 'view_bag' ) ); ?></a>
			<a class="btn btn--block" href="<?php echo esc_url( $back ); ?>" data-panel-close><?php echo esc_html( miroir_text( 'continue_shopping' ) ); ?></a>
		</div>
</dialog>
