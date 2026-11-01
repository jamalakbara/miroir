<?php
/**
 * My account menu: lowercase mono links, "log out" in ink-3.
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );
?>
<nav class="account-menu woocommerce-MyAccount-navigation" aria-label="<?php esc_attr_e( 'Account pages', 'miroir' ); ?>">
	<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<li class="account-menu__item account-menu__item--<?php echo esc_attr( $endpoint ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"<?php echo wc_is_current_account_menu_item( $endpoint ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( strtolower( $label ) ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
<?php
do_action( 'woocommerce_after_account_navigation' );
