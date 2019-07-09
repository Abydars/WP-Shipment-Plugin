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


$agents  = isset($_POST['assigned_agents']) && is_array($_POST['assigned_agents']) ? $_POST['assigned_agents'] : array() ;

$assigned_agents = array();
foreach( $agents as $agent ){
    
    $agent = intval(sanitize_text_field($agent)) ? intval(sanitize_text_field($agent)) : 0;
    if ($agent){
        $assigned_agents[] = $agent;
    }
    
}

foreach ($ticket_ids as $ticket_id){
    $ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );
    
    if( $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'change_assign_agent' )){
        $ticket_oprations->change_assign_agent( $assigned_agents, $ticket_id );
    }
}

