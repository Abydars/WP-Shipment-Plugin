<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$license_key = get_option( 'wpsp_license_key_emailpipe' );
if ( $license_key === false ) {
  $license_key = '';
}

$messege = '';
$class   = '';
if ( $license_key == '') {
    $messege = 'To receive updates, please enter your valid license key.';
} else {
    
  $api_params = array(
      'edd_action' => 'check_license',
      'license'    => $license_key,
      'item_id'    => WPSP_EP_STRORE_ID,
      'url'        => site_url()
  );
  $response = wp_remote_post( WPSP_STORE_URL, array( 'body' => $api_params, 'timeout' => 15, 'sslverify' => false ) );
  if ( !is_wp_error( $response ) ) {
      
      $license_data = json_decode( wp_remote_retrieve_body( $response ) );
      
      if ( $license_data->license == 'valid' ) {
          
        $expiry_date = '';
        if ($license_data->expires == 'lifetime') {
          $messege = 'Your license key is active for Lifetime.';
        } else {
          $expiry_date = new DateTime($license_data->expires);
          $expiry_date = $expiry_date->format('F d, Y');
          $messege = 'Your license key expires on '.$expiry_date;
        }
          
      } else if ( $license_data->license == 'expired' ) {
        
        $expiry_date = new DateTime($license_data->expires);
        $expiry_date = $expiry_date->format('F d, Y');
        
        $messege = 'Your license key expired on '.$expiry_date.' You can renew it from <a href="https://www.wpsupportplus.com/account/" target="_blank">your account</a>.';
        $class   = 'error_messege';
        
      } else {
        
        $messege = 'Invalid license key. Please check license key by login to <a href="https://www.wpsupportplus.com/account/" target="_blank">your account</a>.';
        $class   = 'warning_messege';
        
      }
      
  }
    
}

?>
<tr>
  <th scope="row"><?php _e('Email Piping', 'wpsp_emailpipe')?></th>
  <td>
    <input type="text" class="regular-text" name="wpsp_addon_license[email_pipig]" value="<?php echo $license_key?>">
    <div class="wpsp_addon_license_messege_container">
      <p class="<?php echo $class;?>"><?php echo $messege;?></p>
    </div>
  </td>
</tr>