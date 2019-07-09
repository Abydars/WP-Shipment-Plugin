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

$username = isset($_REQUEST['username']) ? sanitize_text_field($_REQUEST['username']) : '';
$password = isset($_REQUEST['password']) ? sanitize_text_field($_REQUEST['password']) : '';
$remember = isset($_REQUEST['remember']) ? true : false;
$response = array();

if( $username && $password ){
    
    $creds = array(
        'user_login'        => $username,
        'user_password'     => $password,
        'remember'          => true
    );

    $user = wp_signon($creds);

    if (is_wp_error($user)) {
        $response['success'] = false;
        $response['messege'] = '<p class="bg-danger wpsp_notice">'.$user->get_error_message().'</p>';
    } else {
        
				$wpsp_user_session = array(
						'type'  => 1,
						'name'  => $user->display_name,
						'email' => $user->user_email
				);
				@setcookie("wpsp_user_session", base64_encode(json_encode($wpsp_user_session)), 0, COOKIEPATH);
				
				$response['success'] = true;
        $response['messege'] = '<p class="bg-success wpsp_notice">'.__('Success!', 'wp-support-plus-responsive-ticket-system').'</p>';
    }
    
} else {
    $response['success'] = false;
    $response['messege'] = __('Cheating huh?', 'wp-support-plus-responsive-ticket-system');
}

$_REQUEST['wpsp_signin_response'] = $response;
