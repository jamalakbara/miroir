<?php
/**
 * Home (screen 01): editorial pairs, product strips and the sale banner.
 * Section copy and targets come from miroir_home_sections() (filterable).
 */

defined( 'ABSPATH' ) || exit;

get_header();

foreach ( miroir_home_sections() as $section ) {
	switch ( $section['type'] ) {
		case 'editorial':
			get_template_part( 'template-parts/home-editorial', null, $section );
			break;

		case 'banner':
			?>
			<section class="home-editorial home-banner">
				<a href="<?php echo esc_url( $section['url'] ); ?>" aria-label="<?php echo esc_attr( $section['caption'] ); ?>"><?php miroir_ph( 'home-banner__image' ); ?></a>
				<p class="home-caption"><a href="<?php echo esc_url( $section['url'] ); ?>"><?php echo esc_html( $section['caption'] ); ?></a></p>
			</section>
			<?php
			break;

		case 'strip':
			$products = function_exists( 'wc_get_products' ) ? wc_get_products(
				array(
					'status'   => 'publish',
					'limit'    => $section['limit'],
					'category' => array( $section['category'] ),
					'orderby'  => 'date',
					'order'    => 'DESC',
				)
			) : array();
			if ( ! $products ) {
				break;
			}
			$strip_id = wp_unique_id( 'home-products-' );
			$columns  = $section['columns'] ?? ( 6 === (int) $section['limit'] ? 6 : 5 );
			?>
			<section class="home-strip home-strip--<?php echo (int) $columns; ?>" data-product-carousel aria-label="<?php echo esc_attr( $section['label'] ); ?>">
				<div class="home-strip__header">
					<?php miroir_section_link( $section['label'], $section['url'] ); ?>
					<div class="home-strip__controls" data-carousel-controls hidden>
						<button type="button" class="home-strip__button" data-carousel-prev aria-controls="<?php echo esc_attr( $strip_id ); ?>" aria-label="<?php esc_attr_e( 'Previous products', 'miroir' ); ?>"><svg width="24" height="12" viewBox="0 0 24 12" fill="none" stroke="currentColor" aria-hidden="true"><path d="M24 6H1m5-5L1 6l5 5"/></svg></button>
						<button type="button" class="home-strip__button" data-carousel-next aria-controls="<?php echo esc_attr( $strip_id ); ?>" aria-label="<?php esc_attr_e( 'Next products', 'miroir' ); ?>"><svg width="24" height="12" viewBox="0 0 24 12" fill="none" stroke="currentColor" aria-hidden="true"><path d="M0 6h23m-5-5l5 5-5 5"/></svg></button>
					</div>
				</div>
				<div class="grid home-strip__grid" id="<?php echo esc_attr( $strip_id ); ?>" data-carousel-track tabindex="0" role="group" aria-label="<?php echo esc_attr( $section['label'] ); ?>"><?php miroir_cards( $products ); ?></div>
			</section>
			<?php
			break;
	}
}

get_footer();
