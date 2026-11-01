<?php
/**
 * Product page (screen 03). A sold-out product, a missing size choice
 * (?choose-size) or a fresh add to bag (?added) shows the state screen
 * instead (template-parts/product-state, screens 38, 44, 39; decided before
 * the sections render, see pre_render_block in inc/block-helpers.php).
 *
 * @var string $content Inner blocks.
 */

defined( 'ABSPATH' ) || exit;

if ( ! miroir_block_product() ) {
	return;
}
echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks.
