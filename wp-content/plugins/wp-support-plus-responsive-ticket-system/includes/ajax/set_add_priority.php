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

$order = (int)$wpdb->get_var("select max(load_order) as current_order from {$wpdb->prefix}wpsp_custom_priority");
$order++;

$priority_name  = sanitize_text_field($_POST['priority_name']);
$priority_color = sanitize_text_field($_POST['color']);

$values = apply_filters('wpsp_set_add_priority', array(
    'name'          => $priority_name,
    'color'         => $priority_color,
    'load_order'    => $order
));

$wpdb->insert( $wpdb->prefix.'wpsp_custom_priority', $values );

do_action('wpsp_after_add_new_priority', $wpdb->insert_id);

/**
 * Update translation option for custom priority label in order to support WPML
 */

$insert_id = $wpdb->insert_id;
$custom_priority_localize = get_option('wpsp_custom_priority_localize');
$custom_priority_localize['label_'.$insert_id] = $priority_name;
update_option('wpsp_custom_priority_localize', $custom_priority_localize);