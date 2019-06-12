<?php

/**
 * Plugin Name: WP Support Plus Export Ticket Add-On
 * Plugin URI: https://www.wpsupportplus.com/export-tickets/
 * Description: Export ticket list to CSV in WP Support Plus
 * Version: 1.0.4
 * Author: Pradeep Makone
 * Author URI: http://profiles.wordpress.org/pradeepmakone07/
 * Text Domain: wpsp_export
 * Domain Path: /lang
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

final class WPSupportPlusExportTicket {

    public function __construct() {
        $this->define_constants();
        add_action('init', array($this, 'load_textdomain'));
        add_action('plugins_loaded', array($this, 'installation'));
        register_activation_hook(__FILE__, array($this, 'installation'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        $this->include_files();
        
        /*
         * Plugin update
         */
        add_action('admin_init',array($this,'plugin_updator'));
    }

    private function define_constants() {
        define('WPSP_EXP_PLUGIN_URL', plugin_dir_url(__FILE__));
        define('WPSP_EXP_PLUGIN_DIR', plugin_dir_path(__FILE__));
        define('WPSP_EXP_VERSION', '1.0.4');
    }
    
    function load_textdomain(){
        $locale = apply_filters( 'plugin_locale', get_locale(), 'wpsp_export' );
        load_textdomain( 'wpsp_export', WP_LANG_DIR . '/wpsp/wpsp_export-' . $locale . '.mo' );
        load_plugin_textdomain( 'wpsp_export', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
    }

    function include_files() {
        if (is_admin()) {
            include_once ( WPSP_EXP_PLUGIN_DIR.'includes/admin/ajax.php' );
            $ajax=new SupportPlusExpAjax();
            add_action( 'wp_ajax_getExportTicketToExcel', array( $ajax, 'getExportTicketToExcel'));
            add_action( 'wp_ajax_setExportTicketToExcel', array( $ajax, 'setExportTicketToExcel'));
        }
    }

    function installation() {
        include( WPSP_EXP_PLUGIN_DIR . 'includes/admin/installation.php' );
    }

    function deactivate() {
        include( WPSP_EXP_PLUGIN_DIR . 'includes/admin/uninstall.php' );
    }

    function plugin_updator(){
        $license_key = trim( get_option( 'wpsp_license_key_exportticket' ) ); 
        $edd_updater = new EDD_SL_Plugin_Updater( WPSP_STORE_URL, __FILE__, array(
                'version' 	=> WPSP_EXP_VERSION,		// current version number
                'license' 	=> $license_key,	// license key (used get_option above to retrieve from DB)
                'item_id'       => '56',	// name of this plugin
                'author' 	=> 'Pradeep Makone',	// author of this plugin
                'url'           => home_url()
        ) );
        //error_log('inside update check : export');
    }
}

if (class_exists('WPSupportPlus')) {
    $GLOBALS['WPSupportPlusExportTicket'] = new WPSupportPlusExportTicket();
}
?>