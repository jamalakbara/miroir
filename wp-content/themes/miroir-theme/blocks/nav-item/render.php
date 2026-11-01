<?php
/**
 * A link of the navigation bar. Search and the menu button open their panels
 * in place (assets/js/theme.js) and work as plain links without JS.
 *
 * @var array $attributes { kind, label }
 */

defined( 'ABSPATH' ) || exit;

$label = trim( (string) $attributes['label'] );

switch ( $attributes['kind'] ) {
	case 'search':
		printf(
			'<a class="site-nav__search" href="%s" data-panel-open="search" aria-controls="search-panel" aria-expanded="false">%s</a>',
			esc_url( add_query_arg( 'panel', 'search' ) ),
			esc_html( '' !== $label ? $label : miroir_text( 'nav_search' ) )
		);
		break;

	case 'account':
		printf(
			'<a class="site-nav__account" href="%s">%s</a>',
			esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' ) ),
			esc_html( '' !== $label ? $label : miroir_text( 'nav_account' ) )
		);
		break;

	case 'bag':
		echo miroir_header_bag(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped helper markup.
		break;

	case 'currency':
		get_template_part( 'template-parts/currency-toggle' );
		break;

	case 'menu-toggle':
		?>
		<a class="site-nav__toggle" href="<?php echo esc_url( add_query_arg( 'panel', 'nav' ) ); ?>" data-panel-open="nav" aria-controls="nav-panel" aria-expanded="false" aria-label="<?php esc_attr_e( 'Menu', 'miroir' ); ?>">
			<span class="site-nav__menu-mark" aria-hidden="true"></span>
		</a>
		<?php
		break;
}
