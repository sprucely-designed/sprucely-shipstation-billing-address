<?php

/**
 * Plugin Name:       ShipStation Billing Address
 * Plugin URI:        https://www.sprucely.net/support?utm_source=sprucelyshipstationbillingaddr&utm_campaign=author_uri&utm_medium=plugin_uri
 * Description:       This adds the customer billing address to Custom Field #2 in ShipStation if different than the shipping address
 * Version:           1.0.0
 * Author:            Terry @ Sprucely Designed
 * Author URI:        https://www.sprucely.net/support
 */

// If this file is called directly, abort.
if ( defined( 'ABSPATH' ) ) {
	die;
}

add_action( 'woocommerce_checkout_create_order', 'addbillingaddress' );

function addbillingaddress( $order, $sent_to_admin, $plain_text, $email ) {
	$billingaddress  = $order->get_formatted_billing_address();
	$shippingaddress = $order->get_formatted_shipping_address();
	$billingname     = $order->get_formatted_billing_full_name();

	/*
	$billingaddress1 = $order->get_billing_address_1();
	$billingaddress2 = $order->get_billing_address_2();
	$billingcity = $order->get_billing_city();
	$billingstate = $order->get_billing_state();
	$billingcountry = $order->get_billing_country();

	$shippingaddress1 = $order->get_shipping_address_1();
	$shippingaddress2 = $order->get_shipping_address_2();
	$shippingcity = $order->get_shipping_city();
	$shippingstate = $order->get_shipping_state();
	$shippingcountry = $order->get_shipping_country();

	if($billingaddress1 == $shippingaddress1 and $billingaddress2 == $shippingaddress2 and
	$billingcity == $shippingcity and $billingstate == $shippingstate and $billingcountry == $shippingcountry){

		$billingaddress = $billingaddress1 . '\n' . $billingaddress2 . '\n' . $billingcity . ', ' . $billingstate . ' ' . $billingcountry;
		$billingname = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
	*/
	if ( $billingaddress != $shippingaddress ) {

		$billingaddress = sanitize_text_field(
			'<td align="right" style="width: .75in">
                                <b>Purchased by:</b>
                            </td>
                            <td style="width: 2.5in; font-size: 14px">
                                <div>' . $billingname . '</div>
                                <div>' . $billingaddress . '</div>
                            </td>'
		);

		add_filter( 'woocommerce_shipstation_export_custom_field_2', 'shipstation_custom_field_2' );
		/**
		 * Update custom field 2 that is sent to SS.
		 *
		 * @see https://gist.github.com/woogists/53d13508835276e66578f9c6f2398d03#file-wc-export-custom-field-data-php
		 * @return string Meta Key
		 */
		function shipstation_custom_field_2() {
			$billingmetakey = 'SSBillingInfo';
			$order->add_meta_data( $billingmetakey, $billingaddress );
			return $billingmetakey; // Replace this with the key of your custom field.
		}
	} else {
		add_filter( 'woocommerce_shipstation_export_custom_field_2', 'shipstation_custom_field_2' );
		/**
		 * Update custom field 2 that is sent to SS.
		 *
		 * @see https://gist.github.com/woogists/53d13508835276e66578f9c6f2398d03#file-wc-export-custom-field-data-php
		 * @return string Meta Key
		 */
		function shipstation_custom_field_2() {
			$billingmetakey = 'SSBillingInfo';
			$order->add_meta_data( $billingmetakey, ' ' );
			return $billingmetakey; // Replace this with the key of your custom field.
		}
	}
}
