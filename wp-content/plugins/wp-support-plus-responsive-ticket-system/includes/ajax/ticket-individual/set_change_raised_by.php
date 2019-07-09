<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$ticket_id  = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : 0 ;
$nonce      = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '' ;

$ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

/**
 * Check nonce
 */
if( !wp_verify_nonce( $nonce, $ticket_id ) || !$wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'change_raised_by' ) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$user_id      = isset($_POST['user_id']) ? intval(sanitize_text_field($_POST['user_id'])) : 0 ;
$user_name    = isset($_POST['guest_name']) ? sanitize_text_field($_POST['guest_name']) : '' ;
$user_email   = isset($_POST['guest_email']) ? sanitize_text_field($_POST['guest_email']) : '' ;

/**
 * Check if user is guest and given email is belong to registered user.
 * If email belong to registered user, treat this user as registered user and use user name and email
 */
if( !$user_id ){
    $user = get_user_by( 'email', $user_email );
    if($user){
        $user_id = $user->ID;
    }
}

if( $user_id ){
    $user = get_userdata($user_id);
    $user_name  = $user->display_name;
    $user_email = $user->user_email;
}

include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';

$ticket_oprations = new WPSP_Ticket_Operations();

$ticket_oprations->change_raised_by( $user_id, $user_name, $user_email, $ticket_id );
