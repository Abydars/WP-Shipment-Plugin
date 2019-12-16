<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb;
global $current_user;
$current_user=wp_get_current_user();
if($current_user->has_cap('manage_options')||$current_user->has_cap('manage_support_plus_agent')){
    $values=array('end_time'=>$_POST['end_time']);
    $wpdb->update($wpdb->prefix.'wpsp_timer',$values,array('id'=>$_POST['id']));
}
?>