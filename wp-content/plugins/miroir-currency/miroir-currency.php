<?php
/**
 * Plugin Name: Miroir Currency
 * Description: Multi-currency setup on top of WPML Multilingual & Multicurrency for WooCommerce (WCML, standalone mode): IDR base plus USD, SGD and MYR with manual exchange rates, and a header currency switcher block (miroir/currency-switcher). Run `wp miroir currency setup` once, `wp miroir currency rates` to refresh rates.
 * Version:     0.1.0
 * Author:      Miroir
 * Requires Plugins: woocommerce, woocommerce-multilingual
 */

defined( 'ABSPATH' ) || exit;

/**
 * WooCommerce shows "$" for both USD and SGD; spell them out so a price on its
 * own is never ambiguous. Rupiah prints as "IDR", as drawn in the lofi.
 */
add_filter(
	'woocommerce_currency_symbol',
	function ( $symbol, $currency ) {
		$symbols = array(
			'IDR' => 'IDR',
			'USD' => 'US$',
			'SGD' => 'S$',
		);
		return $symbols[ $currency ] ?? $symbol;
	},
	10,
	2
);

/**
 * Header currency switcher as a block, so a theme can place it in its header
 * template. Until the miroir theme does, Block Hooks put it after the main
 * menu in any header part or pattern, ahead of WooCommerce's account and
 * mini-cart icons (which hook the same spot at priority 9).
 */
add_action(
	'init',
	function () {
		wp_register_script( 'miroir-currency-switcher-editor', false, array( 'wp-blocks', 'wp-element', 'wp-server-side-render' ), '0.1.0', true );
		wp_add_inline_script(
			'miroir-currency-switcher-editor',
			"wp.blocks.registerBlockType( 'miroir/currency-switcher', { edit: function () { return wp.element.createElement( wp.serverSideRender, { block: 'miroir/currency-switcher' } ); }, save: function () { return null; } } );"
		);

		wp_register_style( 'miroir-currency-switcher', false, array(), '0.1.0' );
		wp_add_inline_style(
			'miroir-currency-switcher',
			// Compact text dropdown that sits in the header row instead of WCML's 15em boxed field.
			'.miroir-currency-switcher{position:relative;font-size:inherit}'
			. '.miroir-currency-switcher .wcml_currency_switcher{width:auto}'
			. '.miroir-currency-switcher .wcml_currency_switcher li{border:0;background:transparent}'
			. '.miroir-currency-switcher .wcml_currency_switcher button{padding:.4em .5em}'
			. '.miroir-currency-switcher .wcml_currency_switcher button:hover,.miroir-currency-switcher .wcml_currency_switcher button:focus-visible{text-decoration:underline}'
			. '.miroir-currency-switcher .wcml_currency_switcher .wcml-cs-item-toggle{padding-right:1.4em}'
			. '.miroir-currency-switcher .wcml_currency_switcher .wcml-cs-item-toggle:after{right:.35em;border-width:.25em;border-top-width:.35em;top:calc(50% - .1em)}'
			. '.miroir-currency-switcher .wcml_currency_switcher .wcml-cs-submenu{left:auto;min-width:100%;border:1px solid currentColor;background:var(--wp--preset--color--base,#fff)}'
			. '.miroir-currency-switcher .wcml_currency_switcher .wcml-cs-submenu li{border:0;background:var(--wp--preset--color--base,#fff)}'
			. '.miroir-currency-switcher .wcml-spinner{display:none}'
		);

		register_block_type(
			'miroir/currency-switcher',
			array(
				'api_version'     => 3,
				'title'           => 'Currency Switcher',
				'category'        => 'widgets',
				'icon'            => 'money-alt',
				'description'     => 'Lets shoppers pick IDR, USD, SGD or MYR.',
				'supports'        => array( 'html' => false ),
				'editor_script'   => 'miroir-currency-switcher-editor',
				'style'           => 'miroir-currency-switcher',
				'render_callback' => function () {
					if ( ! has_action( 'wcml_currency_switcher' ) ) {
						return '';
					}
					ob_start();
					do_action(
						'wcml_currency_switcher',
						array(
							'switcher_id'    => 'header',
							// Opens on click, not hover, so it works on touch screens.
							'switcher_style' => 'wcml-dropdown-click',
							'format'         => '%code%',
							'color_scheme'   => array_fill_keys( Miroir_Currency::COLOR_SCHEME_KEYS, '' ),
						)
					);
					$switcher = ob_get_clean();
					if ( '' === trim( $switcher ) ) {
						return '';
					}
					return sprintf(
						'<div %s>%s</div>',
						get_block_wrapper_attributes( array( 'class' => 'miroir-currency-switcher' ) ),
						$switcher
					);
				},
			)
		);
	}
);

