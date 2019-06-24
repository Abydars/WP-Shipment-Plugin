<?php
/*
Plugin Name: WPSP USPS Add-on
Plugin URI: hztech.biz
description: This plugin works only with WPSP Shipment Plugin.
Version: 1.0
Author: Hztech
Author URI: hztech.biz
*/

define('WPSP_EZEESHIP_DEBUG', true);


class WPSP_EZEESHIP
{
    function __construct()
    {
        // Filters
        add_filter('wpsp_shipment_carriers', [$this, 'wpsp_add_ezeeship_carrier']);
        add_filter('wpsp_shipment_ups_levels', [$this, 'wpsp_shipment_ups_levels']);
        add_filter('wpsp_shipment_fedex_levels', [$this, 'wpsp_shipment_fedex_levels']);
        add_filter('wpsp_shipment_ups_package_types', [$this, 'wpsp_shipment_ups_package_types']);
        add_filter('wpsp_shipment_fedex_package_types', [$this, 'wpsp_shipment_fedex_package_types']);

        add_filter('wpsp_get_markup_rate_usps', [$this, 'wpsp_get_markup_rate_usps'], 10, 3);
//        add_filter( 'wpsp_label_summary_usps', [ $this, 'wpsp_label_summary_usps' ] );
        add_filter('wpsp_shipment_usps_services', [$this, 'wpsp_shipment_usps_services']);
//        add_filter( 'wpsp_email_piping_field_value_usps', [ $this, 'wpsp_email_piping_field_value_usps' ], 10, 2 );

        // Actions
        add_action('wpsp_verify_address_ups', [$this, 'wpsp_verify_address_ups'], 10, 3);
        add_action('wpsp_verify_address_fedex', [$this, 'wpsp_verify_address_fedex'], 10, 3);
        add_action('wpsp_create_shipment_ups', [$this, 'wpsp_usps_create_shipment'], 10, 3);
        add_action('wpsp_create_label_usps', [$this, 'wpsp_create_label_usps'], 10, 3);
        add_action('wpsp_label_rates_fedex', [$this, 'wpsp_label_rates_fedex'], 10, 3);
//        add_action('wpsp_label_rates_ups', [$this, 'wpsp_label_rates_ups'], 10, 3);
        add_action('wpsp_void_label_usps', [$this, 'wpsp_void_label_usps'], 10, 2);
        add_action('wpsp_service_rates_ups', [$this, 'wpsp_service_rates_ups'], 10, 3);
        add_action('wpsp_service_rates_fedex', [$this, 'wpsp_service_rates_fedex'], 10, 3);
    }

//    function wpsp_email_piping_field_value_usps( $value, $key )
//    {
//        if ( in_array( $key, [ 'shipping_method', 'package_type' ] ) ) {
//            $value = strtoupper( $value );
//        }
//
//        return $value;
//    }
//
//    function wpsp_label_summary_usps( $text )
//    {
//        return $text;
//    }
//
    function wpsp_service_rates_ups($data, &$error, &$rates)
    {
        $rates = [];
        $error = false;
        $endpoint = 'https://ezeeship.com/api/ezeeship-openapi/shipment/estimateRate';
        $type = 'POST';
        $from_address = WPSP_Address::get_address($data->from);
        $to_address = WPSP_Address::get_address($data->to);
        $from_zip = $from_address['zip_code'];
        $to_zip = $to_address['zip_code'];
        $services = apply_filters('wpsp_shipment_ups_services', []);
        $services['All'] = 'All';

//        $d = [];


        $d = [];
        $d['from'] = [];
        $d['from']['countryCode'] = $from_address['country'];
        $d['from']['stateCode'] = $from_address['state'];
        $d['from']['city'] = $from_address['city'];
        $d['from']['addressLine1'] = $from_address['street_1'] . $from_address['street_2'];
        $d['from']['zipCode'] = $from_address['zip_code'];
        $d['to'] = [];
        $d['to']['countryCode'] = $to_address['country'];
        $d['to']['stateCode'] =  $to_address['state'];
        $d['to']['city'] = $to_address['city'];
        $d['to']['addressLine1'] = $to_address['street_1'] . $from_address['street_2'];
        $d['to']['zipCode'] = $to_address['zip_code'];
        $d['carrierCode'] = 'ups';
        $d['serviceCode'] = $data->shipping_method;
        $d['isTest'] = true;
        $d['parcels'] = [];


        foreach ($data->packages as $k => $package) {
            $pac = [
                "packageNum" => $k,
                "length" => $package['length'],
                "width" => $package['width'],
                "height" => $package['height'],
                "distanceUnit" => "in",
                "weight" => $package['weight'],
                "massUnit" => "oz",
                "packageCode" => $data->package_type,
            ];
            $d['parcels'][] = $pac;
        }

        $d = json_encode($d);
//        var_dump($d);
//        die();

        $res = $this->request($endpoint, $type, $d);
        var_dump($res);
        die();

        if (!isset($res['Error'])) {
            $res = $res['RateV4Response'];
            $packages = [];

            if (count($data->packages) == 1) {
                $packages[] = $res['Package'];
            } else {
                $packages = $res['Package'];
            }

            foreach ($packages as $package) {
                $postages = [];

                if (!empty($package['Postage']['_attributes'])) {
                    $postages[] = $package['Postage'];
                } else {
                    $postages = $package['Postage'];
                }

                foreach ($postages as $postage) {
                    $levels = apply_filters('wpsp_shipment_usps_services', []);
                    $level = null;
                    $mail_service = strtolower(strip_tags(html_entity_decode($postage['MailService'])));

                    foreach ($levels as $lvl_k => $lvl_v) {
                        $lv = strtolower($lvl_v);
                        $lk = strtolower($lvl_k);

                        if (strpos($mail_service, $lk) !== false) {
                            $level = $lvl_v;
                            break;
                        }
                    }

                    if (!isset($rates[$postage['MailService']])) {
                        $rates[$postage['MailService']] = [
                            'name' => html_entity_decode($postage['MailService']),
                            'rate' => 0,
                            'level' => $level,
                            'package_type' => $data->package_type
                        ];
                    }

                    $rates[$postage['MailService']]['rate'] += $postage['Rate'];
                }
            }

            $rates = array_values($rates);

        } else {
            $error = $res['Error']['Description'];
        }
    }
//
//    function wpsp_void_label_usps( &$error, $shipment_id )
//    {
//        $error    = false;
//        $shipment = WPSP_Shipment::get_shipment( $shipment_id );
//
//        try {
//            $d = [
//                'BarcodeNumber' => $shipment->shipKey
//            ];
//
//            $xml = ShipmentArrayToXml::convert( $d, [
//                'rootElementName' => 'eVSCancelRequest',
//                '_attributes'     => [
//                    'USERID' => WPSP_USPS_USER_ID,
//                ],
//            ], true, 'UTF-8' );
//
//            $url = add_query_arg( [
//                'API' => 'eVSCancel',
//                'XML' => urlencode( $xml )
//            ], "ShippingAPI.dll" );
//
//            $res = $this->request( $url );
//            $res = ShipmentXmlToArray::convert( $res );
//
//            if ( isset( $res['Error'] ) ) {
//                $error = $res['Error']['Description'];
//            } else {
//                $res = $res['eVSCancelResponse'];
//
//                if ( isset( $res['Status'] ) && $res['Status'] != 'Cancelled' ) {
//                    $error = $res['Reason'];
//                }
//            }
//
//        } catch ( Exception $e ) {
//            $error = $e->getMessage();
//        }
//    }
//
//    function wpsp_get_markup_rate_usps( $rate, $customer )
//    {
//        $value = get_user_meta( $customer, 'usps_rate', true );
//
//        if ( $value === false ) {
//            $value = 0;
//        }
//
//        return floatval( $value );
//    }
//
//    function wpsp_label_rates_usps( $data, &$error, &$rates )
//    {
//        $error        = false;
//        $from_address = WPSP_Address::get_address( $data->from );
//        $to_address   = WPSP_Address::get_address( $data->to );
//        $from_zip     = $from_address['zip_code'];
//        $to_zip       = $to_address['zip_code'];
//
//        $d        = [
//            'Revision' => 2,
//        ];
//        $services = apply_filters( 'wpsp_shipment_usps_services', [] );
//
//        foreach ( $data->packages as $k => $package ) {
//            $id = $k + 1;
//
//            $d["Package_{$id}"] = [
//                '_attributes'    => [
//                    'ID' => $id,
//                ],
//                'Service'        => array_search( $data->shipping_method, $services ),
//                'ZipOrigination' => $from_zip,
//                'ZipDestination' => $to_zip,
//                'Pounds'         => ( $package['weight'] / 16 ),
//                'Ounces'         => $package['weight'],
//                'Container'      => $data->package_type,
//                'Size'           => 'REGULAR',
//                'Width'          => $package['width'],
//                'Length'         => $package['length'],
//                'Height'         => $package['height'],
//                'Girth'          => ( ( $package['width'] + $package['height'] ) * 2 )
//            ];
//        }
//
//        $xml = ShipmentArrayToXml::convert( $d, [
//            'rootElementName' => 'RateV4Request',
//            '_attributes'     => [
//                'USERID' => WPSP_USPS_USER_ID,
//            ],
//        ], true, 'UTF-8' );
//
//        foreach ( $data->packages as $k => $package ) {
//            $id  = $k + 1;
//            $xml = str_replace( "Package_{$id}", "Package", $xml );
//        }
//
//        $url = add_query_arg( [
//            'API' => 'RateV4',
//            'XML' => urlencode( $xml )
//        ], "ShippingAPI.dll" );
//
//        $res = $this->request( $url );
//        $res = ShipmentXmlToArray::convert( $res );
//
//        if ( ! isset( $res['Error'] ) ) {
//            $res   = $res['RateV4Response'];
//            $rates = 0;
//
//            if ( count( $data->packages ) > 1 ) {
//                foreach ( $res['Package'] as $package ) {
//                    if ( ! empty( $package['Postage']['Rate'] ) ) {
//                        $rates += $package['Postage']['Rate'];
//                    }
//                }
//            } else {
//                $rates += $res['Package']['Postage']['Rate'];
//            }
//        } else {
//            $error = $res['Error']['Description'];
//        }
//    }
//
//    function wpsp_create_label_usps( &$error, &$encoded_images, $shipment_data )
//    {
//        $error = __( 'Label not found', WPSP_LANG );
//
//        if ( ! empty( $shipment_data['label'] ) ) {
//            $pdf_file_name = "{$shipment_data['shipKey']}.pdf";
//            $filepath      = apply_filters( 'wpsp_file_dir', $pdf_file_name );
//
//            file_put_contents( $filepath, base64_decode( $shipment_data['label'] ) );
//
//            $encoded_images[] = $filepath;
//        }
//        $error = false;
//    }
//
//    function wpsp_usps_create_shipment( $data, &$error, &$shipment_data )
//    {
//        try {
//            $error        = false;
//            $from_address = WPSP_Address::get_address( $data->from );
//            $to_address   = WPSP_Address::get_address( $data->to );
//            $from_zip     = $from_address['zip_code'];
//            $packages     = $data->packages;
//
//            list( $from_zip4, $from_zip5 ) = $this->zip4_5( $from_zip );
//
//            foreach ( $packages as $package ) {
//                $d = [
//                    'Option'                     => 1,
//                    'ImageParameters'            => [ 'ImageParameter' => '4X6LABEL' ],
//                    'FromName'                   => $from_address['full_name'],
//                    'FromFirm'                   => $from_address['company'],
//                    'FromAddress1'               => $from_address['street_2'],
//                    'FromAddress2'               => $from_address['street_1'],
//                    'FromCity'                   => $from_address['city'],
//                    'FromState'                  => $from_address['state'],
//                    'FromZip5'                   => $from_zip5,
//                    'FromZip4'                   => $from_zip4,
//                    'FromPhone'                  => $from_address['phone'],
//                    'AllowNonCleansedOriginAddr' => "false",
//                    'ToName'                     => $to_address['full_name'],
//                    'ToFirm'                     => $to_address['company'],
//                    'ToAddress1'                 => $to_address['street_2'],
//                    'ToAddress2'                 => $to_address['street_1'],
//                    'ToCity'                     => $to_address['city'],
//                    'ToState'                    => $to_address['state'],
//                    'ToZip5'                     => $from_zip5,
//                    'ToZip4'                     => $from_zip4,
//                    'ToPhone'                    => $to_address['phone'],
//                    'POBox'                      => "false",
//                    'AllowNonCleansedDestAddr'   => "false",
//                    'WeightInOunces'             => $package['weight'],
//                    'ServiceType'                => $data->shipping_method,
//                    'Container'                  => $data->package_type,
//                    'Width'                      => $package['width'],
//                    'Length'                     => $package['length'],
//                    'Height'                     => $package['height'],
//                    'Machinable'                 => "true",
//                    'ProcessingCategory'         => [],
//                    'PriceOptions'               => 'Commercial Plus',
//                    'AddressServiceRequested'    => "true",
//                    'ExpressMailOptions'         => [ 'DeliveryOption' => [], 'WaiverOfSignature' => [] ],
//                    'ShipDate'                   => date( "m/d/Y", strtotime( $data->shipping_date ) ),
//                    'CustomerRefNo'              => $data->customer,
//                    'RecipientName'              => $to_address['fullName'],
//                    'ImageType'                  => 'PDF',
//                    'PrintCustomerRefNo'         => "false",
//                    'OptOutOfSPE'                => "false",
//                    'ePostageMailerReporting'    => []
//                ];
//
//                $xml = ShipmentArrayToXml::convert( $d, [
//                    'rootElementName' => 'eVSRequest',
//                    '_attributes'     => [
//                        'USERID' => WPSP_USPS_USER_ID,
//                    ],
//                ], true, 'UTF-8' );
//
//                $url = add_query_arg( [
//                    'API' => 'eVS',
//                    'XML' => urlencode( $xml )
//                ], "ShippingAPI.dll" );
//
//                $res = $this->request( $url );
//                $res = ShipmentXmlToArray::convert( $res );
//
//                if ( ! isset( $res['Error'] ) ) {
//                    $res           = $res['eVSResponse'];
//                    $shipment_data = array(
//                        "shipKey" => $res['BarcodeNumber'],
//                        "label"   => $res['LabelImage']
//                    );
//                } else {
//                    $error = $res['Error']['Description'];
//                }
//            }
//        } catch ( Exception $e ) {
//            $error = $e->getMessage();
//        }
//    }

