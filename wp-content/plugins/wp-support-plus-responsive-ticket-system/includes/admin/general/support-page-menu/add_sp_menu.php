<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$custom_menu      = $wpsupportplus->functions->sanitize_string_array($_POST['wpsp_sp_menu']);
$custom_menu_text = $custom_menu['name'];
$custom_menu_url  = $custom_menu['url'];
$custom_menu_icon = $custom_menu['img_url'];

$order = (int)$wpdb->get_var("select max(load_order) as current_order from {$wpdb->prefix}wpsp_support_menu");
$order++;

$values=array(
    'name'         => $custom_menu_text,
    'redirect_url' => $custom_menu_url,
    'icon'         => $custom_menu_icon,
		'load_order'   => $order
);

$wpdb->insert($wpdb->prefix.'wpsp_support_menu',$values);

wp_redirect( 'admin.php?page=wp-support-plus&setting=general&section=support-page-menu' );
exit();