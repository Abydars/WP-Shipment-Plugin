<?php 
global $wpdb;
$old_company_users= $wpdb->get_var("select users from {$wpdb->prefix}wpsp_companies where id=".$_REQUEST['id']);
$old_company_users= explode(',',$old_company_users);

foreach($old_company_users as $user){
    $user_companies=get_user_meta($user,'wpspUserComapnies',true);
    if($user_companies){
        unset($user_companies[array_search($_REQUEST['id'], $user_companies)]);
        update_user_meta($user, 'wpspUserComapnies', $user_companies);
    }
}

$wpdb->delete( $wpdb->prefix.'wpsp_companies', array( 'id' => $_REQUEST['id'] ) );
wp_redirect(admin_url('admin.php?page=wp-support-plus-company'));
?>
