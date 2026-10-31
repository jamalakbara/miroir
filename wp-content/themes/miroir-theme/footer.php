<?php
/**
 * Site footer: pen component "Footer" (oqbEv) plus the floating WhatsApp button.
 */

defined( 'ABSPATH' ) || exit;

$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
?>
</main>

<footer class="site-footer">
	<p class="site-footer__location"><?php echo esc_html( get_theme_mod( 'miroir_location', __( 'Location : Indonesia', 'miroir' ) ) ); ?></p>
	<?php
	miroir_menu(
		'footer-account',
		array(
			__( 'My account', 'miroir' )           => $account_url,
			__( 'Payment confirmation', 'miroir' ) => miroir_page_url( 'payment-confirmation' ),
			__( 'Shipping information', 'miroir' ) => miroir_page_url( 'shipping-information' ),
		),
		'site-footer__links'
	);
	miroir_menu(
		'footer-policy',
		array(
			__( 'FAQ', 'miroir' )              => miroir_page_url( 'faq' ),
			__( 'Return policy', 'miroir' )    => miroir_page_url( 'returns-exchanges' ),
			__( 'Condition of use', 'miroir' ) => miroir_page_url( 'terms-of-use' ),
		),
		'site-footer__links'
	);
	miroir_menu(
		'footer-contact',
		array(
			__( 'Contact customer care', 'miroir' ) => miroir_page_url( 'contact' ),
			__( 'Instagram', 'miroir' )             => 'https://www.instagram.com/miroir.id/',
		),
		'site-footer__links'
	);
	?>
	<p class="site-footer__copyright">&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php echo esc_html( strtolower( get_bloginfo( 'name' ) ) ); ?></p>
</footer>

<?php
miroir_whatsapp_float();
wp_footer();
?>
</body>
</html>
