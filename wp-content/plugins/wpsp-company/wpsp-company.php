<?php

/**
 * Plugin Name: WP Support Plus Company/Usergroup Add-On
 * Plugin URI: https://www.wpsupportplus.com/company-feature/
 * Description: Users can see all tickets of their company/usergroup in WP Support Plus
 * Version: 1.0.4
 * Author: Pradeep Makone
 * Author URI: http://profiles.wordpress.org/pradeepmakone07/
 * Text Domain: wpsp-company
 * Domain Path: /lang
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(class_exists('WPSupportPlus')){
    $GLOBALS['WPSP_COMPANY'] = new WPSP_COMPANY();
}

final class WPSP_COMPANY {
    
    /*
     * Constructor
     */
    public function __construct() {
        $this->define_constants();
        add_action( 'init', array($this,'load_textdomain') );
        add_action( 'plugins_loaded', array($this,'installation') );
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
        define( 'WPSP_COMP_URL', plugin_dir_url( __FILE__ ) );
        define( 'WPSP_COMP_DIR', plugin_dir_path( __FILE__ ) );
        define( 'WPSP_COMP_VERSION', '1.0.4' );
    }
    
    /*
     * This will be called while plugin activation.
     * You can write code related installation in installation.php on given path.
     */
    function installation(){
        include( WPSP_COMP_DIR.'includes/admin/installation.php' );
    }

    /*
     * This will be called while plugin deactivation
     * You can write code for removing something after plugin deactivate etc.
     */
    function deactivate(){
        include( WPSP_COMP_DIR.'includes/admin/uninstall.php' );
    }
    
    /*
     * Textdomain loaded for this customization.
     */
    function load_textdomain(){
        $locale = apply_filters( 'plugin_locale', get_locale(), 'wpsp-company' );
        load_plugin_textdomain( 'wpsp-company', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
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
            include( WPSP_COMP_DIR.'includes/admin/admin.php' );
            $backend=new WPSPCompanyBackend();
            add_action( 'wpsp_create_company', array( $backend, 'wpsp_create_company'));
            add_action('wpspSearchRegisteredUser',array($backend,'searchRegisteredUsaers'));
            add_action('wpspSelectRegisteredUser', array( $backend, 'wpspSelectRegisteredUser' ) );
            add_action('wp_ajax_setCompanyUser', array( $backend, 'setCompanyUser' ) );
            add_action('wp_ajax_editCompanyUser', array( $backend, 'editCompanyUser' ) );
            add_filter('wpsp_create_new_ticket_values',array($backend,'wpsp_insert_company_id_in_ticket'),10,1);
            add_filter('wpsp_get_ticket_list_where_frontend',array($backend,'wpsp_get_ticket_list_where_frontend'),2,3);
            add_filter('wpsp_hack_flag_front_for_otherthan_staff_user',array($backend,'wpsp_hack_flag_front_for_otherthan_staff_user'),2,3);
            
        } else {
            /*
             * Frontend related actions
             * All shortcode related, header, footer related actions comes here
             */
           
            include( WPSP_COMP_DIR.'includes/frontend.php' );
            $frontend=new WPSPCompanyFrontend();
            add_action( 'wp_enqueue_scripts', array( $frontend, 'loadScripts') );
        }
    }
    
    function plugin_updator(){
        $license_key = trim( get_option( 'wpsp_license_key_company' ) ); 
        $edd_updater = new EDD_SL_Plugin_Updater( WPSP_STORE_URL, __FILE__, array(
                'version' 	=> WPSP_COMP_VERSION,		// current version number
                'license' 	=> $license_key,	// license key (used get_option above to retrieve from DB)
                'item_id'       => '1148',	// name of this plugin
                'author' 	=> 'Pradeep Makone',	// author of this plugin
                'url'           => home_url()
        ) );
        //error_log('inside update check : company');
    }
}
?>
