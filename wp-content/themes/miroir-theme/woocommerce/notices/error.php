<?php
/**
 * WooCommerce error notices as the lofi Notice (screen 41): surface box, mono title, body.
 *
 * @var array $notices
 */

defined( 'ABSPATH' ) || exit;

if ( ! $notices ) {
	return;
}

foreach ( $notices as $notice ) :
	?>
	<div class="notice notice--flat notice--error woocommerce-error" role="alert"<?php echo wc_get_notice_data_attr( $notice ); // phpcs:ignore WordPress.Security.EscapeOutput ?>>
		<p class="notice__title"><?php echo esc_html( miroir_notice_title( 'error', $notice['notice'] ) ); ?></p>
		<div class="notice__text"><?php echo wc_kses_notice( $notice['notice'] ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
	</div>
	<?php
endforeach;
