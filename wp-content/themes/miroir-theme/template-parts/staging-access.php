<?php
/**
 * Private access page (screen S1). Standalone document printed by
 * inc/staging-access.php for visitors without access.
 *
 * @var array $args { error: text id of the message to show, or empty. }
 */

defined( 'ABSPATH' ) || exit;

$error = isset( $args['error'] ) ? (string) $args['error'] : '';
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
			<p class="maint__status"><?php echo esc_html( miroir_text( 'staging_status' ) ); ?></p>
			<h1 class="maint__headline"><?php echo esc_html( miroir_text( 'staging_headline' ) ); ?></h1>
			<p class="maint__copy"><?php echo esc_html( miroir_text( 'staging_copy' ) ); ?></p>
			<form class="maint__form" method="post">
				<div class="field">
					<label class="field__label" for="miroir-staging-password"><?php echo esc_html( miroir_text( 'staging_label' ) ); ?></label>
					<span class="field__control">
						<input class="field__input field__input--icon" id="miroir-staging-password" name="miroir_staging_password" type="password" autocomplete="current-password" required autofocus<?php echo $error ? ' aria-invalid="true" aria-describedby="miroir-staging-error"' : ''; ?>>
						<button class="field__toggle" type="button" data-password-toggle aria-controls="miroir-staging-password" aria-pressed="false" aria-label="<?php esc_attr_e( 'Show password', 'miroir' ); ?>"><?php echo miroir_icon( 'eye-off', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></button>
					</span>
				</div>
				<?php if ( $error ) : ?>
					<p class="field-error" id="miroir-staging-error" role="alert"><?php echo esc_html( miroir_text( $error ) ); ?></p>
				<?php endif; ?>
				<button class="btn btn--primary btn--block" type="submit"><?php echo esc_html( miroir_text( 'staging_button' ) ); ?></button>
			</form>
		</div>
	</main>
	<p class="maint__copyright"><?php echo esc_html( miroir_copyright() ); ?><?php echo '' !== miroir_text( 'maint_place' ) ? ' · ' . esc_html( miroir_text( 'maint_place' ) ) : ''; ?></p>
</div>
<?php wp_footer(); ?>
</body>
</html>
