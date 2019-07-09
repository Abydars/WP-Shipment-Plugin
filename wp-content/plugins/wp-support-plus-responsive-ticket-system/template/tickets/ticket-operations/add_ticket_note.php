<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpsupportplus;

$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();

$ticket_id          = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : 0;
$reply_body         = isset($_POST['reply_body']) ? wp_kses_post($_POST['reply_body']) : '';
$nonce              = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';

$ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

/**
 * Check nonce
 */
if( !wp_verify_nonce( $nonce, $ticket_id ) || !$wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'add_note' ) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

/**
 * Reply body should not be empty
 */
if( !$reply_body ){
    die('Note body empty');
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
$values = array(
    'ticket_id'         => $ticket_id,
    'body'              => htmlspecialchars($reply_body, ENT_QUOTES),
    'is_note'           => '1',
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
	'updated_by'  => $current_user->ID 
);
$this->change_ticket_fields( $values, $ticket_id );

do_action( 'wpsp_after_ticket_add_note', $ticket_id, $thread_id );
