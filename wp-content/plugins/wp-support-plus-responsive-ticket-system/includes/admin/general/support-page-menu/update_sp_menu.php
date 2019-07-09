<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$custom_menu      = $wpsupportplus->functions->sanitize_string_array($_POST['wpsp_sp_menu']);
$smid             = $custom_menu['id'];
$custom_menu_text = $custom_menu['name'];
$custom_menu_url  = $custom_menu['url'];
$custom_menu_icon = $custom_menu['img_url'];

$values=array(
    'name'         => $custom_menu_text,
    'redirect_url' => $custom_menu_url,
    'icon'         => $custom_menu_icon
);

$wpdb->update($wpdb->prefix.'wpsp_support_menu', $values, array('id'=>$smid));

wp_redirect( 'admin.php?page=wp-support-plus&setting=general&section=support-page-menu' );
exit();