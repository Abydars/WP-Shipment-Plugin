<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();

$time = current_time('mysql', 1);

/**
 * update ticket
 */
$values = array(
    'created_by'    => $user_id,
    'guest_name'    => $user_name,
    'guest_email'   => $user_email,
    'update_time'   => $time,
		'updated_by'		=> $current_user->ID
);
$wpdb->update($wpdb->prefix . 'wpsp_ticket', $values, array('id' => $ticket_id));

/**
 * Insert log thread
 */
$values = array(
    'ticket_id'         => $ticket_id,
    'body'              => $user_id,
    'is_note'           => '6',
    'create_time'       => $time,
    'created_by'        => $current_user->ID,
    'guest_name'        => $user_name,
    'guest_email'       => $user_email
);
$wpdb->insert($wpdb->prefix . 'wpsp_ticket_thread', $values);
do_action('wpsp_sla_checkpoint',$ticket_id);
do_action('wpsp_after_change_raised_by',$ticket_id);
