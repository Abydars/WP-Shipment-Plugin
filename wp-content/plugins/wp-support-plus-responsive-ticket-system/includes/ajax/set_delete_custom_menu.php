<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$cm_id = isset($_POST['load_id']) ? intval(sanitize_text_field($_POST['load_id'])) : 0;
$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $cm_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$wpdb->delete( $wpdb->prefix.'wpsp_panel_custom_menu', array('id'=>$cm_id) );

do_action('wpsp_after_delete_custom_menu', $cm_id);