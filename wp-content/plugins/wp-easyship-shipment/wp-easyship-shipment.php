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

        // Actions
        add_action('wpsp_verify_address_ups', [$this, 'wpsp_verify_address_ups'], 10, 3);
        add_action('wpsp_verify_address_fedex', [$this, 'wpsp_verify_address_fedex'], 10, 3);
        add_action('wpsp_create_shipment_ups', [$this, 'wpsp_ups_create_shipment'], 10, 3);
        add_action('wpsp_create_shipment_fedex', [$this, 'wpsp_fedex_create_shipment'], 10, 3);
        add_action('wpsp_create_label_ups', [$this, 'wpsp_create_label_ups'], 10, 3);
        add_action('wpsp_label_rates_fedex', [$this, 'wpsp_label_rates_fedex'], 10, 3);
        add_action('wpsp_label_rates_ups', [$this, 'wpsp_label_rates_ups'], 10, 3);
        add_action('wpsp_void_label_usps', [$this, 'wpsp_void_label_usps'], 10, 2);
        add_action('wpsp_service_rates_ups', [$this, 'wpsp_service_rates_ups'], 10, 3);
        add_action('wpsp_service_rates_fedex', [$this, 'wpsp_service_rates_fedex'], 10, 3);
    }

    //api request

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

    //end api request

    //Addon filter

    function wpsp_add_ezeeship_carrier($carriers)
    {
        $carriers['fedex'] = 'FedEx';
        $carriers['ups'] = 'UPS';

        return $carriers;
    }

    function wpsp_shipment_fedex_package_types()
    {
        $levels = [
            'your_package' => 'YourPackage',
            'FedEx_Envelope' => 'FedEx® Envelope',
            'FedEx_Pak_1' => 'FedEx® Pak (1)',
            'FedEx_Tube' => 'FedEx® Tube',
            'fedex_box' => 'FedEx® Box',
            'fedex_Small_Box' => 'FEDEX_SMALL_BOX'
        ];

        return $levels;
    }

    function wpsp_shipment_ups_package_types()
    {
        $levels = [
            'your_package' => 'YourPackage',
            'ups_letter' => 'UPS Letter',
            'UPS_Express_Tube' => 'UPS Tube',
            'UPS_Express_Pak' => 'UPS Express® Pak',
            'UPS_Express_Box_Small' => 'UPS Express® Box - Small',
            'UPS_Express_Box_Medium' => 'UPS Express® Box - Medium',
            'UPS_Express_Box_Large' => 'UPS Express® Box - Large'
        ];

        return $levels;
    }

    function wpsp_shipment_fedex_levels()
    {

        $services = [
            'fedex_home_delivery' => 'FedEx Home Delivery®',
            'fedex_priority_overnight' => 'FedEx Priority Overnight®',
            'fedex_standard_overnight' => 'FedEx Standard Overnight®',
            'fedex_2_day_am' => 'FedEx 2Day® A.M.',
            'fedex_2_day' => 'FedEx 2Day®',
            'fedex_express_saver' => 'FedEx Express Saver®',
            'fedex_ground' => 'FedEx Ground®',
            'fedex_smart_post' => 'FedEx SmartPost(only support single parcel)',
            'fedex_international_economy' => 'FedEx International Economy®',
            'fedex_international_priority' => 'FedEx International Priority®'
        ];

        return $services;
    }

    function wpsp_shipment_ups_levels()
    {

        $services = [
            'ups_next_day_air_early_am' => 'UPS Next Day Air® Early',
            'ups_next_day_air' => 'UPS Next Day Air®',
            'ups_next_day_air_saver' => 'UPS Next Day Air Saver®',
            'ups_second_day_air_am' => 'UPS 2nd Day Air AM®',
            'ups_second_day_air' => 'UPS 2nd Day Air®',
            'ups_3_day_select' => 'UPS 3 Day Select®',
            'ups_ground' => 'UPS® Ground'
        ];

        return $services;
    }

    //end Addon filter


    //Addon Action

    //create label
    function wpsp_create_label_fedex(&$error, &$encoded_images, $shipment_data)
    {
        $error = __('Label not found', WPSP_LANG);

        if (!empty($shipment_data['label'])) {
            $pdf_file_name = "{$shipment_data['shipKey']}.pdf";
            $filepath = apply_filters('wpsp_file_dir', $pdf_file_name);

            file_put_contents($filepath, file_get_contents($shipment_data['label']));

            $encoded_images[] = $filepath;

//            var_dump($encoded_images);
//            die();
        }
        $error = false;
    }

    function wpsp_create_label_ups(&$error, &$encoded_images, $shipment_data)
    {
        $error = __('Label not found', WPSP_LANG);

        if (!empty($shipment_data['label'])) {
            $pdf_file_name = "{$shipment_data['shipKey']}.pdf";
            $filepath = apply_filters('wpsp_file_dir', $pdf_file_name);

            file_put_contents($filepath, file_get_contents($shipment_data['label']));

            $encoded_images[] = $filepath;

//            var_dump($encoded_images);
//            die();
        }
        $error = false;
    }

    //end create label

    // Rates

    function wpsp_label_rates_fedex($data, &$error, &$rates)
    {
        $this->wpsp_service_rates_fedex($data, $error, $rates);
        $res = $rates;
        if (!$error) {
            $rates = 0;
            $rates += $res[0]['rate'];
        }
    }

    function wpsp_label_rates_ups($data, &$error, &$rates)
    {
        $this->wpsp_service_rates_ups($data, $error, $rates);
        $res = $rates;
        if (!$error) {
            $rates = 0;
            $rates += $res[0]['rate'];
//            var_dump($rates);
//            die();
        } else {
            $error = $error;
        }
    }

    //end rate

    //create Shipment

    function wpsp_fedex_create_shipment($data, &$error, &$shipment_data)
    {
        $error = false;
        $shipment_data = [];
        $data->carrier = 'fedex';
        $this->wpsp_ezeeship_create_shipment($data, $error, $shipment_data);
    }

    function wpsp_ups_create_shipment($data, &$error, &$shipment_data)
    {
        $error = false;
        $shipment_data = [];
        $data->carrier = 'ups';
        $this->wpsp_ezeeship_create_shipment($data, $error, $shipment_data);
    }

    function wpsp_ezeeship_create_shipment($data, &$error, &$shipment_data)
    {

        try {
            $from_address = WPSP_Address::get_address($data->from);
            $to_address = WPSP_Address::get_address($data->to);
            $packages = $data->packages;
            $endpoint = 'https://ezeeship.com/api/ezeeship-openapi/label/create';
            $type = 'POST';

            $d = [];
            $d['from'] = [];
            $d['from']['personName'] = $from_address['full_name'];
            $d['from']['countryCode'] = $from_address['country'];
            $d['from']['stateCode'] = $from_address['state'];
            $d['from']['phone'] = $from_address['phone'];
            $d['from']['city'] = $from_address['city'];
            $d['from']['addressLine1'] = $from_address['street_1'];
            $d['from']['zipCode'] = $from_address['zip_code'];

            $d['to'] = [];
            $d['to']['personName'] = $to_address['full_name'];
            $d['to']['company'] = $to_address['company'];
            $d['to']['countryCode'] = $to_address['country'];
            $d['to']['stateCode'] = $to_address['state'];
            $d['to']['city'] = $to_address['city'];
            $d['to']['phone'] = $to_address['phone'];
            $d['to']['addressLine1'] = $to_address['street_1'];
            $d['to']['zipCode'] = $to_address['zip_code'];
            $d['carrierCode'] = $data->carrier;
            $d['serviceCode'] = $data->shipping_method;
            if (WPSP_EZEESHIP_DEBUG == false) {
                $d['isTest'] = false;
            } else {
                $d['isTest'] = true;
            }
            $d['parcels'] = [];
            foreach ($packages as $k => $package) {
                $pac = [
                    "packageNum" => $k + 1,
                    "length" => $package['length'],
                    "width" => $package['width'],
                    "height" => $package['height'],
                    "distanceUnit" => "in",
                    "weight" => $package['weight'],
                    "massUnit" => "lb",
                    "packageCode" => $data->package_type,

                ];
                $d['parcels'][] = $pac;
            }

            $d = json_encode($d);
            $res = $this->request($endpoint, $type, $d);

            $res = json_decode($res);


            if ($res->result == 'OK') {
                $shipment_data = array(
                    "shipKey" => $res->data->objectId,
                    "label" => $res->data->pdfUrl,
                );
            } else {
                $error = $res->message;

            }

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    //end create shipment

    //Estimate Rates

    function wpsp_service_rates_fedex($data, &$error, &$rates)
    {
        $error = false;
        $rates = [];
        $data->carrier = 'fedex';
        $this->wpsp_service_rates_ezeeship($data, $error, $rates);

    }

    function wpsp_service_rates_ups($data, &$error, &$rates)
    {
        $error = false;
        $data->carrier = 'ups';
        $this->wpsp_service_rates_ezeeship($data, $error, $rates);
    }

    function wpsp_service_rates_ezeeship($data, &$error, &$rates)
    {
        $rates = [];
        $error = false;
        $endpoint = 'https://ezeeship.com/api/ezeeship-openapi/shipment/estimateRate';
        $type = 'POST';
        $from_address = WPSP_Address::get_address($data->from);
        $to_address = WPSP_Address::get_address($data->to);

        if ($data->shipping_method == 'All') {
            if ($data->carrier == 'fedex') {
                $ups_services = $this->wpsp_shipment_fedex_levels();
            } else {
                $ups_services = $this->wpsp_shipment_ups_levels();
            }
            foreach ($ups_services as $key => $service) {
                $d = [];
                $d['from'] = [];
                $d['from']['countryCode'] = $from_address['country'];
                $d['from']['stateCode'] = $from_address['state'];
                $d['from']['city'] = $from_address['city'];
                $d['from']['addressLine1'] = $from_address['street_1'];
                $d['from']['zipCode'] = $from_address['zip_code'];
                $d['to'] = [];
                $d['to']['countryCode'] = $to_address['country'];
                $d['to']['stateCode'] = $to_address['state'];
                $d['to']['city'] = $to_address['city'];
                $d['to']['addressLine1'] = $to_address['street_1'];
                $d['to']['zipCode'] = $to_address['zip_code'];
                $d['carrierCode'] = $data->carrier;
                $d['serviceCode'] = $key;
                if (WPSP_EZEESHIP_DEBUG == false) {
                    $d['isTest'] = false;
                } else {
                    $d['isTest'] = true;
                }
                $d['parcels'] = [];


                foreach ($data->packages as $k => $package) {
                    $pac = [
                        "packageNum" => $k + 1,
                        "length" => $package['length'],
                        "width" => $package['width'],
                        "height" => $package['height'],
                        "distanceUnit" => "in",
                        "weight" => $package['weight'],
                        "massUnit" => "lb",
                        "packageCode" => $data->package_type,

                    ];
                    $d['parcels'][] = $pac;
                }

                $d = json_encode($d);
                $res = $this->request($endpoint, $type, $d);
                $res = json_decode($res);

                if ($res->result == 'OK') {
                    $error = false;
                    $rates[] = [
                        'name' => $service,
                        'rate' => $res->data->rate,
                        'level' => $key,
                        'package_type' => $data->package_type
                    ];
                    $rates = array_values($rates);
                } else {
                    $error = $res->message;
                }
            }

        } else {
            $d = [];
            $d['from'] = [];
            $d['from']['countryCode'] = $from_address['country'];
            $d['from']['stateCode'] = $from_address['state'];
            $d['from']['city'] = $from_address['city'];
            $d['from']['addressLine1'] = $from_address['street_1'];
            $d['from']['zipCode'] = $from_address['zip_code'];
            $d['to'] = [];
            $d['to']['countryCode'] = $to_address['country'];
            $d['to']['stateCode'] = $to_address['state'];
            $d['to']['city'] = $to_address['city'];
            $d['to']['addressLine1'] = $to_address['street_1'];
            $d['to']['zipCode'] = $to_address['zip_code'];
            $d['carrierCode'] = $data->carrier;
            $d['serviceCode'] = $data->shipping_method;
            $d['isTest'] = true;
            $d['parcels'] = [];


            foreach ($data->packages as $k => $package) {
                $pac = [
                    "packageNum" => $k + 1,
                    "length" => $package['length'],
                    "width" => $package['width'],
                    "height" => $package['height'],
                    "distanceUnit" => "in",
                    "weight" => $package['weight'],
                    "massUnit" => "lb",
                    "packageCode" => $data->package_type,

                ];
                $d['parcels'][] = $pac;
            }

            $d = json_encode($d);


            $res = $this->request($endpoint, $type, $d);
            $res = json_decode($res);

            if ($res->result == 'OK') {
                $error = false;
                $rates[] = [
                    'name' => $data->shipping_method,
                    'rate' => $res->data->rate,
                    'level' => $data->shipping_method,
                    'package_type' => $data->package_type
                ];
                $rates = array_values($rates);
            } else {
                $error = $res->message;

            }

        }
    }

    //end Estimate Rates

    //void label

    function wpsp_void_label_ups(&$error, $shipment_id)
    {
        $error = false;
        $this->wpsp_void_label_ezeeship($error, $shipment_id);
    }

    function wpsp_void_label_fedex(&$error, $shipment_id)
    {
        $error = false;
        $this->wpsp_void_label_ezeeship($error, $shipment_id);
    }

    function wpsp_void_label_ezeeship(&$error, $shipment_id)
    {
        $error = false;
        $shipment = WPSP_Shipment::get_shipment($shipment_id);
        $endpoint = 'https://ezeeship.com/api/ezeeship-openapi/label/cancel';
        $type = 'POST';

        try {
            $d = [];
            $d['objectId'] = $shipment->shipKey;
            $d = json_encode($d);
            $res = $this->request($endpoint, $type, $d);
            $res = json_decode($res);

            if ($res->result != 'OK') {
                $error = $res->message;
            } else {
                $error = false;
            }

        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }

    //end void label

    // address verification

    function wpsp_verify_address_fedex($data, &$error)
    {
        $this->wpsp_verify_address_ezeeship($data, $error);
    }

    function wpsp_verify_address_ups($data, &$error)
    {
        $this->wpsp_verify_address_ezeeship($data, $error);
    }

    function wpsp_verify_address_ezeeship($data, &$errror)
    {
        $d = [];
        $d["countryCode"] = $data->country;
        $d["stateCode"] = $data->state;
        $d["city"] = $data->state;
        $d["addressLine1"] = $data->street_1 . $data->street_2;
        $d["zipCode"] = $data->zip_code;
        $d = json_encode($d);
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

    //end address verification

    //end Addon Action
}

new WPSP_EZEESHIP();