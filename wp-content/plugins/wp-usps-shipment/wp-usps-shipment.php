<?php
/*
Plugin Name: WPSP USPS Add-on
Plugin URI: hztech.biz
description: This plugin works only with WPSP Shipment Plugin.
Version: 1.0
Author: Hztech
Author URI: hztech.biz
*/

define('WPSP_USPS_USER_ID', '572SHIP46470');
define('WPSP_USPS_DEBUG', true);

//includes
include 'includes/class-shipment-xml-array.php';
include 'includes/class-shipment-array-xml.php';

class WPSP_USPS
{
    function __construct()
    {
        // Filters
        add_filter('wpsp_shipment_carriers', [$this, 'wpsp_add_usps_carrier']);
        add_filter('wpsp_shipment_usps_levels', [$this, 'wpsp_shipment_usps_levels']);
        add_filter('wpsp_shipment_usps_package_types', [$this, 'wpsp_shipment_usps_package_types']);

        // Actions
        add_action('wpsp_verify_address_usps', [$this, 'wpsp_verify_address_usps'], 10, 3);
        add_action('wpsp_create_shipment_usps', [$this, 'wpsp_usps_create_shipment'], 10, 3);
        add_action('wpsp_label_rates_usps', [$this, 'wpsp_label_rates_usps'], 10, 3);
    }

    function wpsp_label_rates_usps($data, &$error, &$rates)
    {
        $from_address = WPSP_Address::getAddress($data->from);
        $to_address = WPSP_Address::getAddress($data->to);
        $from_zip = $from_address['zip_code'];
        $to_zip = $to_address['zip_code'];

        $from_zip4 = $from_zip5 = "";
        $to_zip4 = $to_zip5 = "";
        $response = [];

        list($from_zip4, $from_zip5) = $this->zip4_5($from_zip);
        list($to_zip4, $to_zip5) = $this->zip4_5($to_zip);

        $d = [
            'Revision' => 2,
        ];

        foreach ($data->packages as $k => $package) {
            $id = $k + 1;
            $pounds = $package['weight'];
            $ounces = $package['ounces'];

            $d["Package_{$id}"] = [
                '_attributes' => [
                    'ID' => $id,
                ],
                'Service' => $data->shipping_method,
                'ZipOrigination' => $from_zip,
                'ZipDestination' => $to_zip,
                'Pounds' => 1,
                'Ounces' => 1,
                'Container' => $data->package_type
            ];
        }

        $xml = ShipmentArrayToXml::convert($d, [
            'rootElementName' => 'RateV4Request',
            '_attributes' => [
                'USERID' => WPSP_USPS_USER_ID,
            ],
        ], true, 'UTF-8');

        foreach ($data->packages as $k => $package) {
            $id = $k + 1;
            $xml = str_replace("Package_{$id}", "Package", $xml);
        }
        echo $xml;
        die;

        $url = add_query_arg([
            'API' => 'RateV4',
            'XML' => urlencode($xml)
        ], "ShippingAPI.dll");

        $res = $this->request($url);
        $res = ShipmentXmlToArray::convert($res);

        var_dump($res);
        die;

        if (!isset($res['Error'])) {
            $res = $res['eVSResponse'];
        }

        $error = false;
        $rates = 10;
    }

