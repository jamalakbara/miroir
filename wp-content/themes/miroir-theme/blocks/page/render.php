<?php
/**
 * Content column of info pages (screens 22-29), guides (12-21, 37) and the
 * FAQ (11). Bag, checkout and account pages print only their content instead
 * (pre_render_block in inc/block-helpers.php): the theme's woocommerce/
 * templates draw those screens.
 *
 * @var array  $attributes { variant: loose | guide | info }
 * @var string $content    Inner blocks (title, content, anything added).
 */

defined( 'ABSPATH' ) || exit;

if ( 'info' === $attributes['variant'] ) :
	?>
	<div class="wrap info">
		<article class="wrap__col info__col">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks. ?>
		</article>
	</div>
	<?php
else :
	?>
	<div class="flow flow--<?php echo 'guide' === $attributes['variant'] ? 'guide' : 'loose'; ?>">
		<article class="flow__col">
			<?php echo $content; // phpcs:ignore WordPress.Security.EscapeOutput -- rendered blocks. ?>
		</article>
	</div>
	<?php
endif;