/**
 * Currencies shoppers can pick, base first (the theme's currency panel lists these).
 */
add_filter(
	'miroir_currency_codes',
	function ( $codes ) {
		global $woocommerce_wpml;
		if ( empty( $woocommerce_wpml->multi_currency ) ) {
			return $codes;
		}
		$enabled = (array) $woocommerce_wpml->multi_currency->get_currency_codes();
		$base    = get_option( 'woocommerce_currency' );
		return array_values( array_unique( array_merge( array( $base ), $enabled ) ) );
	}
);

/**
 * ?miroir_currency=USD (the theme's currency panel form) switches the client
 * currency the same way WCML's own switcher does, then drops the parameter.
 */
add_action(
	'wp_loaded',
	function () {
		global $woocommerce_wpml;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display preference for this visitor's session only.
		$code = isset( $_GET['miroir_currency'] ) ? strtoupper( sanitize_key( wp_unslash( $_GET['miroir_currency'] ) ) ) : '';
		if ( ! $code || empty( $woocommerce_wpml->multi_currency ) || is_admin() ) {
			return;
		}
		if ( in_array( $code, (array) apply_filters( 'miroir_currency_codes', array() ), true ) ) {
			// Same steps as WCML's AJAX switcher: the choice lives in the WooCommerce session, which a guest only gets once its cookie is set.
			$woocommerce_wpml->multi_currency->set_client_currency( $code );
			if ( WC()->session && ! is_user_logged_in() && ! WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie( true );
			}
			wcml_user_store_set( 'client_currency_switched', true );
		}
		wp_safe_redirect( remove_query_arg( array( 'miroir_currency', 'panel' ) ) );
		exit;
	}
);

add_filter(
	'hooked_block_types',
	function ( $hooked_blocks, $position, $anchor_block, $context ) {
		if ( 'after' !== $position || 'core/navigation' !== $anchor_block ) {
			return $hooked_blocks;
		}

		$is_header_part    = $context instanceof WP_Block_Template && 'header' === $context->area;
		$is_header_pattern = is_array( $context ) && (
			in_array( 'core/template-part/header', $context['blockTypes'] ?? array(), true )
			|| in_array( 'header', $context['categories'] ?? array(), true )
		);
		if ( ! $is_header_part && ! $is_header_pattern ) {
			return $hooked_blocks;
		}

		// Skip if the header already places the block itself.
		$content = $is_header_part ? $context->content : ( $context['content'] ?? '' );
		if ( false === strpos( $content, 'wp:miroir/currency-switcher' ) ) {
			$hooked_blocks[] = 'miroir/currency-switcher';
		}
		return $hooked_blocks;
	},
	8,
	4
);

class Miroir_Currency {

	/** WCML reads every color key of a switcher unguarded; empty string = theme default. */
	const COLOR_SCHEME_KEYS = array(
		'font_current_normal',
		'font_current_hover',
		'background_current_normal',
		'background_current_hover',
		'font_other_normal',
		'font_other_hover',
		'background_other_normal',
		'background_other_hover',
		'border_normal',
	);
}

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	return;
}

/**
 * Configures WCML multi-currency. Prices are stored in IDR only; other
 * currencies are converted with the rates kept in WCML settings. WCML's own
 * automatic rate services all need an API key, so rates are pulled from the
 * keyless Frankfurter API (ECB reference rates) on demand instead.
 */
class Miroir_Currency_Command {

	const BASE = 'IDR';

	/** Secondary currencies: code => [position, thousand sep, decimal sep, decimals]. Order = switcher order. */
	const CURRENCIES = array(
		'USD' => array( 'left', ',', '.', 2 ),
		'SGD' => array( 'left', ',', '.', 2 ),
		'MYR' => array( 'left', ',', '.', 2 ),
	);

	const RATES_URL = 'https://api.frankfurter.dev/v1/latest';

