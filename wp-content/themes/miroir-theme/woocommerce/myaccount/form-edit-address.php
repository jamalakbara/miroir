<?php
/**
 * Addresses: list (screen 19, my-address.php) or edit form (screen 20).
 * Keeps WooCommerce's edit_address contract; fields come from WooCommerce's
 * address field config (company dropped, labels as in the lofi).
 *
 * @var string $load_address 'billing' | 'shipping' | ''.
 * @var array  $address      Field configs with values.
 */

defined( 'ABSPATH' ) || exit;

if ( ! $load_address ) {
	wc_get_template( 'myaccount/my-address.php' );
	return;
}

$labels = array(
	'first_name' => __( 'First name', 'miroir' ),
	'last_name'  => __( 'Last name', 'miroir' ),
	'country'    => __( 'Country / Region', 'miroir' ),
	'address_1'  => __( 'Street address', 'miroir' ),
	'address_2'  => __( 'Apartment / unit', 'miroir' ),
	'city'       => __( 'Town / City', 'miroir' ),
	'state'      => __( 'Province', 'miroir' ),
	'postcode'   => __( 'Postcode', 'miroir' ),
	'phone'      => __( 'Phone', 'miroir' ),
	'email'      => __( 'Email address', 'miroir' ),
);
$placeholders = array(
	'address_1' => __( 'House number and street name', 'miroir' ),
	'address_2' => __( 'Optional', 'miroir' ),
	'state'     => __( 'Choose a province', 'miroir' ),
);
$country = '';
foreach ( $address as $key => $field ) {
	if ( str_ends_with( $key, '_country' ) ) {
		$country = $field['value'] ? $field['value'] : WC()->countries->get_base_country();
	}
}

do_action( 'woocommerce_before_edit_account_address_form' );
?>
<p class="flow__label"><?php echo esc_html( 'billing' === $load_address ? __( 'Billing address', 'miroir' ) : __( 'Shipping address', 'miroir' ) ); ?></p>
<form class="flow__form woocommerce-address-fields" method="post">
	<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>
	<?php
	foreach ( $address as $key => $field ) {
		$short = preg_replace( '/^(billing|shipping)_/', '', $key );
		if ( 'company' === $short ) {
			continue;
		}
		$label = ( $labels[ $short ] ?? wp_strip_all_tags( $field['label'] ?? $short ) ) . ( ! empty( $field['required'] ) ? ' *' : '' );
		$args  = array(
			'name'         => $key,
			'label'        => $label,
			'value'        => $field['value'] ?? '',
			'required'     => ! empty( $field['required'] ),
			'autocomplete' => $field['autocomplete'] ?? '',
			'placeholder'  => $placeholders[ $short ] ?? '',
			'type'         => in_array( $field['type'] ?? 'text', array( 'email', 'tel' ), true ) ? $field['type'] : 'text',
		);
		if ( 'country' === $short ) {
			$args['type']    = 'select';
			$args['value']   = $country;
			$args['options'] = WC()->countries->get_allowed_countries();
		} elseif ( 'state' === $short ) {
			$states = WC()->countries->get_states( $country );
			if ( $states ) {
				$args['type']    = 'select';
				$args['options'] = $states;
			}
		}
		miroir_field( $args );
	}
	?>
	<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>
	<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
	<input type="hidden" name="action" value="edit_address">
	<button type="submit" class="btn btn--primary btn--block" name="save_address" value="<?php esc_attr_e( 'Save address', 'miroir' ); ?>"><?php esc_html_e( 'Save address', 'miroir' ); ?></button>
</form>
<a class="btn btn--block" href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>"><?php esc_html_e( 'Cancel', 'miroir' ); ?></a>
<?php
do_action( 'woocommerce_after_edit_account_address_form' );
