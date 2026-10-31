<?php
/**
 * Header currency toggle ("IDR"). Opens the currency selector panel (screen 34).
 */

defined( 'ABSPATH' ) || exit;

$current = function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'IDR';
?>
<a class="site-nav__currency" href="<?php echo esc_url( add_query_arg( 'panel', 'currency' ) ); ?>" data-panel-open="currency" aria-controls="currency-panel" aria-expanded="false" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: currency code. */ __( 'Currency: %s', 'miroir' ), $current ) ); ?>"><?php echo esc_html( $current ); ?></a>
