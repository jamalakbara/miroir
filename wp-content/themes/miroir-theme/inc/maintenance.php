<?php
/**
 * Maintenance mode (screen 30). `wp miroir theme maintenance on|off` toggles it;
 * visitors get the maintenance page with a 503, logged-in admins browse normally.
 * On a local environment `?miroir-maintenance-preview` shows the page without
 * turning the mode on.
 *
 * `wp miroir theme preview-link` prints a secret link for client review. Opening
 * it stores a cookie that lets that browser see the store during maintenance.
 * `--rotate` replaces the secret, which ends every existing preview.
 */

defined( 'ABSPATH' ) || exit;

const MIROIR_PREVIEW_COOKIE = 'miroir_store_preview';

/**
 * Cookie value for the current preview secret; empty when no secret exists.
 */
function miroir_preview_cookie_value() {
	$token = (string) get_option( 'miroir_preview_token', '' );
	return '' === $token ? '' : hash_hmac( 'sha256', 'miroir-store-preview', $token );
}

/**
 * Whether this browser holds a valid preview cookie.
 */
function miroir_has_store_preview() {
	$expected = miroir_preview_cookie_value();
	$cookie   = isset( $_COOKIE[ MIROIR_PREVIEW_COOKIE ] ) ? (string) $_COOKIE[ MIROIR_PREVIEW_COOKIE ] : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	return '' !== $expected && hash_equals( $expected, $cookie );
}

// Exchange the secret link for a cookie, then drop the secret from the address bar.
add_action(
	'template_redirect',
	function () {
		if ( ! isset( $_GET['miroir-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return;
		}
		$token = (string) get_option( 'miroir_preview_token', '' );
		$given = is_string( $_GET['miroir-preview'] ) ? wp_unslash( $_GET['miroir-preview'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput
		if ( '' === $token || ! hash_equals( $token, $given ) ) {
			return;
		}
		setcookie(
			MIROIR_PREVIEW_COOKIE,
			miroir_preview_cookie_value(),
			array(
				'expires'  => time() + 30 * DAY_IN_SECONDS,
				'path'     => '/',
				'secure'   => is_ssl(),
				'httponly' => true,
				'samesite' => 'Lax',
			)
		);
		nocache_headers();
		wp_safe_redirect( home_url( '/' ) );
		exit;
	},
	-1
);

add_action(
	'template_redirect',
	function () {
		$preview = isset( $_GET['miroir-maintenance-preview'] ) && ( 'local' === wp_get_environment_type() || current_user_can( 'manage_options' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$active  = 'on' === get_option( 'miroir_maintenance', 'off' ) && ! current_user_can( 'manage_options' );
		if ( $active && miroir_has_store_preview() ) {
			// Never let a cache keep this response for public visitors.
			$active = false;
			defined( 'DONOTCACHEPAGE' ) || define( 'DONOTCACHEPAGE', true );
			nocache_headers();
			header( 'X-Robots-Tag: noindex, nofollow' );
		}
		if ( ! $preview && ! $active ) {
			return;
		}
		if ( $active ) {
			status_header( 503 );
			header( 'Retry-After: 3600' );
		}
		nocache_headers();
		get_template_part( 'template-parts/maintenance' );
		exit;
	},
	0
);
