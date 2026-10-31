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
			<h1 class="flow__title"><?php esc_html_e( 'Orders', 'miroir' ); ?></h1>
			<p class="flow__label"><a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>"><?php esc_html_e( 'My account', 'miroir' ); ?></a> / <?php esc_html_e( 'Orders', 'miroir' ); ?></p>
			<?php woocommerce_output_all_notices(); ?>
			<p class="flow__heading"><?php esc_html_e( 'No orders to show yet.', 'miroir' ); ?></p>
			<p class="flow__intro"><?php esc_html_e( 'Orders appear here once recorded by our team. If you have ordered through WhatsApp, contact us for an update.', 'miroir' ); ?></p>
			<a class="btn btn--primary btn--block" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Explore the collection', 'miroir' ); ?></a>
			<a class="btn btn--block" href="<?php echo esc_url( apply_filters( 'miroir_whatsapp_url', miroir_page_url( 'contact' ) ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Ask about an existing order', 'miroir' ); ?></a>
		</div>
	</div>
	<?php
	return;
endif;

if ( 'orders' === $endpoint ) :
	?>
	<div class="wrap account-wrap">
		<div class="wrap__col">
			<h1 class="page-title"><?php esc_html_e( 'My account', 'miroir' ); ?></h1>
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
	''             => __( 'My account', 'miroir' ),
	'edit-account' => __( 'Account details', 'miroir' ),
	'edit-address' => get_query_var( 'edit-address' ) ? __( 'Edit address', 'miroir' ) : __( 'My addresses', 'miroir' ),
	'view-order'   => __( 'Order details', 'miroir' ),
);
?>
<div class="flow account-flow">
	<div class="flow__col">
		<h1 class="flow__title"><?php echo esc_html( $titles[ $endpoint ] ?? wc_page_endpoint_title( __( 'My account', 'miroir' ) ) ); ?></h1>
		<?php wc_get_template( 'myaccount/navigation-tabs.php' ); ?>
		<?php do_action( 'woocommerce_account_content' ); ?>
	</div>
</div>
