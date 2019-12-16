<?php
/**
 * Plugin Name: WP Support Plus Pro Timer Add-On
 * Plugin URI: http://pradeepmakone.com/wpsupportplus/
 * Description: Allow your agent to count time spend for a ticket
 * Version: 1.0.2
 * Author: Pradeep Makone
 * Author URI: http://profiles.wordpress.org/pradeepmakone07/
 * Text Domain: wpsp-timer
 * Domain Path: /lang
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if(class_exists('WPSupportPlus')){
    $GLOBALS['WPSP_TIMER'] = new WPSP_TIMER();
}

final class WPSP_TIMER{
    
    /*
     * Constructor
     */
    public function __construct() {
        $this->define_constants();
        add_action( 'init', array($this,'load_textdomain') );
        add_action('plugins_loaded', array($this, 'installation'));
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
        define( 'WPSP_TIMER_URL', plugin_dir_url( __FILE__ ) );
        define( 'WPSP_TIMER_DIR', plugin_dir_path( __FILE__ ) );
        define( 'WPSP_TIMER_VERSION', '1.0.2' );
    }
    
    /*
     * This will be called while plugin activation.
     * You can write code related installation in installation.php on given path.
     */
    function installation(){
        include( WPSP_TIMER_DIR.'includes/admin/installation.php' );
    }

    /*
     * This will be called while plugin deactivation
     * You can write code for removing something after plugin deactivate etc.
     */
    function deactivate(){
        include( WPSP_TIMER_DIR.'includes/admin/uninstall.php' );
    }
    
    /*
     * Textdomain loaded for this customization.
     */
    function load_textdomain(){
        $locale = apply_filters( 'plugin_locale', get_locale(), 'wpsp-timer' );
        load_plugin_textdomain( 'wpsp-timer', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
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
            include( WPSP_TIMER_DIR.'includes/admin/admin.php' );
            $backend=new WPSPTimerBackend();
            add_action( 'admin_enqueue_scripts', array( $backend, 'loadScripts') );
            add_action( 'admin_menu', array($backend,'custom_menu_page') );
            add_action('wp_ajax_getTimerLog',array($backend,'getTimerLog'));
            add_action('wp_ajax_setStartTimer', array( $backend, 'setStartTimer'));
            add_action('wp_ajax_wpsp_stop_time',array($backend,'wpsp_stop_time'));
            add_action('wp_ajax_wpsp_calculate_total_time_required',array($backend,'wpsp_calculate_total_time_required'));
            add_action('wp_ajax_wpsp_getEditEndTime',array($backend,'wpsp_getEditEndTime'));
            add_action('wp_ajax_wpsp_setEditEndTime',array($backend,'wpsp_setEditEndTime'));
            add_action('wpsp_open_ticket_backend_before_subject_header',array($backend,'wpsp_display_total_time_required_to_ticket'));
            add_action('wpsp_open_ticket_frontend_before_subject_header',array($backend,'wpsp_display_total_time_required_to_ticket'));
            
        } else {
            /*
             * Frontend related actions
             * All shortcode related, header, footer related actions comes here
             */
            include( WPSP_TIMER_DIR.'includes/frontend.php' );
            $frontend=new WPSPTimerFrontend();
            add_action( 'wp_enqueue_scripts', array( $frontend, 'loadScripts') );
        }
    }
    
    function plugin_updator(){
        $license_key = trim( get_option( 'wpsp_license_key_timer' ) ); 
        $edd_updater = new EDD_SL_Plugin_Updater( WPSP_STORE_URL, __FILE__, array(
                'version' 	=> WPSP_TIMER_VERSION,		// current version number
                'license' 	=> $license_key,	// license key (used get_option above to retrieve from DB)
                'item_id'       => '1151',	// name of this plugin
                'author' 	=> 'Pradeep Makone',	// author of this plugin
                'url'           => home_url()
        ) );
        //error_log('inside update check : timer');
    }
}
?>
