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

//includes
include 'includes/class-shipment-xml-array.php';
include 'includes/class-shipment-array-xml.php';

// Filters
add_filter('wpsp_shipment_carriers', 'wpsp_add_usps_carrier');

// Actions
add_action('wpsp_create_shipment_usps', 'wpsp_usps_create_shipment', 10, 3);
add_action('wpsp_label_rates_usps', 'wpsp_label_rates_usps', 10, 3);

function wpsp_label_rates_usps($data, &$error, &$rates)
{
    $error = false;
    $rates = 10;
}

function wpsp_usps_create_shipment($data, &$error, &$shipment_id)
{
    try {
        $from_address = WPSP_Address::getAddress($data->from);
//        var_dump($from_address);
        $to_address = WPSP_Address::getAddress($data->to);
//        var_dump($to_address);
        $from_zip = $from_address['zip_code'];
        $from_zip4 = $from_zip5 = "";
        $response = [];

        if (strlen($from_zip) == 5) {
            $from_zip5 = $from_zip;
        } else if (strlen($from_zip) == 4) {
            $from_zip4 = $from_zip;
        } else if (strlen($from_zip) > 5) {
            $from_zip = explode('-', $from_zip);
            $from_zip5 = $from_zip[0];
            $from_zip4 = $from_zip[1];
        }

        $packages = $data->packages;
//        $service_level = str_replace('USPS_', '', $data->serverLevel);
//        $package_type = str_replace('USPS_', '', $data->packageType);
        //hardcode value
        $service_level = 'Priority';
        $package_type = 'Rectangular';


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
                'ServiceType' => $service_level,
                'Container' => $package_type,
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
//            var_dump($d);
//            die();

            $xml = ShipmentArrayToXml::convert($d, [
                'rootElementName' => 'eVSRequest',
                '_attributes' => [
                    'USERID' => WPSP_USPS_USER_ID,
                ],
            ], true, 'UTF-8');
//            var_dump($xml);
//            die();
            $url = add_query_arg([
                'API' => 'eVS',
                'XML' => urlencode($xml)
            ], "ShippingAPI.dll");
            $res = request($url);
            var_dump($res);
            die();

            $res = ShipmentXmlToArray::convert($res);

            if (!isset($res['Error'])) {
                $res = $res['eVSResponse'];

                $response = [
                    'key' => $res['BarcodeNumber'],
                    'status' => 'Pending',
                    'server' => 'USPS',
                    'serverLevel' => $data['serverLevel'],
                    'packageType' => $data['packageType'],
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

function wpsp_add_usps_carrier($carriers)
{
    $carriers['usps'] = 'USPS';

    return $carriers;
}

function request($endpoint)
{
    $ch = curl_init();

    $endpoint = "https://stg-secure.shippingapis.com/{$endpoint}";

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