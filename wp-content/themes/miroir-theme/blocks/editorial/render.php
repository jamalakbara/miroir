<?php
/**
 * miroir/editorial: the home editorial pair.
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

get_template_part(
	'template-parts/home-editorial',
	null,
	array(
		'layout'      => $attributes['layout'],
		'small_image' => $attributes['smallImage'],
		'large_image' => $attributes['largeImage'],
		'caption'     => $attributes['caption'],
		'url'         => $attributes['url'] ? $attributes['url'] : ( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ),
	)
);
