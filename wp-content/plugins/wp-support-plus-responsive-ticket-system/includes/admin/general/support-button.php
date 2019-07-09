<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$support_btn_settings = $wpsupportplus->functions->get_support_btn_settings();
if($_REQUEST['section']=='support-button'){
		$tab=(empty($_REQUEST['action']))?'list': sanitize_text_field($_REQUEST['action']);
		switch ($tab){
				case 'list':
						include WPSP_ABSPATH.'includes/admin/general/custom-menu/support_btn_custom_menu.php';
				break;
				case 'add':
						include WPSP_ABSPATH.'includes/admin/general/custom-menu/support_btn_custom_menu_add.php';
				break;
				case 'edit':
						include WPSP_ABSPATH.'includes/admin/general/custom-menu/support_btn_custom_menu_update.php';
				break;
		}
}
?>
