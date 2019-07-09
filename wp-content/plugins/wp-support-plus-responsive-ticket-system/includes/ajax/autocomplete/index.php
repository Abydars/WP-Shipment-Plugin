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

$input_id   = isset($_REQUEST['input_id']) ? sanitize_text_field($_REQUEST['input_id']) : '';

switch ($input_id){
    
    case 'wpsp_auto_supervisor' : include WPSP_ABSPATH . 'includes/ajax/autocomplete/supervisor.php';
        break;
    
    case 'wp_user' : include WPSP_ABSPATH . 'includes/ajax/autocomplete/wp_user.php';
        break;
    
    case 'ticket_filter' : include WPSP_ABSPATH . 'includes/ajax/autocomplete/ticket_filter.php';
        break;
    
}
