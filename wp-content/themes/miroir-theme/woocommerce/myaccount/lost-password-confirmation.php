<?php
/**
 * Reset email sent (screen 14). "Resend" returns to the form, which is where a
 * new link is requested.
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_lost_password_confirmation_message' );
?>
<div class="flow">
	<div class="flow__col">
		<h1 class="flow__title"><?php esc_html_e( 'Reset email sent', 'miroir' ); ?></h1>
		<p class="flow__intro"><?php esc_html_e( 'Check your inbox', 'miroir' ); ?></p>
		<p class="flow__help"><?php esc_html_e( 'If an account exists for this email, you’ll receive a password reset link. Check your spam folder too.', 'miroir' ); ?></p>
		<a class="btn btn--primary btn--block" href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php esc_html_e( 'Resend reset email', 'miroir' ); ?></a>
		<a class="btn btn--block" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'Back to log in', 'miroir' ); ?></a>
	</div>
</div>
<?php
do_action( 'woocommerce_after_lost_password_confirmation_message' );
