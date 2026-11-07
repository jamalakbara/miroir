<?php
/**
 * Apply the photos in tools/catalog.json to the named products, changing nothing else.
 * Unlike import-catalog.php this keeps prices, sales and copy edited in wp-admin.
 *
 * Run with wp eval-file, passing product names as arguments.
 */
defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/catalog-media.php';
if ( ! $args ) { WP_CLI::error( 'Pass the product names to update.' ); }
$rows = json_decode( file_get_contents( __DIR__ . '/catalog.json' ), true, 512, JSON_THROW_ON_ERROR );
$rows = array_column( $rows, null, 'name' );
foreach ( $args as $name ) {
    if ( ! isset( $rows[ $name ] ) ) { WP_CLI::error( 'Not in catalog.json: ' . $name ); }
    $post = get_page_by_path( sanitize_title( $name ), OBJECT, 'product' );
    if ( ! $post ) { WP_CLI::error( 'No product: ' . $name ); }
    $product = wc_get_product( $post->ID );
    $photos  = array_map( fn( $f ) => miroir_catalog_media( $f, $name ), $rows[ $name ]['photos'] );
    $product->set_image_id( array_shift( $photos ) );
    $product->set_gallery_image_ids( $photos );
    $product->save();
    wc_delete_product_transients( $post->ID );
    WP_CLI::log( $post->ID . ': ' . $name );
}
WP_CLI::success( 'Updated photos for ' . count( $args ) . ' products.' );
