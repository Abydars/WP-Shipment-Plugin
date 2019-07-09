<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $current_user, $wpdb;

/**
 * Check nonce
 */
if( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce']) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$name       = isset($_REQUEST['guest_name']) ? sanitize_text_field($_REQUEST['guest_name']) : '';
$email      = isset($_REQUEST['guest_email']) ? sanitize_text_field($_REQUEST['guest_email']) : '';
$response   = array();

if( $name && $email ){
    
    $user = get_user_by( 'email' , $email );
    
    if($user){
				
        $response['success'] = false;
        $response['messege'] = '<p class="bg-danger wpsp_notice">'
                                    .__('This email address already registered as user. Please sign in above!', 'wp-support-plus-responsive-ticket-system')
                                .'</p>';
        
    } else {
        
				$wpsp_user_session = array(
            'type'  => 0,
            'name'  => $name,
            'email' => $email
        );
        @setcookie("wpsp_user_session", base64_encode(json_encode($wpsp_user_session)), 0, COOKIEPATH);
    
        $response['success'] = true;
        $response['messege'] = '<p class="bg-success wpsp_notice">'.__('Success!', 'wp-support-plus-responsive-ticket-system').'</p>';
    }
    
} else {
		$response['success'] = false;
    $response['messege'] = __('Cheating huh?', 'wp-support-plus-responsive-ticket-system');
}

$_REQUEST['wpsp_guest_signin_response'] = $response;
