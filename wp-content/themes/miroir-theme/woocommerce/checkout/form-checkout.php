<?php
/**
 * Checkout = "Order via WhatsApp" review (screen 06). The form posts to the
 * miroir-whatsapp-order plugin, which saves the order and opens WhatsApp.
 *
 * @var WC_Checkout $checkout
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'miroir_wa_cart_lines' ) ) {
	echo '<div class="wrap"><p class="wrap__col">' . esc_html__( 'Ordering is unavailable: the Miroir WhatsApp Order plugin is not active.', 'miroir' ) . '</p></div>';
	return;
}

$lines     = miroir_wa_cart_lines();
$values    = miroir_wa_field_values();
$countries = WC()->countries->get_allowed_countries();
$chevron   = miroir_icon( 'chevron-down', 14, 'field__icon' );
?>
<div class="wrap checkout-wrap">
	<div class="wrap__col checkout">
		<h1 class="page-title"><?php esc_html_e( 'Order via WhatsApp', 'miroir' ); ?></h1>
		<?php woocommerce_output_all_notices(); ?>
		<p class="checkout__intro hair-b"><?php esc_html_e( "Add your details and we'll open WhatsApp with your order written out. Our team confirms stock, shipping cost and payment in the chat.", 'miroir' ); ?></p>

		<form class="checkout__columns" method="post" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" data-wa-form novalidate>
			<?php miroir_wa_form_fields(); ?>
			<div class="checkout__form">
				<section class="form-section">
					<h2 class="form-section__title"><?php esc_html_e( 'your details', 'miroir' ); ?></h2>
					<div class="field">
						<label class="field__label" for="wa_name"><?php esc_html_e( 'Full name *', 'miroir' ); ?></label>
						<input class="field__input" id="wa_name" name="wa_name" type="text" autocomplete="name" required value="<?php echo esc_attr( $values['name'] ); ?>">
					</div>
				</section>

				<section class="form-section">
					<h2 class="form-section__title"><?php esc_html_e( 'deliver to', 'miroir' ); ?></h2>
					<div class="form-row">
						<div class="field">
							<label class="field__label" for="wa_country"><?php esc_html_e( 'Country / Region *', 'miroir' ); ?></label>
							<div class="field__control">
								<select class="field__input field__input--select" id="wa_country" name="wa_country" autocomplete="country" required>
									<?php foreach ( $countries as $code => $label ) : ?>
										<option value="<?php echo esc_attr( $code ); ?>"<?php selected( $values['country'], $code ); ?>><?php echo esc_html( $label ); ?></option>
									<?php endforeach; ?>
								</select>
								<?php echo $chevron; // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?>
							</div>
						</div>
						<div class="field">
							<label class="field__label" for="wa_city"><?php esc_html_e( 'Town / City *', 'miroir' ); ?></label>
							<input class="field__input" id="wa_city" name="wa_city" type="text" autocomplete="address-level2" required value="<?php echo esc_attr( $values['city'] ); ?>">
						</div>
					</div>
				</section>

				<section class="form-section">
					<h2 class="form-section__title"><?php esc_html_e( 'notes', 'miroir' ); ?></h2>
					<div class="field">
						<label class="field__label" for="wa_notes"><?php esc_html_e( 'Order notes (optional)', 'miroir' ); ?></label>
						<input class="field__input" id="wa_notes" name="wa_notes" type="text" placeholder="<?php esc_attr_e( 'Size questions, gift wrap, preferred courier…', 'miroir' ); ?>" value="<?php echo esc_attr( $values['notes'] ); ?>">
					</div>
				</section>

				<section class="form-section form-section--preview">
					<div class="preview-head">
						<h2 class="form-section__title"><?php esc_html_e( 'message preview', 'miroir' ); ?></h2>
						<p class="preview-head__hint"><?php esc_html_e( 'Sent from your own WhatsApp', 'miroir' ); ?></p>
					</div>
					<p class="message-bubble" data-wa-preview aria-live="polite"><?php echo esc_html( miroir_wa_preview_message() ); ?></p>
				</section>
			</div>

			<aside class="order-summary" aria-label="<?php esc_attr_e( 'Your order', 'miroir' ); ?>">
				<h2 class="form-section__title"><?php esc_html_e( 'your order', 'miroir' ); ?></h2>
				<?php foreach ( $lines as $line ) : ?>
					<div class="summary-item">
						<?php miroir_ph( 'summary-item__thumb', $line['name'], 14 ); ?>
						<div class="summary-item__meta">
							<p class="summary-item__name"><?php echo esc_html( $line['name'] ); ?></p>
							<p class="summary-item__variant"><?php echo esc_html( trim( $line['variant'] . ' × ' . $line['qty'] ) ); ?></p>
						</div>
						<p class="summary-item__price"><?php echo wp_kses_post( $line['total_html'] ); ?></p>
					</div>
				<?php endforeach; ?>
				<div class="summary-line hair-t"><span><?php esc_html_e( 'Subtotal', 'miroir' ); ?></span><span><?php wc_cart_totals_subtotal_html(); ?></span></div>
				<div class="summary-line hair-t"><span><?php esc_html_e( 'Shipping', 'miroir' ); ?></span><span class="totals-row__muted"><?php esc_html_e( 'Confirmed in WhatsApp', 'miroir' ); ?></span></div>
				<div class="summary-line summary-line--total hair-t"><span><?php esc_html_e( 'Estimated total', 'miroir' ); ?></span><span><?php wc_cart_totals_subtotal_html(); ?></span></div>
				<div class="next-steps hair-t">
					<p class="next-steps__title"><?php esc_html_e( 'What happens next', 'miroir' ); ?></p>
					<?php
					$steps = array(
						__( 'Send the prefilled message in WhatsApp', 'miroir' ),
						__( 'We confirm stock and shipping cost', 'miroir' ),
						__( 'Pay by bank transfer, then we ship', 'miroir' ),
					);
					foreach ( $steps as $i => $step ) :
						?>
						<p class="next-steps__step"><span class="next-steps__no"><?php echo esc_html( sprintf( '%02d', $i + 1 ) ); ?></span><span><?php echo esc_html( $step ); ?></span></p>
					<?php endforeach; ?>
				</div>
				<p class="order-summary__privacy"><?php esc_html_e( 'WhatsApp opens in a new tab with this message. Nothing is charged on this site.', 'miroir' ); ?></p>
				<button class="btn btn--whatsapp btn--block" type="submit"><?php echo miroir_icon( 'message-circle', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?><?php esc_html_e( 'Continue to WhatsApp', 'miroir' ); ?></button>
			</aside>
		</form>
	</div>
</div>
