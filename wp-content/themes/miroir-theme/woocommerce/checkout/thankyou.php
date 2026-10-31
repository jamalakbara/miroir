<?php
/**
 * Order sent (screen 07): WhatsApp opens with the order message; retry
 * button, order overview, bank details and the order summary.
 *
 * @var WC_Order|false $order
 */

defined( 'ABSPATH' ) || exit;

if ( ! $order ) {
	echo '<div class="wrap"><p class="wrap__col">' . esc_html__( 'We could not find this order.', 'miroir' ) . '</p></div>';
	return;
}

$wa_url   = function_exists( 'miroir_wa_order_url' ) ? miroir_wa_order_url( $order ) : '';
$lines    = function_exists( 'miroir_wa_order_lines' ) ? miroir_wa_order_lines( $order ) : array();
$total    = wc_price( $order->get_subtotal(), array( 'currency' => $order->get_currency() ) );
$country  = WC()->countries->get_countries()[ $order->get_billing_country() ] ?? $order->get_billing_country();
$accounts = array_filter( (array) get_option( 'woocommerce_bacs_accounts', array() ) );
$account  = $accounts ? reset( $accounts ) : array();
$qty      = array_sum( wp_list_pluck( $lines, 'qty' ) );
?>
<?php $fallback = isset( $_GET['wa-fallback'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display state only. ?>
<div class="wrap sent-wrap" data-wa-sent<?php echo $fallback ? ' hidden' : ''; ?>>
	<div class="wrap__col sent">
		<div class="sent__head">
			<h1 class="page-title"><?php esc_html_e( 'Opening WhatsApp…', 'miroir' ); ?></h1>
			<p class="sent__lead"><?php esc_html_e( 'Your order message is ready. Press send in WhatsApp so our team can confirm stock and shipping.', 'miroir' ); ?></p>
			<div class="sent__actions">
				<?php if ( $wa_url ) : ?>
					<a class="btn btn--whatsapp" href="<?php echo esc_url( $wa_url ); ?>" data-wa-open><?php echo miroir_icon( 'message-circle', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?><?php esc_html_e( "WhatsApp didn't open? Try again", 'miroir' ); ?></a>
				<?php endif; ?>
				<a class="btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Continue shopping', 'miroir' ); ?></a>
			</div>
		</div>

		<dl class="sent__overview hair-tb">
			<div><dt><?php esc_html_e( 'items', 'miroir' ); ?></dt><dd><?php echo esc_html( $qty ); ?></dd></div>
			<div><dt><?php esc_html_e( 'prepared', 'miroir' ); ?></dt><dd><?php echo esc_html( wc_format_datetime( $order->get_date_created(), 'j F Y' ) ); ?></dd></div>
			<div><dt><?php esc_html_e( 'estimated total', 'miroir' ); ?></dt><dd><?php echo wp_kses_post( $total ); ?></dd></div>
			<div><dt><?php esc_html_e( 'deliver to', 'miroir' ); ?></dt><dd><?php echo esc_html( trim( $order->get_billing_city() . ', ' . $country, ', ' ) ); ?></dd></div>
		</dl>

		<div class="notice sent__bank">
			<h2 class="form-section__title"><?php esc_html_e( 'payment, after we confirm in chat', 'miroir' ); ?></h2>
			<?php /* translators: %s: bank name. */ ?>
			<p class="sent__bank-line"><?php echo esc_html( sprintf( __( 'Bank name: %s', 'miroir' ), $account['bank_name'] ?? '—' ) ); ?></p>
			<?php /* translators: %s: account holder. */ ?>
			<p class="sent__bank-line"><?php echo esc_html( sprintf( __( 'Account name: %s', 'miroir' ), $account['account_name'] ?? '—' ) ); ?></p>
			<?php /* translators: %s: account number. */ ?>
			<p class="sent__bank-line"><?php echo esc_html( sprintf( __( 'Account number: %s', 'miroir' ), $account['account_number'] ?? '—' ) ); ?></p>
			<p class="sent__bank-line"><?php esc_html_e( 'Transfer the confirmed total (product + shipping), then send the receipt in the same WhatsApp chat.', 'miroir' ); ?></p>
		</div>

		<div class="sent__details">
			<h2 class="form-section__title"><?php esc_html_e( 'your order summary', 'miroir' ); ?></h2>
			<div class="detail-row detail-row--head hair-b"><span><?php esc_html_e( 'product', 'miroir' ); ?></span><span><?php esc_html_e( 'total', 'miroir' ); ?></span></div>
			<?php foreach ( $lines as $line ) : ?>
				<div class="detail-row hair-b"><span><?php echo esc_html( $line['name'] . ( $line['variant'] ? ' · ' . $line['variant'] : '' ) . ' × ' . $line['qty'] ); ?></span><span><?php echo wp_kses_post( $line['total_html'] ); ?></span></div>
			<?php endforeach; ?>
			<div class="detail-row hair-b"><span><?php esc_html_e( 'Subtotal', 'miroir' ); ?></span><span><?php echo wp_kses_post( $total ); ?></span></div>
			<div class="detail-row hair-b"><span><?php esc_html_e( 'Shipping', 'miroir' ); ?></span><span class="totals-row__muted"><?php esc_html_e( 'Confirmed in WhatsApp', 'miroir' ); ?></span></div>
			<div class="detail-row hair-b"><span><?php esc_html_e( 'Payment', 'miroir' ); ?></span><span><?php echo esc_html( $order->get_payment_method_title() ? $order->get_payment_method_title() : __( 'Bank transfer after confirmation', 'miroir' ) ); ?></span></div>
			<div class="detail-row detail-row--total hair-b"><span><?php esc_html_e( 'Estimated total', 'miroir' ); ?></span><span><?php echo wp_kses_post( $total ); ?></span></div>
		</div>
	</div>
</div>

<?php if ( $wa_url ) : ?>
	<?php $message = (string) $order->get_meta( '_miroir_wa_message' ); ?>
	<div class="flow" data-wa-fallback<?php echo $fallback ? '' : ' hidden'; ?>>
		<div class="flow__col">
			<h2 class="flow__title"><?php esc_html_e( 'Open WhatsApp', 'miroir' ); ?></h2>
			<div class="notice notice--flat" role="alert">
				<p class="notice__title"><?php esc_html_e( 'Your message is ready', 'miroir' ); ?></p>
				<p class="notice__text"><?php esc_html_e( 'We couldn’t open WhatsApp. Your order has not been confirmed or sent by this website.', 'miroir' ); ?></p>
			</div>
			<p class="flow__intro wa-message" data-wa-message><?php echo esc_html( $message ); ?></p>
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( $wa_url ); ?>"><?php esc_html_e( 'Open WhatsApp again', 'miroir' ); ?></a>
			<button class="btn btn--block" type="button" data-copy-message><?php esc_html_e( 'Copy order message', 'miroir' ); ?></button>
			<a class="btn btn--block" href="<?php echo esc_url( miroir_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact customer care', 'miroir' ); ?></a>
			<p class="flow__help wa-copied" data-copied role="status"><?php esc_html_e( 'Message copied. Paste it into your WhatsApp conversation.', 'miroir' ); ?></p>
		</div>
	</div>
<?php endif; ?>
