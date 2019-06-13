<?php

class WPSP_ShipmentActions
{
    function action_save_label()
    {
        $response = [];

        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wpsp_save_label')) {

            $post_data = (object)$_POST;
            $error = false;
            $shipment_id = false;
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

                if ($user_funds > $rates) {

                    // create shipment
                    do_action_ref_array("wpsp_create_shipment_{$post_data->carrier}", [
                        $post_data,
                        &$error,
                        &$shipment_id
                    ]);

                    if (!$error && $shipment_id) {

                        // db entry

                        // create label
                        do_action_ref_array("wpsp_create_label_{$post_data->carrier}", [
                            $post_data,
                            &$error
                        ]);

                        // send label via email and fax

                        // funds deduct
                        WPSP_Customer::deduct_funds($post_data->customer, $rates);
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

            header('Content-Type: application/json');
        } else {
            $response['status'] = false;
            $response['message'] = __('Please try again', WPSP_LANG);
        }

        echo json_encode($response);
        die;
    }

    function action_add_address()
    {
        $response = [];

        if (isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'wpsp_add_address')) {
//		    var_dump($_POST);
            $address = WPSP_Address::store_address($_POST);
            $response['status'] = true;
            $response['message'] = __('Address created successfully', WPSP_LANG);
            $response['data'] = $address;
        } else {
            $response['status'] = false;
            $response['message'] = __('Please try again', WPSP_LANG);
        }

        echo json_encode($response);
        die;
    }

    function shipment_details()
    {
        include('templates/shipment-details.php');
    }
}