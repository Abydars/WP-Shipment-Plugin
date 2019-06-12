<?php
/**
 * Plugin Name: WP Support Plus Google Calender Event Add-On
 * Plugin URI: https://www.wpsupportplus.com/
 * Description: Add create google calender event button inside ticket.
 * Version: 1.0.1
 * Author: Pradeep Makone
 * Author URI: http://profiles.wordpress.org/pradeepmakone07/
 * Text Domain: wpsp-google-sync
 * Domain Path: /lang
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(class_exists('WPSupportPlus')){
    $GLOBALS['WPSP_GOOGLE_CAL_EVENT']=new WPSP_GOOGLE_CAL_EVENT();
}

final class WPSP_GOOGLE_CAL_EVENT {
    
    public function __construct() {
        $this->define_constants();
        add_action( 'init', array($this,'load_textdomain') );
        register_activation_hook( __FILE__, array($this,'installation') );
        register_deactivation_hook( __FILE__, array($this,'deactivate') );
        $this->include_files();
        
        /*
         * Plugin update
         */
        add_action('admin_init',array($this,'plugin_updator'));
    }
    
    function define_constants(){
        define( 'WPSP_GOOGLESYNC_URL', plugin_dir_url( __FILE__ ) );
        define( 'WPSP_GOOGLESYNC_DIR', plugin_dir_path( __FILE__ ) );
        define( 'WPSP_GOOGLESYNC_VERSION', '1.0.1' );
    }
    
    function installation(){
        include( WPSP_GOOGLESYNC_DIR.'includes/admin/installation.php' );
    }

    function deactivate(){
        include( WPSP_GOOGLESYNC_DIR.'includes/admin/uninstall.php' );
    }
    
    function load_textdomain(){
        $locale = apply_filters( 'plugin_locale', get_locale(), 'wpsp-google-sync' );
        load_plugin_textdomain( 'wpsp-google-sync', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
    }
    
    function include_files(){
        if (is_admin()) {
            include( WPSP_GOOGLESYNC_DIR.'includes/admin/admin.php' );
            $backend=new WPSPGoogleCalenderEventBackend();
            add_action( 'wpsp_ticket_action_before_more_btn_dashboard', array( $backend, 'wpsp_getindi_after_buttons_icon_planner') );
            add_action( 'wpsp_ticket_action_before_more_btn_frontend', array( $backend, 'wpsp_getindi_after_buttons_icon_planner') );
        } else {
            
        }
    }
    
    function plugin_updator(){
        $license_key = trim( get_option( 'wpsp_license_key_gcal' ) ); 
        $edd_updater = new EDD_SL_Plugin_Updater( WPSP_STORE_URL, __FILE__, array(
                'version' 	=> WPSP_GOOGLESYNC_VERSION,		// current version number
                'license' 	=> $license_key,	// license key (used get_option above to retrieve from DB)
                'item_id'       => '3566',	// name of this plugin
                'author' 	=> 'Pradeep Makone',	// author of this plugin
                'url'           => home_url()
        ) );
    }
}
?>