<?php
/**
 * Page not found (screen 27): product search and a way back to the shop.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="flow flow--loose">
	<div class="flow__col">
		<h1 class="flow__title"><?php esc_html_e( 'Page not found', 'miroir' ); ?></h1>
		<p class="flow__intro"><?php esc_html_e( '404 — this page is no longer here. The link may have changed or the page may have been removed.', 'miroir' ); ?></p>
		<form class="flow__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php miroir_field( array( 'name' => 's', 'id' => 'notfound-search', 'type' => 'search', 'label' => __( 'Search products', 'miroir' ), 'placeholder' => __( 'Search by product name', 'miroir' ) ) ); ?>
			<input type="hidden" name="post_type" value="product">
			<button class="btn btn--primary btn--block" type="submit"><?php esc_html_e( 'Search', 'miroir' ); ?></button>
		</form>
		<a class="btn btn--primary btn--block" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>"><?php esc_html_e( 'Continue shopping', 'miroir' ); ?></a>
	</div>
</div>
<?php
get_footer();
