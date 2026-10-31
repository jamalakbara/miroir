<?php
/**
 * Logged-out account page: login (screen 08), or with ?action=register the
 * create-account form (12), or after a password reset the confirmation (16).
 * Keeps WooCommerce's login/register contracts (field names, nonces, buttons).
 */

defined( 'ABSPATH' ) || exit;

$account_url = wc_get_page_permalink( 'myaccount' );

// phpcs:disable WordPress.Security.NonceVerification -- display state only; WooCommerce verifies the POSTs.
if ( isset( $_GET['password-reset'] ) ) :
	// The screen itself says the password changed; drop WooCommerce's duplicate notice.
	wc_clear_notices();
	?>
	<div class="flow">
		<div class="flow__col">
			<h1 class="flow__title"><?php esc_html_e( 'Password updated', 'miroir' ); ?></h1>
			<p class="flow__intro"><?php esc_html_e( 'Your password has been changed.', 'miroir' ); ?></p>
			<p class="flow__help"><?php esc_html_e( 'You can now sign in with your new password.', 'miroir' ); ?></p>
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( $account_url ); ?>"><?php esc_html_e( 'Log in', 'miroir' ); ?></a>
		</div>
	</div>
	<?php
	return;
endif;

// Prints notices (login/registration errors) for both forms below.
do_action( 'woocommerce_before_customer_login_form' );

if ( 'register' === ( $_GET['action'] ?? '' ) || isset( $_POST['register'] ) ) :
	$posted = wp_unslash( $_POST );
	?>
	<div class="flow">
		<div class="flow__col">
			<h1 class="flow__title"><?php esc_html_e( 'Create account', 'miroir' ); ?></h1>
			<p class="flow__intro"><?php esc_html_e( 'Create an account to keep your details in one place.', 'miroir' ); ?></p>
			<?php woocommerce_output_all_notices(); ?>
			<form class="flow__form woocommerce-form woocommerce-form-register register" method="post" action="<?php echo esc_url( add_query_arg( 'action', 'register', $account_url ) ); ?>" <?php do_action( 'woocommerce_register_form_tag' ); ?>>
				<?php do_action( 'woocommerce_register_form_start' ); ?>
				<?php
				miroir_field( array( 'name' => 'first_name', 'id' => 'reg_first_name', 'label' => __( 'First name *', 'miroir' ), 'autocomplete' => 'given-name', 'required' => true, 'value' => wc_clean( $posted['first_name'] ?? '' ) ) );
				miroir_field( array( 'name' => 'last_name', 'id' => 'reg_last_name', 'label' => __( 'Last name *', 'miroir' ), 'autocomplete' => 'family-name', 'required' => true, 'value' => wc_clean( $posted['last_name'] ?? '' ) ) );
				if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) {
					miroir_field( array( 'name' => 'username', 'id' => 'reg_username', 'label' => __( 'Username *', 'miroir' ), 'autocomplete' => 'username', 'required' => true, 'value' => wc_clean( $posted['username'] ?? '' ) ) );
				}
				miroir_field( array( 'name' => 'email', 'id' => 'reg_email', 'type' => 'email', 'label' => __( 'Email address *', 'miroir' ), 'autocomplete' => 'email', 'required' => true, 'value' => sanitize_email( $posted['email'] ?? '' ) ) );
				if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) {
					miroir_field( array( 'name' => 'password', 'id' => 'reg_password', 'type' => 'password', 'label' => __( 'Password *', 'miroir' ), 'autocomplete' => 'new-password', 'required' => true ) );
				}
				do_action( 'woocommerce_register_form' );
				?>
				<p class="flow__help">
					<?php
					$privacy = get_privacy_policy_url();
					echo wp_kses(
						sprintf(
							/* translators: %s: privacy policy link. */
							__( 'Use at least 12 characters. Your personal data is used to manage your account. Read our %s.', 'miroir' ),
							$privacy ? '<a href="' . esc_url( $privacy ) . '">' . esc_html__( 'Privacy policy', 'miroir' ) . '</a>' : esc_html__( 'Privacy policy', 'miroir' )
						),
						array( 'a' => array( 'href' => array() ) )
					);
					?>
				</p>
				<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
				<button type="submit" class="btn btn--primary btn--block" name="register" value="<?php esc_attr_e( 'Create account', 'miroir' ); ?>"><?php esc_html_e( 'Create account', 'miroir' ); ?></button>
				<?php do_action( 'woocommerce_register_form_end' ); ?>
			</form>
			<a class="btn btn--block" href="<?php echo esc_url( $account_url ); ?>"><?php esc_html_e( 'Already registered? Log in', 'miroir' ); ?></a>
		</div>
	</div>
	<?php
	do_action( 'woocommerce_after_customer_login_form' );
	return;
endif;
// phpcs:enable
?>
<div class="wrap login-wrap">
	<div class="wrap__col">
		<h1 class="page-title"><?php esc_html_e( 'Welcome', 'miroir' ); ?></h1>
		<?php woocommerce_output_all_notices(); ?>

		<form class="woocommerce-form woocommerce-form-login login account-form" method="post" novalidate>
			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<div class="field">
				<label class="field__label" for="username"><?php esc_html_e( 'Email address *', 'miroir' ); ?></label>
				<input class="field__input" type="text" name="username" id="username" autocomplete="username" required value="<?php echo ! empty( $_POST['username'] ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing ?>">
			</div>

			<div class="account-form__password">
				<div class="field">
					<label class="field__label" for="password"><?php esc_html_e( 'Password *', 'miroir' ); ?></label>
					<div class="field__control">
						<input class="field__input field__input--icon" type="password" name="password" id="password" autocomplete="current-password" required>
						<button class="field__toggle" type="button" data-password-toggle aria-controls="password" aria-pressed="false" aria-label="<?php esc_attr_e( 'Show password', 'miroir' ); ?>"><?php echo miroir_icon( 'eye-off', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></button>
					</div>
				</div>
				<a class="account-form__lost" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'miroir' ); ?></a>
			</div>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<label class="check check--strong">
				<input name="rememberme" type="checkbox" id="rememberme" value="forever">
				<?php esc_html_e( 'Remember me', 'miroir' ); ?>
			</label>

			<div class="account-form__actions">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<button type="submit" class="btn" name="login" value="<?php esc_attr_e( 'Log in', 'miroir' ); ?>"><?php esc_html_e( 'Log in', 'miroir' ); ?></button>
				<?php if ( 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
					<a class="btn" href="<?php echo esc_url( add_query_arg( 'action', 'register', $account_url ) ); ?>"><?php esc_html_e( 'Register', 'miroir' ); ?></a>
				<?php endif; ?>
			</div>

			<?php do_action( 'woocommerce_login_form_end' ); ?>
		</form>
	</div>
</div>
<?php
do_action( 'woocommerce_after_customer_login_form' );
