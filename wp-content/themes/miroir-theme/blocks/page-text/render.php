<?php
/**
 * Text column of a page built with Elementor: the Text page widget's text in
 * the same wrapper miroir/page-content prints the_content in.
 *
 * @var array    $attributes { html }
 * @var WP_Block $block      Context miroir/pageVariant.
 */

defined( 'ABSPATH' ) || exit;

$variant = $block->context['miroir/pageVariant'] ?? '';
$html    = do_shortcode( (string) $attributes['html'] );

if ( '' === $variant ) {
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput -- text set by an editor in Elementor.
	return;
}
printf( '<div class="%s">%s</div>', 'info' === $variant ? 'info__content' : 'flow__content', $html ); // phpcs:ignore WordPress.Security.EscapeOutput -- text set by an editor in Elementor.
