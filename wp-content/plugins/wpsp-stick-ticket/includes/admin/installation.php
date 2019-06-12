<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/*
 * Installation part start below
 */
global $wpdb;

if( get_option( 'wpsp_stick_ticket_settings' ) === false ) {
    $stickTicketSettings=array(
        'wpsp_allow_user_to_stick_ticket'=>1,
        'stick_ticket_color'=>'#ffff00'
    );
    update_option('wpsp_stick_ticket_settings',$stickTicketSettings);
}
?>