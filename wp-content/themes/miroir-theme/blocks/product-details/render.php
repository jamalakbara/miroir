<?php
/**
 * Narrow details column of the product page.
 *
 * @var string $content Inner blocks.
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="pdp__info">
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks. ?>
</div>
