<?php
/**
 * Shared editorial pair. Orientation changes image order, never geometry.
 *
 * @var array $args { layout, caption, url, small_image?, large_image? (attachment ids) }
 */
defined( 'ABSPATH' ) || exit;

$layout = 'large-first' === ( $args['layout'] ?? '' ) ? 'large-first' : 'small-first';
$sizes  = 'large-first' === $layout ? array( 'large', 'small' ) : array( 'small', 'large' );
?>
<section class="home-editorial home-editorial--<?php echo esc_attr( $layout ); ?>">
	<a class="home-editorial__row" href="<?php echo esc_url( $args['url'] ); ?>" aria-label="<?php echo esc_attr( $args['caption'] ); ?>">
		<?php foreach ( $sizes as $size ) : ?>
			<?php if ( 'small' === $size ) : ?>
				<div class="home-editorial__small-col"><?php miroir_image( (int) ( $args['small_image'] ?? 0 ), 'large', 'home-editorial__small' ); ?></div>
			<?php else : ?>
				<?php miroir_image( (int) ( $args['large_image'] ?? 0 ), 'full', 'home-editorial__large' ); ?>
			<?php endif; ?>
		<?php endforeach; ?>
	</a>
	<p class="home-caption"><a href="<?php echo esc_url( $args['url'] ); ?>"><?php echo esc_html( $args['caption'] ); ?></a></p>
</section>
