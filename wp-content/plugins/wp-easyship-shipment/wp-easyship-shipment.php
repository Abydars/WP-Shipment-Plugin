<?php
/*
Plugin Name: WPSP Ezeeship Add-on
Plugin URI: hztech.biz
description: This plugin works only with WPSP Shipment Plugin.
Version: 1.0
Author: Hztech
Author URI: hztech.biz
*/

class WPSP_Ezeeship
{
	function __construct()
	{
		// Filters
		add_filter( 'wpsp_shipment_carriers', [ $this, 'wpsp_add_carriers' ] );
		add_filter( 'wpsp_shipment_ups_levels', [ $this, 'wpsp_shipment_ups_levels' ] );
		add_filter( 'wpsp_shipment_fedex_levels', [ $this, 'wpsp_shipment_fedex_levels' ] );
		add_filter( 'wpsp_shipment_ups_package_types', [ $this, 'wpsp_shipment_ups_package_types' ] );
		add_filter( 'wpsp_shipment_fedex_package_types', [ $this, 'wpsp_shipment_fedex_package_types' ] );
		add_filter( 'wpsp_fedex_settings', [ $this, 'wpsp_fedex_settings' ], 10, 2 );

		// Actions
		add_action( 'wpsp_verify_address_ups', [ $this, 'wpsp_verify_address_any' ], 10, 3 );
		add_action( 'wpsp_verify_address_fedex', [ $this, 'wpsp_verify_address_any' ], 10, 3 );

		add_action( 'wpsp_create_shipment_ups', [ $this, 'wpsp_any_create_shipment' ], 10, 3 );
		add_action( 'wpsp_create_shipment_fedex', [ $this, 'wpsp_any_create_shipment' ], 10, 3 );

		add_action( 'wpsp_create_label_ups', [ $this, 'wpsp_create_label_any' ], 10, 3 );
		add_action( 'wpsp_create_label_fedex', [ $this, 'wpsp_create_label_any' ], 10, 3 );

		add_action( 'wpsp_label_rates_fedex', [ $this, 'wpsp_label_rates_any' ], 10, 3 );
		add_action( 'wpsp_label_rates_ups', [ $this, 'wpsp_label_rates_any' ], 10, 3 );

		add_action( 'wpsp_service_rates_ups', [ $this, 'wpsp_service_rates_ups' ], 10, 3 );
		add_action( 'wpsp_service_rates_fedex', [ $this, 'wpsp_service_rates_fedex' ], 10, 3 );

		add_action( 'wpsp_service_pickup_rates_ups', [ $this, 'wpsp_service_pickup_rates_ups' ], 10, 3 );
		add_action( 'wpsp_service_pickup_rates_fedex', [ $this, 'wpsp_service_pickup_rates_fedex' ], 10, 3 );

		add_action( 'wpsp_void_label_ups', [ $this, 'wpsp_void_label_any' ], 10, 2 );
		add_action( 'wpsp_void_label_fedex', [ $this, 'wpsp_void_label_any' ], 10, 2 );
	}

	function wpsp_fedex_settings( $settings )
	{
		$settings[] = [
			'id'      => 'paid_number_of_packages_per_week',
			'label'   => 'Paid number of packages per week (for each from address)',
			'type'    => 'number',
			'default' => 3
		];

		return $settings;
	}

	function wpsp_create_label_any( &$error, &$encoded_images, $shipment_data )
	{
		$error = __( 'Label not found', WPSP_LANG );

		if ( ! empty( $shipment_data['label'] ) ) {
			$pdf_file_name = "{$shipment_data['shipKey']}.pdf";
			$filepath      = apply_filters( 'wpsp_file_dir', $pdf_file_name );

			file_put_contents( $filepath, file_get_contents( $shipment_data['label'] ) );

			$encoded_images[] = $filepath;
		}
		$error = false;
	}

