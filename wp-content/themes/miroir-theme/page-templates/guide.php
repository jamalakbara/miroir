<?php
/**
 * Template Name: Guide
 *
 * Content column with the tighter rhythm of screens 12-21 and 37 (size guide):
 * title 42px, copy 20px lines. Content comes from blocks, as on other pages.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	?>
	<div class="flow flow--guide">
		<article class="flow__col">
			<h1 class="flow__title"><?php the_title(); ?></h1>
			<div class="flow__content"><?php the_content(); ?></div>
		</article>
	</div>
	<?php
}

get_footer();
