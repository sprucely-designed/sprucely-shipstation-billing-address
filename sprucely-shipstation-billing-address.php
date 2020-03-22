<?php
/**
 * Plugin Name:       ShipStation Billing Address
 * Plugin URI:        https://www.sprucely.net/support?utm_source=sprucelyshipstationbillingaddr&utm_campaign=author_uri&utm_medium=plugin_uri
 * Description:       This adds the customer billing address to Custom Field #2 in ShipStation if different than the shipping address
 * Author:            Terry @ Sprucely Designed
 * Author URI:        https://www.sprucely.net/support
 * Version:           1.0.0
 */

if( !function_exists( 'shipstation_custom_field_2' )){
	die;
}

$billing_meta_key = '_ss_billing_info';

add_filter( 'woocommerce_shipstation_export_custom_field_2', 'shipstation_custom_field_2' );
/**
 * Update custom field 2 that is sent to SS.
 *
 * @see https://gist.github.com/woogists/53d13508835276e66578f9c6f2398d03#file-wc-export-custom-field-data-php
 * @return string Meta Key
 */
function shipstation_custom_field_2() {
	return $billing_meta_key; // Replace this with the key of your custom field.
}

add_action( 'woocommerce_checkout_create_order', 'sprucely_add_billing_address', 10, 2 );

function sprucely_add_billing_address( $order, $data ) {
	$billing_address  = $order->get_formatted_billing_address();
	$shipping_address = $order->get_formatted_shipping_address();
	$billing_name     = $order->get_formatted_billing_full_name();

	if ( $billing_address != $shipping_address ) {

		$billing_address = sanitize_text_field(
			'<td align="right" style="width: .75in">
				<b>Purchased by:</b>
			</td>
			<td style="width: 2.5in; font-size: 14px">
				<div>' . $billing_name . '</div>
				<div>' . $billing_address . '</div>
			</td>'
		);

		$order->add_meta_data( $billing_meta_key, $billing_address );
	} else {
		$order->add_meta_data( $billing_meta_key, '' );
	}
}
