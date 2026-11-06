<?php
/**
 * Order sent (screen 07): WhatsApp opens with the order message; retry
 * button, order overview, bank details and the order summary.
 *
 * @var WC_Order|false $order
 */

defined( 'ABSPATH' ) || exit;

if ( ! $order ) {
	echo '<div class="wrap"><p class="wrap__col">' . esc_html( miroir_text( 'sent_missing' ) ) . '</p></div>';
	return;
}

$wa_url   = function_exists( 'miroir_wa_order_url' ) ? miroir_wa_order_url( $order ) : '';
$lines    = function_exists( 'miroir_wa_order_lines' ) ? miroir_wa_order_lines( $order ) : array();
$money    = array( 'currency' => $order->get_currency() );
$subtotal = wc_price( $order->get_subtotal(), $money );
$total    = wc_price( $order->get_subtotal() - $order->get_discount_total(), $money );
$country  = WC()->countries->get_countries()[ $order->get_billing_country() ] ?? $order->get_billing_country();
$accounts = array_filter( (array) get_option( 'woocommerce_bacs_accounts', array() ) );
$account  = $accounts ? reset( $accounts ) : array();
$paypal   = trim( (string) get_option( 'miroir_wa_paypal', '' ) );
$states   = WC()->countries->get_states( $order->get_billing_country() );
$state    = $states[ $order->get_billing_state() ] ?? $order->get_billing_state();
$qty      = array_sum( wp_list_pluck( $lines, 'qty' ) );
?>
<?php $fallback = isset( $_GET['wa-fallback'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display state only. ?>
<div class="wrap sent-wrap" data-wa-sent<?php echo $fallback ? ' hidden' : ''; ?>>
	<div class="wrap__col sent">
		<div class="sent__head">
			<h1 class="page-title"><?php echo esc_html( miroir_text( 'sent_title' ) ); ?></h1>
			<p class="sent__lead"><?php echo esc_html( miroir_text( 'sent_lead' ) ); ?></p>
			<div class="sent__actions">
				<?php if ( $wa_url ) : ?>
					<a class="btn btn--whatsapp" href="<?php echo esc_attr( $wa_url ); ?>" target="_blank" rel="noopener" data-wa-open><?php echo miroir_icon( 'whatsapp', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?><?php echo esc_html( miroir_text( 'sent_retry' ) ); ?></a>
				<?php endif; ?>
				<a class="btn" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php echo esc_html( miroir_text( 'continue_shopping' ) ); ?></a>
			</div>
		</div>

		<dl class="sent__overview hair-tb">
			<div><dt><?php echo esc_html( miroir_text( 'sent_items' ) ); ?></dt><dd><?php echo esc_html( $qty ); ?></dd></div>
			<div><dt><?php echo esc_html( miroir_text( 'sent_prepared' ) ); ?></dt><dd><?php echo esc_html( wc_format_datetime( $order->get_date_created(), 'j F Y' ) ); ?></dd></div>
			<div><dt><?php echo esc_html( miroir_text( 'sent_total' ) ); ?></dt><dd><?php echo wp_kses_post( $total ); ?></dd></div>
			<div><dt><?php echo esc_html( miroir_text( 'deliver_to' ) ); ?></dt><dd><?php echo esc_html( implode( ', ', array_filter( array( $order->get_billing_address_1(), $order->get_billing_address_2(), $order->get_billing_city(), $state, $order->get_billing_postcode(), $country ) ) ) ); ?></dd></div>
		</dl>

		<div class="notice sent__bank">
			<h2 class="form-section__title"><?php echo esc_html( miroir_text( 'sent_payment_title' ) ); ?></h2>
			<?php /* translators: %s: bank name. */ ?>
			<p class="sent__bank-line"><?php echo esc_html( miroir_text( 'bank_name', $account['bank_name'] ?? '—' ) ); ?></p>
			<?php /* translators: %s: account holder. */ ?>
			<p class="sent__bank-line"><?php echo esc_html( miroir_text( 'bank_account_name', $account['account_name'] ?? '—' ) ); ?></p>
			<?php /* translators: %s: account number. */ ?>
			<p class="sent__bank-line"><?php echo esc_html( miroir_text( 'bank_account_number', $account['account_number'] ?? '—' ) ); ?></p>
			<?php if ( '' !== $paypal ) : ?>
				<?php /* translators: %s: PayPal account. */ ?>
				<p class="sent__bank-line"><?php echo esc_html( miroir_text( 'paypal_account', $paypal ) ); ?></p>
			<?php endif; ?>
			<p class="sent__bank-line"><?php echo esc_html( miroir_text( 'sent_payment_text' ) ); ?></p>
		</div>

		<div class="sent__details">
			<h2 class="form-section__title"><?php echo esc_html( miroir_text( 'sent_summary' ) ); ?></h2>
			<div class="detail-row detail-row--head hair-b"><span><?php echo esc_html( miroir_text( 'col_product' ) ); ?></span><span><?php echo esc_html( miroir_text( 'col_total' ) ); ?></span></div>
			<?php foreach ( $lines as $line ) : ?>
				<div class="detail-row hair-b"><span><?php echo esc_html( $line['name'] . ( $line['variant'] ? ' · ' . $line['variant'] : '' ) . ' × ' . $line['qty'] ); ?></span><span><?php echo wp_kses_post( $line['total_html'] ); ?></span></div>
			<?php endforeach; ?>
			<div class="detail-row hair-b"><span><?php echo esc_html( miroir_text( 'subtotal' ) ); ?></span><span><?php echo wp_kses_post( $subtotal ); ?></span></div>
			<?php if ( $order->get_discount_total() > 0 ) : ?>
				<div class="detail-row hair-b"><span><?php echo esc_html( miroir_text( 'discount' ) . ' (' . strtoupper( implode( ', ', $order->get_coupon_codes() ) ) . ')' ); ?></span><span><?php echo wp_kses_post( '-' . wc_price( $order->get_discount_total(), $money ) ); ?></span></div>
			<?php endif; ?>
			<div class="detail-row hair-b"><span><?php echo esc_html( miroir_text( 'shipping' ) ); ?></span><span class="totals-row__muted"><?php echo esc_html( miroir_text( 'shipping_note' ) ); ?></span></div>
			<div class="detail-row hair-b"><span><?php echo esc_html( miroir_text( 'payment' ) ); ?></span><span><?php echo esc_html( $order->get_payment_method_title() ? $order->get_payment_method_title() : miroir_text( 'payment_default' ) ); ?></span></div>
			<div class="detail-row detail-row--total hair-b"><span><?php echo esc_html( miroir_text( 'estimated_total' ) ); ?></span><span><?php echo wp_kses_post( $total ); ?></span></div>
		</div>
	</div>
</div>

<?php if ( $wa_url ) : ?>
	<?php $message = (string) $order->get_meta( '_miroir_wa_message' ); ?>
	<div class="flow" data-wa-fallback<?php echo $fallback ? '' : ' hidden'; ?>>
		<div class="flow__col">
			<h2 class="flow__title"><?php echo esc_html( miroir_text( 'sent_fallback_title' ) ); ?></h2>
			<div class="notice notice--flat" role="alert">
				<p class="notice__title"><?php echo esc_html( miroir_text( 'sent_fallback_notice' ) ); ?></p>
				<p class="notice__text"><?php echo esc_html( miroir_text( 'sent_fallback_text' ) ); ?></p>
			</div>
			<p class="flow__intro wa-message" data-wa-message><?php echo esc_html( $message ); ?></p>
			<a class="btn btn--primary btn--block" href="<?php echo esc_attr( $wa_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( miroir_text( 'sent_fallback_retry' ) ); ?></a>
			<button class="btn btn--block" type="button" data-copy-message><?php echo esc_html( miroir_text( 'sent_copy' ) ); ?></button>
			<a class="btn btn--block" href="<?php echo esc_url( miroir_page_url( 'contact' ) ); ?>"><?php echo esc_html( miroir_text( 'contact_care' ) ); ?></a>
			<p class="flow__help wa-copied" data-copied role="status"><?php echo esc_html( miroir_text( 'sent_copied' ) ); ?></p>
		</div>
	</div>
<?php endif; ?>
