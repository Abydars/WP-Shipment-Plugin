<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Check nonce
 */
if( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce']) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$order = (int)$wpdb->get_var("select max(load_order) as current_order from {$wpdb->prefix}wpsp_custom_status");
$order++;

$status_name  = sanitize_text_field($_POST['status_name']);
$status_color = sanitize_text_field($_POST['color']);

$values = apply_filters('wpsp_set_add_status', array(
    'name'          => $status_name,
    'color'         => $status_color,
    'load_order'    => $order
));

$wpdb->insert( $wpdb->prefix.'wpsp_custom_status', $values );

do_action('wpsp_after_add_new_status', $wpdb->insert_id);

/**
 * Update translation option for custom status label in order to support WPML
 */

$status_id = $wpdb->insert_id;
$custom_status_localize = get_option('wpsp_custom_status_localize');
$custom_status_localize['label_'.$status_id] = $status_name;
update_option('wpsp_custom_status_localize', $custom_status_localize);