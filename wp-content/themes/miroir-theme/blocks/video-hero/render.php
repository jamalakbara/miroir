<?php
/** Campaign video hero. @var array $attributes Block attributes. */
defined( 'ABSPATH' ) || exit;

get_template_part(
	'template-parts/home-video-hero',
	null,
	array_merge(
		$attributes,
		array( 'url' => $attributes['url'] ?: miroir_cat_url( 'new-collection' ) )
	)
);
