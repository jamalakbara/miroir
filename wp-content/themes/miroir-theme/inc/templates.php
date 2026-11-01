<?php
/**
 * Block templates (templates/, parts/): data upgrades from the classic theme.
 */

defined( 'ABSPATH' ) || exit;

/** Classic page template files => block template slugs (theme.json customTemplates). */
const MIROIR_PAGE_TEMPLATES = array(
	'page-templates/faq.php'   => 'page-faq',
	'page-templates/guide.php' => 'page-guide',
);

// Pages that used the classic FAQ / Guide templates point at their block templates (runs once).
add_action(
	'init',
	function () {
		if ( (int) get_option( 'miroir_block_templates' ) >= 1 ) {
			return;
		}
		global $wpdb;
		foreach ( MIROIR_PAGE_TEMPLATES as $old => $new ) {
			$wpdb->update( $wpdb->postmeta, array( 'meta_value' => $new ), array( 'meta_key' => '_wp_page_template', 'meta_value' => $old ) ); // phpcs:ignore WordPress.DB.SlowDBQuery
		}
		wp_cache_flush_group( 'post_meta' );
		update_option( 'miroir_block_templates', 1 );
	}
);
