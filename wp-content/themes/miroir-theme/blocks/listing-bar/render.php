<?php
/**
 * Collection title and the filter & sort button (opens the filter panel, screen 33).
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="plp__bar">
	<p class="plp__bar-title"><?php echo esc_html( miroir_listing_title() ); ?></p>
	<a class="plp__bar-filter" href="<?php echo esc_url( add_query_arg( 'panel', 'filters' ) ); ?>" data-panel-open="filters" aria-controls="filter-panel" aria-expanded="false"><?php echo esc_html( miroir_text( 'filter_sort_link' ) ); ?></a>
</div>
