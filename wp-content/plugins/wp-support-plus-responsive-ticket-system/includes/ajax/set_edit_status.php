<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$status_id    = isset($_POST['load_id']) ? intval(sanitize_text_field($_POST['load_id'])) : 0;
$status_name  = isset($_POST['status_name']) ? sanitize_text_field($_POST['status_name']) : '';
$status_color = isset($_POST['color']) ? sanitize_text_field($_POST['color']) : '';
$nonce        = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $status_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$values = apply_filters('wpsp_set_edit_status', array(
    'name'          => $status_name,
    'color'         => $status_color
));

$wpdb->update( $wpdb->prefix.'wpsp_custom_status', $values, array('id'=>$status_id) );

do_action('wpsp_after_edit_status', $status_id);

/**
 * Update translation option for custom status label in order to support WPML
 */

$custom_status_localize = get_option('wpsp_custom_status_localize');
$custom_status_localize['label_'.$status_id] = $status_name;
update_option('wpsp_custom_status_localize', $custom_status_localize);