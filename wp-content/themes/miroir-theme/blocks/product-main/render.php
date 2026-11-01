<?php
/**
 * Product layout: details column, spacer and gallery side by side (.pdp grid).
 *
 * @var string $content Inner blocks.
 */

defined( 'ABSPATH' ) || exit;

$product = miroir_block_product();
?>
<div class="pdp" id="product-<?php echo (int) ( $product ? $product->get_id() : 0 ); ?>">
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks. ?>
</div>
