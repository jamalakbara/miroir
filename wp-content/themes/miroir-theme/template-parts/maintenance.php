<?php
/**
 * Maintenance page (screen 30): logo, centred message, WhatsApp help, copyright.
 * Standalone document: no header, footer or floating button.
 */

defined( 'ABSPATH' ) || exit;

$wa_url = apply_filters( 'miroir_whatsapp_url', 'https://wa.me/' . preg_replace( '/\D/', '', (string) get_option( 'miroir_wa_number', '' ) ) );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'maintenance-mode' ); ?>>
<div class="maint">
	<div class="maint__brand"><span class="site-nav__logo"><?php miroir_logo(); ?></span></div>
	<main class="maint__main">
		<div class="maint__col">
			<p class="maint__status"><?php esc_html_e( 'A LITTLE PAUSE', 'miroir' ); ?></p>
			<h1 class="maint__headline"><?php esc_html_e( 'We’ll be back soon.', 'miroir' ); ?></h1>
			<p class="maint__copy"><?php esc_html_e( 'Our store is taking a short break for maintenance. Thank you for your patience while we make a few updates.', 'miroir' ); ?></p>
			<section class="maint__section">
				<h2 class="maint__label"><?php esc_html_e( 'Already placed an order?', 'miroir' ); ?></h2>
				<p class="maint__copy"><?php esc_html_e( 'Our team can still help with your existing order on WhatsApp.', 'miroir' ); ?></p>
			</section>
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Contact us on WhatsApp', 'miroir' ); ?></a>
			<a class="btn btn--block" href="<?php echo esc_url( home_url( add_query_arg( array() ) ) ); ?>"><?php esc_html_e( 'Try again', 'miroir' ); ?></a>
			<a class="maint__social" href="https://www.instagram.com/miroir.id/" target="_blank" rel="noopener"><?php esc_html_e( 'Follow miroir on Instagram ↗', 'miroir' ); ?></a>
		</div>
	</main>
	<p class="maint__copyright">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( strtolower( get_bloginfo( 'name' ) ) ); ?> · <?php esc_html_e( 'Indonesia', 'miroir' ); ?></p>
</div>
<?php wp_footer(); ?>
</body>
</html>
