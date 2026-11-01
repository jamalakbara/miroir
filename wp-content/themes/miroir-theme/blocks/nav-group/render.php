<?php
/**
 * One side of the navigation bar: .site-nav__primary (menu, search) or
 * .site-nav__utility (currency, account, bag).
 *
 * @var array  $attributes { slot }
 * @var string $content    Inner blocks.
 */

defined( 'ABSPATH' ) || exit;

$slot = 'utility' === $attributes['slot'] ? 'utility' : 'primary';
?>
<div class="site-nav__<?php echo esc_attr( $slot ); ?>">
	<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks. ?>
</div>
