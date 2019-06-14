<?php

class WPSP_ShipmentActions
{
    function action_void_label()
    {
        $response = [];
        $shipment_id = $_REQUEST['id'];
        $refund = isset($_REQUEST['refund']) && $_REQUEST['refund'] == '1';
        $shipment = WPSP_Shipment::get_shipment($shipment_id);
        $carrier = $shipment->carrier;
        $error = false;

        $response['status'] = true;
        $response['message'] = __('Label void successfully', WPSP_LANG);

        do_action_ref_array("wpsp_void_label_{$carrier}", [
            &$error,
            $shipment_id
        ]);

        if ($refund) {
            // TODO: add funds again to customer account
        }

        if ($error) {
            $response['status'] = false;
            $response['message'] = $error;
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        die;
    }

    function action_save_label()
    {
        global $wpdb;

        $response = [];

        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wpsp_save_label')) {

            $post_data = (object)$_POST;
            $error = false;
            $shipment_data = false;
            $rates = 0;

            // rates
            do_action_ref_array("wpsp_label_rates_{$post_data->carrier}", [
                $post_data,
                &$error,
                &$rates
            ]);

            if (!$error && $rates) {

                // check for funds
                $user_funds = WPSP_Customer::get_account_funds($post_data->customer);
                $markup_rate = apply_filters("wpsp_get_markup_rate_{$post_data->carrier}", 0, $post_data->customer);
                $label_rates = $rates;

                if ($markup_rate > 0) {
                    $rates += ($rates * ($markup_rate / 100));
                }

                if ($user_funds > $rates) {

                    // create shipment
                    do_action_ref_array("wpsp_create_shipment_{$post_data->carrier}", [
                        $post_data,
                        &$error,
                        &$shipment_data
                    ]);

                    if (!$error && !empty($shipment_data)) {

                        // db entry
                        $row = array(
                            "ticket_id" => $post_data->ticket_id,
                            "customer_id" => $post_data->customer,
                            "creator_id" => get_current_user_id(),
                            "server" => $post_data->carrier,
                            "status" => "Pending",
                            "serverLevel" => $post_data->shipping_method,
                            "packageType" => $post_data->package_type,
                            "creation_date" => date("Y/m/d"),
                            "shipDate" => date('Y-m-d', strtotime($post_data->shipping_date)),
                            "toAddress_id" => intval($post_data->to),
                            "fromAddress_id" => intval($post_data->from),
                            "pickup_date" => !empty($post_data->pickup_date) ? date('Y-m-d H:i:s', strtotime($post_data->pickup_date)) : null,
                            "dropOffType" => "",
                            "confirmation" => "",
                            "reference" => "",
                            "shipmentNo" => "",
                            "rates" => $rates,
                            "markupRate" => $markup_rate,
                            "labelRate" => $label_rates
                        );

                        $columns = array_keys($row);
                        $row = array_merge($row, $shipment_data);
                        $row = array_filter($row, function ($key) use ($columns) {
                            return in_array($key, $columns);
                        }, ARRAY_FILTER_USE_KEY);

                        $inserted = $wpdb->insert($wpdb->prefix . 'shipments', $row);

                        if ($inserted) {
                            $shipment_id = $wpdb->insert_id;
                            $encoded_image = false;

                            // create label
                            do_action_ref_array("wpsp_create_label_{$post_data->carrier}", [
                                &$error,
                                &$encoded_image,
                                $shipment_data,
                                $shipment_id,
                                $post_data,
                            ]);

                            if (!$error) {

                                // TODO: generate label
                                //$file_name = "{$post_data->carrier}-{$shipment_id}.tiff";
                                //list( $file_path, $file_url ) = WPSP_PdfHelper::generate( $encoded_image, $file_name );

                                //var_dump( $file_url, $file_path );
                                //die;

                                // TODO: send label via email and fax

                                // funds deduct
                                WPSP_Customer::deduct_funds($post_data->customer, $rates);

                                $response['status'] = true;
                                $response['data'] = [
                                    'shipment_id' => $shipment_id
                                ];
                                $response['message'] = __('Shipment created successfully', WPSP_LANG);
                            }

                        } else {
                            $error = __('Failed to add shipment', WPSP_LANG);
                        }
                    }
                } else {
                    $error = __('No funds available', WPSP_LANG);
                }
            } else {
                $error = __('Rates not found', WPSP_LANG);
            }

            if ($error !== false) {
                $response['status'] = false;
                $response['message'] = $error;
            }

        } else {
            $response['status'] = false;
            $response['message'] = __('Please try again', WPSP_LANG);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        die;
    }


    function action_add_address()
    {
        $response = [];

        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wpsp_add_address')) {

            $error = false;
            $post_data = (object)$_POST;
//echo "wpsp_verify_address_{$post_data->carrier}";die();
            do_action_ref_array("wpsp_verify_address_{$post_data->carrier}", [
                $post_data,
                &$error
            ]);

            if (!$error) {
//			    var_dump('here');die();
                $address = WPSP_Address::store_address($post_data);
                $response['status'] = true;
                $response['message'] = __('Address created successfully', WPSP_LANG);
                $response['data'] = $address;
            } else {
                $response['status'] = false;
                $response['message'] = $error;
            }
        } else {
            $response['status'] = false;
            $response['message'] = __('Please try again', WPSP_LANG);
        }

        header('Content-Type: application/json');
        echo json_encode($response);
        die;
    }

    function action_carrier_levels()
    {
        $carrier = $_REQUEST['carrier'];
        $levels = [];
        $levels = apply_filters("wpsp_shipment_{$carrier}_levels", $levels);

        header('Content-Type: application/json');
        echo json_encode($levels);
        die;
    }

    function action_package_types()
    {
        $carrier = $_REQUEST['carrier'];
        $types = [];
        $types = apply_filters("wpsp_shipment_{$carrier}_package_types", $types);

        header('Content-Type: application/json');
        echo json_encode($types);
        die;
    }

    function action_get_rates()
    {
        die;
    }

    function action_get_addresses()
    {
        $customer_id = $_REQUEST['customer_id'];
        $addresses = WPSP_Address::get_addresses_by_customer($customer_id);

        header('Content-Type: application/json');
        echo json_encode($addresses);
        die;
    }

    function shipment_details()
    {
        include('templates/shipment-details.php');
    }
}