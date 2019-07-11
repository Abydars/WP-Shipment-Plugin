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

//includes
include 'includes/class-shipment-xml-array.php';
include 'includes/class-shipment-array-xml.php';
include 'includes/user-meta.php';

class WPSP_USPS
{
	function __construct()
	{
		// Filters
		add_filter( 'wpsp_shipment_carriers', [ $this, 'wpsp_add_usps_carrier' ], 9 );
		add_filter( 'wpsp_shipment_usps_levels', [ $this, 'wpsp_shipment_usps_levels' ] );
		add_filter( 'wpsp_shipment_usps_package_types', [ $this, 'wpsp_shipment_usps_package_types' ] );
		add_filter( 'wpsp_label_summary_usps', [ $this, 'wpsp_label_summary_usps' ] );
		add_filter( 'wpsp_shipment_usps_services', [ $this, 'wpsp_shipment_usps_services' ] );
		add_filter( 'wpsp_email_piping_field_value_usps', [ $this, 'wpsp_email_piping_field_value_usps' ], 10, 2 );

		// Actions
		add_action( 'wpsp_verify_address_usps', [ $this, 'wpsp_verify_address_usps' ], 10, 3 );
		add_action( 'wpsp_create_shipment_usps', [ $this, 'wpsp_usps_create_shipment' ], 10, 3 );
		add_action( 'wpsp_create_label_usps', [ $this, 'wpsp_create_label_usps' ], 10, 3 );
		add_action( 'wpsp_label_rates_usps', [ $this, 'wpsp_label_rates_usps' ], 10, 3 );
		add_action( 'wpsp_void_label_usps', [ $this, 'wpsp_void_label_usps' ], 10, 2 );
		add_action( 'wpsp_service_rates_usps', [ $this, 'wpsp_service_rates_usps' ], 10, 3 );
		add_action( 'wpsp_service_pickup_rates_usps', [ $this, 'wpsp_service_pickup_rates_usps' ], 10, 3 );

		//User Extras
		$wpsp_user_meta = new WPSP_USPS_UserMeta();

		add_action( 'show_user_profile', array( $wpsp_user_meta, 'extra_user_profile_fields' ) );
		add_action( 'edit_user_profile', array( $wpsp_user_meta, 'extra_user_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $wpsp_user_meta, 'save_extra_user_profile_fields' ) );
		add_action( 'personal_options_update', array( $wpsp_user_meta, 'save_extra_user_profile_fields' ) );
	}

	function wpsp_service_pickup_rates_usps( $data, &$error, &$pickup_rates )
	{
		$error        = false;
		$pickup_rates = 0;

		if ( $data->schedule === 'yes' ) {
			$usps_pickup_rates = WPSP::get_option( 'wpsp_usps_pickup_rates', 0 );
			$usps_pickup_rates = floatval( $usps_pickup_rates );
			$pickup_rates      = $usps_pickup_rates;
		}
	}

	function wpsp_email_piping_field_value_usps( $value, $key )
	{
		if ( in_array( $key, [ 'shipping_method', 'package_type' ] ) ) {
			$value = strtoupper( $value );
		}

		return $value;
	}

	function wpsp_label_summary_usps( $text )
	{
		return $text;
	}

	function wpsp_service_rates_usps( $data, &$error, &$rates )
	{
		$rates        = [];
		$error        = false;
		$from_address = WPSP_Address::get_address( $data->from );
		$to_address   = WPSP_Address::get_address( $data->to );
		$from_zip     = $from_address['zip_code'];
		$to_zip       = $to_address['zip_code'];
		$services     = apply_filters( 'wpsp_shipment_usps_levels', [] );
		$package_type = $data->package_type;
		$level        = $data->shipping_method;
		$user_id      = $data->customer;

		if ( empty( $package_type ) ) {
			$package_type = $this->get_default_package_type();
		}

		if ( empty( $level ) ) {
			$level = $this->get_default_service_level();
		}

		$level_name = $services[ $level ];
		$services   = [ $level => $level_name ];

		foreach ( $services as $key => $service ) {
			$d = [
				'Revision' => 2,
			];

			foreach ( $data->packages as $k => $package ) {
				$id = $k + 1;

				if ( $package['unit'] == 'oz' ) {
					$weight_in_lbs = $package['weight'] / 16;
					$weight_in_oz  = $package['weight'];
				} else {
					$weight_in_lbs = $package['weight'];
					$weight_in_oz  = $package['weight'] * 16;
				}

				$d["Package_{$id}"] = [
					'_attributes'    => [
						'ID' => $id,
					],
					'Service'        => $key,
					'ZipOrigination' => $from_zip,
					'ZipDestination' => $to_zip,
					'Pounds'         => $weight_in_lbs,
					'Ounces'         => $weight_in_oz,
					'Container'      => $package_type,
					'Size'           => 'REGULAR',
					'Width'          => $package['width'],
					'Length'         => $package['length'],
					'Height'         => $package['height'],
					'Girth'          => ( ( $package['width'] + $package['height'] ) * 2 ),
					'Machinable'     => 'True'
				];
			}

			$xml = ShipmentArrayToXml::convert( $d, [
				'rootElementName' => 'RateV4Request',
				'_attributes'     => [
					'USERID' => $this->get_customer_id( $user_id ),
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
				$res      = $res['RateV4Response'];
				$packages = [];

				if ( count( $data->packages ) == 1 ) {
					$packages[] = $res['Package'];
				} else {
					$packages = $res['Package'];
				}

				foreach ( $packages as $package ) {
					$postages = [];

					if ( ! empty( $package['Postage']['_attributes'] ) ) {
						$postages[] = $package['Postage'];
					} else {
						$postages = $package['Postage'];
					}

					foreach ( $postages as $postage ) {
						$levels       = apply_filters( 'wpsp_shipment_usps_services', [] );
						$level        = null;
						$mail_service = strtolower( strip_tags( html_entity_decode( $postage['MailService'] ) ) );

						foreach ( $levels as $lvl_k => $lvl_v ) {
							$lv = strtolower( $lvl_v );
							$lk = strtolower( $lvl_k );

							if ( strpos( $mail_service, $lk ) !== false ) {
								$level = $lvl_v;
								break;
							}
						}

						if ( ! isset( $rates[ $postage['MailService'] ] ) ) {
							$rates[ $postage['MailService'] ] = [
								'name'         => html_entity_decode( $postage['MailService'] ),
								'rate'         => 0,
								'level'        => $level,
								'package_type' => $package_type
							];
						}

						$rates[ $postage['MailService'] ]['rate'] += $postage['Rate'];
					}
				}

			} else {
				$error = $res['Error']['Description'];
			}
		}
		$rates = array_values( $rates );
	}

	private function get_default_package_type()
	{
		$types = apply_filters( "wpsp_shipment_usps_package_types", [] );
		$types = array_keys( $types );

		return $types[0];
	}

	private function get_default_service_level()
	{
		$levels = apply_filters( "wpsp_shipment_usps_levels", [] );
		$levels = array_keys( $levels );

		return $levels[0];
	}

	function request( $endpoint )
	{
		$ch = curl_init();

		if ( WPSP::is_test() ) {
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

	function wpsp_void_label_usps( &$error, $shipment_id )
	{
		$error    = false;
		$shipment = WPSP_Shipment::get_shipment( $shipment_id );
		$user_id  = $shipment->creator_id;

		try {
			$d = [
				'BarcodeNumber' => $shipment->shipKey
			];

			$xml = ShipmentArrayToXml::convert( $d, [
				'rootElementName' => 'eVSCancelRequest',
				'_attributes'     => [
					'USERID' => $this->get_customer_id( $user_id ),
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

	function wpsp_label_rates_usps( $data, &$error, &$rates )
	{
		$error        = false;
		$from_address = WPSP_Address::get_address( $data->from );
		$to_address   = WPSP_Address::get_address( $data->to );
		$from_zip     = $from_address['zip_code'];
		$to_zip       = $to_address['zip_code'];
		$user_id      = $data->customer;

		$d        = [
			'Revision' => 2,
		];
		$services = apply_filters( 'wpsp_shipment_usps_services', [] );

		foreach ( $data->packages as $k => $package ) {
			$id = $k + 1;

			$d["Package_{$id}"] = [
				'_attributes'    => [
					'ID' => $id,
				],
				'Service'        => array_search( $data->shipping_method, $services ),
				'ZipOrigination' => $from_zip,
				'ZipDestination' => $to_zip,
				'Pounds'         => ( $package['weight'] / 16 ),
				'Ounces'         => $package['weight'],
				'Container'      => $data->package_type,
				'Size'           => 'REGULAR',
				'Width'          => $package['width'],
				'Length'         => $package['length'],
				'Height'         => $package['height'],
				'Girth'          => ( ( $package['width'] + $package['height'] ) * 2 )
			];
		}

		$xml = ShipmentArrayToXml::convert( $d, [
			'rootElementName' => 'RateV4Request',
			'_attributes'     => [
				'USERID' => $this->get_customer_id( $user_id ),
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
					} else if ( ! empty( $package['Error'] ) ) {
						$error = $package['Error']['Description'];
						break;
					}
				}
			} else {
				if ( ! empty( $res['Package']['Postage']['Rate'] ) ) {
					$rates += $res['Package']['Postage']['Rate'];
				} else if ( ! empty( $res['Package']['Error']['Description'] ) ) {
					$error = $res['Package']['Error']['Description'];
				}
			}
		} else {
			$error = $res['Error']['Description'];
		}
	}

	function wpsp_create_label_usps( &$error, &$encoded_images, $shipment_data )
	{
		$error = __( 'Label not found', WPSP_LANG );

		if ( ! empty( $shipment_data['label'] ) ) {
			$pdf_file_name = "{$shipment_data['shipKey']}.pdf";
			$filepath      = apply_filters( 'wpsp_file_dir', $pdf_file_name );

			file_put_contents( $filepath, base64_decode( $shipment_data['label'] ) );

			$encoded_images[] = $filepath;
		}
		$error = false;
	}

	function wpsp_usps_create_shipment( $data, &$error, &$shipment_data )
	{
		try {
			$error        = false;
			$from_address = WPSP_Address::get_address( $data->from );
			$to_address   = WPSP_Address::get_address( $data->to );
			$from_zip     = $from_address['zip_code'];
			$packages     = $data->packages;
			$user_id      = $data->customer;

			list( $from_zip4, $from_zip5 ) = $this->zip4_5( $from_zip );

			foreach ( $packages as $package ) {
				$d = [
					'Option'                     => 1,
					'ImageParameters'            => [ 'ImageParameter' => '4X6LABEL' ],
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
						'USERID' => $this->get_customer_id( $user_id ),
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

	function wpsp_verify_address_usps( $data, &$error, &$is_residential )
	{
		list( $from_zip4, $from_zip5 ) = $this->zip4_5( $data->zip_code );
		$user_id = $data->customer;

		$is_residential = false;
		$d              = [
			'Revision' => 1,
			'Address'  => [
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
				'USERID' => $this->get_customer_id( $user_id ),
			],
		], true, 'UTF-8' );

		$url = add_query_arg( [
			                      'API' => 'Verify',
			                      'XML' => urlencode( $xml )
		                      ], "ShippingAPI.dll" );

		$res = $this->request( $url );
		$res = ShipmentXmlToArray::convert( $res );

		if ( ! isset( $res['AddressValidateResponse']['Address']['Error'] ) ) {
			$error          = false;
			$is_residential = $res['AddressValidateResponse']['Address']['Business'] === "N";
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
			'PRIORITY'             => 'PRIORITY',
			'PRIORITY EXPRESS'     => 'PRIORITY EXPRESS',
			'FIRST CLASS'          => 'FIRST CLASS',
			'PARCEL SELECT GROUND' => 'PARCEL SELECT GROUND',
			'LIBRARY'              => 'LIBRARY',
			'MEDIA'                => 'MEDIA',
			'BPM'                  => 'BPM',
			'PRIORITY MAIL CUBIC'  => 'PRIORITY MAIL CUBIC'
		];

		return $levels;
	}

	function wpsp_shipment_usps_services()
	{
		$services = [
			'First Class'                          => 'FIRST CLASS',
			'First Class Commercial'               => 'FIRST CLASS',
			'First Class HFP Commercial'           => 'FIRST CLASS',
			'Priority Mail Express'                => 'PRIORITY EXPRESS',
			'Priority Mail Express Commercial'     => 'PRIORITY EXPRESS',
			'Priority Mail Express CPP'            => 'PRIORITY EXPRESS',
			'Priority Mail Express Sh'             => 'PRIORITY EXPRESS',
			'Priority Mail Express Sh Commercial'  => 'PRIORITY EXPRESS',
			'Priority Mail Express HFP'            => 'PRIORITY EXPRESS',
			'Priority Mail Express HFP Commercial' => 'PRIORITY EXPRESS',
			'Priority Mail Express HFP CPP'        => 'PRIORITY EXPRESS',
			'Priority Mail Cubic'                  => 'PRIORITY MAIL CUBIC',
			'Priority Mail 1-Day™ Cubic'           => 'PRIORITY MAIL CUBIC',
			'Priority Mail'                        => 'PRIORITY',
			'Priority Commercial'                  => 'PRIORITY',
			'Priority Cpp'                         => 'PRIORITY',
			'Priority HFP Commercial'              => 'PRIORITY',
			'Priority HFP CPP'                     => 'PRIORITY',
			'Retail Ground'                        => 'PARCEL SELECT GROUND',
			'Media'                                => 'MEDIA',
			'Library'                              => 'LIBRARY',
			'All'                                  => 'ALL',
			'Online'                               => 'ALL',
			'Plus'                                 => 'ALL',
			'BPM'                                  => 'BPM'
		];

		return $services;
	}

	function wpsp_shipment_usps_package_types()
	{
		$types = [
			'VARIABLE'                     => 'VARIABLE',
			'FLAT RATE ENVELOPE'           => 'FLAT RATE ENVELOPE',
			'LEGAL FLAT RATE ENVELOPE'     => 'LEGAL FLAT RATE ENVELOPE',
			'PADDED FLAT RATE ENVELOPE'    => 'PADDED FLAT RATE ENVELOPE',
			'GIFT CARD FLAT RATE ENVELOPE' => 'GIFT CARD FLAT RATE ENVELOPE',
			'SM FLAT RATE ENVELOPE'        => 'SM FLAT RATE ENVELOPE',
			'WINDOW FLAT RATE ENVELOPE'    => 'WINDOW FLAT RATE ENVELOPE',
			'SM FLAT RATE BOX'             => 'SM FLAT RATE BOX',
			'MD FLAT RATE BOX'             => 'MD FLAT RATE BOX',
			'LG FLAT RATE BOX'             => 'LG FLAT RATE BOX',
			'REGIONALRATEBOXA'             => 'REGIONALRATEBOXA',
			'REGIONALRATEBOXB'             => 'REGIONALRATEBOXB',
			'RECTANGULAR'                  => 'RECTANGULAR',
			'NONRECTANGULAR'               => 'NONRECTANGULAR',
			'PACKAGE SERVICE'              => 'PACKAGE SERVICE',
			'CUBIC PARCELS'                => 'CUBIC PARCELS',
			'CUBIC SOFT PACK'              => 'CUBIC SOFT PACK'
		];

		return $types;
	}

	function get_customer_id( $user_id )
	{
		$customer_id = WPSP_USPS_UserMeta::get_field( $user_id, 'usps_customer_id' );

		if ( empty( $customer_id ) ) {
			$customer_id = WPSP_USPS_USER_ID;
		}

		return $customer_id;
	}
}

if ( defined( 'WPSP_LANG' ) ) {
	new WPSP_USPS();
}