    function request($endpoint, $type, $data)
    {
        $ch = curl_init();


        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization: e10f1561942a11e98bd9792e89914171',
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $response = curl_exec($ch);

        if (curl_errno($ch) !== 0) {
            $error_message = "";

            return array(
                "status" => false,
                "error" => $error_message
            );
        }

        if (is_resource($ch)) {
            curl_close($ch);
        }

        return $response;
    }

    function wpsp_add_ezeeship_carrier($carriers)
    {
        $carriers['fedex'] = 'FedEx';
        $carriers['ups'] = 'UPS';

        return $carriers;
    }

    function wpsp_shipment_ups_levels()
    {

        $services = [
            'ups_next_day_air_early_am',
            'ups_next_day_air',
            'ups_next_day_air_saver',
            'ups_second_day_air_am',
            'ups_second_day_air',
            'ups_3_day_select',
            'ups_ground'
        ];

        return $services;
    }

    function wpsp_shipment_fedex_levels()
    {

        $services = [
            'fedex_home_delivery',
            'fedex_priority_overnight',
            'fedex_standard_overnight',
            'fedex_2_day_am',
            'fedex_2_day',
            'fedex_express_saver',
            'fedex_ground',
            'fedex_smart_post',
            'fedex_international_economy',
            'fedex_international_priority',
        ];

        return $services;
    }

