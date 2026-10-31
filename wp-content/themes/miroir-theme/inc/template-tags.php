<?php
/**
 * Markup helpers for the pen components that are not WooCommerce templates.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Miroir wordmark: the two artwork paths of pen frame "Miroir SVG logo" (110 x 37.512).
 */
function miroir_logo() {
	?>
	<svg class="logo-mark" width="110" height="37.512" viewBox="0 0 110 37.512" role="img" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
		<svg x="57.316" y="0" width="52.684" height="37.512" viewBox="0 0 249.83985 177.89401" preserveAspectRatio="none" overflow="visible"><path fill="currentColor" d="M59.73181 0.01199c5.444-0.25 8.05099 3.446 10.47699 7.618 6.60901-0.12801 13.89002 2.11501 12.413 10.717-0.63 3.65601-4.28299 5.91499-7.22202 7.783 2.91501 1.985 6.59201 3.33 9.841 4.809 5.55283 2.57678 10.64194 6.05289 15.06205 10.288 28.34399 27.435 29.13501 91.97699 1.664 121.02798-8.32102 8.79801-22.62299 15.46302-34.83402 15.62101l-0.018-8.66599c10.48001-1.79599 17.98801-3.35699 26.20599-10.99499 14.03-13.23401 17.965-32.25602 18.91101-50.646 1.72-33.39001-10.91-71.812-50.138-73.099-30.59799-0.647-47.88599 23.508-51.40201 51.75-2.73001 21.918-0.87601 48.22901 12.89401 66.50501 8.21201 10.901 18.129 14.70999 31.23301 16.29501q-0.02301 4.44 0.09399 8.87399l-0.237-0.00299c-21.68601-0.457-40.74301-16.746-47.99701-36.91101-12.522-34.802-9.23499-86.55099 25.409-107.681 4.37701-2.66901 9.36899-4.205 13.819-6.761l0.45999-0.26801c-3.65301-2.22999-7.77499-5.175-7.57599-9.882 0.31299-7.509 6.40701-8.944 12.469-8.64799 2.452-3.916 3.797-6.593 8.47201-7.728m147.659 27.05c3.71197-0.355 10.71399-0.177 14.56701-0.17999l27.11701-0.01401c0.64801 16.314 0.60901 32.388 0.67999 48.702l0.08502 63.67801c-0.03497 11.52499-0.44897 24.47799-0.18499 35.88-3.24598-0.384-7.69001-0.30499-10.969-0.07599 0.37799-11.72198 0.09101-24.83801 0.09101-36.656l-0.00599-67.586c0-10.824-0.31298-23.79199 0.14698-34.452-0.76136-0.15648-1.52863-0.28224-2.30005-0.37704-3.85199-0.45799-10.73401-0.13001-14.82398-0.13401-13.345-0.019-26.91198-0.31599-37.87201 8.667-13.134 10.76401-13.78399 30.13699-3.26098 42.83801 9.49798 10.784 22.35599 13.526 35.88 15.30701-8.95599 13.483-20.28799 28.48901-29.80401 41.76599l-12.21399 17.1c-2.00101 2.82199-7.21799 10.70002-9.943 12.72101-2.13 1.582-9.74398 0.69699-13.15701 1.03101l-0.53299 0.05301c11.77701-15.53299 22.883-32.37601 34.53501-48.05802 4.35598-5.86599 9.389-13.78799 13.91-19.225-9.26999-2.94699-16.969-5.42301-24.63599-11.84201-7.81305-6.43829-12.66806-15.78003-13.44705-25.874-0.81512-10.259 2.50775-20.41779 9.228-28.212 9.14099-10.492 23.53698-14.076 36.91101-15.05701"/></svg>
		<svg x="0" y="5.641" width="86.801" height="31.345" viewBox="0 0 411.63403 148.64528" preserveAspectRatio="none" overflow="visible"><path fill="currentColor" d="M181.65901 0.24001c5.72501-0.334 13.68401-0.129 19.54401-0.08501 16.613 0.12399 31.26098-1.08701 46.84201 6.045 8.76057 3.91747 15.58543 11.18285 18.94798 20.171 3.64975 10.04401 3.17444 21.12445-1.32199 30.819-6.95999 14.689-19.865 19.05099-33.85503 24.052 1.07599 2.297 4.051 6.229 5.62799 8.426l9.327 12.98999c10.73201 14.98001 21.511 30.35199 32.42403 45.18201-2.56101 0.28698-9.267 1.23001-11.409 0.583-3.25201-0.987-9.22-10.46798-11.23801-13.277l-13.22502-18.43399-17.832-24.85001c-3.67799-5.121-7.765-10.60199-11.19999-15.849l7.69-1.27499c19.601-3.71799 37.091-14.99301 35.21402-37.24201-0.59415-7.52713-4.19469-14.49783-9.989-19.33899-10.832-9.13699-26.02499-9.18201-39.40201-9.113-5.09 0.026-10.699-0.15199-15.703 0.263l-0.00701 88.49701-0.02 32.522c-0.002 5.53699-0.201 12.595 0.12199 17.95599-3.25599-0.29901-7.095-0.164-10.39199-0.10199 0.10499-13.03201-0.525-27.642-0.676-40.875l-0.101-64.427q-0.05-14.67999 0.136-29.35899c0.071-3.832 0.03901-9.63499 0.49601-13.27901m-46.043-0.24c2.62199 0.106 4.52299 0.144 7.134 0.01899-0.097 4.839 0.17999 10.668 0.26601 15.59101q0.24099 14.065 0.3 28.134c0.26132 34.80786 0.19165 69.61743-0.209 104.42398-3.295 0.431-7.095 0.21399-10.45 0.11999l-0.05499-86.1c0-5.40799 0.46201-41.423-0.492-43.74401l-0.52001 0.088c-3.811 4.644-8.438 11.73901-11.88 16.78601l-16.07201 23.31601-18.244 26.39899c-3.097 4.48001-7.458 10.38599-10.13 14.97699-2.332-2.71899-5.425-7.08402-7.662-10.043l-43.78-58.36801-14.79-19.72c-2.828-3.771-6.039-8.244-9.032-11.80901 14.87 0.591 11.156-2.774 20.362 9.437l11.14301 14.77405 26.834 35.561c5.548 7.349 11.127 14.97501 16.88099 22.12399 3.516-3.99301 10.058-14.07999 13.294-18.76001l27.139-39.414c4.522-6.61301 8.99899-13.45599 13.686-19.94099 1.81101-2.505 3.262-3.489 6.278-3.851m24.435 0.24001c2.621-0.194 5.021-0.11501 7.631 0.1 0.397 11.041 0.367 22.511 0.47099 33.58q0.34 30.36002 0.269 60.72 0.069 18.522-0.194 37.04001c-0.037 2.37299 0.19 15.345-0.74899 16.53202l-0.85201 0.00299c-2.925-0.103-5.56399-0.068-8.479-0.01502-0.465-13.72799-0.472-28.28601-0.579-42.02899q-0.332-32.656-0.235-65.31201-0.015-14.092 0.21001-28.18201c0.03399-2.18601 0.018-9.661 0.40899-11.48199 0.84801-0.83299 0.77001-0.601 2.09801-0.955m241.35501 0.15499c3.10501 0.10001 6.66501 0.033 9.79901 0.038-0.25201 2.703 0.13501 10.22301 0.185 13.38099q0.17499 13.022 0.211 26.04502l0.03201 64.586c-0.02899 14.18301 0.09101 29.17999-0.501 43.29199l-0.12601 0.16699c-0.77899 0.46301-7.76099 0.24301-9.58899 0.384 0.487-39.281-0.23401-78.69699 0.06201-117.99301 0.07599-9.886 0.13699-20.02499-0.073-29.89999m-401.06903 22.15802c1.755 0.33701 9.79 10.23401 10.11 11.70499 1.223 5.616 0.705 20.26999 0.703 25.99899l-0.008 41.19699c0.002 4.71399 0.456 44.91501-0.254 46.85199-2.799-0.33402-7.585-0.18201-10.416-0.065 0.5-25.76599 0.044-53.19699 0.05-79.107l0.003-29.75799c0-5.047 0.217-12.036-0.188-16.823"/></svg>
	</svg>
	<?php
}

