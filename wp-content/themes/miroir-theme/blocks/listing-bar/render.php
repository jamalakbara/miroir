<?php
/**
 * Collection title, result count and the Size and Sort by dropdowns. Both are
 * <details> panels that open under the bar (one at a time, see theme.js), so they
 * also work without JavaScript. Choices apply at once.
 */

defined( 'ABSPATH' ) || exit;

global $wp_query;

$count = (int) $wp_query->found_posts;
$sorts = miroir_listing_sorts();
$sort  = miroir_listing_sort();
$sizes = miroir_listing_sizes();
$size  = miroir_text( 'filter_size_panel' );
$size  = ucfirst( mb_strtolower( $size ) );
if ( $sizes['chosen'] ) {
	$size .= ' (' . count( $sizes['chosen'] ) . ')';
}
?>
<div class="plp__bar" data-listing-bar>
	<p class="plp__bar-title"><?php echo esc_html( miroir_listing_title() ); ?></p>
	<p class="plp__bar-count" data-listing-count role="status"><?php echo esc_html( sprintf( /* translators: %d: number of products in the listing. */ _n( '%d item', '%d items', $count, 'miroir' ), $count ) ); ?></p>
	<?php if ( $sizes['terms'] ) : ?>
		<details class="plp__dd plp__dd--size" data-dd="size">
			<summary class="plp__dd-trigger"><span><?php echo esc_html( $size ); ?></span><?php echo miroir_icon( 'chevron-down', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></summary>
			<div class="plp__dd-panel" role="group" aria-label="<?php echo esc_attr( miroir_text( 'filter_size_panel' ) ); ?>">
				<?php foreach ( $sizes['terms'] as $term ) : ?>
					<?php
					$on   = in_array( $term->slug, $sizes['chosen'], true );
					$next = $on ? array_values( array_diff( $sizes['chosen'], array( $term->slug ) ) ) : array_merge( $sizes['chosen'], array( $term->slug ) );
					?>
					<a class="plp__dd-option filter-check" href="<?php echo esc_url( miroir_listing_url( array( 'filter_size' => $next ) ) ); ?>" rel="nofollow" role="checkbox" aria-checked="<?php echo $on ? 'true' : 'false'; ?>" data-size="<?php echo esc_attr( $term->slug ); ?>" data-listing-link><?php echo esc_html( mb_strtolower( $term->name ) ); ?></a>
				<?php endforeach; ?>
				<?php if ( $sizes['chosen'] ) : ?>
					<a class="plp__dd-option plp__dd-clear" href="<?php echo esc_url( miroir_listing_url( array( 'filter_size' => null ) ) ); ?>" rel="nofollow" data-size="" data-listing-link><?php echo esc_html( miroir_text( 'size_clear' ) ); ?></a>
				<?php endif; ?>
			</div>
		</details>
	<?php endif; ?>
	<details class="plp__dd plp__dd--sort" data-dd="sort">
		<summary class="plp__dd-trigger"><span><?php echo esc_html( miroir_text( 'sort_label' ) ); ?></span><span class="plp__dd-value"><?php echo esc_html( $sorts[ $sort ] ); ?></span><?php echo miroir_icon( 'chevron-down', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></summary>
		<div class="plp__dd-panel" role="group" aria-label="<?php echo esc_attr( miroir_text( 'sort_label' ) ); ?>">
			<?php foreach ( $sorts as $value => $label ) : ?>
				<a class="plp__dd-option" href="<?php echo esc_url( miroir_listing_url( array( 'orderby' => $value ) ) ); ?>" rel="nofollow" data-sort-option data-listing-link<?php echo (string) $value === $sort ? ' aria-current="true"' : ''; ?>><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</div>
	</details>
</div>
