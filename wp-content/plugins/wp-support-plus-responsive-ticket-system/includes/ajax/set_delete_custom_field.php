<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$field_id      = isset($_POST['load_id']) ? intval(sanitize_text_field($_POST['load_id'])) : 0;
$nonce          = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $field_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$wpdb->delete( $wpdb->prefix.'wpsp_custom_fields', array('id'=>$field_id) );

$sql = "ALTER TABLE `{$wpdb->prefix}wpsp_ticket` DROP `cust".$field_id."`";
$wpdb->query($sql);

/**
 * Remove record from form management
 */
$form_manage_id = $wpdb->get_var("select id from {$wpdb->prefix}wpsp_ticket_form_order WHERE field_key=".$field_id);
if( $form_manage_id){
    
    $wpdb->delete( $wpdb->prefix.'wpsp_ticket_form_order', array('id'=>$form_manage_id) );
    
    $custom_fields_localize = get_option('wpsp_custom_fields_localize');
    unset($custom_fields_localize['label_'.$field_id]);
    update_option('wpsp_custom_fields_localize', $custom_fields_localize);
    
}

$wpdb->delete( $wpdb->prefix.'wpsp_ticket_list_order', array('field_key'=>$field_id) );

do_action('wpsp_after_delete_custom_field', $field_id);
