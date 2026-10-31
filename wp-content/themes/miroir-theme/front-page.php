<?php
/**
 * Home (screen 01): editorial pairs, product strips and the sale banner.
 * The front page is built from Miroir blocks (Pages > Home); until it has
 * any, the sections of miroir_home_sections() (filterable) are shown.
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( miroir_front_page_id() ) {
	while ( have_posts() ) {
		the_post();
		the_content();
	}
} else {
	foreach ( miroir_home_sections() as $section ) {
		switch ( $section['type'] ) {
			case 'editorial':
				get_template_part( 'template-parts/home-editorial', null, $section );
				break;

			case 'banner':
				get_template_part( 'template-parts/home-banner', null, $section );
				break;

			case 'strip':
				$section['products'] = function_exists( 'wc_get_products' ) ? miroir_strip_products( $section ) : array();
				$section['columns']  = $section['columns'] ?? ( 6 === (int) $section['limit'] ? 6 : 5 );
				get_template_part( 'template-parts/home-strip', null, $section );
				break;
		}
	}
}

get_footer();
