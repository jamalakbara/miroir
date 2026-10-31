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
			<p class="maint__status"><?php echo esc_html( miroir_text( 'maint_status' ) ); ?></p>
			<h1 class="maint__headline"><?php echo esc_html( miroir_text( 'maint_headline' ) ); ?></h1>
			<p class="maint__copy"><?php echo esc_html( miroir_text( 'maint_copy' ) ); ?></p>
			<section class="maint__section">
				<h2 class="maint__label"><?php echo esc_html( miroir_text( 'maint_label' ) ); ?></h2>
				<p class="maint__copy"><?php echo esc_html( miroir_text( 'maint_help' ) ); ?></p>
			</section>
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( $wa_url ); ?>" target="_blank" rel="noopener"><?php echo esc_html( miroir_text( 'maint_whatsapp' ) ); ?></a>
			<a class="btn btn--block" href="<?php echo esc_url( home_url( add_query_arg( array() ) ) ); ?>"><?php echo esc_html( miroir_text( 'maint_retry' ) ); ?></a>
			<?php if ( miroir_mod( 'instagram_url' ) ) : ?>
				<a class="maint__social" href="<?php echo esc_url( miroir_mod( 'instagram_url' ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( miroir_text( 'maint_social' ) ); ?></a>
			<?php endif; ?>
		</div>
	</main>
	<p class="maint__copyright"><?php echo esc_html( miroir_copyright() ); ?><?php echo '' !== miroir_text( 'maint_place' ) ? ' · ' . esc_html( miroir_text( 'maint_place' ) ) : ''; ?></p>
</div>
<?php wp_footer(); ?>
</body>
</html>
