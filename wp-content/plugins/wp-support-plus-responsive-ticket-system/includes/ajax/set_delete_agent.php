<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$agent_id    = isset($_POST['load_id']) ? intval(sanitize_text_field($_POST['load_id'])) : 0;
$nonce       = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $agent_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$wpdb->delete( $wpdb->prefix.'wpsp_users', array('user_id'=>$agent_id) );

$user = get_userdata($agent_id);
    
$user->remove_cap('wpsp_agent');
$user->remove_cap('wpsp_supervisor');
$user->remove_cap('wpsp_administrator');

do_action('wpsp_after_delete_agent', $agent_id);
