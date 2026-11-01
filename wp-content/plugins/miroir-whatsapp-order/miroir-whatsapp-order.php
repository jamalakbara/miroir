<?php
/**
 * Plugin Name: Miroir WhatsApp Order
 * Description: Catalogue-mode ordering (quotation QUO-2026-003): product pages and the bag hand the order to the shop's WhatsApp with a prefilled message. The checkout page becomes a short review form; submitting it saves an on-hold WooCommerce order (so it shows under My account > Orders) and opens WhatsApp. No payment gateway. Number, message wording and order handling: WooCommerce > Settings > WhatsApp ordering.
 * Version:     0.2.0
 * Author:      Miroir
 * Requires Plugins: woocommerce
 */

defined( 'ABSPATH' ) || exit;

define( 'MIROIR_WA_VERSION', '0.2.0' );

/**
 * Message building and order creation. Markup lives in the theme's
 * woocommerce/checkout templates; this class only provides data and behaviour.
 */
class Miroir_WA_Order {

	const NONCE    = 'miroir_wa_checkout';
	const META_MSG = '_miroir_wa_message';

	/** Settings tab id under WooCommerce > Settings. */
	const TAB = 'miroir_wa';

	/**
	 * Message templates and order settings: option => default. Templates use
	 * {placeholders}; an emptied field falls back to its default.
	 */
	public static function defaults() {
		return array(
			'miroir_wa_greeting'       => __( "Hi {shop}, I'd like to order:", 'miroir' ),
			'miroir_wa_line'           => '{n}. {name} — {variant} × {qty} — {price}',
			'miroir_wa_subtotal'       => __( 'Subtotal: {subtotal} (excl. shipping)', 'miroir' ),
			'miroir_wa_name'           => __( 'Name: {name}', 'miroir' ),
			'miroir_wa_deliver'        => __( 'Deliver to: {city}, {country}', 'miroir' ),
			'miroir_wa_notes'          => __( 'Notes: {notes}', 'miroir' ),
			'miroir_wa_ref'            => __( 'Order ref: #{ref}', 'miroir' ),
			'miroir_wa_one_size'       => __( 'One size', 'miroir' ),
			'miroir_wa_payment_title'  => __( 'Bank transfer after confirmation', 'miroir' ),
			'miroir_wa_order_status'   => 'on-hold',
			'miroir_wa_require_city'   => 'yes',
			'miroir_wa_open_delay'     => 800,
			'miroir_wa_fallback_delay' => 4000,
		);
	}

	/**
	 * A setting, or its default when unset or emptied.
	 *
	 * @param string $key Option name from defaults().
	 */
	public static function opt( $key ) {
		$defaults = self::defaults();
		$value    = get_option( $key, '' );
		return '' === trim( (string) $value ) ? $defaults[ $key ] : $value;
	}

	/**
	 * Fill a message template's {placeholders}.
	 *
	 * @param string $key  Template option name.
	 * @param array  $vars placeholder => value.
	 */
	public static function fill( $key, $vars ) {
		$pairs = array();
		foreach ( $vars as $name => $value ) {
			$pairs[ '{' . $name . '}' ] = (string) $value;
		}
		return strtr( (string) self::opt( $key ), $pairs );
	}

	/** Shop's WhatsApp number, digits only (country code first, e.g. 62812...). */
	public static function number() {
		return preg_replace( '/\D/', '', (string) get_option( 'miroir_wa_number', '' ) );
	}

	/**
	 * wa.me link with a prefilled message.
	 *
	 * @param string $text Message.
	 */
	public static function url( $text = '' ) {
		$url = 'https://wa.me/' . self::number();
		return '' === $text ? $url : $url . '?text=' . rawurlencode( $text );
	}

	/** Plain-text price for messages ("IDR 279,000"). */
	public static function price( $amount ) {
		return html_entity_decode( wp_strip_all_tags( wc_price( $amount ) ), ENT_QUOTES, 'UTF-8' );
	}

	/** Greeting line that opens every message. */
	public static function greeting() {
		return self::fill( 'miroir_wa_greeting', array( 'shop' => strtolower( get_bloginfo( 'name' ) ) ) );
	}

