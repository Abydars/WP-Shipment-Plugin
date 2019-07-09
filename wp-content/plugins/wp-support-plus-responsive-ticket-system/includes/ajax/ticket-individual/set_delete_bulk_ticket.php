<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;
include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';
$ticket_oprations = new WPSP_Ticket_Operations();

$ticket_id_data  = isset($_POST['ticket_id']) ? (sanitize_text_field($_POST['ticket_id'])) : '' ;
$ticket_ids = explode(',', $ticket_id_data);
$nonce      = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '' ;


/**
 * Check nonce
 */
if( !wp_verify_nonce( $nonce ) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}
$values = array( 'active' => 0 );
foreach ($ticket_ids as $ticket_id){
    $ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );
    
    if( $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'delete_ticket' ) ){
        $ticket_oprations->change_ticket_fields( $values, $ticket_id );
    }
}
