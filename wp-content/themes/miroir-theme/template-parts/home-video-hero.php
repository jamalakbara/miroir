<?php
/** Full-bleed campaign film; the poster is usable with no video, no JS or reduced motion. */
defined( 'ABSPATH' ) || exit;

$caption = trim( (string) ( $args['caption'] ?? '' ) ) ?: __( 'new arrivals: browse the collection', 'miroir' );
$url     = $args['url'] ?? miroir_cat_url( 'new-collection' );
$video   = esc_url( $args['video'] ?? '' );
$mobile  = esc_url( $args['videoMobile'] ?? '' );
$posters = array();
foreach ( array( 'desktop' => 'poster', 'tablet' => 'posterTablet', 'mobile' => 'posterMobile' ) as $device => $key ) {
	$posters[ $device ] = wp_get_attachment_image_url( (int) ( $args[ $key ] ?? 0 ), 'full' );
}
// A chosen desktop poster is also the fallback for unconfigured compact devices.
$posters['mobile']  = $posters['mobile'] ?: $posters['tablet'] ?: $posters['desktop'];
$posters['tablet']  = $posters['tablet'] ?: $posters['desktop'];
foreach ( $posters as $device => $poster ) {
	$posters[ $device ] = $poster ?: MIROIR_THEME_URI . '/assets/images/hero-video-poster-' . $device . '.webp';
}
?>
<section class="home-hero" aria-label="<?php esc_attr_e( 'Miroir campaign', 'miroir' ); ?>" data-video-hero>
	<div class="home-hero__media">
		<a class="home-hero__link" href="<?php echo esc_url( $url ); ?>" aria-label="<?php echo esc_attr( $caption ); ?>">
			<picture class="home-hero__poster">
				<source media="(max-width: 599px)" srcset="<?php echo esc_url( $posters['mobile'] ); ?>">
				<source media="(max-width: 1023px)" srcset="<?php echo esc_url( $posters['tablet'] ); ?>">
				<img src="<?php echo esc_url( $posters['desktop'] ); ?>" alt="" width="1440" height="640" fetchpriority="high" loading="eager">
			</picture>
			<?php if ( $video || $mobile ) : ?>
				<video class="home-hero__video" muted loop playsinline preload="none" aria-hidden="true" tabindex="-1" data-src="<?php echo esc_url( $video ?: $mobile ); ?>" data-mobile-src="<?php echo esc_url( $mobile ?: $video ); ?>"></video>
			<?php endif; ?>
		</a>
		<?php if ( $video || $mobile ) : ?>
			<button type="button" class="home-hero__toggle" hidden aria-label="<?php esc_attr_e( 'Play campaign video', 'miroir' ); ?>" data-play-label="<?php esc_attr_e( 'Play campaign video', 'miroir' ); ?>" data-pause-label="<?php esc_attr_e( 'Pause campaign video', 'miroir' ); ?>">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path class="home-hero__pause" d="M9 5v14M15 5v14"/><path class="home-hero__play" d="m8 5 11 7-11 7Z"/></svg>
			</button>
		<?php endif; ?>
	</div>
	<p class="home-caption"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $caption ); ?></a></p>
</section>
