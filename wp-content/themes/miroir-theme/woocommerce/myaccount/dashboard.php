<?php
/**
 * Account dashboard (screen 17): greeting and three sections with a button each.
 *
 * @var WP_User $current_user
 */

defined( 'ABSPATH' ) || exit;

$sections = array(
	array( miroir_text( 'acc_orders' ), miroir_text( 'acc_orders_text' ), miroir_text( 'acc_orders_link' ), wc_get_account_endpoint_url( 'orders' ) ),
	array( miroir_text( 'acc_addresses' ), miroir_text( 'acc_addresses_text' ), miroir_text( 'acc_addresses_link' ), wc_get_account_endpoint_url( 'edit-address' ) ),
	array( miroir_text( 'acc_details' ), miroir_text( 'acc_details_text' ), miroir_text( 'acc_details_link' ), wc_get_account_endpoint_url( 'edit-account' ) ),
);
?>
<?php /* translators: %s: customer's first or display name. */ ?>
<p class="flow__intro"><?php echo esc_html( miroir_text( 'acc_hello', $current_user->first_name ? $current_user->first_name : $current_user->display_name ) ); ?></p>
<p class="flow__intro"><?php echo esc_html( miroir_text( 'acc_intro' ) ); ?></p>
<?php foreach ( $sections as list( $title, $copy, $label, $url ) ) : ?>
	<section class="flow__section hair-t">
		<h2 class="flow__label"><?php echo esc_html( $title ); ?></h2>
		<p class="flow__copy"><?php echo esc_html( $copy ); ?></p>
		<a class="btn btn--block" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
	</section>
<?php endforeach; ?>
<?php
do_action( 'woocommerce_account_dashboard' );
