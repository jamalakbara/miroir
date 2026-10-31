<?php
/**
 * Product page (screen 03): narrow info column, centred placeholder image
 * stack, similar products row. Replaces WooCommerce's single-product layout.
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$product = wc_get_product( get_the_ID() );
	if ( ! $product ) {
		continue;
	}
	$state = miroir_product_state( $product );
	if ( $state ) {
		get_template_part( 'template-parts/product-state', null, array( 'product' => $product, 'state' => $state ) );
		continue;
	}
	$views   = max( 4, 1 + count( $product->get_gallery_image_ids() ) );
	$related = wc_get_related_products( $product->get_id(), 7 );
	?>
	<div class="pdp" id="product-<?php the_ID(); ?>">
		<div class="pdp__info">
			<?php woocommerce_output_all_notices(); ?>
			<h1 class="pdp__title"><?php the_title(); ?></h1>

			<div class="pdp__desc"><?php the_content(); ?></div>

			<p class="pdp__price"><?php echo miroir_card_price( $product ); // phpcs:ignore WordPress.Security.EscapeOutput -- built from wc_price(). ?></p>

			<div class="pdp__buy">
				<?php get_template_part( 'template-parts/product-buy', null, array( 'product' => $product ) ); ?>
			</div>

			<nav class="pdp__pager" aria-label="<?php esc_attr_e( 'More products', 'miroir' ); ?>">
				<?php
				$prev = get_adjacent_post( true, '', true, 'product_cat' );
				$next = get_adjacent_post( true, '', false, 'product_cat' );
				?>
				<?php if ( $prev ) : ?>
					<a href="<?php echo esc_url( get_permalink( $prev ) ); ?>" rel="prev"><?php esc_html_e( 'Previous product', 'miroir' ); ?></a>
				<?php else : ?>
					<span aria-disabled="true"><?php esc_html_e( 'Previous product', 'miroir' ); ?></span>
				<?php endif; ?>
				<?php if ( $next ) : ?>
					<a href="<?php echo esc_url( get_permalink( $next ) ); ?>" rel="next"><?php esc_html_e( 'Next product', 'miroir' ); ?></a>
				<?php else : ?>
					<span aria-disabled="true"><?php esc_html_e( 'Next product', 'miroir' ); ?></span>
				<?php endif; ?>
			</nav>
		</div>

		<div class="pdp__spacer" aria-hidden="true"></div>

		<div class="pdp__gallery">
			<div class="pdp__lead">
				<?php miroir_ph( 'pdp__view', $product->get_name() ); ?>
				<button class="pdp__zoom" type="button" data-zoom aria-label="<?php esc_attr_e( 'Enlarge images', 'miroir' ); ?>"><?php echo miroir_icon( 'maximize-2', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></button>
			</div>
			<div class="pdp__more">
				<?php for ( $i = 1; $i < $views; $i++ ) : ?>
					<?php miroir_ph( 'pdp__view' ); ?>
				<?php endfor; ?>
			</div>
		</div>
	</div>

	<?php if ( $related ) : ?>
		<section class="similar">
			<h2 class="similar__title"><?php esc_html_e( 'Find similar product', 'miroir' ); ?></h2>
			<div class="grid similar__grid"><?php miroir_cards( array_filter( array_map( 'wc_get_product', $related ) ) ); ?></div>
		</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