	/**
	 * One message line: "1. Name — Size M × 1 — IDR 279,000".
	 */
	public static function line( $index, $name, $variant, $qty, $total ) {
		return self::fill(
			'miroir_wa_line',
			array(
				'n'       => (int) $index,
				'name'    => $name,
				'variant' => $variant ? $variant : self::opt( 'miroir_wa_one_size' ),
				'qty'     => (int) $qty,
				'price'   => $total,
			)
		);
	}

	/**
	 * Bag contents for the review page and the message.
	 *
	 * @return array[] { name, variant, qty, total (plain text), total_html, product }
	 */
	public static function cart_lines() {
		$lines = array();
		foreach ( WC()->cart->get_cart() as $item ) {
			$product = $item['data'];
			$parent  = $product->is_type( 'variation' ) ? wc_get_product( $product->get_parent_id() ) : $product;
			$total   = WC()->cart->get_product_subtotal( $product, $item['quantity'] );
			$lines[] = array(
				'name'         => $parent->get_name(),
				'variant'      => function_exists( 'miroir_cart_item_variant' ) ? miroir_cart_item_variant( $item ) : '',
				'qty'          => (int) $item['quantity'],
				'total'        => html_entity_decode( wp_strip_all_tags( $total ), ENT_QUOTES, 'UTF-8' ),
				'total_html'   => $total,
				'product'      => $parent,
				'item_product' => $product,
			);
		}
		return $lines;
	}

	/**
	 * Full order message. The review page builds the same text live in JS
	 * (assets/checkout.js) from the fields; keep both in step.
	 *
	 * @param array  $lines   From cart_lines() or order_lines().
	 * @param string $subtotal Plain-text subtotal.
	 * @param array  $details { name, city, country, notes }.
	 * @param string $ref     Order reference appended once the order exists.
	 */
	public static function message( $lines, $subtotal, $details, $ref = '' ) {
		// Groups are separated by a blank line, as in the lofi message preview.
		$out = array( self::greeting(), '' );
		foreach ( array_values( $lines ) as $i => $line ) {
			$out[] = self::line( $i + 1, $line['name'], $line['variant'], $line['qty'], $line['total'] );
		}
		$out[] = '';
		$out[] = self::fill( 'miroir_wa_subtotal', array( 'subtotal' => $subtotal ) );
		$out[] = '';
		$out[] = self::fill( 'miroir_wa_name', array( 'name' => $details['name'] ) );
		$out[] = self::fill( 'miroir_wa_deliver', array( 'city' => $details['city'], 'country' => $details['country'] ) );
		$out[] = self::fill( 'miroir_wa_notes', array( 'notes' => '' !== $details['notes'] ? $details['notes'] : '—' ) );
		if ( $ref ) {
			$out[] = self::fill( 'miroir_wa_ref', array( 'ref' => $ref ) );
		}
		return implode( "\n", $out );
	}

	/**
	 * Lines of a saved order, in cart_lines() shape.
	 *
	 * @param WC_Order $order Order.
	 */
	public static function order_lines( $order ) {
		$lines = array();
		foreach ( $order->get_items() as $item ) {
			$variant = array();
			foreach ( $item->get_formatted_meta_data( '_', true ) as $meta ) {
				$variant[] = $meta->display_key . ' ' . wp_strip_all_tags( $meta->display_value );
			}
			$product = $item->get_product();
			$lines[] = array(
				'name'       => $item->get_name(),
				'variant'    => $variant ? implode( ', ', $variant ) : ( $product ? (string) $product->get_attribute( 'size' ) : '' ),
				'qty'        => (int) $item->get_quantity(),
				'total'      => self::price( $item->get_subtotal() ),
				'total_html' => wc_price( $item->get_subtotal(), array( 'currency' => $order->get_currency() ) ),
			);
		}
		return $lines;
	}

