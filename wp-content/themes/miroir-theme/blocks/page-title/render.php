<?php
/**
 * Page title inside the page layout block.
 *
 * @var WP_Block $block Context miroir/pageVariant.
 */

defined( 'ABSPATH' ) || exit;

$title = get_the_ID() ? get_the_title() : __( 'Page title', 'miroir' );
printf( '<h1 class="%s">%s</h1>', 'info' === ( $block->context['miroir/pageVariant'] ?? '' ) ? 'page-title' : 'flow__title', $title ); // phpcs:ignore WordPress.Security.EscapeOutput -- get_the_title() as the_title() prints it.
