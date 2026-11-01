<?php
/**
 * Search field at the top of the results page.
 */

defined( 'ABSPATH' ) || exit;
?>
<form class="search-bar" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="search-query"><?php echo esc_html( miroir_text( 'search_label' ) ); ?></label>
	<input class="search-bar__input" id="search-query" type="search" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off">
	<input type="hidden" name="post_type" value="product">
	<a class="search-bar__clear" href="<?php echo esc_url( add_query_arg( array( 's' => '', 'post_type' => 'product' ), home_url( '/' ) ) ); ?>" aria-label="<?php esc_attr_e( 'Clear search', 'miroir' ); ?>"><?php echo miroir_icon( 'x', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></a>
</form>
