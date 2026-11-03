<?php
/** Apply the approved hi-fi content to existing editable Elementor and block sections. */
defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/catalog-media.php';
$ids = array_map( 'intval', (array) get_option( 'miroir_catalog_product_ids', array() ) );
if ( 18 !== count( $ids ) ) { WP_CLI::error( 'Run import-catalog.php first.' ); }
require_once __DIR__ . '/apply-video-hero.php';
$media = array();
foreach ( array( 'hero-navy.jpg', 'hero-yellow.jpg', 'banner-desktop-2.jpg' ) as $file ) {
    $media[$file] = miroir_catalog_media( $file, 'Miroir collection' );
}
$shop = wc_get_page_permalink( 'shop' );
$image = fn( $file ) => array( 'id' => $media[$file], 'url' => wp_get_attachment_url( $media[$file] ) );
$pairs = array(
    array( 'hero-navy.jpg', 'hero-yellow.jpg', 'shop women: relaxed by design' ),
);
$home = (int) get_option( 'page_on_front' );
$elements = json_decode( get_post_meta( $home, '_elementor_data', true ), true );
$index = 0;
$walk = function ( &$nodes ) use ( &$walk, &$index, $pairs, $image, $shop, $ids ) {
    foreach ( $nodes as &$node ) {
        if ( 'miroir-editorial' === ( $node['widgetType'] ?? '' ) ) {
            $pair = $pairs[$index++];
            $node['settings']['smallImage'] = $image( $pair[0] );
            $node['settings']['largeImage'] = $image( $pair[1] );
            $node['settings']['caption'] = $pair[2];
            $node['settings']['url'] = array( 'url' => $shop );
        } elseif ( 'miroir-banner' === ( $node['widgetType'] ?? '' ) ) {
            foreach ( array( 'image', 'imageTablet', 'imageMobile' ) as $key ) { $node['settings'][$key] = $image( 'banner-desktop-2.jpg' ); }
            $node['settings']['caption'] = 'the collection: everyday, in good company';
            $node['settings']['url'] = array( 'url' => $shop );
        } elseif ( 'miroir-product-row' === ( $node['widgetType'] ?? '' ) ) {
            $node['settings']['source'] = 'ids';
            $node['settings']['ids'] = array_map( 'strval', $ids );
            $node['settings']['url'] = array( 'url' => $shop );
        }
        if ( ! empty( $node['elements'] ) ) { $walk( $node['elements'] ); }
    }
};
if ( $elements ) {
    $walk( $elements );
    update_post_meta( $home, '_elementor_data', wp_slash( wp_json_encode( $elements ) ) );
}
// Keep the theme's block fallback editable with the same source assets and catalog.
$content = get_post_meta( $home, '_miroir_blocks_before_elementor', true ) ?: get_post_field( 'post_content', $home );
$blocks = parse_blocks( $content ); $index = 0;
foreach ( $blocks as &$block ) {
    if ( 'miroir/editorial' === $block['blockName'] ) {
        $pair = $pairs[$index++];
        $block['attrs'] = array_merge( $block['attrs'], array( 'smallImage' => $media[$pair[0]], 'largeImage' => $media[$pair[1]], 'caption' => $pair[2], 'url' => $shop ) );
    } elseif ( 'miroir/banner' === $block['blockName'] ) {
        $block['attrs'] = array_merge( $block['attrs'], array( 'image' => $media['banner-desktop-2.jpg'], 'imageTablet' => 0, 'imageMobile' => 0, 'caption' => 'the collection: everyday, in good company', 'url' => $shop ) );
    } elseif ( 'miroir/product-strip' === $block['blockName'] ) {
        $block['attrs'] = array_merge( $block['attrs'], array( 'source' => 'ids', 'ids' => $ids, 'url' => $shop ) );
    }
}
unset( $block );
update_post_meta( $home, '_miroir_blocks_before_elementor', wp_slash( serialize_blocks( $blocks ) ) );
wp_update_post( array( 'ID' => $home, 'post_content' => wp_slash( serialize_blocks( $blocks ) ) ) );
$kit = (int) get_option( 'elementor_active_kit' );
$settings = (array) get_post_meta( $kit, '_elementor_page_settings', true );
foreach ( array( 'product_photos' => 'yes', 'related_count' => '4', 'similar_cols_d' => '4' ) as $key => $value ) {
    $settings['miroir_set_' . $key] = $value;
    set_theme_mod( $key, 'yes' === $value ? true : (int) $value );
}
foreach ( $settings['custom_typography'] ?? array() as $i => $font ) {
    if ( 'miroir_sans' === ( $font['_id'] ?? '' ) ) { $settings['custom_typography'][$i]['typography_font_family'] = 'Avenir'; }
}
update_post_meta( $kit, '_elementor_page_settings', $settings );
if ( did_action( 'elementor/loaded' ) ) { \Elementor\Plugin::$instance->files_manager->clear_cache(); }
wp_cache_flush();
WP_CLI::success( 'Applied hi-fi images, captions, catalog strips and responsive product settings.' );
