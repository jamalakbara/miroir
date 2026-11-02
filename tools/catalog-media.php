<?php
/** Idempotent import of the supplied media into the WordPress library. */
defined( 'ABSPATH' ) || exit;
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
function miroir_catalog_media( $file, $title ) {
    $found = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 1, 'meta_key' => '_miroir_catalog_file', 'meta_value' => $file, 'fields' => 'ids' ) );
    if ( $found ) { return (int) $found[0]; }
    $source = WP_CONTENT_DIR . '/uploads/miroir-catalog-source/' . $file;
    if ( ! is_file( $source ) ) { WP_CLI::error( 'Missing media: ' . $file ); }
    $tmp = wp_tempnam( $file );
    copy( $source, $tmp );
    $id = media_handle_sideload( array( 'name' => $file, 'tmp_name' => $tmp ), 0, $title );
    if ( is_wp_error( $id ) ) { WP_CLI::error( $id->get_error_message() ); }
    update_post_meta( $id, '_miroir_catalog_file', $file );
    update_post_meta( $id, '_wp_attachment_image_alt', $title );
    return $id;
}
