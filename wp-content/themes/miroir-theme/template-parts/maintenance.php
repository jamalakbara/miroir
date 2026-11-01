<?php
/**
 * Maintenance page (screen 30). Standalone document: no header, footer or
 * floating button. The body is template-parts/maintenance-body, or the
 * Elementor template chosen for the maintenance page.
 */

defined( 'ABSPATH' ) || exit;

$template = function_exists( 'miroir_elementor_template' ) ? miroir_elementor_template( 'maintenance' ) : 0;
$body     = $template ? miroir_elementor_render( $template ) : '';
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="noindex">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'maintenance-mode' ); ?>>
<?php
if ( $template ) {
	echo $body; // phpcs:ignore WordPress.Security.EscapeOutput -- Elementor template.
} else {
	get_template_part( 'template-parts/maintenance-body' );
}
wp_footer();
?>
</body>
</html>
