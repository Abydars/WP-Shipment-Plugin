<?php

class WPSP_ShipmentActions
{
    function save_label()
    {
        if (!isset($_POST['create_label_form']) || !wp_verify_nonce($_POST['create_label_form'], 'create_label')
        ) {

            $post_data = $_POST['data'];
            $address = WPSP_Address::getAddress($post_data->from);
            if ($post_data->courier == 'ups') {
                $courier_id = 'b4552ed2-ae95-4647-9746-5790bf252c7f';
            }
//            $customer = Customer::getCustomer($post_data->customer);
            $data = [

                'selected_courier_id' => '',
                'destination_country_alpha2' => $address->country,
                'destination_city' => $address->city,
                'destination_postal_code' => $address->postal_code,
                'destination_state' => $address->state,
                'destination_name' => $address->full_name,
                'destination_address_line_1' => $address->street_1,
                'destination_address_line_2' => $address->street_2,
                'destination_phone_number' => $address->number,
                'destination_email_address' => $address->email,
                'items' => [
                    'description' => $post_data->packages[0]->description,
                    'sku' => $post_data->packages[0]->sku,
                    'actual_weight' => $post_data->packages[0]->weight,
                    'height' => $post_data->packages[0]->height,
                    'width' => $post_data->packages[0]->width,
                    'length' => $post_data->packages[0]->length,
                    'declared_currency' => $post_data->packages[0]->currency,
                    'declared_customs_value' => $post_data->packages[0]->value,
                ]
            ];

//            if()
            // rates
            // check for funds

            if ($_POST['courier'] == 'WPSP_USPS') {
                $shipment = new WPSP_USPS();
            } else {
                $shipment = new WPSP_EasyShip();
            }

            $shipment = $shipment->createShipment($data);
            $response = [];

            if ($shipment['status']) {
                // db entry
                // create label
                // send label via email and fax
                // funds deduct
            } else {
                // show error message
                $response['status'] = false;
                $response['message'] = $shipment['message'];
            }

            header('Content-Type: application/json');

            echo json_encode($response);
            exit;
        } else {
            var_dump($_POST);
            die;
        }
    }

    function save_from_address()
    {
        if (!isset($_POST['create_from_address']) || !wp_verify_nonce($_POST['create_from_address'], 'from_address')
        ) {
            echo 'Sorry, your nonce did not verify.';
            exit;
        } else {
            var_dump($_POST);
            die;
        }
    }

    function save_to_address()
    {
        if (!isset($_POST['create_to_address']) || !wp_verify_nonce($_POST['create_to_address'], 'to_address')
        ) {
            echo 'Sorry, your nonce did not verify.';
            exit;
        } else {
            var_dump($_POST);
            die;
        }
    }

    function shipment_details()
    {
        include('templates/shipment-details.php');
    }
}