<?php



class shipmentActions
{

    function save_label()
    {
        if (!isset($_POST['create_label_form']) || !wp_verify_nonce($_POST['create_label_form'], 'create_label')
        ) {

            $post_data = $_POST['data'];
            $address = Address::getAddress($post_data->from);
            if($post_data->courier == 'ups')
            {
                $courier_id = 'b4552ed2-ae95-4647-9746-5790bf252c7f';
            }
//            $customer = Customer::getCustomer($post_data->customer);
            $data = [

                'selected_courier_id' => '',
                'destination_country_alpha2' =>$address->country,
                'destination_city' => $address->city,
                'destination_postal_code' =>$address->postal_code,
                'destination_state' => $address->state,
                'destination_name' => $address->full_name,
                'destination_address_line_1' => $address->street_1,
                'destination_address_line_2' => $address->street_2,
                'destination_phone_number' => $address->number,
                'destination_email_address' => $address->email,
                'items' => [
                    'description' => $post_data->packages[0]->description,
                    'sku' =>$post_data->packages[0]->sku,
                    'actual_weight' =>$post_data->packages[0]->weight,
                    'height' =>$post_data->packages[0]->height,
                    'width' =>$post_data->packages[0]->width,
                    'length' =>$post_data->packages[0]->length,
                    'declared_currency' =>$post_data->packages[0]->currency,
                    'declared_customs_value' =>$post_data->packages[0]->value,
                    ]
            ];

//            if()
            // rates
            // check for funds

            if ($_POST['courier'] == 'usps') {
                $shipment = new Usps();
            } else {
                $shipment = new EasyShip();
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

//    function jal_install()
//    {
//        global $wpdb;
//        global $jal_db_version;
//
//        $table_name = $wpdb->prefix . 'wpsp-shippment';
//
//        $charset_collate = $wpdb->get_charset_collate();
//
//        $sql = "CREATE TABLE $table_name (
//		id mediumint(9) NOT NULL AUTO_INCREMENT,
//		status text
//		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
//		name tinytext NOT NULL,
//		text text NOT NULL,
//		url varchar(55) DEFAULT '' NOT NULL,
//		PRIMARY KEY  (id)
//	) $charset_collate;";
//
//        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
//        dbDelta($sql);
//
//        add_option('jal_db_version', $jal_db_version);
//    }
//
//    function jal_install_data()
//    {
//        global $wpdb;
//
//        $welcome_name = 'Mr. WordPress';
//        $welcome_text = 'Congratulations, you just completed the installation!';
//
//        $table_name = $wpdb->prefix . 'liveshoutbox';
//
//        $wpdb->insert(
//            $table_name,
//            array(
//                'time' => current_time('mysql'),
//                'name' => $welcome_name,
//                'text' => $welcome_text,
//            )
//        );
//    }
}