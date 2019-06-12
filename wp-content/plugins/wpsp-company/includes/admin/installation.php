<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;
/*
 * Installation part start below
 */
$pro_installation_flag = get_option( 'wpsp_comapny_pro_activation' );
$installed_version = get_option( 'wp_support_plus_company_version' );
if ( current_filter() != 'plugins_loaded' || $installed_version != WPSP_COMP_VERSION || $pro_installation_flag ){
    
    update_option( 'wp_support_plus_company_version', WPSP_COMP_VERSION );
    update_option( 'wpsp_comapny_pro_activation', FALSE );
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
    $tables=$wpdb->get_results("show tables like '%wpsp_companies'");
    if(count($tables)==0){
        $sql = "CREATE TABLE {$wpdb->prefix}wpsp_companies(
            id integer NOT NULL AUTO_INCREMENT,
            name TINYTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
            users VARCHAR( 100 ) NULL DEFAULT '0',
            PRIMARY KEY  (id)
        );";
        dbDelta( $sql );
    }

    $coloums=$wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}wpsp_ticket like '%cid'");
    if(count($coloums)==0){
        $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_ticket ADD cid varchar(50) NULL DEFAULT NULL");
    }
    
}
?>