<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
global $wpdb, $wpsupportplus;
$ticket_id  = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : 0 ;
$nonce      = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '' ;
$wpsp_settings_general = get_option('wpsp_settings_general');

$ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

/**
 * Check nonce
 */
if( !wp_verify_nonce( $nonce, $ticket_id ) || !$wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'close_ticket' ) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';
$status_id = $wpsp_settings_general['close_ticket_status'];
$ticket_oprations = new WPSP_Ticket_Operations();
if($status_id != ''){
    $ticket_oprations->change_status($status_id, $ticket_id);
}

do_action('wpsp_close_ticket',$ticket);