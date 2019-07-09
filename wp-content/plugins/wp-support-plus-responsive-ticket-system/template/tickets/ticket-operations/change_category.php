<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();

$time           = current_time('mysql', 1);
$guest_name     = $wpsp_user_session['name'];
$guest_email    = $wpsp_user_session['email'];

$values = array(
    'cat_id'        => $cat_id,
    'update_time'   => $time,
		'updated_by'		=> $current_user->ID
);
$wpdb->update($wpdb->prefix . 'wpsp_ticket', $values, array('id' => $ticket_id));

$values = array(
    'ticket_id'         => $ticket_id,
    'body'              => $cat_id,
    'is_note'           => '4',
    'create_time'       => $time,
    'created_by'        => $current_user->ID,
    'guest_name'        => $guest_name,
    'guest_email'       => $guest_email
);
$wpdb->insert($wpdb->prefix . 'wpsp_ticket_thread', $values);

do_action('wpsp_sla_checkpoint',$ticket_id);
do_action('wpsp_after_change_ticket_category',$ticket_id);
