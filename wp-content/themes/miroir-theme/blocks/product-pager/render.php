<?php
/**
 * Previous / next product in the same collection.
 */

defined( 'ABSPATH' ) || exit;

$preview = miroir_block_preview();
$prev    = $preview ? null : get_adjacent_post( true, '', true, 'product_cat' );
$next    = $preview ? null : get_adjacent_post( true, '', false, 'product_cat' );
?>
<nav class="pdp__pager" aria-label="<?php esc_attr_e( 'More products', 'miroir' ); ?>">
	<?php if ( $prev ) : ?>
		<a href="<?php echo esc_url( get_permalink( $prev ) ); ?>" rel="prev"><?php echo esc_html( miroir_text( 'prev_product' ) ); ?></a>
	<?php else : ?>
		<span aria-disabled="true"><?php echo esc_html( miroir_text( 'prev_product' ) ); ?></span>
	<?php endif; ?>
	<?php if ( $next ) : ?>
		<a href="<?php echo esc_url( get_permalink( $next ) ); ?>" rel="next"><?php echo esc_html( miroir_text( 'next_product' ) ); ?></a>
	<?php else : ?>
		<span aria-disabled="true"><?php echo esc_html( miroir_text( 'next_product' ) ); ?></span>
	<?php endif; ?>
</nav>
