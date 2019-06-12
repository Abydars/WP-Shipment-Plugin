<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$wpsp_en_notification = get_option('wpsp_en_notification') ? get_option('wpsp_en_notification') : array();
$notification         = isset($_REQUEST['wpsp_en_notification']) ? $_REQUEST['wpsp_en_notification'] : array();

// exit if no notification found
if( !$notification ) {
  wp_redirect( 'admin.php?page=wp-support-plus&setting=addons&section=email_notification' );
  exit();
}

// insert new index for email notification
$wpsp_en_notification[] = $notification;

// get last index of the array
end($wpsp_en_notification);
$key = key($wpsp_en_notification);
reset($wpsp_en_notification);

update_option( 'wpsp_en_notification', $wpsp_en_notification );

// continue edit of the notification
wp_redirect( 'admin.php?page=wp-support-plus&setting=addons&section=email_notification&wpsp_en_action=edit_notification&en_id='.$key );
exit();
