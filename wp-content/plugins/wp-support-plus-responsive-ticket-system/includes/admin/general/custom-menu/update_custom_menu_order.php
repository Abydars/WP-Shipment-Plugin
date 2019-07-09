<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$custom_menu_sort_order = isset($_POST['custom_menu_sort_order'])? sanitize_text_field($_POST['custom_menu_sort_order']) : '';

$menu_order     = explode( ',', $custom_menu_sort_order);
$sectionsid     = isset($_POST['custom_menu_sort_order']) && is_array($menu_order) ? $menu_order : array();
$order          = $wpsupportplus->functions->sanitize_integer_array($sectionsid);

//save order
$counter = 0;
foreach ($order as $menu_id): 
    $values = array('load_order' => ++$counter);
    $wpdb->update($wpdb->prefix . 'wpsp_panel_custom_menu', $values, array('id' => $menu_id));
endforeach;

//save button text
$btn_sttings = isset($_POST['btn_settings']) && is_array($_POST['btn_settings']) ? $wpsupportplus->functions->sanitize_string_array($_POST['btn_settings']) : array();
$btn_sttings['allow_support_btn'] = isset($btn_sttings['allow_support_btn']) ? 1 : 0;
update_option( 'wpsp_settings_support_btn', $btn_sttings );

wp_redirect( 'admin.php?page=wp-support-plus&setting=general&section=support-button' );
exit();