	function wpsp_any_create_shipment( $data, &$error, &$shipment_data )
	{

		try {
			$from_address = WPSP_Address::get_address( $data->from );
			$to_address   = WPSP_Address::get_address( $data->to );
			$packages     = $data->packages;
			$endpoint     = 'https://ezeeship.com/api/ezeeship-openapi/label/create';
			$type         = 'POST';

			$d                         = [];
			$d['from']                 = [];
			$d['from']['personName']   = $from_address['full_name'];
			$d['from']['countryCode']  = $from_address['country'];
			$d['from']['stateCode']    = $from_address['state'];
			$d['from']['phone']        = $from_address['phone'];
			$d['from']['city']         = $from_address['city'];
			$d['from']['addressLine1'] = $from_address['street_1'];
			$d['from']['zipCode']      = $from_address['zip_code'];

			$d['to']                 = [];
			$d['to']['personName']   = $to_address['full_name'];
			$d['to']['company']      = $to_address['company'];
			$d['to']['countryCode']  = $to_address['country'];
			$d['to']['stateCode']    = $to_address['state'];
			$d['to']['city']         = $to_address['city'];
			$d['to']['phone']        = $to_address['phone'];
			$d['to']['addressLine1'] = $to_address['street_1'];
			$d['to']['zipCode']      = $to_address['zip_code'];
			$d['carrierCode']        = $data->carrier;
			$d['serviceCode']        = $data->shipping_method;
			$d['isTest']             = WPSP::is_test();
			$d['parcels']            = [];

			foreach ( $packages as $k => $package ) {
				$pac            = [
					"packageNum"   => $k + 1,
					"length"       => $package['length'],
					"width"        => $package['width'],
					"height"       => $package['height'],
					"distanceUnit" => "in",
					"weight"       => $package['weight'],
					"massUnit"     => "lb",
					"packageCode"  => $data->package_type,

				];
				$d['parcels'][] = $pac;
			}

			$d   = json_encode( $d );
			$res = $this->request( $endpoint, $type, $d );
			$res = json_decode( $res );

			if ( $res->result == 'OK' ) {
				$shipment_data = array(
					"shipKey" => $res->data->objectId,
					"label"   => $res->data->pdfUrl,
				);
			} else {
				$error = $res->message;

			}

		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}
	}

