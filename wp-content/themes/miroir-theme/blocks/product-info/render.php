<?php
/**
 * Notices, product name, description and price.
 */

defined( 'ABSPATH' ) || exit;

$product = miroir_block_product();
if ( ! $product ) {
	return;
}
if ( miroir_block_preview() ) {
	// The sample product is the current post here: Elementor's editor fills the_content of the
	// edited template, which would print the template again inside the description.
	$current         = $GLOBALS['post'] ?? null;
	$GLOBALS['post'] = get_post( $product->get_id() ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride
	$description     = apply_filters( 'the_content', $product->get_description() );
	$GLOBALS['post'] = $current; // phpcs:ignore WordPress.WP.GlobalVariablesOverride
} else {
	ob_start();
	the_content();
	$description = ob_get_clean();
}
echo miroir_block_notices(); // phpcs:ignore WordPress.Security.EscapeOutput -- WooCommerce notice templates.
?>
<h1 class="pdp__title"><?php echo get_the_title( $product->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput -- as the_title(). ?></h1>

<div class="pdp__desc"><?php echo $description; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content output. ?></div>

<p class="pdp__price"><?php echo miroir_card_price( $product ); // phpcs:ignore WordPress.Security.EscapeOutput -- built from wc_price(). ?></p>
