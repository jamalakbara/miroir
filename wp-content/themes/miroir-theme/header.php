<?php
/**
 * Site header: pen component "Header" (dk4Eq) and its tablet/mobile overrides.
 */

defined( 'ABSPATH' ) || exit;

$announcement = get_theme_mod( 'miroir_announcement', __( 'free shipping across indonesia', 'miroir' ) );
$shop_url     = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$account_url  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' );
$sale_term    = get_term_by( 'slug', 'sale', 'product_cat' );
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'miroir' ); ?></a>

<header class="site-header">
	<div class="announce">
		<div class="announce__track">
			<?php for ( $group = 0; $group < 2; $group++ ) : ?>
				<div class="announce__group"<?php echo $group ? ' aria-hidden="true"' : ''; ?>>
					<?php for ( $i = 0; $i < 3; $i++ ) : ?>
						<p class="announce__text"<?php echo $i ? ' aria-hidden="true"' : ''; ?>><?php echo esc_html( $announcement ); ?></p>
					<?php endfor; ?>
				</div>
			<?php endfor; ?>
		</div>
	</div>

	<nav class="site-nav" aria-label="<?php esc_attr_e( 'Main', 'miroir' ); ?>">
		<a class="site-nav__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php miroir_logo(); ?></a>

		<div class="site-nav__primary">
			<?php
			miroir_menu(
				'primary',
				array(
					__( 'sale', 'miroir' )       => $sale_term && ! is_wp_error( $sale_term ) ? get_term_link( $sale_term ) : $shop_url,
					__( 'shop women', 'miroir' ) => $shop_url,
				),
				'site-nav__menu'
			);
			?>
			<a class="site-nav__search" href="<?php echo esc_url( add_query_arg( 'panel', 'search' ) ); ?>" data-panel-open="search" aria-controls="search-panel" aria-expanded="false"><?php esc_html_e( 'search', 'miroir' ); ?></a>
		</div>

		<div class="site-nav__utility">
			<?php get_template_part( 'template-parts/currency-toggle' ); ?>
			<a class="site-nav__account" href="<?php echo esc_url( $account_url ); ?>"><?php esc_html_e( 'my account', 'miroir' ); ?></a>
			<?php echo miroir_header_bag(); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped helper markup. ?>
		</div>
		<a class="site-nav__toggle" href="<?php echo esc_url( add_query_arg( 'panel', 'nav' ) ); ?>" data-panel-open="nav" aria-controls="nav-panel" aria-expanded="false" aria-label="<?php esc_attr_e( 'Menu', 'miroir' ); ?>">
			<span class="site-nav__menu-mark" aria-hidden="true"></span>
		</a>
	</nav>
</header>

<main id="main" class="site-main<?php echo in_array( miroir_open_panel(), array( 'nav', 'filters' ), true ) ? ' has-panel' : ''; ?>">
<?php get_template_part( 'template-parts/panels' ); ?>
