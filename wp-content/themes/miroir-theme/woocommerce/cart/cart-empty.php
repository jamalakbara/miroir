<?php
/**
 * Empty bag (screen 05).
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="wrap cart-empty">
	<div class="wrap__col cart-empty__col">
		<?php woocommerce_output_all_notices(); ?>
		<p class="cart-empty__message hair-b"><?php esc_html_e( 'Your bag is empty', 'miroir' ); ?></p>
		<a class="btn" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"><?php esc_html_e( 'Return to shop', 'miroir' ); ?></a>
	</div>
</div>
