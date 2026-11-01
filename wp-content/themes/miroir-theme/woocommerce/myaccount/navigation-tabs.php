<?php
/**
 * Account tabs (screens 17-21): Dashboard, Orders, Addresses, Account details, Log out.
 * Screen 09 uses the side menu in navigation.php instead.
 */

defined( 'ABSPATH' ) || exit;
?>
<nav class="acct-nav hair-b" aria-label="<?php esc_attr_e( 'Account pages', 'miroir' ); ?>">
	<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"<?php echo wc_is_current_account_menu_item( $endpoint ) ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a>
	<?php endforeach; ?>
</nav>
