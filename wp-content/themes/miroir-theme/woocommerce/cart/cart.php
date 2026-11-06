<?php
/**
 * Bag (screen 04). Replaces WooCommerce's cart table and totals: coupon
 * field, no shipping calculator; shipping is confirmed in WhatsApp.
 *
 * Keeps the cart form contract (cart[key][qty], nonce, update_cart) so
 * quantity changes go through WooCommerce; the stepper submits on change.
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_cart' );

$stock_problem = miroir_cart_stock_problem();
if ( $stock_problem ) {
	get_template_part( 'template-parts/cart-stock', null, $stock_problem );
	return;
}
?>
<div class="wrap cart-wrap">
	<div class="wrap__col cart">
		<h1 class="page-title"><?php echo esc_html( miroir_text( 'bag_title' ) ); ?></h1>
		<?php woocommerce_output_all_notices(); ?>

		<form class="woocommerce-cart-form cart-table" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post" data-cart-form>
			<div class="woocommerce-cart-form__contents cart-table__body">
			<div class="cart-table__head hair-b" aria-hidden="true">
				<span class="cart-table__cell cart-table__cell--product"><?php echo esc_html( miroir_text( 'col_product' ) ); ?></span>
				<span class="cart-table__cell"><?php echo esc_html( miroir_text( 'col_price' ) ); ?></span>
				<span class="cart-table__cell"><?php echo esc_html( miroir_text( 'col_quantity' ) ); ?></span>
				<span class="cart-table__cell cart-table__cell--end"><?php echo esc_html( miroir_text( 'col_subtotal' ) ); ?></span>
			</div>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
				$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 ) {
					continue;
				}
				$name    = $_product->is_type( 'variation' ) ? wc_get_product( $_product->get_parent_id() )->get_name() : $_product->get_name();
				$link    = $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '';
				$max_qty = $_product->get_max_purchase_quantity();
				?>
				<div class="cart-item cart_item hair-b">
					<div class="cart-table__cell cart-table__cell--product cart-item__product">
						<?php miroir_image( miroir_product_image_id( $_product ), 'woocommerce_thumbnail', 'cart-item__thumb', $name, 18 ); ?>
						<div class="cart-item__meta">
							<p class="cart-item__name"><?php echo $link ? '<a href="' . esc_url( $link ) . '">' . esc_html( $name ) . '</a>' : esc_html( $name ); ?></p>
							<?php $variant = miroir_cart_item_variant( $cart_item ); ?>
							<?php if ( $variant ) : ?>
								<p class="cart-item__variant"><?php echo esc_html( $variant ); ?></p>
							<?php endif; ?>
							<a class="cart-item__remove" href="<?php echo esc_url( wc_get_cart_remove_url( $cart_item_key ) ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: product name. */ __( 'Remove %s from bag', 'miroir' ), $name ) ); ?>"><?php echo esc_html( miroir_text( 'remove' ) ); ?></a>
						</div>
					</div>
					<div class="cart-table__cell cart-item__price"><?php echo WC()->cart->get_product_price( $_product ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
					<div class="cart-table__cell">
						<?php if ( $_product->is_sold_individually() ) : ?>
							<input type="hidden" name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]" value="1">
							<span class="qty qty--static"><span class="qty__input">1</span></span>
						<?php else : ?>
							<div class="qty" data-qty>
								<button class="qty__btn" type="button" data-qty-step="-1" aria-label="<?php esc_attr_e( 'Decrease quantity', 'miroir' ); ?>">−</button>
								<input class="qty__input" type="number" inputmode="numeric" name="cart[<?php echo esc_attr( $cart_item_key ); ?>][qty]" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" min="0"<?php echo $max_qty > 0 ? ' max="' . esc_attr( $max_qty ) . '"' : ''; ?> aria-label="<?php echo esc_attr( sprintf( /* translators: %s: product name. */ __( '%s quantity', 'miroir' ), $name ) ); ?>">
								<button class="qty__btn" type="button" data-qty-step="1" aria-label="<?php esc_attr_e( 'Increase quantity', 'miroir' ); ?>">+</button>
							</div>
						<?php endif; ?>
					</div>
					<div class="cart-table__cell cart-table__cell--end cart-item__subtotal"><?php echo WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				</div>
			<?php endforeach; ?>

			</div>
			<?php // WooCommerce's cart.js turns a submit via this button into an AJAX quantity update. ?>
			<button class="screen-reader-text" type="submit" name="update_cart" value="1"><?php esc_html_e( 'Update bag', 'miroir' ); ?></button>
			<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
		</form>

		<div class="cart-footer">
			<div class="notice cart-footer__note">
				<p class="notice__title"><?php echo esc_html( miroir_text( 'bag_note_title' ) ); ?></p>
				<p class="notice__text"><?php echo esc_html( miroir_text( 'bag_note' ) ); ?></p>
			</div>

			<div class="cart-totals cart_totals">
				<p class="cart-totals__title"><?php echo esc_html( miroir_text( 'bag_totals' ) ); ?></p>
				<div class="totals-row"><span><?php echo esc_html( miroir_text( 'subtotal' ) ); ?></span><span><?php wc_cart_totals_subtotal_html(); ?></span></div>
				<?php foreach ( miroir_cart_discounts() as $discount ) : ?>
					<div class="totals-row">
						<span><?php echo esc_html( $discount['label'] ); ?> <a class="coupon-remove" href="<?php echo esc_url( add_query_arg( 'remove_coupon', rawurlencode( $discount['code'] ), wc_get_cart_url() ) ); ?>"><?php echo esc_html( miroir_text( 'coupon_remove' ) ); ?></a></span>
						<span><?php echo wp_kses_post( $discount['amount_html'] ); ?></span>
					</div>
				<?php endforeach; ?>
				<div class="totals-row"><span><?php echo esc_html( miroir_text( 'shipping' ) ); ?></span><span class="totals-row__muted"><?php echo esc_html( miroir_text( 'shipping_note' ) ); ?></span></div>
				<div class="totals-row totals-row--total"><span><?php echo esc_html( miroir_text( 'estimated_total' ) ); ?></span><span><?php echo wp_kses_post( miroir_cart_total_html() ); ?></span></div>
				<?php if ( wc_coupons_enabled() ) : ?>
					<form class="coupon" method="post" action="<?php echo esc_url( wc_get_cart_url() ); ?>">
						<label class="field__label" for="coupon_code"><?php echo esc_html( miroir_text( 'coupon_label' ) ); ?></label>
						<div class="coupon__row">
							<input class="field__input" id="coupon_code" name="coupon_code" type="text" autocomplete="off" required>
							<button class="btn" type="submit" name="apply_coupon" value="1"><?php echo esc_html( miroir_text( 'coupon_apply' ) ); ?></button>
						</div>
					</form>
				<?php endif; ?>
				<a class="btn btn--whatsapp btn--block" href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php echo miroir_icon( 'whatsapp', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?><?php echo esc_html( miroir_text( 'checkout_button' ) ); ?></a>
				<a class="btn-link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php echo esc_html( miroir_text( 'continue_shopping' ) ); ?></a>
			</div>
		</div>
	</div>
</div>
<?php
do_action( 'woocommerce_after_cart' );