    function wpsp_usps_create_shipment($data, &$error, &$shipment_id)
    {
        try {
            $from_address = WPSP_Address::getAddress($data->from);
            $to_address = WPSP_Address::getAddress($data->to);
            $from_zip = $from_address['zip_code'];
            $from_zip4 = $from_zip5 = "";
            $response = [];

            list($from_zip4, $from_zip5) = $this->zip4_5($from_zip);

            $packages = $data->packages;

            foreach ($packages as $package) {
                $d = [
                    'Option' => 1,
                    'ImageParameters' => ['ImageParameter' => 'CROP'],
                    'FromName' => $from_address['full_name'],
                    'FromFirm' => $from_address['company'],
                    'FromAddress1' => $from_address['street_1'],
                    'FromAddress2' => $from_address['street_2'],
                    'FromCity' => $from_address['city'],
                    'FromState' => $from_address['state'],
                    'FromZip5' => $from_zip5,
                    'FromZip4' => $from_zip4,
                    'FromPhone' => $from_address['phone'],
                    'AllowNonCleansedOriginAddr' => "false",
                    'ToName' => $to_address['full_name'],
                    'ToFirm' => $to_address['company'],
                    'ToAddress1' => $to_address['street_1'],
                    'ToAddress2' => $to_address['street_2'],
                    'ToCity' => $to_address['city'],
                    'ToState' => $to_address['state'],
                    'ToZip5' => $from_zip5,
                    'ToZip4' => $from_zip4,
                    'ToPhone' => $to_address['phone'],
                    'POBox' => "false",
                    'AllowNonCleansedDestAddr' => "false",
                    'WeightInOunces' => $package['weight'],
                    'ServiceType' => $data->shipping_method,
                    'Container' => $data->package_type,
                    'Width' => $package['width'],
                    'Length' => $package['length'],
                    'Height' => $package['height'],
                    'Machinable' => "true",
                    'ProcessingCategory' => [],
                    'PriceOptions' => 'Commercial Plus',
                    'AddressServiceRequested' => "true",
                    'ExpressMailOptions' => ['DeliveryOption' => [], 'WaiverOfSignature' => []],
                    'ShipDate' => date("m/d/Y", strtotime($data->shipping_date)),
                    'CustomerRefNo' => $data->customer,
                    'RecipientName' => $to_address['fullName'],
                    'ImageType' => 'PDF',
                    'PrintCustomerRefNo' => "false",
                    'OptOutOfSPE' => "false",
                    'ePostageMailerReporting' => []
                ];

                $xml = ShipmentArrayToXml::convert($d, [
                    'rootElementName' => 'eVSRequest',
                    '_attributes' => [
                        'USERID' => WPSP_USPS_USER_ID,
                    ],
                ], true, 'UTF-8');

                $url = add_query_arg([
                    'API' => 'eVS',
                    'XML' => urlencode($xml)
                ], "ShippingAPI.dll");

                $res = $this->request($url);
                $res = ShipmentXmlToArray::convert($res);

                if (!isset($res['Error'])) {
                    $res = $res['eVSResponse'];

                    $response = [
                        'key' => $res['BarcodeNumber'],
                        'status' => 'Pending',
                        'server' => 'USPS',
                        'serverLevel' => $data->shipping_method,
                        'packageType' => $data->package_type,
                        'dropOffType' => '',
                        'confirmation' => '',
                        'trackingNumber' => $res['BarcodeNumber'],
                        'label' => $res['LabelImage'],
                        'rates' => [
                            'customerCharge' => $res['Postage']
                        ]
                    ];
                } else {
                    $response = [
                        'message' => $res['Error']['Description']
                    ];
                }
            }
        } catch (Exception $e) {
            $response = [
                'message' => $e->getMessage()
            ];
        }

        var_dump($response);
        die;

        return $response;

        /*
         *
         *
         * Yahan par tumhe USPS ki API execute karni hai, $data ke variable men saari fields milengi tumhe
         * var_dump $data karke check bhi karskte ho kia kia milega tumhe yahan par, console open karke admin
         * se form submit karna, Network men response check karna, jo yahan var dump karoge wahan show hoga.
         * Customer "Hadmin Hadmin" select karna
         * and Carrier men USPS
         * then submit karnaTASK FOR ASAD
         *
         */

        var_dump($data);
        die;

        $error = false;
        $shipment_id = 1;
    }

    function wpsp_verify_address_usps($data, &$error)
    {

        list($from_zip4, $from_zip5) = $this->zip4_5($data->zip_code);
        $d = [
            'Address' => [
                '_attributes' => [
                    'ID' => 0,
                ],
                'Address1' => $data->street_1,
                'Address2' => $data->street_2,
                'City' => $data->city,
                'State' => $data->state,
                'Zip5' => $from_zip5,
                'Zip4' => $from_zip4,
            ]
        ];

        $xml = ShipmentArrayToXml::convert($d, [
            'rootElementName' => 'AddressValidateRequest',
            '_attributes' => [
                'USERID' => WPSP_USPS_USER_ID,
            ],
        ], true, 'UTF-8');
        $url = add_query_arg([
            'API' => 'Verify',
            'XML' => urlencode($xml)
        ], "ShippingAPI.dll");
//        echo $url;
//        die();
        $res = $this->request($url);
        $res = ShipmentXmlToArray::convert($res);

        if (!isset($res['AddressValidateResponse']['Address']['Error'])) {
            $error = false;
        } else {
            $error = $res['AddressValidateResponse']['Address']['Error']['Description'];

        }
    }

    function wpsp_add_usps_carrier($carriers)
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

    function request($endpoint)
    {
        $ch = curl_init();

        if (WPSP_USPS_DEBUG) {
            $endpoint = "https://stg-secure.shippingapis.com/{$endpoint}";
        } else {
            $endpoint = "https://secure.shippingapis.com/{$endpoint}";
        }

        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
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

    function zip4_5($from_zip)
    {
        $from_zip4 = $from_zip5 = "";
//        $to_zip4      = $to_zip5 = "";
//        $response     = [];

        if (strlen($from_zip) == 5) {
            $from_zip5 = $from_zip;
        } else if (strlen($from_zip) == 4) {
            $from_zip4 = $from_zip;
        } else if (strlen($from_zip) > 5) {
            $from_zip = explode('-', $from_zip);
            $from_zip5 = $from_zip[0];
            $from_zip4 = $from_zip[1];
        }
        return [$from_zip4, $from_zip5];
    }
}

new WPSP_USPS();