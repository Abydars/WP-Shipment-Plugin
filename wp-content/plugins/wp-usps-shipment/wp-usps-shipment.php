<?php
/*
Plugin Name: WPSP USPS Add-on
Plugin URI: hztech.biz
description: This plugin works only with WPSP Shipment Plugin.
Version: 1.0
Author: Hztech
Author URI: hztech.biz
*/

define( 'WPSP_USPS_USER_ID', '572SHIP46470' );
define( 'WPSP_USPS_DEBUG', true );

//includes
include 'includes/class-shipment-xml-array.php';
include 'includes/class-shipment-array-xml.php';

class WPSP_USPS
{
	function __construct()
	{
		// Filters
		add_filter( 'wpsp_shipment_carriers', [ $this, 'wpsp_add_usps_carrier' ] );
		add_filter( 'wpsp_shipment_usps_levels', [ $this, 'wpsp_shipment_usps_levels' ] );
		add_filter( 'wpsp_shipment_usps_package_types', [ $this, 'wpsp_shipment_usps_package_types' ] );
		add_filter( 'wpsp_get_markup_rate_usps', [ $this, 'wpsp_get_markup_rate_usps' ], 10, 3 );

		// Actions
		add_action( 'wpsp_verify_address_usps', [ $this, 'wpsp_verify_address_usps' ], 10, 3 );
		add_action( 'wpsp_create_shipment_usps', [ $this, 'wpsp_usps_create_shipment' ], 10, 3 );
		add_action( 'wpsp_create_label_usps', [ $this, 'wpsp_create_label_usps' ], 10, 3 );
		add_action( 'wpsp_label_rates_usps', [ $this, 'wpsp_label_rates_usps' ], 10, 3 );
		add_action( 'wpsp_void_label_usps', [ $this, 'wpsp_void_label_usps' ], 10, 2 );
	}

	function wpsp_void_label_usps( &$error, $shipment_id )
	{
		$error    = false;
		$shipment = WPSP_Shipment::get_shipment( $shipment_id );

		try {
			$d = [
				'BarcodeNumber' => $shipment->shipKey
			];

			$xml = ShipmentArrayToXml::convert( $d, [
				'rootElementName' => 'eVSCancelRequest',
				'_attributes'     => [
					'USERID' => WPSP_USPS_USER_ID,
				],
			], true, 'UTF-8' );

			$url = add_query_arg( [
				                      'API' => 'eVSCancel',
				                      'XML' => urlencode( $xml )
			                      ], "ShippingAPI.dll" );

			$res = $this->request( $url );
			$res = ShipmentXmlToArray::convert( $res );

			if ( isset( $res['Error'] ) ) {
				$error = $res['Error']['Description'];
			} else {
				$res = $res['eVSCancelResponse'];

				if ( isset( $res['Status'] ) && $res['Status'] != 'Cancelled' ) {
					$error = $res['Reason'];
				}
			}

		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}
	}

	function wpsp_get_markup_rate_usps( $rate, $customer )
	{
		$value = get_user_meta( $customer, 'usps_rate', true );

		if ( $value === false ) {
			$value = 0;
		}

		return floatval( $value );
	}

	function wpsp_label_rates_usps2( $data, &$error, &$rates )
	{
		// TODO: RATES API

		$rates = 5;
		$error = false;
	}

