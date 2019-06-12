<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$email_piping = get_option('wpsp_email_pipe_settings');

$connection =  isset($_REQUEST['wpsp_imap_connection']) && is_array($_REQUEST['wpsp_imap_connection']) ? $_REQUEST['wpsp_imap_connection'] : array();

$nonce =  isset($_REQUEST['nonce']) ? sanitize_text_field($_REQUEST['nonce']) : '';
if( !$nonce || !wp_verify_nonce($nonce) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$flag = true;

$response = array(
  'is_error' => 0,
  'msg'=> '<span style="color:green;">'.__('Connection Successfull!', 'wpsp_emailpipe').'</span>'
);

if( $flag && ( !extension_loaded('imap') || !extension_loaded('fileinfo') ) ){
  $response = array(
    'is_error' => 1,
    'msg'=> '<span style="color:red;">'.__('php-imap or php-fileinfo module not enabled for your server. Please contact to your host provider.', 'wpsp_emailpipe').'</span>'
  );
  $flag = false;
}

$user = get_user_by( 'email', $connection['email'] );
if( $flag && $user ){
  $response = array(
    'is_error' => 1,
    'msg'=> '<span style="color:red;">'.__('Email account belong to registered user can not be piped.', 'wpsp_emailpipe').'</span>'
  );
  $flag = false;
}

$conn = false;
if ($flag) {
  $imap_encryption = $connection['encryption']=='none' ? '/novalidate-cert':'/imap/ssl/novalidate-cert';
  $imap_encryption = apply_filters('wpsp_ep_imap_encryption',$imap_encryption);
  $imap_server     = $connection['mail_server'];
  $imap_port       = $connection['server_port'];
  $imap_username   = $connection['username'];
  $imap_password   = $connection['password'];
  $conn   = imap_open('{'.$imap_server.':'.$imap_port.$imap_encryption.'}INBOX', $imap_username, $imap_password);
  if (!$conn) {
    $response = array(
      'is_error' => 1,
      'msg'=> '<span style="color:red;">'.imap_last_error().'</span>'
    );
    $flag = false;
  }
}

if( $flag && $conn ){

  $uids     = imap_search($conn, 'ALL', SE_UID);
  $last_uid = 0;
  if($uids){
    $last_uid = $uids[count($uids)-1];
  }
  $connection['last_uid'] = $last_uid;

  $wpsp_ep_imap_connections = get_option('wpsp_ep_imap_connections') ? get_option('wpsp_ep_imap_connections') : array();
  foreach ($wpsp_ep_imap_connections as $key => $value) {
    if ( $value['email'] == $connection['email'] ) {
      unset($wpsp_ep_imap_connections[$key]);
      unset($email_piping['email_categories'][$connection['email']]);
      update_option('wpsp_email_pipe_settings',$email_piping);
      break;
    }
  }
  $wpsp_ep_imap_connections[] = $connection;

  update_option( 'wpsp_ep_imap_connections', $wpsp_ep_imap_connections );

  $email_piping['email_categories'][$connection['email']] = 1;
  update_option('wpsp_email_pipe_settings',$email_piping);

}

echo json_encode($response);
