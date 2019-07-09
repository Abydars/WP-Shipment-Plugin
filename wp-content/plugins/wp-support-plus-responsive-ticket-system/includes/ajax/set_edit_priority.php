<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$priority_id    = isset($_POST['load_id']) ? intval(sanitize_text_field($_POST['load_id'])) : 0;
$priority_name  = isset($_POST['priority_name']) ? sanitize_text_field($_POST['priority_name']) : '';
$priority_color = isset($_POST['color']) ? sanitize_text_field($_POST['color']) : '';
$nonce          = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $priority_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$values = apply_filters('wpsp_set_edit_priority', array(
    'name'          => $priority_name,
    'color'         => $priority_color
));

$wpdb->update( $wpdb->prefix.'wpsp_custom_priority', $values, array('id'=>$priority_id) );

do_action('wpsp_after_edit_priority', $priority_id);

/**
 * Update translation option for custom priority label in order to support WPML
 */

$custom_priority_localize = get_option('wpsp_custom_priority_localize');
$custom_priority_localize['label_'.$priority_id] = $priority_name;
update_option('wpsp_custom_priority_localize', $custom_priority_localize);