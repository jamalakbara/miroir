<?php
/**
 * Maintenance mode (screen 30). `wp miroir theme maintenance on|off` toggles it;
 * visitors get the maintenance page with a 503, logged-in admins browse normally.
 * On a local environment `?miroir-maintenance-preview` shows the page without
 * turning the mode on.
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'template_redirect',
	function () {
		$preview = isset( $_GET['miroir-maintenance-preview'] ) && ( 'local' === wp_get_environment_type() || current_user_can( 'manage_options' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$active  = 'on' === get_option( 'miroir_maintenance', 'off' ) && ! current_user_can( 'manage_options' );
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
