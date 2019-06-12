<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;

$pro_installation_flag = get_option( 'wpsp_export_pro_activation' );
$installed_version = get_option( 'wpsp_export_ticket_version' );
if ( current_filter() != 'plugins_loaded' || $installed_version != WPSP_EXP_VERSION || $pro_installation_flag) {
    update_option( 'wpsp_export_ticket_version', WPSP_EXP_VERSION );
    update_option( 'wpsp_export_pro_activation', FALSE );
    //update script goes below
    if( get_option( 'wpsp_plugin_reloaded_date' ) === false ) { 
            $dateup=  date('d-m-Y');
            update_option('wpsp_plugin_reloaded_date',$dateup);
    }
}
?>