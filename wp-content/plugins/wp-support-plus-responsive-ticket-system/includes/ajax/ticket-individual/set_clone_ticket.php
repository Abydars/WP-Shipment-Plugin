<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$ticket_id  = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : 0 ;
$subject    = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
$nonce      = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '' ;

$ticket = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wpsp_ticket WHERE id=".$ticket_id );

/**
 * Check nonce
 */
if( !wp_verify_nonce( $nonce, $ticket_id ) || !$wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'clone_ticket' ) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';

$ticket_oprations = new WPSP_Ticket_Operations();

$values = array(
    
    'subject'       => $subject,
    'created_by'    => $ticket->created_by,
    'updated_by'    => $ticket->updated_by,
    'guest_name'    => $ticket->guest_name,
    'guest_email'   => $ticket->guest_email,
    'status_id'     => $ticket->status_id,
    'cat_id'        => $ticket->cat_id,
    'priority_id'   => $ticket->priority_id,
    'type'          => $ticket->type,
    'agent_created' => $ticket->agent_created,
    'create_time'   => $ticket->create_time,
    'update_time'   => $ticket->update_time,
    'assigned_to'   => $ticket->assigned_to,
    'active'        => $ticket->active,
    
);

$sql = "SELECT id from {$wpdb->prefix}wpsp_custom_fields";
$custom_fields = $wpdb->get_results($sql);

foreach ( $custom_fields as $field ){
    
    $col_name = 'cust'.$field->id;
    $values[$col_name] = $ticket->$col_name;
}

if( !$wpsupportplus->functions->get_ticket_id_sequence() ){

    $id = 0;
    do {
        $id = rand(1111111111, 9999999999);
        $sql = "select id from {$wpdb->prefix}wpsp_ticket where id=" . $id;
        $result = $wpdb->get_var($sql);
    } while ($result);

    $values['id'] = $id;
}

$values = apply_filters( 'wpsp_clone_ticket_values', $values );

$clone_id = $ticket_oprations->create_new_ticket($values);

$ticket_threads = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_ticket_thread where ticket_id=".$ticket_id." order by create_time asc");

foreach ( $ticket_threads as $thread ){
    
    $values = array(
        'ticket_id'         => $clone_id,
        'body'              => $thread->body,
        'attachment_ids'    => $thread->attachment_ids,
        'create_time'       => $thread->create_time,
        'created_by'        => $thread->created_by,
        'guest_name'        => $thread->guest_name,
        'guest_email'       => $thread->guest_email,
        'is_note'           => $thread->is_note,
    );
    
    $values = apply_filters('wpsp_clone_ticket_thread_values', $values);
    
    $ticket_oprations->create_new_thread($values);
}

echo json_encode( array( 'clone_id' => $clone_id ) );
