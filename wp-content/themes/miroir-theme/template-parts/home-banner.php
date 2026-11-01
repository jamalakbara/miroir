<?php
/**
 * Full-width home banner (the sale banner of screen 01). Each breakpoint has
 * its own proportions, so it takes a desktop, tablet and mobile image; a
 * missing one falls back to the next larger.
 *
 * @var array $args { caption, url, image?, image_tablet?, image_mobile? (attachment ids) }
 */
defined( 'ABSPATH' ) || exit;

$desktop = (int) ( $args['image'] ?? 0 );
$tablet  = (int) ( $args['image_tablet'] ?? 0 );
$tablet  = $tablet ? $tablet : $desktop;
$mobile  = (int) ( $args['image_mobile'] ?? 0 );
$mobile  = $mobile ? $mobile : $tablet;
?>
<section class="home-editorial home-banner">
	<a href="<?php echo esc_url( $args['url'] ); ?>" aria-label="<?php echo esc_attr( $args['caption'] ); ?>">
		<?php if ( $desktop || $tablet || $mobile ) : ?>
			<div class="ph ph--filled home-banner__image">
				<picture>
					<?php
					foreach ( array( 599 => $mobile, 1023 => $tablet ) as $max => $id ) :
						if ( ! $id || ! wp_attachment_is_image( $id ) ) {
							continue;
						}
						$srcset = wp_get_attachment_image_srcset( $id, 'full' );
						?>
						<source media="(max-width: <?php echo (int) $max; ?>px)" srcset="<?php echo esc_attr( $srcset ? $srcset : wp_get_attachment_image_url( $id, 'full' ) ); ?>" sizes="100vw">
					<?php endforeach; ?>
					<?php echo wp_get_attachment_image( $desktop ? $desktop : $tablet, 'full', false, array( 'alt' => '', 'sizes' => '100vw' ) ); ?>
				</picture>
			</div>
		<?php else : ?>
			<?php miroir_ph( 'home-banner__image' ); ?>
		<?php endif; ?>
	</a>
	<p class="home-caption"><a href="<?php echo esc_url( $args['url'] ); ?>"><?php echo esc_html( $args['caption'] ); ?></a></p>
</section>
