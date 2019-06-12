<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$id    = isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) ? intval($_REQUEST['id']) : 'NA' ;
$nonce = isset($_REQUEST['nonce']) ? sanitize_text_field($_REQUEST['nonce']) : '';

if( !is_numeric($id) || !wp_verify_nonce($nonce,$id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$email_piping     = get_option('wpsp_email_pipe_settings');
$imap_connections = get_option('wpsp_ep_imap_connections');

$connection = $imap_connections[$id];

unset($imap_connections[$id]);
update_option('wpsp_ep_imap_connections',$imap_connections);

unset($email_piping['email_categories'][$connection['email']]);
update_option('wpsp_email_pipe_settings',$email_piping);
