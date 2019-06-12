<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$wpsp_en_notification = get_option('wpsp_en_notification') ? get_option('wpsp_en_notification') : array();
$en_id                = isset( $_REQUEST['en_id'] ) ? intval($_REQUEST['en_id']) : 'NA';
$notification         = isset($_REQUEST['wpsp_en_notification']) ? $_REQUEST['wpsp_en_notification'] : array();

// exit if no notification found
if( !is_numeric($en_id) || !$notification ) {
  wp_redirect( 'admin.php?page=wp-support-plus&setting=addons&section=email_notification' );
  exit();
}

// insert new index for email notification
$wpsp_en_notification[$en_id] = $notification;
update_option( 'wpsp_en_notification', $wpsp_en_notification );

wp_redirect( 'admin.php?page=wp-support-plus&setting=addons&section=email_notification' );
exit();