	/**
	 * Turn on WCML multi-currency and add USD/SGD/MYR with current rates. The
	 * switcher itself is the header block above. Safe to re-run; it also refreshes rates.
	 *
	 * @when after_wp_load
	 */
	public function setup() {
		global $woocommerce_wpml;

		if ( self::BASE !== get_option( 'woocommerce_currency' ) ) {
			WP_CLI::error( 'Store currency must be ' . self::BASE . ' (run `wp miroir demo seed` or set it in WooCommerce settings).' );
		}

		$settings = $woocommerce_wpml->get_settings();

		$settings['enable_multi_currency'] = WCML_MULTI_CURRENCIES_INDEPENDENT;

		// Base currency mirrors the WooCommerce price format.
		$settings['currency_options'][ self::BASE ] = array_merge(
			$settings['currency_options'][ self::BASE ] ?? array(),
			array(
				'position'      => get_option( 'woocommerce_currency_pos' ),
				'thousand_sep'  => get_option( 'woocommerce_price_thousand_sep' ),
				'decimal_sep'   => get_option( 'woocommerce_price_decimal_sep' ),
				'num_decimals'  => (int) get_option( 'woocommerce_price_num_decimals' ),
				'location_mode' => 'all',
			)
		);

		foreach ( self::CURRENCIES as $code => list( $position, $thousand_sep, $decimal_sep, $decimals ) ) {
			$settings['currency_options'][ $code ] = array_merge(
				$settings['currency_options'][ $code ] ?? array(),
				array(
					'position'           => $position,
					'thousand_sep'       => $thousand_sep,
					'decimal_sep'        => $decimal_sep,
					'num_decimals'       => $decimals,
					'rounding'           => 'disabled',
					'rounding_increment' => 1,
					'auto_subtract'      => 0,
					'location_mode'      => 'all',
					'countries'          => array(),
				)
			);
		}

		$settings['currencies_order'] = array_merge( array( self::BASE ), array_keys( self::CURRENCIES ) );

		// The header block is the switcher; WCML's product-page one would duplicate it on every PDP.
		// Its settings are still kept because the WCML admin screen expects a 'product' switcher.
		$settings['currency_switcher_product_visibility'] = 0;
		$settings['currency_switchers']['product']        = array_merge(
			$settings['currency_switchers']['product'] ?? array(),
			array(
				'switcher_style' => 'wcml-dropdown',
				'template'       => '%code%',
				'widget_title'   => '',
				'color_scheme'   => array_merge(
					array_fill_keys( Miroir_Currency::COLOR_SCHEME_KEYS, '' ),
					$settings['currency_switchers']['product']['color_scheme'] ?? array()
				),
			)
		);

		$settings = $this->apply_rates( $settings );
		$woocommerce_wpml->update_settings( $settings );

		// Everyone starts in IDR and picks another currency with the switcher.
		\WCML\MultiCurrency\Settings::setMode( \WCML\MultiCurrency\Settings::MODE_BY_LOCATION );

		WP_CLI::success( 'Multi-currency on: ' . implode( ', ', $settings['currencies_order'] ) . '.' );
	}

	/**
	 * Refresh exchange rates from ECB reference rates (Frankfurter API).
	 *
	 * @when after_wp_load
	 */
	public function rates() {
		global $woocommerce_wpml;

		$settings = $woocommerce_wpml->get_settings();
		if ( WCML_MULTI_CURRENCIES_INDEPENDENT !== (int) $settings['enable_multi_currency'] ) {
			WP_CLI::error( 'Multi-currency is off; run `wp miroir currency setup` first.' );
		}

		$woocommerce_wpml->update_settings( $this->apply_rates( $settings ) );
		WP_CLI::success( 'Exchange rates updated.' );
	}

	private function apply_rates( $settings ) {
		$url      = add_query_arg(
			array(
				'base'    => self::BASE,
				'symbols' => implode( ',', array_keys( self::CURRENCIES ) ),
			),
			self::RATES_URL
		);
		$response = wp_remote_get( $url, array( 'timeout' => 15 ) );
		$body     = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( is_wp_error( $response ) || empty( $body['rates'] ) ) {
			$reason = is_wp_error( $response ) ? $response->get_error_message() : 'HTTP ' . wp_remote_retrieve_response_code( $response );
			WP_CLI::warning( "Could not fetch rates ($reason); keeping the current ones." );
		}

		foreach ( array_keys( self::CURRENCIES ) as $code ) {
			$rate = (float) ( $body['rates'][ $code ] ?? 0 );
			if ( $rate <= 0 ) {
				if ( empty( $settings['currency_options'][ $code ]['rate'] ) ) {
					WP_CLI::error( "No exchange rate for $code; set one in WooCommerce > WCML > Multicurrency." );
				}
				continue;
			}

			$option = &$settings['currency_options'][ $code ];
			if ( ! empty( $option['rate'] ) && (float) $option['rate'] !== $rate ) {
				$option['previous_rate'] = $option['rate'];
			}
			$option['rate']    = $rate;
			$option['updated'] = current_time( 'mysql' );
			unset( $option );

			WP_CLI::log( sprintf( 'rate   1 %s = %s %s (1 %s = %s %s, ECB %s)', self::BASE, rtrim( sprintf( '%.10F', $rate ), '0' ), $code, $code, number_format( 1 / $rate, 0, ',', '.' ), self::BASE, $body['date'] ) );
		}

		return $settings;
	}
}

WP_CLI::add_command( 'miroir currency', 'Miroir_Currency_Command' );
