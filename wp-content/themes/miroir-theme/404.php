<?php
/**
 * Page not found (screen 27): product search and a way back to the shop.
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="flow flow--loose">
	<div class="flow__col">
		<h1 class="flow__title"><?php echo esc_html( miroir_text( 'nf_title' ) ); ?></h1>
		<p class="flow__intro"><?php echo esc_html( miroir_text( 'nf_text' ) ); ?></p>
		<form class="flow__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<?php miroir_field( array( 'name' => 's', 'id' => 'notfound-search', 'type' => 'search', 'label' => miroir_text( 'search_label' ), 'placeholder' => miroir_text( 'nf_placeholder' ) ) ); ?>
			<input type="hidden" name="post_type" value="product">
			<button class="btn btn--primary btn--block" type="submit"><?php echo esc_html( miroir_text( 'search_submit' ) ); ?></button>
		</form>
		<a class="btn btn--primary btn--block" href="<?php echo esc_url( function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ) ); ?>"><?php echo esc_html( miroir_text( 'continue_shopping' ) ); ?></a>
	</div>
</div>
<?php
get_footer();
