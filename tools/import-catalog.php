<?php
/** Run with wp eval-file after preparing tools/catalog.json and source images. */
defined( 'ABSPATH' ) || exit;
require_once __DIR__ . '/catalog-media.php';
$rows = json_decode( file_get_contents( __DIR__ . '/catalog.json' ), true, 512, JSON_THROW_ON_ERROR );
$source = WP_CONTENT_DIR . '/uploads/miroir-catalog-source/';
foreach ( $rows as $row ) {
    foreach ( $row['photos'] as $photo ) {
        if ( ! is_file( $source . $photo ) ) { WP_CLI::error( 'Missing photo: ' . $photo ); }
    }
}
$ids = array();
foreach ( $rows as $i => $row ) {
    $slug = sanitize_title( $row['name'] );
    $post = get_page_by_path( $slug, OBJECT, 'product' );
    $product = new WC_Product_Simple( $post ? $post->ID : 0 );
    // Convert a matching demo product in place so its URL and order references survive.
    if ( $post && get_post_meta( $post->ID, '_miroir_demo', true ) ) {
        foreach ( get_posts( array( 'post_type' => 'product_variation', 'post_parent' => $post->ID, 'numberposts' => -1, 'fields' => 'ids' ) ) as $variation ) {
            wp_update_post( array( 'ID' => $variation, 'post_status' => 'private' ) );
        }
        wp_set_object_terms( $post->ID, 'simple', 'product_type' );
    }
    $product->set_name( $row['name'] );
    $product->set_slug( $slug );
    $product->set_sku( '' ); // The supplied SKU column is blank; do not retain demo SKUs.
    $product->set_status( 'publish' );
    $product->set_regular_price( $row['price'] );
    $product->set_sale_price( '' );
    $product->set_price( $row['price'] );
    $product->set_manage_stock( false );
    $product->set_stock_quantity( null );
    $product->set_stock_status( 'instock' );
    $product->set_backorders( 'no' );
    $product->set_weight( wc_get_weight( $row['weight_grams'], get_option( 'woocommerce_weight_unit', 'kg' ), 'g' ) );
    $product->set_menu_order( $i );
    $product->set_featured( false );
    $product->set_tag_ids( array() );
    $description = '<p>' . esc_html( $row['description'] ) . '</p><ul>';
    foreach ( explode( '|', $row['details'] ) as $detail ) { $description .= '<li>' . esc_html( trim( $detail ) ) . '</li>'; }
    $description .= '</ul>';
    $product->set_description( $description );
    $product->set_short_description( $row['description'] );
    $attributes = array();
    foreach ( array( 'Size' => 'All Size', 'Material' => 'Cotton' ) as $name => $value ) {
        $a = new WC_Product_Attribute(); $a->set_name( $name ); $a->set_options( array( $value ) ); $a->set_visible( true ); $a->set_variation( false ); $attributes[] = $a;
    }
    foreach ( array( 'white' => 'white', 'yellow' => 'yellow', 'cream' => 'neutrals', 'navy' => 'blue', 'green' => 'green', 'black' => 'black', 'brown' => 'brown', 'blue' => 'blue' ) as $word => $color ) {
        if ( false !== stripos( $row['name'], $word ) && taxonomy_exists( 'pa_color' ) ) {
            $term = get_term_by( 'slug', $color, 'pa_color' );
            if ( ! $term ) {
                $created = wp_insert_term( ucfirst( $color ), 'pa_color', array( 'slug' => $color ) );
                if ( is_wp_error( $created ) ) { WP_CLI::error( $created->get_error_message() ); }
                $term = get_term( $created['term_id'], 'pa_color' );
            }
            $a = new WC_Product_Attribute();
            $a->set_id( wc_attribute_taxonomy_id_by_name( 'pa_color' ) );
            $a->set_name( 'pa_color' );
            $a->set_options( array( (int) $term->term_id ) );
            $a->set_visible( false );
            $attributes[] = $a;
            break;
        }
    }
    $product->set_attributes( $attributes );
    $product->set_default_attributes( array() );
    $categories = array();
    foreach ( array( 'shop', 'women', 'new-collection', 'top' === $row['category'] ? 'tops' : 'bottoms' ) as $cat ) {
        $term = get_term_by( 'slug', $cat, 'product_cat' );
        if ( $term ) { $categories[] = (int) $term->term_id; }
    }
    $product->set_category_ids( $categories );
    $photos = array_map( fn( $f ) => miroir_catalog_media( $f, $row['name'] ), $row['photos'] );
    $product->set_image_id( array_shift( $photos ) );
    $product->set_gallery_image_ids( $photos );
    $id = $product->save();
    delete_post_meta( $id, '_miroir_demo' );
    update_post_meta( $id, '_miroir_catalog_source', '189UsC34OMTTsrtorkxbQIUQT-GioP19L' );
    update_post_meta( $id, '_miroir_catalog_row', $row['source_row'] );
    if ( 'Lumi Pants' === $row['name'] ) { update_post_meta( $id, '_miroir_catalog_review', 'Source length 962 cm omitted; needs correction.' ); }
    wc_delete_product_transients( $id );
    $ids[] = $id;
    WP_CLI::log( $id . ': ' . $row['name'] );
}
// Only retire positively identified demo products, and only after all real products imported.
foreach ( get_posts( array( 'post_type' => 'product', 'post_status' => 'publish', 'numberposts' => -1, 'meta_key' => '_miroir_demo', 'fields' => 'ids' ) ) as $id ) {
    wp_update_post( array( 'ID' => $id, 'post_status' => 'draft' ) );
}
update_option( 'woocommerce_default_catalog_orderby', 'menu_order' );
update_option( 'miroir_catalog_product_ids', $ids, false );
WP_CLI::success( 'Imported ' . count( $ids ) . ' real products; remaining demo products moved to drafts.' );
