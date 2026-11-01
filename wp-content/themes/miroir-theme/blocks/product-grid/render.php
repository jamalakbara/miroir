<?php
/**
 * Product cards of the shop / collection with the load more button.
 */

defined( 'ABSPATH' ) || exit;

$products = miroir_block_preview() || woocommerce_product_loop() ? miroir_block_listing_products() : array();
?>
<div class="plp__main">
	<?php echo miroir_block_notices(); // phpcs:ignore WordPress.Security.EscapeOutput -- WooCommerce notice templates. ?>
	<?php if ( $products ) : ?>
		<div class="grid plp__grid" data-load-more-list>
			<?php miroir_cards( $products ); ?>
		</div>
		<?php echo miroir_block_load_more(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped helper markup. ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/empty-results', null, array( 'context' => 'collection' ) ); ?>
	<?php endif; ?>
</div>
