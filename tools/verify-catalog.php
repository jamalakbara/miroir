<?php
defined( 'ABSPATH' ) || exit;
$rows = json_decode( file_get_contents( __DIR__ . '/catalog.json' ), true );
$ids = (array) get_option( 'miroir_catalog_product_ids' );
$errors = array();
foreach ( $rows as $i => $row ) {
    $p = wc_get_product( $ids[$i] ?? 0 );
    if ( ! $p || $p->get_name() !== $row['name'] || 'publish' !== $p->get_status() ) { $errors[] = $row['name'] . ': missing or unpublished'; continue; }
    if ( ! $p->is_type( 'simple' ) || $p->managing_stock() || ! $p->is_purchasable() || ! $p->is_in_stock() ) { $errors[] = $p->get_name() . ': purchase settings'; }
    if ( (float) $p->get_regular_price() !== (float) $row['price'] || $p->get_sale_price() ) { $errors[] = $p->get_name() . ': price'; }
    if ( $p->get_attribute( 'Size' ) !== 'All Size' ) { $errors[] = $p->get_name() . ': size'; }
    if ( (int) round( wc_get_weight( $p->get_weight(), 'g' ) ) !== $row['weight_grams'] ) { $errors[] = $p->get_name() . ': weight'; }
    $images = array_merge( array( $p->get_image_id() ), $p->get_gallery_image_ids() );
    if ( count( $images ) !== count( $row['photos'] ) ) { $errors[] = $p->get_name() . ': photo count'; }
    foreach ( $images as $j => $id ) {
        if ( get_post_meta( $id, '_miroir_catalog_file', true ) !== $row['photos'][$j] || ! is_file( get_attached_file( $id ) ) ) { $errors[] = $p->get_name() . ': photo mapping'; }
    }
    if ( false !== strpos( $p->get_description(), '962 cm' ) ) { $errors[] = 'Lumi length typo'; }
}
$demo = get_posts( array( 'post_type' => 'product', 'post_status' => 'publish', 'meta_key' => '_miroir_demo', 'fields' => 'ids' ) );
if ( $demo ) { $errors[] = 'Published demo products remain'; }
if ( $errors ) { WP_CLI::error( implode( "\n", $errors ) ); }
WP_CLI::success( '18 products: names, prices, weights, All Size, stock policy, exact photo mappings; no published demo products.' );
