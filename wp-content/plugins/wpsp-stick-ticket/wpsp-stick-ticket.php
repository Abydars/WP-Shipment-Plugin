<?php
/**
 * Plugin Name: WP Support Plus Stick Tickets Add-On
 * Plugin URI: http://pradeepmakone.com/wpsupportplus/
 * Description: WPSP Stick Ticket Addon!
 * Version: 1.0.0
 * Author: Pradeep Makone
 * Author URI: http://profiles.wordpress.org/pradeepmakone07/
 * Text Domain: wpsp-stick-ticket
 * Domain Path: /lang
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(class_exists('WPSupportPlus')){
    new WPSP_STICK_TICKET();
}

final class WPSP_STICK_TICKET {
    
    /*
     * Constructor
     */
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
    
    /*
     * Define constants for current plugin url and directory
     * This will help in including files in this plugin and url of the images or resources like js & css
     * This should be unique in overall wordpress
     */
    function define_constants(){
        define( 'WPSP_STICK_URL', plugin_dir_url( __FILE__ ) );
        define( 'WPSP_STICK_DIR', plugin_dir_path( __FILE__ ) );
    }
    
    /*
     * This will be called while plugin activation.
     * You can write code related installation in installation.php on given path.
     */
    function installation(){
        include( WPSP_STICK_DIR.'includes/admin/installation.php' );
    }

    /*
     * This will be called while plugin deactivation
     * You can write code for removing something after plugin deactivate etc.
     */
    function deactivate(){
        include( WPSP_STICK_DIR.'includes/admin/uninstall.php' );
    }
    
    /*
     * Textdomain loaded for this customization.
     */
    function load_textdomain(){
        $locale = apply_filters( 'plugin_locale', get_locale(), 'wpsp-stick-ticket' );
        load_plugin_textdomain( 'wpsp-stick-ticket', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
    }
    
    /*
     * Customization process start
     * You can write backend and frontend actions
     */
    function include_files(){
        if (is_admin()) {
            /*
             * Backend related actions
             * All ajax related actions comes under this even though ajax is called from frontend
             */
            include( WPSP_STICK_DIR.'includes/admin/admin.php' );
            $backend=new WPSPStickTicketBackend();
            
            /*
             * If customization require backend setting, un-comment below admin_menu and admin_enqueue_scripts actions.
             */
            add_action( 'admin_menu', array($backend,'custom_menu_page') );
            add_action('admin_enqueue_scripts', array( $backend, 'loadScripts') );
            add_action('wp_ajax_wpsp_getstickticket',array( $backend, 'wpsp_getstickticket') );   
            add_action('wpsp_after_th_in_getticketsbyfilter',array($backend,'wpsp_after_th_in_getticketsbyfilter'));
            add_action('wpsp_after_checkbox_td_in_getticketsbyfilter',array($backend,'wpsp_add_td'),10,2);
            add_action('wpsp_before_ticket_list',array($backend,'wpsp_before_ticket_list'),10,1);
            add_action('wpsp_in_table_of_getfrontendticket',array($backend,'wpsp_after_th_in_getticketsbyfilter'));
            add_action('wpsp_after_tr_in_frontend',array($backend,'wpsp_add_td'),10,2);
            add_action('wpsp_before_ticket_list_frontend',array($backend,'wpsp_before_ticket_list_frontend'));    
            add_action('wp_ajax_wpsp_setsticksetting',array($backend,'wpsp_setsticksetting'));
        } else {
            /*
             * Frontend related actions
             * All shortcode related, header, footer related actions comes here
             */
            include( WPSP_STICK_DIR.'includes/frontend.php' );
            $frontend=new WPSPStickTicketFrontend();
            
            /*
             * If customization require to load js and css file un-comment below wp_enqueue_scripts action
             */
            add_action( 'wp_enqueue_scripts', array( $frontend, 'loadScripts') );
            add_action('wp_ajax_wpsp_getstickticket',array( $frontend, 'wpsp_getstickticketfront') );
        }
    }
    
    function plugin_updator(){
        $license_key = trim( get_option( 'wpsp_license_key_stickticket' ) ); 
        $edd_updater = new EDD_SL_Plugin_Updater( WPSP_STORE_URL, __FILE__, array(
                'version' 	=> WPSP_PIPE_VERSION,		// current version number
                'license' 	=> $license_key,	// license key (used get_option above to retrieve from DB)
                'item_id'       => '7580',	// name of this plugin
                'author' 	=> 'Pradeep Makone',	// author of this plugin
                'url'           => home_url()
        ) );
    }
}
