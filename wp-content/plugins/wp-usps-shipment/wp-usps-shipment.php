<?php
/*
Plugin Name: WPSP USPS Add-on
Plugin URI: hztech.biz
description: This plugin works only with WPSP Shipment Plugin.
Version: 1.0
Author: Hztech
Author URI: hztech.biz
*/

// Filters
add_filter( 'wpsp_shipment_carriers', 'wpsp_add_usps_carrier' );

// Actions
add_action( 'wpsp_create_shipment_usps', 'wpsp_usps_create_shipment', 10, 3 );
add_action( 'wpsp_label_rates_usps', 'wpsp_label_rates_usps', 10, 3 );

function wpsp_label_rates_usps( $data, &$error, &$rates )
{
	$error = false;
	$rates = 10;
}

function wpsp_usps_create_shipment( $data, &$error, &$shipment_id )
{
	/*
	 * TASK FOR ASAD
	 *
	 * Yahan par tumhe USPS ki API execute karni hai, $data ke variable men saari fields milengi tumhe
	 * var_dump $data karke check bhi karskte ho kia kia milega tumhe yahan par, console open karke admin
	 * se form submit karna, Network men response check karna, jo yahan var dump karoge wahan show hoga.
	 * Customer "Hadmin Hadmin" select karna
	 * and Carrier men USPS
	 * then submit karna
	 *
	 */
	var_dump( $data );
	die;

	$error       = false;
	$shipment_id = 1;
}

function wpsp_add_usps_carrier( $carriers )
{
	$carriers['usps'] = 'USPS';

	return $carriers;
}