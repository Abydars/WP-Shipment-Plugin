<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;
/*
 * Installation part start below
 */
$pro_installation_flag = get_option( 'wpsp_timer_pro_activation' );
$installed_version = get_option( 'wp_support_plus_timer_version' );
if ( current_filter() != 'plugins_loaded' || $installed_version != WPSP_TIMER_VERSION || $pro_installation_flag ){
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    $installed_version=WPSP_TIMER_VERSION;
    update_option('wp_support_plus_timer_version',$installed_version);
    update_option( 'wpsp_timer_pro_activation', FALSE );
    
    $tables=$wpdb->get_results("show tables like '%wpsp_timer'");
    if(count($tables)==0){
        $sql = "CREATE TABLE {$wpdb->prefix}wpsp_timer(
            id integer NOT NULL AUTO_INCREMENT,
            ticket_id integer,
            start_time VARCHAR( 100 ) NULL DEFAULT NULL,
            end_time VARCHAR( 100 ) NULL DEFAULT NULL,
            timer_status integer DEFAULT 1,
            PRIMARY KEY  (id)
        );";
        dbDelta( $sql );
    }
    
    if( get_option('wpsp_timer_settings' ) === false ){
        $timerSettings=array(
            'timer_visibility'=>0
        );
        update_option('wpsp_timer_settings',$timerSettings);
    }
}
?>