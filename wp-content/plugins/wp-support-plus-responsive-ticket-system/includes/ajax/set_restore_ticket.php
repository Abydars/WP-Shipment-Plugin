<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$ticket_id = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : '';
$nonce     = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $ticket_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpdb;

$values = array(
    'active' => 1
);

$wpdb->update($wpdb->prefix . 'wpsp_ticket', $values, array('id' => $ticket_id));