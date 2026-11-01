<?php
/**
 * Row of related products under the product page.
 *
 * @var array $attributes { count: -1 = Customize > Products > Similar products shown }
 */

defined( 'ABSPATH' ) || exit;

$product = miroir_block_product();
$count   = (int) $attributes['count'] >= 0 ? (int) $attributes['count'] : (int) miroir_mod( 'related_count' );
$related = $product && $count ? wc_get_related_products( $product->get_id(), $count ) : array();
if ( ! $related ) {
	return;
}
?>
<section class="similar">
	<h2 class="similar__title"><?php echo esc_html( miroir_text( 'similar_title' ) ); ?></h2>
	<div class="grid similar__grid"><?php miroir_cards( array_filter( array_map( 'wc_get_product', $related ) ) ); ?></div>
</section>
