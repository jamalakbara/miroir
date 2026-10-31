<?php
/**
 * Template Name: FAQ
 *
 * Screen 11: accordion of Details blocks under the title.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) {
	the_post();
	get_template_part( 'template-parts/info-page' );
}

get_footer();
