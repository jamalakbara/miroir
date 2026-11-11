<?php
/** Full-bleed campaign film; the poster is usable with no video, no JS or reduced motion. */
defined( 'ABSPATH' ) || exit;

$caption = trim( (string) ( $args['caption'] ?? '' ) ) ?: __( 'new arrivals: a little something new, made to be loved.', 'miroir' );
$url     = $args['url'] ?? miroir_cat_url( 'new-collection' );
// Bundled media is versioned by mtime so replaced files bypass browser caches.
$bundled = static fn( $file ) => MIROIR_THEME_URI . $file . '?ver=' . filemtime( MIROIR_THEME_DIR . $file );
// Uploaded films win; otherwise the bundled Miroir campaign films play.
$video   = esc_url( ( $args['video'] ?? '' ) ?: $bundled( '/assets/video/hero-desktop.mp4' ) );
$mobile  = esc_url( ( $args['videoMobile'] ?? '' ) ?: $bundled( '/assets/video/hero-mobile.mp4' ) );
$posters = array();
foreach ( array( 'desktop' => 'poster', 'tablet' => 'posterTablet', 'mobile' => 'posterMobile' ) as $device => $key ) {
	$posters[ $device ] = wp_get_attachment_image_url( (int) ( $args[ $key ] ?? 0 ), 'full' );
}
// A chosen desktop poster is also the fallback for unconfigured compact devices.
$posters['mobile']  = $posters['mobile'] ?: $posters['tablet'] ?: $posters['desktop'];
$posters['tablet']  = $posters['tablet'] ?: $posters['desktop'];
foreach ( $posters as $device => $poster ) {
	$posters[ $device ] = $poster ?: $bundled( '/assets/images/hero-video-poster-' . $device . '.webp' );
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
	</div>
	<p class="home-caption"><a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $caption ); ?></a></p>
</section>
