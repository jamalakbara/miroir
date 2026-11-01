<?php
/**
 * Menu and filters replace page content; search and currency open as modals.
 * Header links fall back to server-opened panels without JavaScript.
 */

defined( 'ABSPATH' ) || exit;

$open     = miroir_open_panel();
$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
$arrow    = miroir_icon( 'arrow-up-right', 18 );

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
$close = function ( $label ) {
	printf( '<button class="btn btn--block" type="button" data-panel-close>%s</button>', esc_html( $label ) );
};
?>
<div class="flow panel" id="nav-panel" data-panel="nav"<?php echo 'nav' === $open ? '' : ' hidden'; ?>>
	<nav class="flow__col" aria-label="<?php esc_attr_e( 'Menu', 'miroir' ); ?>">
		<h2 class="flow__title"><?php echo esc_html( miroir_text( 'menu_title' ) ); ?></h2>
		<?php $close( miroir_text( 'menu_close' ) ); ?>
		<?php
		$locations = get_nav_menu_locations();
		$items     = ! empty( $locations['panel-nav'] ) ? wp_get_nav_menu_items( $locations['panel-nav'] ) : array();
		if ( $items ) {
			foreach ( $items as $item ) {
				$row( $item->title, $item->url );
			}
		} else {
			$row( __( 'New arrivals', 'miroir' ), miroir_cat_url( 'new-collection' ) );
			$row( __( 'Shop women', 'miroir' ), $shop_url );
			$row( __( 'Collections', 'miroir' ), miroir_cat_url( 'shop-by-collection' ) );
			$row( __( 'Sale', 'miroir' ), miroir_cat_url( 'sale' ) );
			$row( __( 'My account', 'miroir' ), function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/' ) );
			$row( __( 'Customer care', 'miroir' ), miroir_page_url( 'contact' ) );
		}
		?>
		<a class="flow__label panel__shortcut" href="<?php echo esc_url( add_query_arg( 'panel', 'currency' ) ); ?>" data-panel-open="currency"><?php echo esc_html( miroir_text( 'menu_currency', function_exists( 'get_woocommerce_currency' ) ? get_woocommerce_currency() : 'IDR' ) ); ?></a>
	</nav>
</div>

<dialog class="flow panel panel--modal" id="search-panel" data-panel="search" aria-labelledby="search-panel-title"<?php echo 'search' === $open ? ' open' : ''; ?>>
	<div class="flow__col">
		<h2 class="flow__title" id="search-panel-title"><?php echo esc_html( miroir_text( 'search_title' ) ); ?></h2>
		<?php $close( miroir_text( 'search_close' ) ); ?>
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
