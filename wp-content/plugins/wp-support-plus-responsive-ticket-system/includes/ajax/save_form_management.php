<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$sectionsid     = isset($_POST['load_order']) ? explode(',', sanitize_text_field($_POST['load_order'])) : array();
$order          = $wpsupportplus->functions->sanitize_integer_array($sectionsid);
$field_status   = isset($_POST['field_status']) && is_array($_POST['field_status']) ? $wpsupportplus->functions->sanitize_string_array($_POST['field_status']) : array();
$full_width     = isset($_POST['field_full_width']) && is_array($_POST['field_full_width']) ? $wpsupportplus->functions->sanitize_string_array($_POST['field_full_width']) : array();
$nonce          = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;


/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, 'form_management') ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$counter = 0;
foreach ($order as $field_id): 
    $values = apply_filters('wpsp_form_manage_setting_update',array(
        'load_order'    => ++$counter,
        'status'        => $field_status[$field_id],
        'full_width'    => $full_width[$field_id]
    ));
    $wpdb->update($wpdb->prefix . 'wpsp_ticket_form_order', $values, array('id' => $field_id));
endforeach;