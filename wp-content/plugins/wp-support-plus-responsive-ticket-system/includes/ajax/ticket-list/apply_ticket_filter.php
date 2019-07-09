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

/**
 * visibility can be 0 so default should be something which can not be index like -1
 */
$visibility = isset($_REQUEST['visibility']) ? intval(sanitize_text_field($_REQUEST['visibility'])) : -1 ;
if( $visibility === -1 ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

if( $visibility === 0 ){
    
    if(isset($_COOKIE["wpsp_ticket_filters"])){
        unset($_COOKIE["wpsp_ticket_filters"]);
        setcookie("wpsp_ticket_filters", null, strtotime('-1 day'), COOKIEPATH);
    }
    
}

if( $visibility === 1 ){
    
    $public_filters = $wpsupportplus->functions->get_public_filters();
    setcookie("wpsp_ticket_filters", base64_encode(json_encode($public_filters[$id]['filter'])), 0, COOKIEPATH);
    
}

if( $visibility === 2 ){
    
    $private_filters = $wpsupportplus->functions->get_private_filters();
    setcookie("wpsp_ticket_filters", base64_encode(json_encode($private_filters[$id]['filter'])), 0, COOKIEPATH );
    
}

$wpsp_ticket_active_pre_value = get_option('wpsp_ticket_active_pre_value');
$wpsp_ticket_active_pre_value['ticket_active'] = 1;
update_option('wpsp_ticket_active_pre_value',$wpsp_ticket_active_pre_value );

echo 'success';
