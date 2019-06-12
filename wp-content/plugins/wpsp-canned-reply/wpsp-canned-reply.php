<?php
/**
 * Plugin Name: WP Support Plus Advanced Canned Reply Add-On
 * Plugin URI: https://www.wpsupportplus.com/
 * Description: Advanced Canned reply!
 * Version: 1.0.0
 * Author: Pradeep Makone
 * Author URI: http://profiles.wordpress.org/pradeepmakone07/
 * Text Domain: wpsp-canned-reply
 * Domain Path: /lang
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(class_exists('WPSupportPlus')){
    $GLOBALS['WPSP_CANNED_REPLY']=new WPSP_CANNED_REPLY();
}

final class WPSP_CANNED_REPLY {
    
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
        define( 'WPSP_CANNED_URL', plugin_dir_url( __FILE__ ) );
        define( 'WPSP_CANNED_DIR', plugin_dir_path( __FILE__ ) );
        define( 'WPSP_CANNED_VERSION', '1.0.0' );
    }
    
    function installation(){
        include( WPSP_CANNED_DIR.'includes/admin/installation.php' );
    }

    function deactivate(){
        include( WPSP_CANNED_DIR.'includes/admin/uninstall.php' );
    }
    
    function load_textdomain(){
        $locale = apply_filters( 'plugin_locale', get_locale(), 'wpsp-canned-reply' );
        load_plugin_textdomain( 'wpsp-canned-reply', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
    }
    
    function include_files(){
        if (is_admin()) {
            include( WPSP_CANNED_DIR.'includes/admin/admin.php' );
            $backend=new WPSPCannedBackend();
            
            add_action('admin_enqueue_scripts', array( $backend, 'loadScripts') );
            add_action('wpsp_in_getcannedreply_before_title',array($backend,'canned_reply_template'));
            add_filter('wpsp_reply_ticket_body',array($backend,'wpsp_reply_ticket_body'),10,2);
            add_filter('wpsp_in_createnewcustomfield_filter',array($backend,'wpsp_in_createnewcustomfield_filter'),10,2);
            add_filter('wpsp_in_deletecustomfield_filter',array($backend,'wpsp_in_deletecustomfield_filter'),10,1);
            add_filter('wpsp_in_updatecustomfield_filter',array($backend,'wpsp_in_updatecustomfield_filter'),10,2);
            add_action('wpsp_editcanned_action',array($backend,'wpsp_editcanned_action'));
           
        } else {
            include( WPSP_CANNED_DIR.'includes/frontend.php' );
            $frontend=new WPSPCannedFrontend();
        }
    }
    
    function plugin_updator(){
        $license_key = trim( get_option( 'wpsp_license_key_acan' ) ); 
        $edd_updater = new EDD_SL_Plugin_Updater( WPSP_STORE_URL, __FILE__, array(
                'version' 	=> WPSP_CANNED_VERSION,		// current version number
                'license' 	=> $license_key,	// license key (used get_option above to retrieve from DB)
                'item_id'       => '3569',	// name of this plugin
                'author' 	=> 'Pradeep Makone',	// author of this plugin
                'url'           => home_url()
        ) );
    }
}
?>