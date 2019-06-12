<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check nonce
 */
if( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce']) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$email_piping = get_option('wpsp_email_pipe_settings');

$email_address = isset($_POST['email_address']) ? sanitize_text_field($_POST['email_address']) : '';

$gmail_connections  = get_option('wpsp_ep_gmail_connections');

if(isset($gmail_connections[$email_address])){

    unset($gmail_connections[$email_address]);
    update_option('wpsp_ep_gmail_connections',$gmail_connections);
    unset($email_piping['email_categories'][$email_address]);
    update_option('wpsp_email_pipe_settings',$email_piping);
}
