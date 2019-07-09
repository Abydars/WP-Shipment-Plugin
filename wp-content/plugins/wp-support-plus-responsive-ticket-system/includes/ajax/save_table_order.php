<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$table = isset($_POST['table']) ? sanitize_text_field($_POST['table']) : '';
$order = isset($_POST['order']) && is_array($_POST['order']) ? $wpsupportplus->functions->sanitize_integer_array($_POST['order']) : array();
$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $table) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$action_flag = false;

switch ($table){
    
    case 'category':
        $counter = 0;
        foreach ($order as $category_id): 
            $values = array('load_order' => ++$counter);
            $wpdb->update($wpdb->prefix . 'wpsp_catagories', $values, array('id' => $category_id));
        endforeach;
        $action_flag = true;
        break;
        
    case 'status':
        $counter = 0;
        foreach ($order as $status_id): 
            $values = array('load_order' => ++$counter);
            $wpdb->update($wpdb->prefix . 'wpsp_custom_status', $values, array('id' => $status_id));
        endforeach;
        $action_flag = true;
        break;
        
    case 'priority':
        $counter = 0;
        foreach ($order as $priority_id): 
            $values = array('load_order' => ++$counter);
            $wpdb->update($wpdb->prefix . 'wpsp_custom_priority', $values, array('id' => $priority_id));
        endforeach;
        $action_flag = true;
        break;
        
}

if($action_flag){
    _e('Order saved successfully!','wp-support-plus-responsive-ticket-system');
} else {
    _e('Error saving order!','wp-support-plus-responsive-ticket-system');
}
