<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$menu_order     = explode( ',', sanitize_text_field($_POST['support_menu_sort_order']));
$sectionsid     = isset($_POST['support_menu_sort_order']) && is_array($menu_order) ? $menu_order : array();
$order          = $wpsupportplus->functions->sanitize_integer_array($sectionsid);

//save order
$counter = 0;
foreach ($order as $menu_id): 
    $values = array('load_order' => ++$counter);
    $wpdb->update($wpdb->prefix . 'wpsp_support_menu', $values, array('id' => $menu_id));
endforeach;

wp_redirect( 'admin.php?page=wp-support-plus&setting=general&section=support-page-menu' );
exit();