/**
 * Grey image placeholder (pen component "Image Placeholder": #EDEDED box with a
 * 28px lucide image icon centred). The lofi is built literally, so every product
 * and editorial image renders as this box at the size the screen gives it.
 *
 * @param string $class Size/position class from the page CSS.
 * @param string $label Accessible label, e.g. the product name.
 * @param int    $icon  Icon size: 28 on images, 18 on bag thumbs, 14 on checkout thumbs.
 */
function miroir_ph( $class = '', $label = '', $icon = 28 ) {
	printf(
		'<div class="ph %1$s"%2$s>%3$s</div>',
		esc_attr( $class ),
		$label ? ' role="img" aria-label="' . esc_attr( $label ) . '"' : ' aria-hidden="true"',
		miroir_icon( 'image', $icon, 'ph__icon' ) // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG.
	);
}

/**
 * Pen component "Section Link": label followed by a 62px long arrow.
 *
 * @param string $label Link text.
 * @param string $url   Target.
 * @param string $class Extra class.
 */
function miroir_section_link( $label, $url, $class = '' ) {
	printf(
		'<a class="section-link %1$s" href="%2$s"><span>%3$s</span><svg class="section-link__arrow" width="62" height="9" viewBox="0 0 62 9" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M0 4.5h61m-5-4l5 4-5 4"/></svg></a>',
		esc_attr( $class ),
		esc_url( $url ),
		esc_html( $label )
	);
}

