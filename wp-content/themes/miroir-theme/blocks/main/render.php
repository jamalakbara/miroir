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
if ( ! miroir_block_preview() ) {
	get_template_part( 'template-parts/panels' );
}
echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks.
?>
</main>
