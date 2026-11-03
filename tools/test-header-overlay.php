<?php
/** No-write WP integration checks for the optional homepage navigation. */
defined( 'ABSPATH' ) || exit;
$check = static function ( $condition, $message ) {
	if ( ! $condition ) { WP_CLI::error( $message ); }
	WP_CLI::log( 'PASS ' . $message );
};
$s = miroir_settings()['home_header_style'];
$check( 'transparent' === $s['default'] && array( 'transparent', 'solid' ) === array_keys( $s['choices'] ), 'Homepage setting offers Transparent over hero / Solid, default Transparent' );
$check( 'miroir_header' === $s['section'] && isset( miroir_customizer_sections()['miroir_header'] ), 'Setting appears in the shared Customizer and Elementor Header section' );
$check( 'transparent' === miroir_sanitizer( $s )( 'invalid' ) && 'solid' === miroir_sanitizer( $s )( 'solid' ), 'Header choice validates against allowed modes' );
$before = $GLOBALS['wp_query'];
$mode = 'transparent';
$override = static function ( $value, $id ) use ( &$mode ) { return 'home_header_style' === $id ? $mode : $value; };
add_filter( 'miroir_mod', $override, 100, 2 );
try {
	$GLOBALS['wp_query'] = new WP_Query( array( 'page_id' => (int) get_option( 'page_on_front' ) ) );
	$check( is_front_page() && in_array( 'home-header-overlay', get_body_class(), true ), 'Homepage receives overlay class when enabled' );
	$mode = 'solid';
	$check( ! in_array( 'home-header-overlay', get_body_class(), true ), 'Solid choice restores the original header' );
	$mode = 'transparent';
	$GLOBALS['wp_query'] = new WP_Query( array( 'page_id' => (int) wc_get_page_id( 'shop' ) ) );
	$check( ! is_front_page() && ! in_array( 'home-header-overlay', get_body_class(), true ), 'Shop retains its normal solid header' );
} finally {
	$GLOBALS['wp_query'] = $before;
	remove_filter( 'miroir_mod', $override, 100 );
}
WP_CLI::success( 'Homepage-header integration checks passed without changing settings.' );
