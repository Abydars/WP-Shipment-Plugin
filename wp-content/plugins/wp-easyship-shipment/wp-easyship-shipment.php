<?php
/*
Plugin Name: WPSP Easyship Add-on
Plugin URI: hztech.biz
description: This plugin works only with WPSP Shipment Plugin.
Version: 1.0
Author: Hztech
Author URI: hztech.biz
*/

// Filters
add_filter( 'wpsp_shipment_carriers', 'wpsp_add_ups_carrier' );

// Actions
add_action( 'wpsp_create_shipment_ups', 'wpsp_easyship_create_shipment', 10, 2 );
add_action( 'wpsp_create_shipment_fedex', 'wpsp_easyship_create_shipment', 10, 2 );

function wpsp_easyship_create_shipment( $data, &$error, &$shipment_id )
{
	$courier_id = 'b4552ed2-ae95-4647-9746-5790bf252c7f';

	if ( $data->carrier == 'fedex' ) {
		$courier_id = '';
	}

	$from_address = WPSP_Address::getAddress( $data->from );
	$to_address   = WPSP_Address::getAddress( $data->to );

	$data = [
		'selected_courier_id'        => '',
		'destination_country_alpha2' => $to_address->country,
		'destination_city'           => $to_address->city,
		'destination_postal_code'    => $to_address->postal_code,
		'destination_state'          => $to_address->state,
		'destination_name'           => $to_address->full_name,
		'destination_address_line_1' => $to_address->street_1,
		'destination_address_line_2' => $to_address->street_2,
		'destination_phone_number'   => $to_address->number,
		'destination_email_address'  => $to_address->email,
		'items'                      => [
			'description'            => $data->packages[0]->description,
			'sku'                    => $data->packages[0]->sku,
			'actual_weight'          => $data->packages[0]->weight,
			'height'                 => $data->packages[0]->height,
			'width'                  => $data->packages[0]->width,
			'length'                 => $data->packages[0]->length,
			'declared_currency'      => $data->packages[0]->currency,
			'declared_customs_value' => $data->packages[0]->value,
		]
	];

	$error       = false;
	$shipment_id = 1;
}

function wpsp_add_ups_carrier( $carriers )
{
	$carriers['ups']   = 'UPS';
	$carriers['fedex'] = 'Fedex';

	return $carriers;
}