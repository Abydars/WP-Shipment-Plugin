<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$cu=wp_get_current_user();
global $wpdb;
$stick_ticket_id=array();
$tic_array=array();
$stick_ticket_id=get_option('wpsp_stick_ticket_id');
$ticket_id=$_POST['ticket_id'];
$stickTicketSettings=get_option( 'wpsp_stick_ticket_settings' );
if($cu->has_cap('manage_options')){
    if($stick_ticket_id && array_search($ticket_id, $stick_ticket_id)> -1){
        if(($key = array_search($ticket_id, $stick_ticket_id)) !== false) {
            unset($stick_ticket_id[$key]);
            update_option('wpsp_stick_ticket_id',$stick_ticket_id);
        }
    }else{
        $stick_ticket_id[]=$ticket_id;
        update_option('wpsp_stick_ticket_id',$stick_ticket_id); 
    }
}

if($cu->has_cap('manage_support_plus_ticket') && (!$cu->has_cap('manage_options')) && $stickTicketSettings['wpsp_allow_user_to_stick_ticket']==0){
    $usermeta=get_user_meta($cu->ID,'wpsp_stick_ticket',TRUE);
    if($usermeta && array_search($ticket_id, $usermeta)> -1){
        if(($key = array_search($ticket_id, $usermeta)) !== false) {
            unset($usermeta[$key]);
            update_user_meta($cu->ID,'wpsp_stick_ticket',$usermeta);
        }
    }else{
        $usermeta[]=$ticket_id;
        update_user_meta($cu->ID,'wpsp_stick_ticket',$usermeta);
    }
}
?>
