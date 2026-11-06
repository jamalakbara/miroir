<?php
/**
 * Private access page for the staging site (screen S1). Runs only when
 * WP_ENVIRONMENT_TYPE is `staging`, so it is inert on live and local.
 *
 * Visitors enter a shared password and receive a cookie for 30 days. Admins pass
 * without it. The password is set in Settings > Staging access and only its hash
 * is stored. Changing it ends every existing visitor's access. With no password
 * set the store stays closed to everyone except admins.
 *
 * Direct file URLs (uploads, theme assets) are served by the web server and are
 * not covered by this page.
 */

defined( 'ABSPATH' ) || exit;

if ( 'staging' !== wp_get_environment_type() ) {
	return;
}

const MIROIR_STAGING_COOKIE       = 'miroir_staging_access';
const MIROIR_STAGING_HASH_OPTION  = 'miroir_staging_password_hash';
const MIROIR_STAGING_MAX_ATTEMPTS = 10;

/**
 * Cookie value for the current password; empty when no password is set.
 */
function miroir_staging_cookie_value() {
	$hash = (string) get_option( MIROIR_STAGING_HASH_OPTION, '' );
	return '' === $hash ? '' : hash_hmac( 'sha256', 'miroir-staging-access', $hash . wp_salt( 'auth' ) );
}

/**
 * Whether this request may see the store.
 */
function miroir_staging_has_access() {
	if ( current_user_can( 'manage_options' ) ) {
		return true;
	}
	$expected = miroir_staging_cookie_value();
	$cookie   = isset( $_COOKIE[ MIROIR_STAGING_COOKIE ] ) ? (string) $_COOKIE[ MIROIR_STAGING_COOKIE ] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	return '' !== $expected && hash_equals( $expected, $cookie );
}

/**
 * Transient key counting wrong passwords from this visitor's address.
 */
function miroir_staging_attempts_key() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? (string) $_SERVER['REMOTE_ADDR'] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	return 'miroir_staging_attempts_' . md5( $ip );
}

/**
 * Check a submitted password. On success sets the cookie and redirects.
 *
 * @return string Text id of the error to show.
 */
