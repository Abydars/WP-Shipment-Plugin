<?php 
if (!defined('ABSPATH')) exit;

global $wpdb;

$company_name=$_POST['wpsp_title'];
$wpsp_company_employees=$_POST['wpsp_company_employee'];
$userstr=implode(',', $wpsp_company_employees);

$values=array(
    'name'=>$company_name,
    'users'=>$userstr
);
$wpdb->insert($wpdb->prefix.'wpsp_companies',$values);
$company_id=$wpdb->insert_id;

foreach ($wpsp_company_employees as $user){
    $user_companies = get_user_meta( $user, 'wpspUserComapnies',true);
    if($user_companies){
        $user_companies[]=$company_id;
    } else {
        $user_companies=array();
        $user_companies[]=$company_id;
    }
    
    update_user_meta($user, 'wpspUserComapnies', $user_companies);
    
}
?>