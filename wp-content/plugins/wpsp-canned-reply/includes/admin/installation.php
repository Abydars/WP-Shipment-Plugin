<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb;
/*
 * Installation part start below
 */

$customFields=$wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsp_custom_fields" );

if( get_option( 'wpsp_canned_reply_template' ) === false ) {
    $templates=array(
                    'reply_by_name' => __("Reply By Name", 'wp-support-plus-responsive-ticket-system' ),
                    'reply_by_email' => __("Reply By Email", 'wp-support-plus-responsive-ticket-system' ),
                    'ticket_status' => __("Ticket Status", 'wp-support-plus-responsive-ticket-system' ),
                    'customer_name' => __("Customer Name", 'wp-support-plus-responsive-ticket-system' ).__("(ticket creator)", 'wp-support-plus-responsive-ticket-system' ),
                    'customer_email' => __("Customer Email", 'wp-support-plus-responsive-ticket-system' ).__("(ticket creator)", 'wp-support-plus-responsive-ticket-system' ),
                    'ticket_id' => __("Ticket ID", 'wp-support-plus-responsive-ticket-system' ),
                    'ticket_subject' => __("Ticket Subject", 'wp-support-plus-responsive-ticket-system' ),
                    'ticket_category' => __("Ticket Category", 'wp-support-plus-responsive-ticket-system' ),
                    'ticket_priotity' => __("Ticket Priority", 'wp-support-plus-responsive-ticket-system' )
    );
    foreach($customFields as $field){
        $templates['cust'.$field->id]=$field->label;
    }
    $wpsp_canned_reply_template=array(
        'templates'=>$templates
    );    
    update_option('wpsp_canned_reply_template',$wpsp_canned_reply_template);
}

?>