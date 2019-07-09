<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Check nonce
 */
if( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce']) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpdb;

$cat_id = isset($_REQUEST['cat_id']) ? intval(sanitize_text_field($_REQUEST['cat_id'])) : 0;

$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_fields");

$cust_field_ids = array();

foreach ( $results as $field ){
    
    $categories = explode( ',' , $field->field_categories);
    if( in_array( $cat_id, $categories ) ){
        $cust_field_ids[] = $field->id;
    }
}

$response = array(
    'keys' => $cust_field_ids
);

echo json_encode($response);