function miroir_staging_handle_submit() {
	$key      = miroir_staging_attempts_key();
	$attempts = (int) get_transient( $key );
	if ( $attempts >= MIROIR_STAGING_MAX_ATTEMPTS ) {
		return 'staging_locked';
	}

	$hash  = (string) get_option( MIROIR_STAGING_HASH_OPTION, '' );
	$given = isset( $_POST['miroir_staging_password'] ) && is_string( $_POST['miroir_staging_password'] ) ? wp_unslash( $_POST['miroir_staging_password'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput

	if ( '' === $hash || '' === $given || ! wp_check_password( $given, $hash ) ) {
		set_transient( $key, $attempts + 1, 15 * MINUTE_IN_SECONDS );
		return 'staging_error';
	}

	delete_transient( $key );
	setcookie(
		MIROIR_STAGING_COOKIE,
		miroir_staging_cookie_value(),
		array(
			'expires'  => time() + 30 * DAY_IN_SECONDS,
			'path'     => '/',
			'secure'   => is_ssl(),
			'httponly' => true,
			'samesite' => 'Lax',
		)
	);
	nocache_headers();
	wp_safe_redirect( home_url( add_query_arg( array() ) ) );
	exit;
}

// Runs before the WooCommerce AJAX router and maintenance, which use priority 0 or later.
add_action(
	'template_redirect',
	function () {
		defined( 'DONOTCACHEPAGE' ) || define( 'DONOTCACHEPAGE', true );
		nocache_headers();
		header( 'X-Robots-Tag: noindex, nofollow' );

		if ( miroir_staging_has_access() ) {
			return;
		}

		$error = '';
		if ( isset( $_POST['miroir_staging_password'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$error = miroir_staging_handle_submit();
		}

		status_header( 403 );
		get_template_part( 'template-parts/staging-access', null, array( 'error' => $error ) );
		exit;
	},
	-100
);

// Close the routes that do not pass through template_redirect.
// rest_pre_dispatch, not rest_authentication_errors: the Store API marks itself as
// authenticated there, which would let product data through.
add_filter(
	'rest_pre_dispatch',
	function ( $result ) {
		if ( miroir_staging_has_access() ) {
			return $result;
		}
		return new WP_Error( 'miroir_staging_private', __( 'This store is private.', 'miroir' ), array( 'status' => 403 ) );
	}
);

add_filter( 'xmlrpc_methods', '__return_empty_array' );

add_action(
	'admin_init',
	function () {
		if ( wp_doing_ajax() && ! miroir_staging_has_access() ) {
			wp_die( '', '', array( 'response' => 403 ) );
		}
	},
	0
);

add_action(
	'admin_menu',
	function () {
		add_options_page(
			__( 'Staging access', 'miroir' ),
			__( 'Staging access', 'miroir' ),
			'manage_options',
			'miroir-staging-access',
			'miroir_staging_settings_page'
		);
	}
);

/**
 * Settings > Staging access.
 */
function miroir_staging_settings_page() {
	$has_password = '' !== (string) get_option( MIROIR_STAGING_HASH_OPTION, '' );
	$status       = isset( $_GET['miroir-staging'] ) ? sanitize_key( $_GET['miroir-staging'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Staging access', 'miroir' ); ?></h1>
		<?php if ( 'saved' === $status ) : ?>
			<div class="notice notice-success"><p><?php esc_html_e( 'Password saved. Everyone must now enter the new password.', 'miroir' ); ?></p></div>
		<?php elseif ( 'short' === $status ) : ?>
			<div class="notice notice-error"><p><?php esc_html_e( 'Use at least 12 characters.', 'miroir' ); ?></p></div>
		<?php elseif ( 'mismatch' === $status ) : ?>
			<div class="notice notice-error"><p><?php esc_html_e( 'The two passwords did not match.', 'miroir' ); ?></p></div>
		<?php endif; ?>
		<p>
			<?php
			echo esc_html(
				$has_password
					? __( 'A password is set. It cannot be shown here, only replaced.', 'miroir' )
					: __( 'No password is set, so only administrators can see the store.', 'miroir' )
			);
			?>
		</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="miroir_staging_password">
			<?php wp_nonce_field( 'miroir_staging_password' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="miroir-staging-new"><?php esc_html_e( 'New password', 'miroir' ); ?></label></th>
					<td><input class="regular-text" id="miroir-staging-new" name="password" type="password" autocomplete="new-password" required minlength="12"></td>
				</tr>
				<tr>
					<th scope="row"><label for="miroir-staging-confirm"><?php esc_html_e( 'Confirm password', 'miroir' ); ?></label></th>
					<td><input class="regular-text" id="miroir-staging-confirm" name="confirm" type="password" autocomplete="new-password" required minlength="12"></td>
				</tr>
			</table>
			<?php submit_button( __( 'Save password', 'miroir' ) ); ?>
		</form>
	</div>
	<?php
}

add_action(
	'admin_post_miroir_staging_password',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( '', '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'miroir_staging_password' );

		$password = isset( $_POST['password'] ) ? wp_unslash( (string) $_POST['password'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$confirm  = isset( $_POST['confirm'] ) ? wp_unslash( (string) $_POST['confirm'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		$status = 'saved';
		if ( strlen( $password ) < 12 ) {
			$status = 'short';
		} elseif ( ! hash_equals( $password, $confirm ) ) {
			$status = 'mismatch';
		} else {
			update_option( MIROIR_STAGING_HASH_OPTION, wp_hash_password( $password ), false );
		}

		wp_safe_redirect( add_query_arg( 'miroir-staging', $status, admin_url( 'options-general.php?page=miroir-staging-access' ) ) );
		exit;
	}
);