/**
 * Number of items in the bag, for the header "bag (n)" link.
 */
function miroir_bag_count() {
	return function_exists( 'WC' ) && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
}

/** Header bag markup shared by the initial page and WooCommerce fragments. */
function miroir_header_bag() {
	$label = function_exists( 'miroir_text' ) ? miroir_text( 'nav_bag', miroir_bag_count() ) : sprintf( __( 'bag (%d)', 'miroir' ), miroir_bag_count() );
	$url   = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/' );
	return sprintf(
		'<a class="site-nav__bag" href="%1$s" aria-label="%2$s"><span class="site-nav__bag-label">%3$s</span><span class="site-nav__bag-compact" aria-hidden="true"><svg viewBox="0 0 24 28" fill="none" stroke="currentColor" stroke-width="1"><path d="M3.5 7.5h17v19h-17zM8.5 7.5v-3a3.5 3.5 0 0 1 7 0v3"/></svg><span class="site-nav__bag-count">%4$d</span></span></a>',
		esc_url( $url ),
		esc_attr( $label ),
		esc_html( $label ),
		miroir_bag_count()
	);
}

/**
 * Header/footer menu, falling back to the lofi's fixed links until menus are assigned.
 *
 * @param string $location Menu location.
 * @param array  $fallback label => url pairs.
 * @param string $class    List class.
 */
function miroir_menu( $location, $fallback, $class ) {
	if ( has_nav_menu( $location ) ) {
		wp_nav_menu(
			array(
				'theme_location' => $location,
				'container'      => false,
				'menu_class'     => $class,
				'depth'          => 1,
				'fallback_cb'    => false,
			)
		);
		return;
	}
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $fallback as $label => $url ) {
		printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( $url ), esc_html( $label ) );
	}
	echo '</ul>';
}

/**
 * URL of a page by slug, or home when it does not exist yet.
 *
 * @param string $slug Page slug.
 */
function miroir_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	return $page ? get_permalink( $page ) : home_url( "/{$slug}/" );
}

/**
 * Pen component "Field": mono label over an underline input or select.
 *
 * @param array $args {
 *     @type string $name         Input name (also the id unless $id is given).
 *     @type string $label        Visible label, including " *" when required.
 *     @type string $type         Input type, or 'select'.
 *     @type string $value        Current value.
 *     @type array  $options      value => label, for selects.
 *     @type string $placeholder  Placeholder.
 *     @type string $autocomplete Autocomplete token.
 *     @type bool   $required     Required.
 *     @type string $id           Element id.
 *     @type string $class        Extra wrapper class.
 * }
 */
