<?php
if (!defined('ABSPATH')) exit;

global $wpdb;

$company_name=$_POST['wpsp_title'];
$wpsp_company_employees=$_POST['wpsp_company_employee'];
$userstr=implode(',', $wpsp_company_employees);

$old_company_users= $wpdb->get_var("select users from {$wpdb->prefix}wpsp_companies where id=".$_POST['comp_id']);
$old_company_users= explode(',',$old_company_users);

foreach($old_company_users as $user){
    $user_companies=get_user_meta($user,'wpspUserComapnies',true);
    if($user_companies){
        unset($user_companies[array_search($_POST['comp_id'], $user_companies)]);
        update_user_meta($user, 'wpspUserComapnies', $user_companies);
    }
}

$values=array(
            'name'=>$company_name,
            'users'=>$userstr
        );
$wpdb->update($wpdb->prefix.'wpsp_companies',$values,array('id'=>$_POST['comp_id']));

foreach ($wpsp_company_employees as $user){
    $user_companies = get_user_meta( $user, 'wpspUserComapnies',true);
    if($user_companies){
        $user_companies[]=$_POST['comp_id'];
    } else {
        $user_companies=array();
        $user_companies[]=$_POST['comp_id'];
    }
    
    update_user_meta($user, 'wpspUserComapnies', $user_companies);
}
?>