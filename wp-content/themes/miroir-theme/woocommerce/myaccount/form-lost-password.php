<?php
/**
 * Forgot password (screen 13).
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_form' );
?>
<div class="flow">
	<div class="flow__col">
		<h1 class="flow__title"><?php echo esc_html( miroir_text( 'lost_title' ) ); ?></h1>
		<p class="flow__intro"><?php echo esc_html( miroir_text( 'lost_intro' ) ); ?></p>
		<?php woocommerce_output_all_notices(); ?>
		<form class="flow__form woocommerce-ResetPassword lost_reset_password" method="post">
			<?php miroir_field( array( 'name' => 'user_login', 'type' => 'text', 'label' => __( 'Email address *', 'miroir' ), 'autocomplete' => 'username', 'required' => true ) ); ?>
			<?php do_action( 'woocommerce_lostpassword_form' ); ?>
			<input type="hidden" name="wc_reset_password" value="true">
			<?php wp_nonce_field( 'lost_password', 'woocommerce-lost-password-nonce' ); ?>
			<button type="submit" class="btn btn--primary btn--block" value="<?php esc_attr_e( 'Send reset link', 'miroir' ); ?>"><?php esc_html_e( 'Send reset link', 'miroir' ); ?></button>
		</form>
		<a class="btn btn--block" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo esc_html( miroir_text( 'back_to_login' ) ); ?></a>
	</div>
</div>
<?php
do_action( 'woocommerce_after_lost_password_form' );
