<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();

$ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

$old_assigned = $ticket->assigned_to;
$new_assigned = $assigned_agents ? implode(',', $assigned_agents) : 0;

$time = current_time('mysql', 1);

/**
 * update ticket
 */
$values = array(
    'assigned_to' => $new_assigned,
		'updated_by'  => $current_user->ID,
    'update_time' => $time
);

$wpdb->update($wpdb->prefix . 'wpsp_ticket', $values, array('id' => $ticket_id));

/**
 * Insert log thread
 */
$values = array(
    'ticket_id'         => $ticket_id,
    'body'              => $new_assigned,
    'is_note'           => '2',
    'create_time'       => $time,
    'created_by'        => $current_user->ID,
    'guest_name'        => $current_user->display_name,
    'guest_email'       => $current_user->user_email
);
$wpdb->insert($wpdb->prefix . 'wpsp_ticket_thread', $values);

do_action('wpsp_sla_checkpoint',$ticket_id);
do_action( 'wpsp_after_change_assign_agent', $ticket_id, $old_assigned, $new_assigned );

