<?php
/**
 * Plugin Name: WPSP Conditional Agent Assign Addon
 * Plugin URI: http://pradeepmakone.com/wpsupportplus/
 * Description: Conditional Agent Assign Addon!
 * Version: 1.0.0
 * Author: Pradeep Makone
 * Author URI: http://profiles.wordpress.org/pradeepmakone07/
 * Text Domain: wpsp-caa
 * Domain Path: /lang
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(class_exists('WPSupportPlus')){
    new WPSP_CONDITIONAL_AGENT_ASSIGN();
}

final class WPSP_CONDITIONAL_AGENT_ASSIGN {
    
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
        define( 'WPSP_CAA_URL', plugin_dir_url( __FILE__ ) );
        define( 'WPSP_CAA_DIR', plugin_dir_path( __FILE__ ) );
    }
    
    /*
     * This will be called while plugin activation.
     * You can write code related installation in installation.php on given path.
     */
    function installation(){
        include( WPSP_CAA_DIR.'includes/admin/installation.php' );
    }

    /*
     * This will be called while plugin deactivation
     * You can write code for removing something after plugin deactivate etc.
     */
    function deactivate(){
        include( WPSP_CAA_DIR.'includes/admin/uninstall.php' );
    }
    
    /*
     * Textdomain loaded for this customization.
     */
    function load_textdomain(){
        $locale = apply_filters( 'plugin_locale', get_locale(), 'wpsp-caa' );
        load_plugin_textdomain( 'wpsp-caa', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
    }
    
    /*
     * Customization process start
     * You can write backend and frontend actions
     */
    function include_files(){
        include( WPSP_CAA_DIR.'includes/wpsp_caa_api.php' );
        if (is_admin()) {
            /*
             * Backend related actions
             * All ajax related actions comes under this even though ajax is called from frontend
             */
            include( WPSP_CAA_DIR.'includes/admin/admin.php' );
            $backend=new WPSPConditionalAgentAssignBackend();
            
            /*
             * If customization require backend setting, un-comment below admin_menu and admin_enqueue_scripts actions.
             */
            add_action( 'admin_menu', array($backend,'custom_menu_page') );
            add_action( 'admin_enqueue_scripts', array( $backend, 'loadScripts') );
            add_action('wp_ajax_wpsp_conditional_agent_assign',array($backend,'wpsp_conditional_agent_assign'));
            add_filter('wpsp_create_new_ticket_values',array($backend,'wpsp_create_new_ticket_values'),10,1);
            add_action('wp_ajax_setEditConditional',array($backend,'wpsp_setEditConditional'));
            add_action('wp_ajax_getEditCondtional',array($backend,'getEditCondtional'));
            add_action('wp_ajax_setDeleteConditional',array($backend,'setDeleteConditional'));
            add_action('wp_ajax_wpsp_add_condition',array($backend,'wpsp_add_condition'));
            
        } else {
            /*
             * Frontend related actions
             * All shortcode related, header, footer related actions comes here
             */
            include( WPSP_CAA_DIR.'includes/frontend.php' );
            $frontend=new  WPSPConditionalAgentAssignFrontend();
            
            /*
             * If customization require to load js and css file un-comment below wp_enqueue_scripts action
             */
            add_action( 'wp_enqueue_scripts', array( $frontend, 'loadScripts') );
        }
    }
    
    function plugin_updator(){
        $license_key = trim( get_option( 'wpsp_license_key_condagentassign' ) ); 
        $edd_updater = new EDD_SL_Plugin_Updater( WPSP_STORE_URL, __FILE__, array(
                'version' 	=> WPSP_PIPE_VERSION,		// current version number
                'license' 	=> $license_key,	// license key (used get_option above to retrieve from DB)
                'item_id'       => '7586',	// name of this plugin
                'author' 	=> 'Pradeep Makone',	// author of this plugin
                'url'           => home_url()
        ) );
    }
}
