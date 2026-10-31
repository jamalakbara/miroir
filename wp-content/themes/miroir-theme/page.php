<?php
/**
 * Pages. WooCommerce pages (bag, checkout, account) draw their own layout in
 * the theme's woocommerce/ templates; other pages use the content column of
 * screens 22-29, filled from the page's blocks.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	if ( function_exists( 'is_woocommerce' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
		the_content();
		continue;
	}
	?>
	<div class="flow flow--loose">
		<article class="flow__col">
			<h1 class="flow__title"><?php the_title(); ?></h1>
			<div class="flow__content"><?php the_content(); ?></div>
		</article>
	</div>
	<?php
}

get_footer();
