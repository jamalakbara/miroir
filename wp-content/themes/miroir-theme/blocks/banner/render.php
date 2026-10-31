<?php
/**
 * miroir/banner: full-width home banner.
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

get_template_part(
	'template-parts/home-banner',
	null,
	array(
		'image'        => $attributes['image'],
		'image_tablet' => $attributes['imageTablet'],
		'image_mobile' => $attributes['imageMobile'],
		'caption'      => $attributes['caption'],
		'url'          => $attributes['url'] ? $attributes['url'] : miroir_cat_url( 'sale' ),
	)
);
