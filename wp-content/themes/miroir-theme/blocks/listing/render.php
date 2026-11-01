<?php
/**
 * Product listing: shop / collection (screen 02) or search results (screen 10).
 * An empty result shows its own screen instead (35 search, 36 collection;
 * decided before the sections render, see pre_render_block in
 * inc/block-helpers.php). The lofi has no breadcrumb, result count or sort
 * dropdown on the listing (sorting lives in the filter panel).
 *
 * @var array  $attributes { layout: plp | search }
 * @var string $content    Inner blocks.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="<?php echo 'search' === $attributes['layout'] ? 'search-body' : 'plp'; ?>">
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks. ?>
</div>
