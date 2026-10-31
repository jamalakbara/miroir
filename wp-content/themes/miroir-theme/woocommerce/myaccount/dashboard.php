<?php
/**
 * Account dashboard (screen 17): greeting and three sections with a button each.
 *
 * @var WP_User $current_user
 */

defined( 'ABSPATH' ) || exit;

$sections = array(
	array( __( 'Orders', 'miroir' ), __( 'View orders recorded by our team.', 'miroir' ), __( 'View orders', 'miroir' ), wc_get_account_endpoint_url( 'orders' ) ),
	array( __( 'Addresses', 'miroir' ), __( 'Manage your billing and delivery details.', 'miroir' ), __( 'View addresses', 'miroir' ), wc_get_account_endpoint_url( 'edit-address' ) ),
	array( __( 'Account details', 'miroir' ), __( 'Update your name, email and password.', 'miroir' ), __( 'View account details', 'miroir' ), wc_get_account_endpoint_url( 'edit-account' ) ),
);
?>
<?php /* translators: %s: customer's first or display name. */ ?>
<p class="flow__intro"><?php echo esc_html( sprintf( __( 'Hello, %s.', 'miroir' ), $current_user->first_name ? $current_user->first_name : $current_user->display_name ) ); ?></p>
<p class="flow__intro"><?php esc_html_e( 'Manage your details, saved addresses and orders here.', 'miroir' ); ?></p>
<?php foreach ( $sections as list( $title, $copy, $label, $url ) ) : ?>
	<section class="flow__section hair-t">
		<h2 class="flow__label"><?php echo esc_html( $title ); ?></h2>
		<p class="flow__copy"><?php echo esc_html( $copy ); ?></p>
		<a class="btn btn--block" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
	</section>
<?php endforeach; ?>
<?php
do_action( 'woocommerce_account_dashboard' );
