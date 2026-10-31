<?php
/**
 * Orders (screen 09). The empty state is screen 42.
 *
 * @var bool   $has_orders
 * @var object $customer_orders
 * @var int    $current_page
 */

defined( 'ABSPATH' ) || exit;

$user = wp_get_current_user();

do_action( 'woocommerce_before_account_orders', $has_orders );
?>
<p class="account__greeting">
	<?php
	printf(
		/* translators: 1: display name, 2: logout URL. */
		wp_kses( __( 'Hello, %1$s (not you? <a href="%2$s">Log out</a>)', 'miroir' ), array( 'a' => array( 'href' => array() ) ) ),
		esc_html( $user->display_name ),
		esc_url( wc_logout_url() )
	);
	?>
</p>

<?php if ( $has_orders ) : ?>
	<div class="orders-table">
		<div class="orders-table__row orders-table__head hair-b" aria-hidden="true">
			<span class="orders-table__cell orders-table__cell--no"><?php esc_html_e( 'order', 'miroir' ); ?></span>
			<span class="orders-table__cell orders-table__cell--date"><?php esc_html_e( 'date', 'miroir' ); ?></span>
			<span class="orders-table__cell orders-table__cell--status"><?php esc_html_e( 'status', 'miroir' ); ?></span>
			<span class="orders-table__cell orders-table__cell--total"><?php esc_html_e( 'total', 'miroir' ); ?></span>
			<span class="orders-table__cell orders-table__cell--actions"><?php esc_html_e( 'actions', 'miroir' ); ?></span>
		</div>
		<?php
		foreach ( $customer_orders->orders as $customer_order ) :
			$order = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$count = $order->get_item_count() - $order->get_item_count_refunded();
			?>
			<div class="orders-table__row hair-b">
				<span class="orders-table__cell orders-table__cell--no">#<?php echo esc_html( $order->get_order_number() ); ?></span>
				<span class="orders-table__cell orders-table__cell--date"><time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created(), 'd M Y' ) ); ?></time></span>
				<span class="orders-table__cell orders-table__cell--status"><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span>
				<span class="orders-table__cell orders-table__cell--total">
					<?php
					/* translators: 1: formatted order total, 2: item count. */
					echo wp_kses_post( sprintf( _n( '%1$s for %2$s item', '%1$s for %2$s items', $count, 'miroir' ), $order->get_formatted_order_total(), $count ) );
					?>
				</span>
				<span class="orders-table__cell orders-table__cell--actions">
					<a class="btn btn--sm" href="<?php echo esc_url( $order->get_view_order_url() ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: order number. */ __( 'View order %s', 'miroir' ), $order->get_order_number() ) ); ?>"><?php esc_html_e( 'View', 'miroir' ); ?></a>
				</span>
			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<nav class="orders-pager">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="btn-link" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'Previous', 'miroir' ); ?></a>
			<?php endif; ?>
			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
				<a class="btn-link" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'miroir' ); ?></a>
			<?php endif; ?>
		</nav>
	<?php endif; ?>
<?php else : ?>
	<p class="account__empty"><?php esc_html_e( 'No order has been made yet.', 'miroir' ); ?></p>
<?php endif; ?>
<?php
do_action( 'woocommerce_after_account_orders', $has_orders );