	/** Review form defaults: posted values, then the logged-in customer's billing details. */
	public static function field_values() {
		$customer = WC()->customer;
		$posted   = wp_unslash( $_POST ); // phpcs:ignore WordPress.Security.NonceVerification -- only echoes back what was posted.
		$name     = $customer ? trim( $customer->get_billing_first_name() . ' ' . $customer->get_billing_last_name() ) : '';
		return array(
			'name'    => wc_clean( $posted['wa_name'] ?? $name ),
			'country' => wc_clean( $posted['wa_country'] ?? ( $customer && $customer->get_billing_country() ? $customer->get_billing_country() : WC()->countries->get_base_country() ) ),
			'city'    => wc_clean( $posted['wa_city'] ?? ( $customer ? $customer->get_billing_city() : '' ) ),
			'notes'   => sanitize_textarea_field( $posted['wa_notes'] ?? '' ),
		);
	}

	/**
	 * Handle the review form: validate, save an on-hold order from the bag,
	 * empty the bag and go to the "order sent" page, which opens WhatsApp.
	 */
	public static function handle_checkout() {
		if ( ! isset( $_POST['miroir_wa_checkout'] ) || ! is_checkout() ) {
			return;
		}
		if ( ! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ?? '' ), self::NONCE ) ) {
			wc_add_notice( __( 'Your session expired. Please check your details and try again.', 'miroir' ), 'error' );
			return;
		}
		if ( WC()->cart->is_empty() ) {
			wp_safe_redirect( wc_get_cart_url() );
			exit;
		}

		$values    = self::field_values();
		$countries = WC()->countries->get_allowed_countries();
		$required  = array( 'name' => __( 'Full name', 'miroir' ) );
		if ( 'yes' === self::opt( 'miroir_wa_require_city' ) ) {
			$required['city'] = __( 'Town / City', 'miroir' );
		}
		foreach ( $required as $key => $label ) {
			if ( '' === $values[ $key ] ) {
				/* translators: %s: field label. */
				wc_add_notice( sprintf( __( '%s is required.', 'miroir' ), $label ), 'error', array( 'id' => 'wa_' . $key ) );
			}
		}
		if ( ! isset( $countries[ $values['country'] ] ) ) {
			wc_add_notice( __( 'Please choose a country we deliver to.', 'miroir' ), 'error', array( 'id' => 'wa_country' ) );
		}
		WC()->cart->check_cart_items();
		if ( wc_notice_count( 'error' ) ) {
			return;
		}

		$lines    = self::cart_lines();
		$subtotal = html_entity_decode( wp_strip_all_tags( WC()->cart->get_cart_subtotal() ), ENT_QUOTES, 'UTF-8' );

		$order = wc_create_order(
			array(
				'customer_id' => get_current_user_id(),
				'created_via' => 'whatsapp',
			)
		);
		foreach ( WC()->cart->get_cart() as $item ) {
			$order->add_product( $item['data'], $item['quantity'], array( 'variation' => $item['variation'] ) );
		}
		$order->set_billing_first_name( $values['name'] );
		$order->set_billing_city( $values['city'] );
		$order->set_billing_country( $values['country'] );
		$order->set_shipping_first_name( $values['name'] );
		$order->set_shipping_city( $values['city'] );
		$order->set_shipping_country( $values['country'] );
		$order->set_customer_note( $values['notes'] );
		$order->set_payment_method_title( self::opt( 'miroir_wa_payment_title' ) );
		$order->set_currency( get_woocommerce_currency() );
		$order->calculate_totals( false );

		$details = array(
			'name'    => $values['name'],
			'city'    => $values['city'],
			'country' => $countries[ $values['country'] ],
			'notes'   => $values['notes'],
		);
		$order->update_meta_data( self::META_MSG, self::message( $lines, $subtotal, $details, $order->get_order_number() ) );
		$order->save();
		// Default on hold = waiting for the team to confirm stock and shipping in WhatsApp; notifies the shop.
		$order->update_status( self::opt( 'miroir_wa_order_status' ), __( 'Order sent via WhatsApp; awaiting confirmation in chat.', 'miroir' ) );

		WC()->cart->empty_cart();
		wp_safe_redirect( $order->get_checkout_order_received_url() );
		exit;
	}

	/**
	 * wa.me link for a saved order (the "order sent" page and its retry button).
	 *
	 * @param WC_Order $order Order.
	 */
	public static function order_url( $order ) {
		return self::url( (string) $order->get_meta( self::META_MSG ) );
	}

	/**
	 * Product-page message before a size is picked; product.js rewrites it on selection.
	 *
	 * @param WC_Product $product Product.
	 */
	public static function product_message( $product ) {
		$variant = $product->is_type( 'variable' ) ? '' : (string) $product->get_attribute( 'size' );
		return self::greeting() . "\n\n" . self::line( 1, $product->get_name(), $variant, 1, self::price( wc_get_price_to_display( $product ) ) ) . "\n\n" . $product->get_permalink();
	}

	/** Fields of the WooCommerce > Settings > WhatsApp ordering tab. */
	public static function settings() {
		$d        = self::defaults();
		$template = function ( $id, $title, $placeholders ) use ( $d ) {
			return array(
				'id'          => $id,
				'title'       => $title,
				'type'        => 'text',
				'css'         => 'min-width: 420px;',
				'placeholder' => $d[ $id ],
				/* translators: %s: list of placeholders. */
				'desc'        => sprintf( __( 'Placeholders: %s. Leave empty for the default.', 'miroir' ), $placeholders ),
			);
		};
		$statuses = array_intersect_key( wc_get_order_statuses(), array_flip( array( 'wc-on-hold', 'wc-pending', 'wc-processing' ) ) );
		return array(
			array( 'id' => 'miroir_wa_general', 'type' => 'title', 'title' => __( 'WhatsApp ordering', 'miroir' ), 'desc' => __( 'Customers order by sending a prefilled WhatsApp message to this number; each order is also saved in WooCommerce.', 'miroir' ) ),
			array(
				'id'          => 'miroir_wa_number',
				'title'       => __( 'WhatsApp number', 'miroir' ),
				'type'        => 'text',
				'placeholder' => '62812…',
				'desc'        => __( 'Country code first, digits only (spaces and + are ignored).', 'miroir' ),
			),
			array(
				'id'      => 'miroir_wa_order_status',
				'title'   => __( 'Status of new orders', 'miroir' ),
				'type'    => 'select',
				'default' => $d['miroir_wa_order_status'],
				// Keys without the "wc-" prefix, as update_status() expects.
				'options' => array_combine( array_map( fn( $s ) => substr( $s, 3 ), array_keys( $statuses ) ), $statuses ),
			),
			array(
				'id'          => 'miroir_wa_payment_title',
				'title'       => __( 'Payment method shown on orders', 'miroir' ),
				'type'        => 'text',
				'placeholder' => $d['miroir_wa_payment_title'],
				'css'         => 'min-width: 420px;',
			),
			array(
				'id'      => 'miroir_wa_require_city',
				'title'   => __( 'Require town / city', 'miroir' ),
				'type'    => 'checkbox',
				'default' => $d['miroir_wa_require_city'],
				'desc'    => __( 'Customers must fill in their town or city on the review page.', 'miroir' ),
			),
			array( 'id' => 'miroir_wa_general', 'type' => 'sectionend' ),

			array( 'id' => 'miroir_wa_message', 'type' => 'title', 'title' => __( 'Message wording', 'miroir' ), 'desc' => __( 'The order message customers send. Lines appear in this order, with blank lines between the greeting, the products, the subtotal and the details.', 'miroir' ) ),
			$template( 'miroir_wa_greeting', __( 'Greeting', 'miroir' ), '{shop}' ),
			$template( 'miroir_wa_line', __( 'Product line', 'miroir' ), '{n} {name} {variant} {qty} {price}' ),
			$template( 'miroir_wa_one_size', __( 'Size for products without sizes', 'miroir' ), '—' ),
			$template( 'miroir_wa_subtotal', __( 'Subtotal line', 'miroir' ), '{subtotal}' ),
			$template( 'miroir_wa_name', __( 'Name line', 'miroir' ), '{name}' ),
			$template( 'miroir_wa_deliver', __( 'Delivery line', 'miroir' ), '{city} {country}' ),
			$template( 'miroir_wa_notes', __( 'Notes line', 'miroir' ), '{notes}' ),
			$template( 'miroir_wa_ref', __( 'Order reference line', 'miroir' ), '{ref}' ),
			array( 'id' => 'miroir_wa_message', 'type' => 'sectionend' ),

			array( 'id' => 'miroir_wa_timing', 'type' => 'title', 'title' => __( 'Order sent page', 'miroir' ) ),
			array(
				'id'                => 'miroir_wa_open_delay',
				'title'             => __( 'Open WhatsApp after (ms)', 'miroir' ),
				'type'              => 'number',
				'default'           => $d['miroir_wa_open_delay'],
				'custom_attributes' => array( 'min' => 0, 'step' => 100 ),
			),
			array(
				'id'                => 'miroir_wa_fallback_delay',
				'title'             => __( 'Show "WhatsApp didn’t open" help after (ms)', 'miroir' ),
				'type'              => 'number',
				'default'           => $d['miroir_wa_fallback_delay'],
				'custom_attributes' => array( 'min' => 1000, 'step' => 500 ),
			),
			array( 'id' => 'miroir_wa_timing', 'type' => 'sectionend' ),
		);
	}
}