	function wpsp_label_rates_usps( $data, &$error, &$rates )
	{
		$error        = false;
		$from_address = WPSP_Address::getAddress( $data->from );
		$to_address   = WPSP_Address::getAddress( $data->to );
		$from_zip     = $from_address['zip_code'];
		$to_zip       = $to_address['zip_code'];

		$d = [
			'Revision' => 2,
		];

		foreach ( $data->packages as $k => $package ) {
			$id = $k + 1;

			$d["Package_{$id}"] = [
				'_attributes'    => [
					'ID' => $id,
				],
				'Service'        => $data->shipping_method,
				'ZipOrigination' => $from_zip,
				'ZipDestination' => $to_zip,
				'Pounds'         => ( $package['weight'] / 16 ),
				'Ounces'         => $package['weight'],
				'Container'      => $data->package_type,
				'Size'           => 'LARGE',
				'Width'          => $package['width'],
				'Length'         => $package['length'],
				'Height'         => $package['height'],
				'Girth'          => ( ( $package['width'] + $package['height'] ) * 2 )
			];
		}

		$xml = ShipmentArrayToXml::convert( $d, [
			'rootElementName' => 'RateV4Request',
			'_attributes'     => [
				'USERID' => WPSP_USPS_USER_ID,
			],
		], true, 'UTF-8' );

		foreach ( $data->packages as $k => $package ) {
			$id  = $k + 1;
			$xml = str_replace( "Package_{$id}", "Package", $xml );
		}

		$url = add_query_arg( [
			                      'API' => 'RateV4',
			                      'XML' => urlencode( $xml )
		                      ], "ShippingAPI.dll" );

		$res = $this->request( $url );
		$res = ShipmentXmlToArray::convert( $res );

		if ( ! isset( $res['Error'] ) ) {
			$res   = $res['RateV4Response'];
			$rates = 0;

			if ( count( $data->packages ) > 1 ) {
				foreach ( $res['Package'] as $package ) {
					if ( ! empty( $package['Postage']['Rate'] ) ) {
						$rates += $package['Postage']['Rate'];
					}
				}
			} else {
				$rates += $res['Package']['Postage']['Rate'];
			}
		} else {
			$error = $res['Error']['Description'];
		}
	}

	function wpsp_create_label_usps( &$error, &$encoded_image, $shipment_data )
	{
		$encoded_image = $shipment_data['label'];
		$error         = empty( $encoded_image ) ? __( 'Label not found', WPSP_LANG ) : false;
	}

	function wpsp_usps_create_shipment( $data, &$error, &$shipment_data )
	{
		try {
			$error        = false;
			$from_address = WPSP_Address::getAddress( $data->from );
			$to_address   = WPSP_Address::getAddress( $data->to );
			$from_zip     = $from_address['zip_code'];
			$packages     = $data->packages;

			list( $from_zip4, $from_zip5 ) = $this->zip4_5( $from_zip );

			foreach ( $packages as $package ) {
				$d = [
					'Option'                     => 1,
					'ImageParameters'            => [ 'ImageParameter' => 'CROP' ],
					'FromName'                   => $from_address['full_name'],
					'FromFirm'                   => $from_address['company'],
					'FromAddress1'               => $from_address['street_2'],
					'FromAddress2'               => $from_address['street_1'],
					'FromCity'                   => $from_address['city'],
					'FromState'                  => $from_address['state'],
					'FromZip5'                   => $from_zip5,
					'FromZip4'                   => $from_zip4,
					'FromPhone'                  => $from_address['phone'],
					'AllowNonCleansedOriginAddr' => "false",
					'ToName'                     => $to_address['full_name'],
					'ToFirm'                     => $to_address['company'],
					'ToAddress1'                 => $to_address['street_2'],
					'ToAddress2'                 => $to_address['street_1'],
					'ToCity'                     => $to_address['city'],
					'ToState'                    => $to_address['state'],
					'ToZip5'                     => $from_zip5,
					'ToZip4'                     => $from_zip4,
					'ToPhone'                    => $to_address['phone'],
					'POBox'                      => "false",
					'AllowNonCleansedDestAddr'   => "false",
					'WeightInOunces'             => $package['weight'],
					'ServiceType'                => $data->shipping_method,
					'Container'                  => $data->package_type,
					'Width'                      => $package['width'],
					'Length'                     => $package['length'],
					'Height'                     => $package['height'],
					'Machinable'                 => "true",
					'ProcessingCategory'         => [],
					'PriceOptions'               => 'Commercial Plus',
					'AddressServiceRequested'    => "true",
					'ExpressMailOptions'         => [ 'DeliveryOption' => [], 'WaiverOfSignature' => [] ],
					'ShipDate'                   => date( "m/d/Y", strtotime( $data->shipping_date ) ),
					'CustomerRefNo'              => $data->customer,
					'RecipientName'              => $to_address['fullName'],
					'ImageType'                  => 'PDF',
					'PrintCustomerRefNo'         => "false",
					'OptOutOfSPE'                => "false",
					'ePostageMailerReporting'    => []
				];

				$xml = ShipmentArrayToXml::convert( $d, [
					'rootElementName' => 'eVSRequest',
					'_attributes'     => [
						'USERID' => WPSP_USPS_USER_ID,
					],
				], true, 'UTF-8' );

				$url = add_query_arg( [
					                      'API' => 'eVS',
					                      'XML' => urlencode( $xml )
				                      ], "ShippingAPI.dll" );

				$res = $this->request( $url );
				$res = ShipmentXmlToArray::convert( $res );

				if ( ! isset( $res['Error'] ) ) {
					$res           = $res['eVSResponse'];
					$shipment_data = array(
						"shipKey" => $res['BarcodeNumber'],
						"label"   => $res['LabelImage']
					);
				} else {
					$error = $res['Error']['Description'];
				}
			}
		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}
	}

