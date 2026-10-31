<?php
/**
 * miroir/product-strip: home product row. The "view all" link defaults to the
 * collection (or the sale collection, or the shop).
 *
 * @var array $attributes Block attributes.
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wc_get_products' ) ) {
	return;
}

$url = $attributes['url'];
if ( ! $url ) {
	$url = 'category' === $attributes['source'] && $attributes['category'] ? miroir_cat_url( $attributes['category'] ) : ( 'sale' === $attributes['source'] ? miroir_cat_url( 'sale' ) : wc_get_page_permalink( 'shop' ) );
}

get_template_part(
	'template-parts/home-strip',
	null,
	array(
		'label'    => $attributes['label'],
		'url'      => $url,
		'columns'  => $attributes['columns'],
		'products' => miroir_strip_products( $attributes ),
	)
);
