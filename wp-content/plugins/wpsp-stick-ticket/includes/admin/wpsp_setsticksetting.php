<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$cu = wp_get_current_user();
if (!$cu->has_cap('manage_options')) exit; // Exit if current user is not admin

global $wpdb;
$stickTicketSettings=get_option( 'wpsp_stick_ticket_settings' );
$stickTicketSettings=array(
    'wpsp_allow_user_to_stick_ticket'=>$_POST['wpsp_allow_user_to_stick_ticket'],
    'stick_ticket_color'=>$_POST['stick_ticket_color']
    
);
update_option('wpsp_stick_ticket_settings',$stickTicketSettings);
?>