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
		?>
		<a class="site-nav__search-compact" href="<?php echo esc_url( add_query_arg( 'panel', 'search' ) ); ?>" data-panel-open="search" aria-controls="search-panel" aria-expanded="false" aria-label="<?php echo esc_attr( miroir_text( 'nav_search' ) ); ?>">
			<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true" focusable="false"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m15.5 15.5 5 5"/></svg>
		</a>
		<?php
		echo miroir_header_bag(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped helper markup.
		break;

	case 'currency':
		get_template_part( 'template-parts/currency-toggle' );
		break;

	case 'menu-toggle':
		?>
		<a class="site-nav__toggle" href="<?php echo esc_url( add_query_arg( 'panel', 'nav' ) ); ?>" data-panel-open="nav" data-menu-label="<?php esc_attr_e( 'Menu', 'miroir' ); ?>" data-close-label="<?php echo esc_attr( miroir_text( 'menu_close' ) ); ?>" aria-controls="nav-panel" aria-expanded="false" aria-label="<?php esc_attr_e( 'Menu', 'miroir' ); ?>">
			<span class="site-nav__menu-mark" aria-hidden="true"><span></span></span>
		</a>
		<?php
		break;
}
