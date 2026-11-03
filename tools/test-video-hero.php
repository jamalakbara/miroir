<?php
/** WP integration checks. Run via wp eval-file; no persistent writes. */
defined( 'ABSPATH' ) || exit;
$check = static function ( $condition, $message ) {
	if ( ! $condition ) { WP_CLI::error( $message ); }
	WP_CLI::log( 'PASS ' . $message );
};
$types = array_column( miroir_home_sections(), 'type' );
$check( array( 'video-hero', 'strip', 'editorial', 'banner', 'strip' ) === $types, 'Default homepage has five sections in the approved order' );
$type = WP_Block_Type_Registry::get_instance()->get_registered( 'miroir/video-hero' );
$check( (bool) $type, 'Video hero is registered' );
$render = static fn( $attrs ) => render_block( miroir_parsed_block( 'miroir/video-hero', $attrs ) );
$html = $render( array() );
$check( false !== strpos( $html, 'data-src="' . MIROIR_THEME_URI . '/assets/video/hero-desktop.mp4?ver=' ) && false !== strpos( $html, 'data-mobile-src="' . MIROIR_THEME_URI . '/assets/video/hero-mobile.mp4?ver=' ), 'No upload falls back to the versioned bundled desktop and mobile campaign films' );
$check( false !== strpos( $html, '/assets/images/hero-video-poster-mobile.webp?ver=' ), 'Bundled posters are versioned' );
$check( false !== strpos( $html, '<picture' ) && 2 === substr_count( $html, '<source' ) && false !== strpos( $html, 'loading="eager"' ), 'Hero has responsive eager posters' );
$html = $render( array( 'video' => 'https://example.test/campaign.mp4', 'videoMobile' => 'https://example.test/mobile.webm' ) );
$check( false !== strpos( $html, 'muted loop playsinline preload="none"' ), 'Video is muted, looping, inline and not preloaded' );
$check( false !== strpos( $html, 'data-src="https://example.test/campaign.mp4"' ) && false !== strpos( $html, 'data-mobile-src="https://example.test/mobile.webm"' ), 'Video sources wait for preference-aware initialization' );
$check( false === strpos( $html, '<button' ) && false === strpos( $html, ' controls' ), 'Campaign hero has no custom or native playback controls' );
$home = (int) get_option( 'page_on_front' );
$content = (string) get_post_meta( $home, '_miroir_blocks_before_elementor', true );
$names = array_values( array_filter( array_column( parse_blocks( $content ), 'blockName' ) ) );
$check( array( 'miroir/video-hero', 'miroir/product-strip', 'miroir/editorial', 'miroir/banner', 'miroir/product-strip' ) === $names, 'Saved editable block fallback matches the approved layout' );
WP_CLI::success( 'Video hero integration checks passed.' );