/* Theme API: template helpers the miroir theme calls when this plugin is active. */

/** @return array[] Bag lines for the review page. */
function miroir_wa_cart_lines() {
	return Miroir_WA_Order::cart_lines();
}

/** @return array Review form values. */
function miroir_wa_field_values() {
	return Miroir_WA_Order::field_values();
}

/** Nonce + marker fields for the review form. */
function miroir_wa_form_fields() {
	wp_nonce_field( Miroir_WA_Order::NONCE );
	echo '<input type="hidden" name="miroir_wa_checkout" value="1">';
}

/** @return array[] Lines of a saved order. */
function miroir_wa_order_lines( $order ) {
	return Miroir_WA_Order::order_lines( $order );
}

/** @return string wa.me link for a saved order. */
function miroir_wa_order_url( $order ) {
	return Miroir_WA_Order::order_url( $order );
}

/** @return string Message text for the review page's first render. */
function miroir_wa_preview_message() {
	$values    = Miroir_WA_Order::field_values();
	$countries = WC()->countries->get_allowed_countries();
	return Miroir_WA_Order::message(
		Miroir_WA_Order::cart_lines(),
		html_entity_decode( wp_strip_all_tags( WC()->cart->get_cart_subtotal() ), ENT_QUOTES, 'UTF-8' ),
		array(
			'name'    => '' !== $values['name'] ? $values['name'] : __( 'Full Name', 'miroir' ),
			'city'    => '' !== $values['city'] ? $values['city'] : __( 'Town / City', 'miroir' ),
			'country' => $countries[ $values['country'] ] ?? $values['country'],
			'notes'   => $values['notes'],
		)
	);
}

