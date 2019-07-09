<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$priority_id      = isset($_POST['load_id']) ? intval(sanitize_text_field($_POST['load_id'])) : 0;
$nonce          = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $priority_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$wpdb->delete( $wpdb->prefix.'wpsp_custom_priority', array('id'=>$priority_id) );

do_action('wpsp_after_delete_priority', $priority_id);

$custom_priority_localize = get_option('wpsp_custom_priority_localize');
unset($custom_priority_localize['label_'.$priority_id]);
update_option('wpsp_custom_priority_localize', $custom_priority_localize);
