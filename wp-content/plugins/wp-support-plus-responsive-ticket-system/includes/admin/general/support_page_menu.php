<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

//$support_btn_settings = $wpsupportplus->functions->get_support_btn_settings();
if($_REQUEST['section']=='support-page-menu'){
		$tab=(empty($_REQUEST['action']))?'list': sanitize_text_field($_REQUEST['action']);
		switch ($tab){
				case 'list':
						include WPSP_ABSPATH.'includes/admin/general/support-page-menu/menu_list.php';
				break;
				case 'add':
						include WPSP_ABSPATH.'includes/admin/general/support-page-menu/menu_list_add.php';
				break;
				case 'edit':
						include WPSP_ABSPATH.'includes/admin/general/support-page-menu/menu_list_update.php';
				break;
		}
}
?>