add_action( 'template_redirect', array( 'Miroir_WA_Order', 'handle_checkout' ) );

// No payment step on this site.
add_filter( 'woocommerce_cart_needs_payment', '__return_false' );
add_filter( 'woocommerce_order_needs_payment', '__return_false' );
add_filter( 'woocommerce_available_payment_gateways', '__return_empty_array' );

// The theme's "Order via WhatsApp" button and floating button.
add_filter(
	'miroir_wa_product_url',
	function ( $url, $product ) {
		return Miroir_WA_Order::number() ? Miroir_WA_Order::url( Miroir_WA_Order::product_message( $product ) ) : $url;
	},
	10,
	2
);
add_filter(
	'miroir_whatsapp_url',
	function () {
		return Miroir_WA_Order::url();
	}
);

add_action(
	'wp_enqueue_scripts',
	function () {
		$base = plugin_dir_url( __FILE__ ) . 'assets/';
		if ( is_product() ) {
			$product = wc_get_product( get_queried_object_id() );
			wp_enqueue_script( 'miroir-wa-product', $base . 'product.js', array( 'jquery' ), MIROIR_WA_VERSION, true );
			wp_localize_script(
				'miroir-wa-product',
				'miroirWaProduct',
				array(
					'number'   => Miroir_WA_Order::number(),
					'greeting' => Miroir_WA_Order::greeting(),
					'line'     => Miroir_WA_Order::opt( 'miroir_wa_line' ),
					'name'     => $product ? $product->get_name() : '',
					'url'      => $product ? $product->get_permalink() : '',
					'oneSize'  => Miroir_WA_Order::opt( 'miroir_wa_one_size' ),
					// WooCommerce leaves price_html empty when all variations cost the same; format display_price instead.
					'currency' => array(
						'symbol'    => html_entity_decode( get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8' ),
						'format'    => html_entity_decode( get_woocommerce_price_format(), ENT_QUOTES, 'UTF-8' ),
						'decimals'  => wc_get_price_decimals(),
						'thousands' => wc_get_price_thousand_separator(),
						'decimal'   => wc_get_price_decimal_separator(),
					),
				)
			);
		}
		if ( is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ) {
			wp_enqueue_script( 'miroir-wa-checkout', $base . 'checkout.js', array(), MIROIR_WA_VERSION, true );
			wp_localize_script(
				'miroir-wa-checkout',
				'miroirWaCheckout',
				array(
					'greeting'    => Miroir_WA_Order::greeting(),
					'lines'       => array_map(
						function ( $line, $i ) {
							return Miroir_WA_Order::line( $i + 1, $line['name'], $line['variant'], $line['qty'], $line['total'] );
						},
						Miroir_WA_Order::cart_lines(),
						array_keys( Miroir_WA_Order::cart_lines() )
					),
					'subtotal'    => Miroir_WA_Order::fill( 'miroir_wa_subtotal', array( 'subtotal' => html_entity_decode( wp_strip_all_tags( WC()->cart->get_cart_subtotal() ), ENT_QUOTES, 'UTF-8' ) ) ),
					'labels'      => array(
						'name'    => Miroir_WA_Order::opt( 'miroir_wa_name' ),
						'deliver' => Miroir_WA_Order::opt( 'miroir_wa_deliver' ),
						'notes'   => Miroir_WA_Order::opt( 'miroir_wa_notes' ),
					),
					'placeholder' => array(
						'name' => __( 'Full Name', 'miroir' ),
						'city' => __( 'Town / City', 'miroir' ),
					),
				)
			);
		}
		if ( is_wc_endpoint_url( 'order-received' ) ) {
			wp_enqueue_script( 'miroir-wa-sent', $base . 'sent.js', array(), MIROIR_WA_VERSION, true );
			wp_localize_script(
				'miroir-wa-sent',
				'miroirWaSent',
				array(
					'openDelay'     => absint( Miroir_WA_Order::opt( 'miroir_wa_open_delay' ) ),
					'fallbackDelay' => absint( Miroir_WA_Order::opt( 'miroir_wa_fallback_delay' ) ),
				)
			);
		}
	}
);

// WooCommerce > Settings > WhatsApp ordering.
add_filter(
	'woocommerce_settings_tabs_array',
	function ( $tabs ) {
		$tabs[ Miroir_WA_Order::TAB ] = __( 'WhatsApp ordering', 'miroir' );
		return $tabs;
	},
	50
);
add_action(
	'woocommerce_settings_tabs_' . Miroir_WA_Order::TAB,
	function () {
		woocommerce_admin_fields( Miroir_WA_Order::settings() );
	}
);
add_action(
	'woocommerce_update_options_' . Miroir_WA_Order::TAB,
	function () {
		woocommerce_update_options( Miroir_WA_Order::settings() );
	}
);

// Without a number every WhatsApp button opens an empty chat.
add_action(
	'admin_notices',
	function () {
		if ( Miroir_WA_Order::number() || ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}
		printf(
			'<div class="notice notice-warning"><p>%s <a href="%s">%s</a></p></div>',
			esc_html__( 'WhatsApp ordering has no shop number yet, so order buttons cannot reach you.', 'miroir' ),
			esc_url( admin_url( 'admin.php?page=wc-settings&tab=' . Miroir_WA_Order::TAB ) ),
			esc_html__( 'Set the WhatsApp number', 'miroir' )
		);
	}
);
