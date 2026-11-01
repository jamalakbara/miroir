<?php
/**
 * Miroir theme bootstrap.
 *
 * Design source: docs/design/miroir.pen (Lofi Design, 44 screens x 1440/768/390).
 * CSS custom properties in assets/css/tokens.css mirror the pen variables 1:1.
 */

defined( 'ABSPATH' ) || exit;

define( 'MIROIR_THEME_VERSION', '0.1.0' );
define( 'MIROIR_THEME_DIR', get_template_directory() );
define( 'MIROIR_THEME_URI', get_template_directory_uri() );

require MIROIR_THEME_DIR . '/inc/customizer/registry.php';
require MIROIR_THEME_DIR . '/inc/customizer/text.php';
require MIROIR_THEME_DIR . '/inc/customizer/customizer.php';
require MIROIR_THEME_DIR . '/inc/setup.php';
require MIROIR_THEME_DIR . '/inc/icons.php';
require MIROIR_THEME_DIR . '/inc/template-tags.php';
require MIROIR_THEME_DIR . '/inc/maintenance.php';
require MIROIR_THEME_DIR . '/inc/blocks.php';
require MIROIR_THEME_DIR . '/inc/block-helpers.php';
require MIROIR_THEME_DIR . '/inc/templates.php';

if ( class_exists( 'WooCommerce' ) ) {
	require MIROIR_THEME_DIR . '/inc/woocommerce.php';
	require MIROIR_THEME_DIR . '/inc/states.php';
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require MIROIR_THEME_DIR . '/inc/cli.php';
}
