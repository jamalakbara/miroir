<?php
/**
 * My addresses (screen 19): billing and shipping, each with its saved address
 * or "You have not saved an address yet." and an add/edit button.
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();
$addresses   = apply_filters(
	'woocommerce_my_account_get_addresses',
	array(
		'billing'  => __( 'Billing address', 'miroir' ),
		'shipping' => __( 'Shipping address', 'miroir' ),
	),
	$customer_id
);
?>
<?php foreach ( $addresses as $name => $title ) : ?>
	<?php $address = wc_get_account_formatted_address( $name ); ?>
	<section class="flow__section hair-t">
		<h2 class="flow__label"><?php echo esc_html( $title ); ?></h2>
		<p class="flow__copy"><?php echo $address ? wp_kses_post( $address ) : esc_html__( 'You have not saved an address yet.', 'miroir' ); ?></p>
		<a class="btn btn--block" href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>">
			<?php
			/* translators: %s: address type, e.g. "billing address". */
			echo esc_html( sprintf( $address ? __( 'Edit %s', 'miroir' ) : __( 'Add %s', 'miroir' ), strtolower( $title ) ) );
			?>
		</a>
	</section>
<?php endforeach; ?>
