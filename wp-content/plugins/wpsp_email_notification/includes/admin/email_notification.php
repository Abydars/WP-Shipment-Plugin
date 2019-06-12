<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$wpsp_en_action = isset( $_REQUEST['wpsp_en_action'] ) ? sanitize_text_field( $_REQUEST['wpsp_en_action'] ) : 'view_settings';

switch ( $wpsp_en_action ) {

	case 'view_settings':
		include WPSP_EN_DIR . 'includes/admin/view_settings.php';
		break;

	case 'add_notification':
		include WPSP_EN_DIR . 'includes/admin/add_notification.php';
		break;

	case 'edit_notification':
		include WPSP_EN_DIR . 'includes/admin/edit_notification.php';
		break;

}
