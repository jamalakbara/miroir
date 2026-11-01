<?php
/**
 * First footer line (location).
 *
 * @var array $attributes { text, show }
 */

defined( 'ABSPATH' ) || exit;

$text = '' !== trim( $attributes['text'] ) ? $attributes['text'] : get_theme_mod( 'miroir_location', __( 'Location : Indonesia', 'miroir' ) );
if ( empty( $attributes['show'] ) || '' === trim( (string) $text ) ) {
	return;
}
?>
<p class="site-footer__location"><?php echo esc_html( $text ); ?></p>
