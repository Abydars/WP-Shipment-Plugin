<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb, $current_user;

/**
 * Check nonce
 */
if( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce']) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

/**
 * If filter is not having any elements in it abort
 */
$filter = isset($_POST['filter']) && is_array($_POST['filter']) ? $wpsupportplus->functions->sanitize_string_array($_POST['filter']) : array();
if( !isset($filter['elements']) ){
    die('No filter items');
}

/**
 * If current user is not part of support staff, abort
 */
if(!$wpsupportplus->functions->is_staff($current_user)){
    die('Only support person can do this');
}

/**
 * If filter type is set and current user not having permission to save private filters, abort
 */
$filter_type = isset($_POST['filter_type']) ? sanitize_text_field($_POST['filter_type']) : '';
if( $filter_type && !$wpsupportplus->functions->is_administrator($current_user)){
    die('Filter type not allowed');
} 

if(!$filter_type) {
    $filter_type = 'private';
}

$filter_name = isset($_POST['filter_name']) ? sanitize_text_field($_POST['filter_name']) : '';
if( !$filter_name ){
    die('Filter name not given');
}

/**
 * Now save filter according to type
 */
if( $filter_type == 'private' ){
    
    $private_filters = $wpsupportplus->functions->get_private_filters();
    
    $filter = array(
        'label'     => $filter_name,
        'filter'    => $filter
    );
    
    $private_filters[] = $filter;
    
    update_user_meta($current_user->ID, 'wpsp_private_ticket_filters', $private_filters);
    
    setcookie("wpsp_ticket_filters", base64_encode(json_encode($filter['filter'])), 0, COOKIEPATH);
    
} else {
    
    $public_filters = $wpsupportplus->functions->get_public_filters();
    
    $filter = array(
        'label'     => $filter_name,
        'filter'    => $filter
    );
    
    $public_filters[] = $filter;
    
    update_option('wpsp_public_ticket_filters',$public_filters);
    
    setcookie("wpsp_ticket_filters", base64_encode(json_encode($filter['filter'])), 0, COOKIEPATH);
    
}
