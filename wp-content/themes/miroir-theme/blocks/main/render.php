<?php
/**
 * <main> of every template. The menu, search, filter and currency panels are
 * printed first (template-parts/panels); an open filter panel hides
 * the sections after it (.site-main.has-panel in flow.css).
 *
 * @var string $content Inner blocks.
 */

defined( 'ABSPATH' ) || exit;
?>
<main id="main" class="site-main<?php echo 'filters' === miroir_open_panel() ? ' has-panel' : ''; ?>">
<?php
// Home and collections draw no page title on desktop; search engines and screen readers still get one.
$miroir_h1 = miroir_block_preview() ? '' : miroir_seo_hidden_h1();
if ( '' !== $miroir_h1 ) {
	printf( '<h1 class="screen-reader-text">%s</h1>', esc_html( $miroir_h1 ) );
}
if ( ! miroir_block_preview() ) {
	get_template_part( 'template-parts/panels' );
}
echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks.
?>
</main>
