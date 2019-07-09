<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpsupportplus;
$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();

$ticket_id              = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : 0;
$reply_body             = isset($_POST['reply_body']) ? wp_kses_post($_POST['reply_body']) : '';
$nonce                  = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
$agent_reply_status     = $wpsupportplus->functions->get_agent_reply_status();
$customer_reply_status  = $wpsupportplus->functions->get_customer_reply_status();

$ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

/**
 * Check nonce
 */
if( !wp_verify_nonce( $nonce, $ticket_id ) || !$wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'post_reply' ) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

/**
 * Reply body should not be empty
 */
if( !$reply_body ){
    die('Reply body empty');
}

$time           = current_time('mysql', 1);
$guest_name     = $wpsp_user_session['name'];
$guest_email    = $wpsp_user_session['email'];

/**
 * Attachments for description
 */
$attachments = isset($_POST['desc_attachment']) && is_array($_POST['desc_attachment']) ? $_POST['desc_attachment'] : array();

foreach ( $attachments as $key => $attachment_id ){

    $attachment_id = intval(sanitize_text_field($attachment_id));
    if($attachment_id){
        $wpdb->update($wpdb->prefix . 'wpsp_attachments', array('active' => 1), array('id' => $attachment_id));
    } else {
        unset($attachments[$key]);
    }
}
$attachments = implode(',', $attachments);

/**
 * Insert thread to DB
 */
$signature = get_user_meta($current_user->ID,'wpsp_agent_signature',true);
if($signature){
	$signature='<br>---<br>' . stripcslashes(htmlspecialchars_decode($signature, ENT_QUOTES));
	$reply_body.= $signature;
}
$values = array(
    'ticket_id'         => $ticket_id,
    'body'              => htmlspecialchars($reply_body, ENT_QUOTES),
    'attachment_ids'    => $attachments,
    'create_time'       => $time,
    'created_by'        => $current_user->ID,
    'guest_name'        => $guest_name,
    'guest_email'       => $guest_email
);
$values = apply_filters('wpsp_reply_ticket_thread_values', $values);

$thread_id = $this->create_new_thread($values);

/**
 * Update ticket
 */
$values = array(
    'update_time' => $time,
		'updated_by'	=> $current_user->ID
);
$this->change_ticket_fields( $values, $ticket_id );

if($agent_reply_status != '' && $wpsupportplus->functions->is_staff($current_user) && $guest_email != $ticket->guest_email){
    $this->change_status($agent_reply_status, $ticket_id);
}

if($customer_reply_status != '' && $guest_email == $ticket->guest_email){
    $this->change_status($customer_reply_status, $ticket_id);
}

do_action( 'wpsp_sla_checkpoint', $ticket_id );
do_action( 'wpsp_after_ticket_reply', $ticket_id, $thread_id );

