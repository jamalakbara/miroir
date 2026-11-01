<?php
/**
 * Navigation bar: wraps the logo, navigation groups and menu button.
 *
 * @var string $content Inner blocks.
 */

defined( 'ABSPATH' ) || exit;
?>
<nav class="site-nav" aria-label="<?php esc_attr_e( 'Main', 'miroir' ); ?>">
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks. ?>
</nav>
