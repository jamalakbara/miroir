<?php
/**
 * Bag — stock changed (screen 40): one bag line asks for more than is in stock.
 * Shown instead of the bag until the line is updated or removed.
 *
 * @var array $args From miroir_cart_stock_problem().
 */

defined( 'ABSPATH' ) || exit;

$product  = $args['product'];
$name     = $product->is_type( 'variation' ) ? wc_get_product( $product->get_parent_id() )->get_name() : $product->get_name();
$variant  = miroir_cart_item_variant( $args['item'] );
$avail    = (int) $args['available'];

// WooCommerce's own "not enough stock" error says the same thing as this screen.
wc_clear_notices();
?>
<div class="flow">
	<div class="flow__col">
		<h1 class="flow__title"><?php esc_html_e( 'Check your bag', 'miroir' ); ?></h1>
		<div class="notice notice--flat" role="alert">
			<p class="notice__title"><?php esc_html_e( 'Your bag needs an update', 'miroir' ); ?></p>
			<p class="notice__text">
				<?php
				echo esc_html(
					$avail
						/* translators: %d: items available. */
						? sprintf( _n( 'Only %d item is currently available. Reduce the quantity to continue.', 'Only %d items are currently available. Reduce the quantity to continue.', $avail, 'miroir' ), $avail )
						: __( 'This item is no longer available. Remove it to continue.', 'miroir' )
				);
				?>
			</p>
		</div>
		<p class="flow__label"><?php echo esc_html( $name . ( $variant ? ' · ' . $variant : '' ) ); ?></p>
		<?php /* translators: 1: requested quantity, 2: available quantity. */ ?>
		<p class="flow__intro"><?php echo esc_html( sprintf( __( 'Requested: %1$d · Available: %2$d', 'miroir' ), $args['requested'], $avail ) ); ?></p>
		<?php if ( $avail ) : ?>
			<form class="flow__form" method="post" action="<?php echo esc_url( wc_get_cart_url() ); ?>">
				<input type="hidden" name="cart[<?php echo esc_attr( $args['key'] ); ?>][qty]" value="<?php echo esc_attr( $avail ); ?>">
				<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				<?php /* translators: %d: new quantity. */ ?>
				<button class="btn btn--primary btn--block" type="submit" name="update_cart" value="1"><?php echo esc_html( sprintf( __( 'Update quantity to %d', 'miroir' ), $avail ) ); ?></button>
			</form>
		<?php endif; ?>
		<a class="btn btn--block" href="<?php echo esc_url( wc_get_cart_remove_url( $args['key'] ) ); ?>"><?php esc_html_e( 'Remove item', 'miroir' ); ?></a>
		<p class="flow__help"><?php esc_html_e( 'Continue to WhatsApp stays unavailable until your bag is updated.', 'miroir' ); ?></p>
	</div>
</div>
