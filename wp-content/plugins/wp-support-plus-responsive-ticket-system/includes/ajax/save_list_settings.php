<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$sectionsid         = isset($_POST['load_order']) ? explode(',', sanitize_text_field($_POST['load_order'])) : array();
$order              = $wpsupportplus->functions->sanitize_integer_array($sectionsid);
$customer_visible   = isset($_POST['customer_visible']) && is_array($_POST['customer_visible']) ? $wpsupportplus->functions->sanitize_string_array($_POST['customer_visible']) : array();
$agent_visible      = isset($_POST['agent_visible']) && is_array($_POST['agent_visible']) ? $wpsupportplus->functions->sanitize_string_array($_POST['agent_visible']) : array();
$customer_filter    = isset($_POST['customer_filter']) && is_array($_POST['customer_filter']) ? $wpsupportplus->functions->sanitize_string_array($_POST['customer_filter']) : array();
$agent_filter       = isset($_POST['agent_filter']) && is_array($_POST['agent_filter']) ? $wpsupportplus->functions->sanitize_string_array($_POST['agent_filter']) : array();
$customer_ticket    = isset($_POST['customer_ticket']) && is_array($_POST['customer_ticket']) ? $wpsupportplus->functions->sanitize_string_array($_POST['customer_ticket']) : array();
$nonce              = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;


/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, 'list_settings') ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$counter = 0;
$result = $wpdb->get_var("select id from {$wpdb->prefix}wpsp_ticket_list_order WHERE field_key = 'deleted_ticket'");

foreach ($order as $field_id): 
    $values = apply_filters('wpsp_form_manage_setting_update',array(
        'load_order'        => ++$counter,
        'customer_visible'  => isset($customer_visible[$field_id]) ? $customer_visible[$field_id] : 0,
        'agent_visible'     => isset($agent_visible[$field_id]) ? $agent_visible[$field_id] : 0,
        'customer_filter'   => isset($customer_filter[$field_id]) ? $customer_filter[$field_id] : 0,
        'agent_filter'      => isset($agent_filter[$field_id]) ? $agent_filter[$field_id] : 0,
				'customer_ticket'   => isset($customer_ticket[$field_id]) ? $customer_ticket[$field_id] : 0
    ));
		
		if($field_id == $result){
			$values['agent_filter'] = 1;
		}
    $wpdb->update($wpdb->prefix . 'wpsp_ticket_list_order', $values, array('id' => $field_id));
endforeach;