    function wpsp_shipment_fedex_package_types()
    {
        $levels = [
            'your_package',
            'FedEx_Envelope',
            'FedEx_Pak_1',
            'FedEx_Tube',
            'fedex_box',
            'fedex_Small_Box'
        ];

        return $levels;
    }

    function wpsp_shipment_ups_package_types()
    {
        $levels = [
            'your_package',
            'ups_letter',
            'UPS_Express_Tube',
            'UPS_Express_Pak',
            'UPS_Express_Box_Small',
            'UPS_Express_Box_Medium',
            'UPS_Express_Box_Large'
        ];

        return $levels;
    }

    function wpsp_verify_address_fedex($data, &$error)
    {
        $d = [];
        $d["countryCode"] = $data->country;
        $d["stateCode"] = $data->state;
        $d["city"] = $data->state;
        $d["addressLine1"] = $data->street_1 . $data->street_2;
        $d["zipCode"] = $data->zip_code;
        $d = json_encode($d);
//        print_r($d);
//        die();
        $endpoint = 'https://ezeeship.com/api/ezeeship-openapi/address/validate';
        $type = 'POST';

        $res = $this->request($endpoint, $type, $d);
        $res = json_decode($res);

        if ($res->result == 'OK') {
            $error = false;
        } else {
            $error = $res->message;

        }
    }

    function wpsp_verify_address_ups($data, &$error)
    {
        $d = [];
        $d["countryCode"] = $data->country;
        $d["stateCode"] = $data->state;
        $d["city"] = $data->state;
        $d["addressLine1"] = $data->street_1 . $data->street_2;
        $d["zipCode"] = $data->zip_code;
        $d = json_encode($d);
//        print_r($d);
//        die();
        $endpoint = 'https://ezeeship.com/api/ezeeship-openapi/address/validate';
        $type = 'POST';

        $res = $this->request($endpoint, $type, $d);
        $res = json_decode($res);

        if ($res->result == 'OK') {
            $error = false;
        } else {
            $error = $res->message;

        }
    }

}

new WPSP_EZEESHIP();