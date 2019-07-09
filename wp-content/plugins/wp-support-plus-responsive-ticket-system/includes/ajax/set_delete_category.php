<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$category_id    = isset($_POST['load_id']) ? intval(sanitize_text_field($_POST['load_id'])) : 0;
$nonce          = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $category_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

/**
 * Reset supervisor capabilities for existing supervisors
 */
$existing_supervisors = $wpdb->get_var("select supervisor from {$wpdb->prefix}wpsp_catagories where id=".$category_id);
$existing_supervisors = $existing_supervisors ? unserialize($existing_supervisors) : array();
foreach ($existing_supervisors as $supervisor){
    $user = get_userdata($supervisor);
    $user->remove_cap('wpsp_cat_'.$category_id);
}

$wpdb->delete( $wpdb->prefix.'wpsp_catagories', array('id'=>$category_id) );

do_action('wpsp_after_delete_category', $category_id);

$custom_category_localize = get_option('wpsp_custom_category_localize');
unset($custom_category_localize['label_'.$category_id]);
update_option('wpsp_custom_category_localize', $custom_category_localize);