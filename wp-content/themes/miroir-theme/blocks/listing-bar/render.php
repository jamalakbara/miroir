<?php
/**
 * Collection title, result count and sort. Below 600px the filter & sort button
 * opens the modal panel (screen 33); from 600px the sort is a dropdown here and
 * the sizes live in the sidebar. Choices apply at once (see theme.js).
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$count   = (int) $wp_query->found_posts;
$sorts   = miroir_listing_sorts();
$sort    = miroir_listing_sort();
$sizes   = miroir_listing_sizes()['chosen'];
$filter  = miroir_text( 'filter_sort_link' );
if ( $sizes ) {
	$filter = preg_replace( '/\s*\+\s*$/', '', $filter ) . ' (' . count( $sizes ) . ') +';
}
?>
<div class="plp__bar" data-listing-bar>
	<p class="plp__bar-title"><?php echo esc_html( miroir_listing_title() ); ?></p>
	<p class="plp__bar-count" data-listing-count role="status"><?php echo esc_html( sprintf( /* translators: %d: number of products in the listing. */ _n( '%d item', '%d items', $count, 'miroir' ), $count ) ); ?></p>
	<a class="plp__bar-filter" href="<?php echo esc_url( add_query_arg( 'panel', 'filters' ) ); ?>" rel="nofollow" data-panel-open="filters" aria-controls="filter-panel" aria-expanded="false"><?php echo esc_html( $filter ); ?></a>
	<details class="plp__sort" data-sort-menu>
		<summary class="plp__sort-trigger"><?php echo esc_html( miroir_text( 'sort_label' ) . ': ' . $sorts[ $sort ] ); ?><?php echo miroir_icon( 'chevron-down', 12 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></summary>
		<ul class="plp__sort-menu">
			<?php foreach ( $sorts as $value => $label ) : ?>
				<li><a href="<?php echo esc_url( miroir_listing_url( array( 'orderby' => $value ) ) ); ?>" rel="nofollow" data-listing-link<?php echo (string) $value === $sort ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $label ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</details>
</div>
