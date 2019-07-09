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

$order = (int)$wpdb->get_var("select max(load_order) as current_order from {$wpdb->prefix}wpsp_catagories");
$order++;

$category_name  = sanitize_text_field($_POST['cat_name']);
$supervisors    = isset($_REQUEST['supervisors']) && is_array($_REQUEST['supervisors']) ? $wpsupportplus->functions->sanitize_integer_array($_REQUEST['supervisors']) : array();

$values = apply_filters('wpsp_set_add_category', array(
    'name'          => $category_name,
    'supervisor'    => serialize($supervisors),
    'load_order'    => $order
));

$wpdb->insert( $wpdb->prefix.'wpsp_catagories', $values );
$insert_id = $wpdb->insert_id;

/**
 * Assign supervisors capability for this category
 */
foreach ($supervisors as $supervisor_id){
    $user = get_userdata($supervisor_id);
    $user->add_cap('wpsp_cat_'.$insert_id);
}

do_action('wpsp_after_add_new_category', $insert_id);

/**
 * Update translation option for custom category label in order to support WPML
 */

$custom_category_localize = get_option('wpsp_custom_category_localize');
$custom_category_localize['label_'.$insert_id] = $category_name;
update_option('wpsp_custom_category_localize', $custom_category_localize);