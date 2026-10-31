<?php
/**
 * Account details (screen 18). Keeps WooCommerce's save_account_details contract.
 *
 * @var WP_User $user
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' );
?>
<form class="flow__form woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?>>
	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>
	<?php
	miroir_field( array( 'name' => 'account_first_name', 'label' => __( 'First name *', 'miroir' ), 'autocomplete' => 'given-name', 'required' => true, 'value' => $user->first_name ) );
	miroir_field( array( 'name' => 'account_last_name', 'label' => __( 'Last name *', 'miroir' ), 'autocomplete' => 'family-name', 'required' => true, 'value' => $user->last_name ) );
	miroir_field( array( 'name' => 'account_display_name', 'label' => __( 'Display name *', 'miroir' ), 'required' => true, 'value' => $user->display_name ) );
	miroir_field( array( 'name' => 'account_email', 'type' => 'email', 'label' => __( 'Email address *', 'miroir' ), 'autocomplete' => 'email', 'required' => true, 'value' => $user->user_email ) );
	?>
	<?php do_action( 'woocommerce_edit_account_form_fields' ); ?>

	<div class="flow__section hair-t" role="group" aria-labelledby="change-password-title">
		<h2 class="flow__label" id="change-password-title"><?php esc_html_e( 'Change password', 'miroir' ); ?></h2>
		<p class="flow__copy"><?php esc_html_e( 'Leave these fields blank to keep your current password.', 'miroir' ); ?></p>
		<?php
		miroir_field( array( 'name' => 'password_current', 'type' => 'password', 'label' => __( 'Current password', 'miroir' ), 'autocomplete' => 'current-password' ) );
		miroir_field( array( 'name' => 'password_1', 'type' => 'password', 'label' => __( 'New password', 'miroir' ), 'autocomplete' => 'new-password' ) );
		miroir_field( array( 'name' => 'password_2', 'type' => 'password', 'label' => __( 'Confirm new password', 'miroir' ), 'autocomplete' => 'new-password' ) );
		?>
	</div>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>
	<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
	<button type="submit" class="btn btn--primary btn--block" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'miroir' ); ?>"><?php esc_html_e( 'Save changes', 'miroir' ); ?></button>
	<input type="hidden" name="action" value="save_account_details">
	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>
<?php
do_action( 'woocommerce_after_edit_account_form' );
