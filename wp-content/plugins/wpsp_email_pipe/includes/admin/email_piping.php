<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$ep_action =  isset($_REQUEST['ep_action']) ? sanitize_text_field($_REQUEST['ep_action']) : 'overview';

switch ($ep_action) {

	case 'overview':
		include_once( WPSP_PIPE_PLUGIN_DIR . 'includes/admin/overview.php' );
		break;

	case 'add_imap':
		include_once( WPSP_PIPE_PLUGIN_DIR . 'includes/admin/imap/add_imap.php' );
		break;

	case 'edit_imap':
		include_once( WPSP_PIPE_PLUGIN_DIR . 'includes/admin/imap/edit_imap.php' );
		break;

}
