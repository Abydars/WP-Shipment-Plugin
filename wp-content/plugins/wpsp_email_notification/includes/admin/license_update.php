<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$license_key = get_option( 'wpsp_license_key_email_notification' );
if ( $license_key === false ) {
  $license_key = '';
}

$updated_license_key =  isset($_REQUEST['wpsp_addon_license']) && isset($_REQUEST['wpsp_addon_license']['en']) ? sanitize_text_field(trim($_REQUEST['wpsp_addon_license']['en'])) : '';

if ( $license_key != $updated_license_key && $updated_license_key ) {
  
    $api_params = array(
        'edd_action' => 'activate_license',
        'license'    => $updated_license_key,
        'item_id'    => WPSP_EN_STORE_ID,
        'url'        => site_url()
    );
    $response = wp_remote_post( WPSP_STORE_URL, array( 'body' => $api_params, 'timeout' => 15, 'sslverify' => false ) );
    $license_data = json_decode( wp_remote_retrieve_body( $response ) );
  
}

if ( $license_key != $updated_license_key && !$updated_license_key ) {
    
  $api_params = array(
      'edd_action' => 'deactivate_license',
      'license'    => $license_key,
      'item_id'    => WPSP_EN_STORE_ID,
      'url'        => site_url()
  );
  $response = wp_remote_post( WPSP_STORE_URL, array( 'body' => $api_params, 'timeout' => 15, 'sslverify' => false ) );
  $license_data = json_decode( wp_remote_retrieve_body( $response ) );
    
}

update_option( 'wpsp_license_key_email_notification', $updated_license_key );
