<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$en_id	=	isset($_REQUEST['en_id']) && is_numeric($_REQUEST['en_id']) ? intval($_REQUEST['en_id']) : 'NA';
$nonce	= isset($_REQUEST['nonce'])	? sanitize_text_field($_REQUEST['nonce']) : '';

if ( !is_numeric($en_id) || !$nonce || !wp_verify_nonce($nonce,$en_id) ) {
	die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$wpsp_en_notification = get_option('wpsp_en_notification') ? get_option('wpsp_en_notification') : array();

if (isset($wpsp_en_notification[$en_id])) {

	$new_en									= $wpsp_en_notification[$en_id];
	$new_en['title']				=	$new_en['title'].' clone';
	$wpsp_en_notification[]	=	$new_en;

	update_option( 'wpsp_en_notification', $wpsp_en_notification );

}

wp_redirect( 'admin.php?page=wp-support-plus&setting=addons&section=email_notification' );
exit();
