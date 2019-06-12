<?php

class ShipmentActions{

    function save_label(){
        if ( ! isset( $_POST['create_label_form'] ) || ! wp_verify_nonce( $_POST['create_label_form'], 'create_label' )
        ) {
            echo 'Sorry, your nonce did not verify.';
            exit;
        } else {
            var_dump($_POST);die;
        }
    }

    function jal_install()
    {
        global $wpdb;
        global $jal_db_version;

        $table_name = $wpdb->prefix . 'wpsp-shippment';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		status text 
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name tinytext NOT NULL,
		text text NOT NULL,
		url varchar(55) DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option('jal_db_version', $jal_db_version);
    }

    function jal_install_data()
    {
        global $wpdb;

        $welcome_name = 'Mr. WordPress';
        $welcome_text = 'Congratulations, you just completed the installation!';

        $table_name = $wpdb->prefix . 'liveshoutbox';

        $wpdb->insert(
            $table_name,
            array(
                'time' => current_time('mysql'),
                'name' => $welcome_name,
                'text' => $welcome_text,
            )
        );
    }

    function save_from_address(){
        if ( ! isset( $_POST['create_from_address'] ) || ! wp_verify_nonce( $_POST['create_from_address'], 'from_address' )
        ) {
            echo 'Sorry, your nonce did not verify.';
            exit;
        } else {
            var_dump($_POST);die;
        }
    }

    function save_to_address(){
        if ( ! isset( $_POST['create_to_address'] ) || ! wp_verify_nonce( $_POST['create_to_address'], 'to_address' )
        ) {
            echo 'Sorry, your nonce did not verify.';
            exit;
        } else {
            var_dump($_POST);die;
        }
    }

    function shipment_details(){
        include ('templates/shipment-details.php');
    }
}