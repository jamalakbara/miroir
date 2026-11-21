<?php
/**
 * Standard WooCommerce hooks for analytics plugins.
 *
 * The product, checkout and "order sent" screens are drawn by the theme's own
 * blocks and Elementor widgets, which skip the classic WooCommerce template
 * hooks. Tracking plugins (Google Analytics for WooCommerce, TikTok for
 * Business …) listen to those hooks for view_item, begin_checkout and purchase,
 * so fire them once per page, early in the footer, before the plugins print
 * their event data (wp_footer, priority 10).
 *
 * The WhatsApp checkout still lands on the order-received page before WhatsApp
 * opens, so the purchase is sent from there.
 *
 * @package miroir
 */

defined( 'ABSPATH' ) || exit;

/**
 * Run a hook once, without WooCommerce's default callbacks that print markup.
 *
 * @param string   $hook     Action name.
 * @param string[] $defaults Core callbacks to detach first (they draw classic UI).
 * @param mixed    ...$args  Hook arguments.
 */
function miroir_fire_wc_hook( $hook, $defaults, ...$args ) {
	if ( did_action( $hook ) ) {
		return;
	}
	foreach ( $defaults as $callback ) {
		remove_action( $hook, $callback, 10 );
	}
	do_action( $hook, ...$args );
}

add_action(
	'wp_footer',
	function () {
		if ( is_order_received_page() ) {
			$order_id = absint( get_query_var( 'order-received' ) );
			if ( $order_id ) {
				miroir_fire_wc_hook( 'woocommerce_thankyou', array( 'woocommerce_order_details_table' ), $order_id );
			}
			return;
		}

		if ( is_checkout() && ! is_wc_endpoint_url() ) {
			if ( WC()->cart && ! WC()->cart->is_empty() ) {
				miroir_fire_wc_hook( 'woocommerce_before_checkout_form', array( 'woocommerce_checkout_login_form', 'woocommerce_checkout_coupon_form' ), WC()->checkout() );
			}
			return;
		}

		if ( is_product() ) {
			$product = wc_get_product( get_queried_object_id() );
			if ( ! $product ) {
				return;
			}
			$previous            = $GLOBALS['product'] ?? null;
			$GLOBALS['product'] = $product;
			miroir_fire_wc_hook( 'woocommerce_before_single_product', array() );
			miroir_fire_wc_hook( 'woocommerce_after_single_product', array() );
			$GLOBALS['product'] = $previous;
		}
	},
	1
);
