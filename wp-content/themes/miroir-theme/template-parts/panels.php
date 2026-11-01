<?php
/**
 * Menu, search and currency open as dialogs; filters replace page content.
 * Header links fall back to server-opened panels without JavaScript.
 */

defined( 'ABSPATH' ) || exit;

$open     = miroir_open_panel();
$arrow    = miroir_icon( 'arrow-up-right', 18 );

if ( function_exists( 'WC' ) && WC()->cart ) {
	get_template_part( 'template-parts/panel-added' );
}

/**
 * One panel row link (hairline, 16px label, arrow).
 */
$row = function ( $label, $url ) use ( $arrow ) {
	printf(
		'<a class="panel-row hair-b" href="%1$s"><span>%2$s</span>%3$s</a>',
		esc_url( $url ),
		esc_html( $label ),
		$arrow // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG.
	);
};
?>
<dialog class="flow panel panel--modal panel--drawer" id="nav-panel" data-panel="nav" aria-label="<?php esc_attr_e( 'Menu', 'miroir' ); ?>"<?php echo 'nav' === $open ? ' open' : ''; ?>>
	<nav class="flow__col" aria-label="<?php esc_attr_e( 'Menu', 'miroir' ); ?>">
		<?php miroir_menu( 'primary', miroir_menu_fallback( 'primary' ), 'panel__links' ); ?>
		<a class="panel-row hair-b" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' ) ); ?>"><?php echo esc_html( miroir_text( 'nav_account' ) ); ?></a>
		<?php $currency = function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'IDR'; ?>
		<a class="panel__currency" href="<?php echo esc_url( add_query_arg( 'panel', 'currency' ) ); ?>" data-panel-open="currency" aria-controls="currency-panel" aria-expanded="false" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: currency code. */ __( 'Currency: %s', 'miroir' ), $currency ) ); ?>"><?php echo esc_html( $currency ); ?></a>
	</nav>
</dialog>

<dialog class="flow panel panel--modal" id="search-panel" data-panel="search" aria-labelledby="search-panel-title"<?php echo 'search' === $open ? ' open' : ''; ?>>
	<div class="flow__col">
		<div class="panel__header">
			<h2 class="flow__title" id="search-panel-title"><?php echo esc_html( miroir_text( 'search_title' ) ); ?></h2>
			<button class="panel__close" type="button" data-panel-close aria-label="<?php echo esc_attr( miroir_text( 'search_close' ) ); ?>"><?php echo miroir_icon( 'x', 24 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?></button>
		</div>
		<form class="flow__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php miroir_field( array( 'name' => 's', 'id' => 'panel-search', 'type' => 'search', 'label' => miroir_text( 'search_label' ), 'placeholder' => miroir_text( 'search_placeholder' ), 'class' => 'field--quiet' ) ); ?>
			<input type="hidden" name="post_type" value="product">
			<button class="btn btn--primary btn--block" type="submit"><?php echo esc_html( miroir_text( 'search_submit' ) ); ?></button>
		</form>
		<?php $popular = miroir_popular_searches(); ?>
		<?php if ( $popular ) : ?>
			<p class="flow__label"><?php echo esc_html( miroir_text( 'search_popular' ) ); ?></p>
			<?php
			foreach ( $popular as $label => $url ) {
				$row( $label, $url );
			}
			?>
		<?php endif; ?>
	</div>
</dialog>

<?php if ( function_exists( 'is_shop' ) && ( is_shop() || is_product_taxonomy() ) ) : ?>
	<?php get_template_part( 'template-parts/panel-filters', null, array( 'open' => 'filters' === $open ) ); ?>
<?php endif; ?>

<?php if ( has_action( 'miroir_currency_options' ) || function_exists( 'get_woocommerce_currency' ) ) : ?>
	<?php get_template_part( 'template-parts/panel-currency', null, array( 'open' => 'currency' === $open ) ); ?>
<?php endif; ?>
