<?php
/**
 * Order details (screen 21).
 *
 * @var int $order_id
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
if ( ! $order ) {
	return;
}

$waiting  = $order->has_status( array( 'pending', 'on-hold' ) );
$currency = array( 'currency' => $order->get_currency() );
$lines    = function_exists( 'miroir_wa_order_lines' ) ? miroir_wa_order_lines( $order ) : array();
$country  = WC()->countries->get_countries()[ $order->get_shipping_country() ] ?? $order->get_shipping_country();
$street   = trim( $order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2() );
$region   = implode( ', ', array_filter( array( $order->get_shipping_city(), $order->get_shipping_state(), $order->get_shipping_postcode(), $country ) ) );
$wa_url   = function_exists( 'miroir_wa_order_url' ) && $order->get_meta( '_miroir_wa_message' ) ? miroir_wa_order_url( $order ) : apply_filters( 'miroir_whatsapp_url', '' );
?>
<?php /* translators: %s: order number. */ ?>
<h2 class="flow__heading"><?php echo esc_html( sprintf( __( 'Order #%s', 'miroir' ), $order->get_order_number() ) ); ?></h2>
<p class="flow__intro"><?php echo esc_html( wc_format_datetime( $order->get_date_created(), 'j F Y' ) . ' · ' . wc_get_order_status_name( $order->get_status() ) ); ?></p>
<?php if ( $waiting ) : ?>
	<p class="flow__intro"><?php esc_html_e( 'Our team will confirm payment and shipping in WhatsApp.', 'miroir' ); ?></p>
<?php endif; ?>

<section class="flow__section hair-t">
	<h3 class="flow__label"><?php esc_html_e( 'Items', 'miroir' ); ?></h3>
	<div class="flow__copy">
		<?php foreach ( $lines as $line ) : ?>
			<p><?php echo esc_html( $line['name'] . ( $line['variant'] ? ' · ' . $line['variant'] : '' ) . ' × ' . $line['qty'] ); ?></p>
			<p><?php echo wp_kses_post( $line['total_html'] ); ?></p>
		<?php endforeach; ?>
	</div>
</section>

<div class="totals-row"><span><?php esc_html_e( 'Subtotal', 'miroir' ); ?></span><span><?php echo wp_kses_post( wc_price( $order->get_subtotal(), $currency ) ); ?></span></div>
<div class="totals-row"><span><?php esc_html_e( 'Shipping', 'miroir' ); ?></span><span><?php echo $waiting ? esc_html__( 'Awaiting confirmation', 'miroir' ) : wp_kses_post( wc_price( $order->get_shipping_total(), $currency ) ); ?></span></div>
<div class="totals-row"><span><?php esc_html_e( 'Payment', 'miroir' ); ?></span><span><?php echo esc_html( $order->is_paid() ? __( 'Bank transfer · paid', 'miroir' ) : __( 'Bank transfer · pending', 'miroir' ) ); ?></span></div>

<section class="flow__section hair-t">
	<h3 class="flow__label"><?php esc_html_e( 'Delivery details', 'miroir' ); ?></h3>
	<div class="flow__copy">
		<p><?php echo esc_html( trim( $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name() ) ); ?></p>
		<?php if ( $street ) : ?>
			<p><?php echo esc_html( $street ); ?></p>
		<?php endif; ?>
		<p><?php echo esc_html( $region ); ?></p>
	</div>
</section>

<?php if ( $wa_url ) : ?>
	<a class="btn btn--primary btn--block" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Contact us on WhatsApp', 'miroir' ); ?></a>
<?php endif; ?>
<a class="btn btn--block" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'Back to orders', 'miroir' ); ?></a>
