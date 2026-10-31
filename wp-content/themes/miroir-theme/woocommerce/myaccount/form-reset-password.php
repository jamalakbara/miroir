<?php
/**
 * Set new password (screen 15).
 *
 * @var array $args { key, login }
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_reset_password_form' );
?>
<div class="flow">
	<div class="flow__col">
		<h1 class="flow__title"><?php echo esc_html( miroir_text( 'reset_title' ) ); ?></h1>
		<p class="flow__intro"><?php echo esc_html( miroir_text( 'reset_intro' ) ); ?></p>
		<?php woocommerce_output_all_notices(); ?>
		<form class="flow__form woocommerce-ResetPassword lost_reset_password" method="post">
			<?php
			miroir_field( array( 'name' => 'password_1', 'type' => 'password', 'label' => __( 'New password *', 'miroir' ), 'autocomplete' => 'new-password', 'required' => true ) );
			miroir_field( array( 'name' => 'password_2', 'type' => 'password', 'label' => __( 'Confirm new password *', 'miroir' ), 'autocomplete' => 'new-password', 'required' => true ) );
			?>
			<p class="flow__help"><?php echo esc_html( miroir_text( 'reset_help' ) ); ?></p>
			<input type="hidden" name="reset_key" value="<?php echo esc_attr( $args['key'] ); ?>">
			<input type="hidden" name="reset_login" value="<?php echo esc_attr( $args['login'] ); ?>">
			<?php do_action( 'woocommerce_resetpassword_form' ); ?>
			<input type="hidden" name="wc_reset_password" value="true">
			<?php wp_nonce_field( 'reset_password', 'woocommerce-reset-password-nonce' ); ?>
			<button type="submit" class="btn btn--primary btn--block" value="<?php esc_attr_e( 'Save new password', 'miroir' ); ?>"><?php esc_html_e( 'Save new password', 'miroir' ); ?></button>
		</form>
		<a class="btn btn--block" href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo esc_html( miroir_text( 'back_to_login' ) ); ?></a>
	</div>
</div>
<?php
do_action( 'woocommerce_after_reset_password_form' );
