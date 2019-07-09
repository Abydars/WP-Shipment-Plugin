<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$category_id    = isset($_POST['load_id']) ? intval(sanitize_text_field($_POST['load_id'])) : 0;
$category_name  = isset($_POST['cat_name']) ? sanitize_text_field($_POST['cat_name']) : '';
$supervisors    = isset($_REQUEST['supervisors']) && is_array($_REQUEST['supervisors']) ? $wpsupportplus->functions->sanitize_integer_array($_REQUEST['supervisors']) : array();
$nonce          = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $category_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

/**
 * Reset supervisor capabilities for existing supervisors
 */
$existing_supervisors = $wpdb->get_var("select supervisor from {$wpdb->prefix}wpsp_catagories where id=".$category_id);
$existing_supervisors = $existing_supervisors ? unserialize($existing_supervisors) : array();
foreach ($existing_supervisors as $supervisor){
    $user = get_userdata($supervisor);
    $user->remove_cap('wpsp_cat_'.$category_id);
}

/**
 * Add capability to selected supervisors
 */
foreach ($supervisors as $supervisor_id){
    $user = get_userdata($supervisor_id);
    $user->add_cap('wpsp_cat_'.$category_id);
}

$values = apply_filters('wpsp_set_edit_category', array(
    'name'          => $category_name,
    'supervisor'    => serialize($supervisors)
));

$wpdb->update( $wpdb->prefix.'wpsp_catagories', $values, array('id'=>$category_id) );

do_action('wpsp_after_edit_category', $category_id);

/**
 * Update translation option for custom category label in order to support WPML
 */

$custom_category_localize = get_option('wpsp_custom_category_localize');
$custom_category_localize['label_'.$category_id] = $category_name;
update_option('wpsp_custom_category_localize', $custom_category_localize);