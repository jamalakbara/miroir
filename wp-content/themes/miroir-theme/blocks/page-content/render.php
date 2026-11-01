<?php
/**
 * The page's own blocks (Pages > edit). Inside the page layout block it is the
 * text column; on its own it prints the content bare (home, bag, checkout,
 * account). A home page without Miroir blocks shows miroir_home_sections().
 *
 * @var WP_Block $block Context miroir/pageVariant.
 */

defined( 'ABSPATH' ) || exit;

$variant = $block->context['miroir/pageVariant'] ?? '';

if ( miroir_block_preview() && 'page' !== get_post_type() ) {
	// Site Editor: the home template shows the front page's blocks, other templates a note.
	$front = (int) get_option( 'page_on_front' );
	$html  = '' === $variant && $front ? apply_filters( 'the_content', get_post_field( 'post_content', $front ) ) : '';
	if ( '' === $html ) {
		$html = '<p class="miroir-block-empty">' . esc_html__( 'Page content: edit it in Pages.', 'miroir' ) . '</p>';
	}
} elseif ( is_front_page() && ! miroir_front_page_id() ) {
	$html = miroir_home_fallback();
} else {
	$html = miroir_block_page_content();
}

if ( '' === $variant ) {
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- the_content output.
	return;
}
printf( '<div class="%s">%s</div>', 'info' === $variant ? 'info__content' : 'flow__content', $html ); // phpcs:ignore WordPress.Security.EscapeOutput -- the_content output.
