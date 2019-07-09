<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$custom_menu      = $wpsupportplus->functions->sanitize_string_array($_POST['wpsp_custom_menu']);
$csid             = $custom_menu['id'];
$custom_menu_text = $custom_menu['name'];
$custom_menu_url  = $custom_menu['url'];
$custom_menu_icon = $custom_menu['img_url'];

$values=array(
    'menu_text'		=>$custom_menu_text,
    'redirect_url'=>$custom_menu_url,
    'menu_icon'		=>$custom_menu_icon
);

$wpdb->update($wpdb->prefix.'wpsp_panel_custom_menu', $values, array('id'=>$csid));

wp_redirect( 'admin.php?page=wp-support-plus&setting=general&section=support-button' );
exit();