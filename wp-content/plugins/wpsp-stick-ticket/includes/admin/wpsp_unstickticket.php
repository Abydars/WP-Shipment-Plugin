<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$cu=wp_get_current_user();
if (!$cu->has_cap('manage_support_plus_ticket')) exit;

global $wpdb;
$stick_ticket_id=array();
$stick_ticket_id=get_option('wpsp_stick_ticket_id');
$ticket_id=$_POST['ticket_id'];

if(array_search($ticket_id, $stick_ticket_id)> -1){
    if(($key = array_search($ticket_id, $stick_ticket_id)) !== false) {
        unset($stick_ticket_id[$key]);
    }
    update_option('wpsp_stick_ticket_id',$stick_ticket_id); 
}else{
    $stick_ticket_id[]=$ticket_id;
    update_option('wpsp_stick_ticket_id',$stick_ticket_id); 
}
?>
