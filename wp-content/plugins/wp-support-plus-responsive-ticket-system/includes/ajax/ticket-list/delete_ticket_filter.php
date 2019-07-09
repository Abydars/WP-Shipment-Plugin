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
 * id can be 0 so default should be something which can not be index like -1
 */
$id = isset($_REQUEST['id']) ? intval(sanitize_text_field($_REQUEST['id'])) : -1 ;
if( $id === -1 ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$visibility = isset($_REQUEST['visibility']) ? intval(sanitize_text_field($_REQUEST['visibility'])) : 0 ;
if( $visibility === 0 ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

/**
 * If current user is not part of support staff, abort
 */
if( $visibility === 1 && !$wpsupportplus->functions->is_staff($current_user)){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

if( $visibility === 1 ){
    
    $public_filters = $wpsupportplus->functions->get_public_filters();
    
    unset( $public_filters[$id] );
    
    update_option('wpsp_public_ticket_filters',$public_filters);
    
}

if( $visibility === 2 ){
    
    $private_filters = $wpsupportplus->functions->get_private_filters();
    
    unset( $private_filters[$id] );
    
    update_user_meta($current_user->ID, 'wpsp_private_ticket_filters', $private_filters);
    
}
