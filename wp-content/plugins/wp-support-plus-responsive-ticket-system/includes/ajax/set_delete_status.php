<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$status_id      = isset($_POST['load_id']) ? intval(sanitize_text_field($_POST['load_id'])) : 0;
$nonce          = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $status_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$wpdb->delete( $wpdb->prefix.'wpsp_custom_status', array('id'=>$status_id) );

do_action('wpsp_after_delete_status', $status_id);

$custom_status_localize = get_option('wpsp_custom_status_localize');
unset($custom_status_localize['label_'.$status_id]);
update_option('wpsp_custom_status_localize', $custom_status_localize);