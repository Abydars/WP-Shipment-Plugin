<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$ticket_id  = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : 0 ;
$thread_id  = isset($_POST['thread_id']) ? intval(sanitize_text_field($_POST['thread_id'])) : 0 ;
$thread_body= isset($_POST['body']) ? wp_kses_post($_POST['body']) : '';
$nonce      = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '' ;

$ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

/**
 * Check nonce
 */
if( !wp_verify_nonce( $nonce, $ticket_id ) || !$thread_id || !$wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'edit_thread' ) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$values = array( 'body' => htmlspecialchars($thread_body, ENT_QUOTES));


include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';

$ticket_oprations = new WPSP_Ticket_Operations();

$ticket_oprations->change_thread_fields( $values, $thread_id );
