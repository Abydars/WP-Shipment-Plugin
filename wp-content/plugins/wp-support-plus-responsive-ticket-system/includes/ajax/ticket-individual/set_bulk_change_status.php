<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;
include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';

$ticket_oprations = new WPSP_Ticket_Operations();

$ticket_id_data  = isset($_POST['ticket_id']) ? sanitize_text_field($_POST['ticket_id']) : '' ;
$ticket_ids = explode(',', $ticket_id_data);
$nonce      = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '' ;

/**
 * Check nonce
 */
if( !wp_verify_nonce( $nonce ) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$cat_id         = isset($_POST['category']) ? intval(sanitize_text_field($_POST['category'])) : 0 ;
$priority_id    = isset($_POST['priority']) ? intval(sanitize_text_field($_POST['priority'])) : 0 ;
$status_id      = isset($_POST['status']) ? intval(sanitize_text_field($_POST['status'])) : 0 ;

foreach ($ticket_ids as $ticket_id){
    $ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );
    $check_status_cap=$wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'change_status' );
    
    if($status_id!=0 && $check_status_cap ){
    $ticket_oprations->change_status( $status_id, $ticket_id );
		}
    if($priority_id!=0 && $check_status_cap ){
    $ticket_oprations->change_priority( $priority_id, $ticket_id );
    }
    if($cat_id!=0 && $check_status_cap ){
    $ticket_oprations->change_category( $cat_id, $ticket_id );
    }
		
}