	function request( $endpoint, $type, $data )
	{
		$ch = curl_init();


		curl_setopt( $ch, CURLOPT_URL, $endpoint );
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
			'Authorization: e10f1561942a11e98bd9792e89914171',
			'Content-Type: application/json'
		) );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $type );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );
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

	function wpsp_label_rates_any( $data, &$error, &$rates )
	{
		$error = false;

		$this->wpsp_service_rates_any( $data, $error, $all_rates );

		if ( ! $error ) {
			$rates = 0;
			$rates += $all_rates[0]['rate'];
		}
	}

	function wpsp_service_rates_any( $data, &$error, &$rates )
	{
		$rates        = [];
		$error        = false;
		$endpoint     = 'https://ezeeship.com/api/ezeeship-openapi/shipment/estimateRate';
		$type         = 'POST';
		$from_address = WPSP_Address::get_address( $data->from );
		$to_address   = WPSP_Address::get_address( $data->to );
		$services     = apply_filters( "wpsp_shipment_{$data->carrier}_levels", [] );
		$package_type = $data->package_type;
		$level        = $data->shipping_method;

		if ( empty( $package_type ) ) {
			$package_type = $this->get_default_package_type( $data->carrier );
		}

		if ( empty( $level ) ) {
			$level = $this->get_default_service_level( $data->carrier, ( $to_address['is_residential'] === 1 ) );
		}

		$level_name = $services[ $level ];
		$services   = [ $level => $level_name ];

		foreach ( $services as $key => $service ) {
			$d                         = [];
			$d['from']                 = [];
			$d['from']['countryCode']  = $from_address['country'];
			$d['from']['stateCode']    = $from_address['state'];
			$d['from']['city']         = $from_address['city'];
			$d['from']['addressLine1'] = $from_address['street_1'];
			$d['from']['zipCode']      = $from_address['zip_code'];
			$d['to']                   = [];
			$d['to']['countryCode']    = $to_address['country'];
			$d['to']['stateCode']      = $to_address['state'];
			$d['to']['city']           = $to_address['city'];
			$d['to']['addressLine1']   = $to_address['street_1'];
			$d['to']['zipCode']        = $to_address['zip_code'];
			$d['carrierCode']          = $data->carrier;
			$d['serviceCode']          = $key;
			$d['isTest']               = WPSP::is_test();
			$d['parcels']              = [];

			foreach ( $data->packages as $k => $package ) {

				$weight = $package['weight'];

				if ( $package['unit'] == 'oz' ) {
					$weight = $package['weight'] / 16;
				}

				$pac            = [
					"packageNum"   => $k + 1,
					"length"       => $package['length'],
					"width"        => $package['width'],
					"height"       => $package['height'],
					"distanceUnit" => "in",
					"weight"       => $weight,
					"massUnit"     => "lb",
					"packageCode"  => $package_type,
				];
				$d['parcels'][] = $pac;
			}

			$d   = json_encode( $d );
			$res = $this->request( $endpoint, $type, $d );
			$res = json_decode( $res );

			if ( $res->result == 'OK' ) {
				$error   = false;
				$rates[] = [
					'name'         => $service,
					'rate'         => $res->data->rate,
					'level'        => $key,
					'package_type' => $package_type
				];
				$rates   = array_values( $rates );
			} else {
				$error = $res->message;
			}
		}
	}

	private function get_default_package_type( $carrier )
	{
		$package_types = apply_filters( "wpsp_shipment_{$carrier}_package_types", [] );
		$package_types = array_keys( $package_types );

		return $package_types[0];
	}

	private function get_default_service_level( $carrier, $residential = true )
	{
		$levels = apply_filters( "wpsp_shipment_{$carrier}_levels", [] );
		$levels = array_keys( $levels );

		if ( $carrier == 'fedex' && $residential ) {
			return 'fedex_home_delivery';
		}

		return $levels[0];
	}

	function wpsp_service_rates_ups( $data, &$error, &$rates )
	{
		$data->carrier = 'ups';
		$this->wpsp_service_rates_any( $data, $error, $rates );
	}

	function wpsp_service_rates_fedex( $data, &$error, &$rates )
	{
		$data->carrier = 'fedex';
		$this->wpsp_service_rates_any( $data, $error, $rates );
	}

	function wpsp_service_pickup_rates_ups( $data, &$error, &$pickup_rates )
	{
		$error        = false;
		$pickup_rates = 0;

		if ( $data->schedule === 'yes' ) {
			$ups_pickup_rates = WPSP::get_option( 'wpsp_ups_pickup_rates', 0 );
			$ups_pickup_rates = floatval( $ups_pickup_rates );
			$pickup_date      = date( 'Y-m-d H:i:s', strtotime( $data->pickup_date ) );
			$today_shipments  = count( $this->get_today_pickup_shipments_by_address( 'ups', $data->from, $pickup_date ) );

			if ( $today_shipments <= 0 ) {
				$pickup_rates = $ups_pickup_rates;
			}
		}
	}

	function wpsp_service_pickup_rates_fedex( $data, &$error, &$pickup_rates )
	{
		$error        = false;
		$pickup_rates = 0;

		if ( $data->schedule === 'yes' ) {
			$fedex_pickup_rates   = WPSP::get_option( 'wpsp_fedex_pickup_rates', 0 );
			$per_week             = WPSP::get_option( 'wpsp_fedex_paid_number_of_packages_per_week', 0 );
			$pickup_date          = date( 'Y-m-d H:i:s', strtotime( $data->pickup_date ) );
			$week_shipments       = $this->get_week_pickup_shipments_by_address( 'fedex', $data->from, $pickup_date );
			$week_shipments_count = 0;

			foreach ( $week_shipments as $s ) {
				$packages             = json_decode( $s->packages, true );
				$week_shipments_count += count( $packages );
			}

			if ( $week_shipments_count < $per_week ) {
				$fedex_pickup_rates = floatval( $fedex_pickup_rates );
				$pickup_packages    = count( $data->packages );

				while ( ( $pickup_packages + $week_shipments_count ) > $per_week ) {
					$pickup_packages --;
				}

				$pickup_rates = $fedex_pickup_rates * $pickup_packages;
			}
		}
	}

	function wpsp_void_label_any( &$error, $shipment_id )
	{
		$error    = false;
		$shipment = WPSP_Shipment::get_shipment( $shipment_id );
		$endpoint = 'https://ezeeship.com/api/ezeeship-openapi/label/cancel';
		$type     = 'POST';

		try {
			$d             = [];
			$d['objectId'] = $shipment->shipKey;
			$d             = json_encode( $d );
			$res           = $this->request( $endpoint, $type, $d );
			$res           = json_decode( $res );

			if ( $res->result != 'OK' ) {
				$error = $res->message;
			}

		} catch ( Exception $e ) {
			$error = $e->getMessage();
		}
	}

	function wpsp_verify_address_any( $data, &$error, &$is_residential )
	{
		$d                 = [];
		$error             = false;
		$d["countryCode"]  = $data->country;
		$d["stateCode"]    = $data->state;
		$d["city"]         = $data->city;
		$d["addressLine1"] = $data->street_1 . $data->street_2;
		$d["zipCode"]      = $data->zip_code;
		$d                 = json_encode( $d );
		$endpoint          = 'https://ezeeship.com/api/ezeeship-openapi/address/validate';
		$type              = 'POST';

		$res = $this->request( $endpoint, $type, $d );
		$res = json_decode( $res );

		if ( $res->result != 'OK' ) {
			$error = $res->message;
		} else {
			$is_residential = $res->data->isResidential;
		}
	}

	function wpsp_add_carriers( $carriers )
	{
		$carriers['fedex'] = 'FedEx';
		$carriers['ups']   = 'UPS';

		return $carriers;
	}

	function wpsp_shipment_fedex_package_types()
	{
		$levels = [
			'your_package'    => 'Your Package',
			'FedEx_Envelope'  => 'FedEx® Envelope',
			'FedEx_Pak_1'     => 'FedEx® Pak (1)',
			'FedEx_Tube'      => 'FedEx® Tube',
			'fedex_box'       => 'FedEx® Box',
			'fedex_Small_Box' => 'FEDEX_SMALL_BOX'
		];

		return $levels;
	}

	function wpsp_shipment_ups_package_types()
	{
		$levels = [
			'your_package'           => 'Your Package',
			'ups_letter'             => 'UPS Letter',
			'UPS_Express_Tube'       => 'UPS Tube',
			'UPS_Express_Pak'        => 'UPS Express® Pak',
			'UPS_Express_Box_Small'  => 'UPS Express® Box - Small',
			'UPS_Express_Box_Medium' => 'UPS Express® Box - Medium',
			'UPS_Express_Box_Large'  => 'UPS Express® Box - Large'
		];

		return $levels;
	}

	function wpsp_shipment_fedex_levels()
	{
		$services = [
			'fedex_ground'                 => 'FedEx Ground®',
			'fedex_priority_overnight'     => 'FedEx Priority Overnight®',
			'fedex_home_delivery'          => 'FedEx Home Delivery®',
			'fedex_standard_overnight'     => 'FedEx Standard Overnight®',
			'fedex_2_day_am'               => 'FedEx 2Day® A.M.',
			'fedex_2_day'                  => 'FedEx 2Day®',
			'fedex_express_saver'          => 'FedEx Express Saver®',
			'fedex_smart_post'             => 'FedEx SmartPost(only support single parcel)',
			'fedex_international_economy'  => 'FedEx International Economy®',
			'fedex_international_priority' => 'FedEx International Priority®',
		];

		return $services;
	}

	function wpsp_shipment_ups_levels()
	{
		$services = [
			'ups_ground'                => 'UPS® Ground',
			'ups_next_day_air_early_am' => 'UPS Next Day Air® Early',
			'ups_next_day_air'          => 'UPS Next Day Air®',
			'ups_next_day_air_saver'    => 'UPS Next Day Air Saver®',
			'ups_second_day_air_am'     => 'UPS 2nd Day Air AM®',
			'ups_second_day_air'        => 'UPS 2nd Day Air®',
			'ups_3_day_select'          => 'UPS 3 Day Select®',
		];

		return $services;
	}

	private function get_week_pickup_shipments_by_address( $carrier, $address_id, $pickup_date )
	{
		list( $start_date, $end_date ) = $this->week_start_end( $pickup_date );
		$where = [
			"fromAddress_id = '{$address_id}'",
			"server = '{$carrier}'",
			"pickup_date BETWEEN '{$start_date}' AND '{$end_date}'"
		];

		return WPSP_Shipment::get_shipments_where( $where );
	}

	private function get_today_pickup_shipments_by_address( $carrier, $address_id, $pickup_date )
	{
		list( $start_date, $end_date ) = $this->start_end_date( $pickup_date );
		$where = [
			"fromAddress_id = '{$address_id}'",
			"server = '{$carrier}'",
			"pickup_date BETWEEN '{$start_date}' AND '{$end_date}'"
		];

		return WPSP_Shipment::get_shipments_where( $where );
	}

	private function start_end_date( $now_date )
	{
		$start_date = date( 'Y-m-d', strtotime( $now_date ) ) . ' 00:00:00';
		$end_date   = date( 'Y-m-d', strtotime( $now_date ) ) . ' 23:59:00';

		return [ $start_date, $end_date ];
	}

	private function week_start_end( $now_date = false )
	{
		if ( $now_date === false ) {
			$now_date = date( 'Y-m-d H:i:s' );
		}

		$start_time = '00:00:00';
		$end_time   = '23:59:00';

		$now       = strtotime( $now_date );
		$start     = date( "Y-m-d", strtotime( "last sunday", $now ) ) . ' ' . $start_time;
		$end       = date( "Y-m-d", strtotime( "next sunday", $now ) ) . ' ' . $end_time;
		$is_sunday = strtolower( date( 'D', $now ) ) == 'sun';

		if ( $is_sunday ) {
			$time_now = strtotime( date( 'Y-m-d H:i:s', $now ) );
			$s_time   = strtotime( date( 'Y-m-d', $now ) . ' ' . $start_time );

			if ( $time_now >= $s_time ) {
				$start = date( "Y-m-d", $now ) . ' ' . $start_time;
			} else {
				$end = date( "Y-m-d", $now ) . ' ' . $end_time;
			}
		}

		return [ $start, $end ];
	}
}

new WPSP_Ezeeship();