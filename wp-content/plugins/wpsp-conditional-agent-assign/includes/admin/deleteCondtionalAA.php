<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $current_user;
if (!$current_user->has_cap('manage_options')) exit; // Exit if current user is not admin

$wpsp_caa_conditions = get_option( 'wpsp_caa_conditions' );
if(isset($_POST['cond_id']) && is_numeric($_POST['cond_id'])){
    $cond_id = intval($_POST['cond_id']);
    unset($wpsp_caa_conditions[$cond_id]);
}
update_option('wpsp_caa_conditions',$wpsp_caa_conditions);
