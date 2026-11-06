<?php
/**
 * My account shell. Orders (screen 09) keep the original side-menu layout;
 * dashboard, details, addresses and order details (screens 17-21) use the
 * content column with tabs.
 */

defined( 'ABSPATH' ) || exit;

$endpoint = WC()->query->get_current_endpoint();

// Orders with nothing to show (screen 42): own column layout, no account menu.
if ( 'orders' === $endpoint && ! wc_get_orders( array( 'customer' => get_current_user_id(), 'limit' => 1, 'return' => 'ids' ) ) ) :
	?>
	<div class="flow">
		<div class="flow__col">
			<h1 class="flow__title"><?php echo esc_html( miroir_text( 'acc_orders' ) ); ?></h1>
			<p class="flow__label"><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo esc_html( miroir_text( 'my_account' ) ); ?></a> / <?php echo esc_html( miroir_text( 'acc_orders' ) ); ?></p>
			<?php woocommerce_output_all_notices(); ?>
			<p class="flow__heading"><?php echo esc_html( miroir_text( 'orders_empty' ) ); ?></p>
			<p class="flow__intro"><?php echo esc_html( miroir_text( 'orders_empty_text' ) ); ?></p>
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php echo esc_html( miroir_text( 'orders_empty_shop' ) ); ?></a>
			<a class="btn btn--block" href="<?php echo esc_url( apply_filters( 'miroir_whatsapp_url', miroir_page_url( 'contact' ) ) ); ?>" target="_blank" rel="noopener"><?php echo miroir_icon( 'whatsapp', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput -- static SVG. ?><?php echo esc_html( miroir_text( 'orders_empty_ask' ) ); ?></a>
		</div>
	</div>
	<?php
	return;
endif;

if ( 'orders' === $endpoint ) :
	?>
	<div class="wrap account-wrap">
		<div class="wrap__col">
			<h1 class="page-title"><?php echo esc_html( miroir_text( 'my_account' ) ); ?></h1>
			<div class="account">
				<?php do_action( 'woocommerce_account_navigation' ); ?>
				<div class="account__content woocommerce-MyAccount-content">
					<?php do_action( 'woocommerce_account_content' ); ?>
				</div>
			</div>
		</div>
	</div>
	<?php
	return;
endif;

$titles = array(
	''             => miroir_text( 'my_account' ),
	'edit-account' => miroir_text( 'acc_details' ),
	'edit-address' => get_query_var( 'edit-address' ) ? miroir_text( 'acc_edit_address' ) : miroir_text( 'acc_my_addresses' ),
	'view-order'   => miroir_text( 'acc_order_details' ),
);
?>
<div class="flow account-flow">
	<div class="flow__col">
		<h1 class="flow__title"><?php echo esc_html( $titles[ $endpoint ] ?? wc_page_endpoint_title( miroir_text( 'my_account' ) ) ); ?></h1>
		<?php wc_get_template( 'myaccount/navigation-tabs.php' ); ?>
		<?php do_action( 'woocommerce_account_content' ); ?>
	</div>
</div>
