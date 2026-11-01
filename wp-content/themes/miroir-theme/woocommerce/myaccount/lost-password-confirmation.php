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
		<h1 class="flow__title"><?php echo esc_html( miroir_text( 'reset_sent_title' ) ); ?></h1>
		<p class="flow__intro"><?php echo esc_html( miroir_text( 'reset_sent_intro' ) ); ?></p>
		<p class="flow__help"><?php echo esc_html( miroir_text( 'reset_sent_help' ) ); ?></p>
		<a class="btn btn--primary btn--block" href="<?php echo esc_url( wc_lostpassword_url() ); ?>"><?php echo esc_html( miroir_text( 'reset_sent_resend' ) ); ?></a>
		<a class="btn btn--block" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo esc_html( miroir_text( 'back_to_login' ) ); ?></a>
	</div>
</div>
<?php
do_action( 'woocommerce_after_lost_password_confirmation_message' );
