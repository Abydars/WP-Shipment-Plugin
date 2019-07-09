<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();

$time           = current_time('mysql', 1);
$user_id        = $current_user->ID ? $current_user->ID : $user_id;
$guest_name     = isset($wpsp_user_session['name']) ? $wpsp_user_session['name'] : $guest_name;
$guest_email    = isset($wpsp_user_session['email']) ? $wpsp_user_session['email'] : $guest_email;

$values = array(
    'status_id'   => $status_id,
		'updated_by'	=> $current_user->ID,
    'update_time' => $time
);
$wpdb->update($wpdb->prefix . 'wpsp_ticket', $values, array('id' => $ticket_id));

$values = array(
    'ticket_id'         => $ticket_id,
    'body'              => $status_id,
    'is_note'           => '3',
    'create_time'       => $time,
    'created_by'        => $current_user->ID,
    'guest_name'        => $guest_name,
    'guest_email'       => $guest_email
);
$wpdb->insert($wpdb->prefix . 'wpsp_ticket_thread', $values);
do_action('wpsp_sla_checkpoint',$ticket_id);
do_action('wpsp_after_change_ticket_status',$ticket_id);


