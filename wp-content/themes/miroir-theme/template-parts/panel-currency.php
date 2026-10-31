<?php
/**
 * Currency panel (screen 34): radios for the enabled currencies; Apply sends
 * ?miroir_currency=XXX, which the miroir-currency plugin turns into WCML's
 * client currency.
 *
 * @var array $args { open: bool }
 */

defined( 'ABSPATH' ) || exit;

$codes = apply_filters( 'miroir_currency_codes', array( get_woocommerce_currency() ) );
if ( count( $codes ) < 2 ) {
	return;
}
$names   = array(
	'IDR' => __( 'Indonesian rupiah', 'miroir' ),
	'USD' => __( 'US dollar', 'miroir' ),
	'SGD' => __( 'Singapore dollar', 'miroir' ),
	'MYR' => __( 'Malaysian ringgit', 'miroir' ),
);
$all     = get_woocommerce_currencies();
$current = get_woocommerce_currency();
$here    = remove_query_arg( array( 'panel', 'miroir_currency' ) );
?>
<div class="flow panel" id="currency-panel" data-panel="currency"<?php echo empty( $args['open'] ) ? ' hidden' : ''; ?>>
	<form class="flow__col" method="get" action="<?php echo esc_url( $here ); ?>">
		<h2 class="flow__title"><?php esc_html_e( 'Currency', 'miroir' ); ?></h2>
		<p class="flow__intro"><?php esc_html_e( 'Choose your display currency. Our team confirms the final payable amount in WhatsApp.', 'miroir' ); ?></p>
		<?php foreach ( $codes as $code ) : ?>
			<label class="radio panel-radio">
				<input type="radio" name="miroir_currency" value="<?php echo esc_attr( $code ); ?>"<?php checked( $current, $code ); ?>>
				<?php echo esc_html( $code . ' · ' . ( $names[ $code ] ?? ( $all[ $code ] ?? $code ) ) ); ?>
			</label>
		<?php endforeach; ?>
		<button class="btn btn--primary btn--block" type="submit"><?php esc_html_e( 'Apply currency', 'miroir' ); ?></button>
		<button class="btn btn--block" type="button" data-panel-close><?php esc_html_e( 'Cancel', 'miroir' ); ?></button>
	</form>
</div>