	function wpsp_verify_address_usps( $data, &$error )
	{

		list( $from_zip4, $from_zip5 ) = $this->zip4_5( $data->zip_code );
		$d = [
			'Address' => [
				'_attributes' => [
					'ID' => 0,
				],
				'Address1'    => $data->street_1,
				'Address2'    => $data->street_2,
				'City'        => $data->city,
				'State'       => $data->state,
				'Zip5'        => $from_zip5,
				'Zip4'        => $from_zip4,
			]
		];

		$xml = ShipmentArrayToXml::convert( $d, [
			'rootElementName' => 'AddressValidateRequest',
			'_attributes'     => [
				'USERID' => WPSP_USPS_USER_ID,
			],
		], true, 'UTF-8' );

		$url = add_query_arg( [
			                      'API' => 'Verify',
			                      'XML' => urlencode( $xml )
		                      ], "ShippingAPI.dll" );

		$res = $this->request( $url );
		$res = ShipmentXmlToArray::convert( $res );

		if ( ! isset( $res['AddressValidateResponse']['Address']['Error'] ) ) {
			$error = false;
		} else {
			$error = $res['AddressValidateResponse']['Address']['Error']['Description'];

		}
	}

	function wpsp_add_usps_carrier( $carriers )
	{
		$carriers['usps'] = 'USPS';

		return $carriers;
	}

	function wpsp_shipment_usps_levels()
	{
		$levels = [
			'PRIORITY EXPRESS',
			'PRIORITY',
			'FIRST CLASS',
			'PARCEL SELECT GROUND',
			'LIBRARY',
			'MEDIA',
			'BPM',
			'PRIORITY MAIL CUBIC'
		];

		return $levels;
	}

	function wpsp_shipment_usps_package_types()
	{
		$levels = [
			'VARIABLE',
			'FLAT RATE ENVELOPE',
			'LEGAL FLAT RATE ENVELOPE',
			'PADDED FLAT RATE ENVELOPE',
			'GIFT CARD FLAT RATE ENVELOPE',
			'SM FLAT RATE ENVELOPE',
			'WINDOW FLAT RATE ENVELOPE',
			'SM FLAT RATE BOX',
			'MD FLAT RATE BOX',
			'LG FLAT RATE BOX',
			'REGIONALRATEBOXA',
			'REGIONALRATEBOXB',
			'RECTANGULAR',
			'NONRECTANGULAR',
			'PACKAGE SERVICE',
			'CUBIC PARCELS',
			'CUBIC SOFT PACK'
		];

		return $levels;
	}

	function request( $endpoint )
	{
		$ch = curl_init();

		if ( WPSP_USPS_DEBUG ) {
			$endpoint = "https://stg-secure.shippingapis.com/{$endpoint}";
		} else {
			$endpoint = "https://secure.shippingapis.com/{$endpoint}";
		}

		curl_setopt( $ch, CURLOPT_URL, $endpoint );
		curl_setopt( $ch, CURLOPT_ENCODING, "" );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "GET" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

		$response = curl_exec( $ch );

		if ( curl_errno( $ch ) !== 0 ) {
			$error_message = "";

			return array(
				"status" => false,
				"error"  => $error_message
			);
		}

		if ( is_resource( $ch ) ) {
			curl_close( $ch );
		}

		return $response;
	}

	function zip4_5( $zip )
	{
		$zip4 = $zip5 = "";

		if ( strlen( $zip ) == 5 ) {
			$zip5 = $zip;
		} else if ( strlen( $zip ) == 4 ) {
			$zip4 = $zip;
		} else if ( strlen( $zip ) > 5 ) {
			$zip  = explode( '-', $zip );
			$zip5 = $zip[0];
			$zip4 = $zip[1];
		}

		return [ $zip4, $zip5 ];
	}
}

new WPSP_USPS();