function miroir_field( $args ) {
	$a  = wp_parse_args(
		$args,
		array(
			'name'         => '',
			'label'        => '',
			'type'         => 'text',
			'value'        => '',
			'options'      => array(),
			'placeholder'  => '',
			'autocomplete' => '',
			'required'     => false,
			'id'           => '',
			'class'        => '',
		)
	);
	$id = $a['id'] ? $a['id'] : $a['name'];

	$attrs = sprintf(
		'class="field__input%s" id="%s" name="%s"%s%s%s',
		'select' === $a['type'] ? ' field__input--select' : '',
		esc_attr( $id ),
		esc_attr( $a['name'] ),
		$a['autocomplete'] ? ' autocomplete="' . esc_attr( $a['autocomplete'] ) . '"' : '',
		$a['required'] ? ' required aria-required="true"' : '',
		$a['placeholder'] && 'select' !== $a['type'] ? ' placeholder="' . esc_attr( $a['placeholder'] ) . '"' : ''
	);
	?>
	<div class="field <?php echo esc_attr( $a['class'] ); ?>">
		<label class="field__label" for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $a['label'] ); ?></label>
		<?php if ( 'select' === $a['type'] ) : ?>
			<select <?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?>>
				<?php if ( $a['placeholder'] ) : ?>
					<option value=""><?php echo esc_html( $a['placeholder'] ); ?></option>
				<?php endif; ?>
				<?php foreach ( $a['options'] as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( (string) $a['value'], (string) $value ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		<?php else : ?>
			<input type="<?php echo esc_attr( $a['type'] ); ?>" <?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above. ?> value="<?php echo esc_attr( $a['value'] ); ?>">
		<?php endif; ?>
	</div>
	<?php
}

/**
 * URL of a product category by slug, falling back to the shop.
 *
 * @param string $slug Category slug.
 */
function miroir_cat_url( $slug ) {
	$term = get_term_by( 'slug', $slug, 'product_cat' );
	if ( $term && ! is_wp_error( $term ) ) {
		return get_term_link( $term );
	}
	return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
}

/**
 * Home page sections in the order of screen 01. Editorial layouts:
 * "small-first" (small image centred beside the large one) and "large-first".
 */
function miroir_home_sections() {
	$shop = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

	return apply_filters(
		'miroir_home_sections',
		array(
			array(
				'type'    => 'editorial',
				'layout'  => 'small-first',
				'caption' => __( 'new arrivals: browse the collection', 'miroir' ),
				'url'     => miroir_cat_url( 'new-collection' ),
			),
			array(
				'type'     => 'strip',
				'label'    => __( 'new arrivals', 'miroir' ),
				'category' => 'new-collection',
				'columns'  => 5,
				'limit'    => -1,
				'url'      => miroir_cat_url( 'new-collection' ),
			),
			array(
				'type'    => 'editorial',
				'layout'  => 'small-first',
				'caption' => __( 'shop women: summer collection', 'miroir' ),
				'url'     => $shop,
			),
			array(
				'type'    => 'editorial',
				'layout'  => 'large-first',
				'caption' => __( 'shop women: browse the collection', 'miroir' ),
				'url'     => $shop,
			),
			array(
				'type'    => 'banner',
				'caption' => __( 'sale: mid year sale 2026', 'miroir' ),
				'url'     => miroir_cat_url( 'sale' ),
			),
			array(
				'type'     => 'strip',
				'label'    => __( 'shop studio collection', 'miroir' ),
				'category' => 'at-the-studio',
				'columns'  => 6,
				'limit'    => -1,
				'url'      => miroir_cat_url( 'at-the-studio' ),
			),
		)
	);
}

/**
 * Floating WhatsApp button (pen component "WhatsApp Float").
 */
function miroir_whatsapp_float() {
	$url = apply_filters( 'miroir_whatsapp_url', 'https://wa.me/' . preg_replace( '/\D/', '', (string) get_option( 'miroir_wa_number', '' ) ) );
	printf(
		'<a class="wa-float" href="%1$s" target="_blank" rel="noopener" aria-label="%2$s">%3$s</a>',
		esc_url( $url ),
		esc_attr__( 'Chat with us on WhatsApp', 'miroir' ),
		miroir_icon( 'message-circle', 24 ) // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG.
	);
}

/**
 * Page content links to "#whatsapp" (e.g. a Button block) open the shop's
 * WhatsApp, so editors never paste the number into content.
 */
add_filter(
	'the_content',
	function ( $content ) {
		if ( false === strpos( $content, '#whatsapp' ) ) {
			return $content;
		}
		$url = apply_filters( 'miroir_whatsapp_url', 'https://wa.me/' . preg_replace( '/\D/', '', (string) get_option( 'miroir_wa_number', '' ) ) );
		return str_replace( array( 'href="#whatsapp"', "href='#whatsapp'" ), 'href="' . esc_url( $url ) . '" target="_blank" rel="noopener"', $content );
	}
);

/**
 * Which header panel (31-34) the request opens server-side: ?panel=nav|search|filters|currency.
 * JS opens the same panels in place without a reload.
 */
function miroir_open_panel() {
	$panel = sanitize_key( wp_unslash( $_GET['panel'] ?? '' ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return in_array( $panel, array( 'nav', 'search', 'filters', 'currency' ), true ) ? $panel : '';
}
