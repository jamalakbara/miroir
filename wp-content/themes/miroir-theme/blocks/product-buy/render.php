<?php
/**
 * Buy box: size picker, add to bag, order via WhatsApp (template-parts/product-buy).
 */

defined( 'ABSPATH' ) || exit;

$product = miroir_block_product();
if ( ! $product ) {
	return;
}
?>
<div class="pdp__buy">
	<?php get_template_part( 'template-parts/product-buy', null, array( 'product' => $product ) ); ?>
</div>
