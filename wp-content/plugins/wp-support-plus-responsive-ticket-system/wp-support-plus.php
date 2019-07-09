<?php 
/**
 * Plugin Name: WP Support Plus
 * Plugin URI: https://wordpress.org/plugins/wp-support-plus-responsive-ticket-system
 * Description: Exceptional customer support solution for WordPress!
 * Version: 9.1.2
 * Author: Pradeep Makone
 * Author URI: https://www.wpsupportplus.com/
 * Requires at least: 4.4
 * Tested up to: 4.9
 * Text Domain: wp-support-plus-responsive-ticket-system
 * Domain Path: /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_Support_Plus' ) ) :

    /**
     * Main WPSupportPlus Class.
     * @class WPSupportPlus
     */
    final class WP_Support_Plus {

        /**
         * WPSP version.
         */
        public $version = '9.1.2';

        /**
         * The single instance of the class.
         */
        protected static $_instance = null;
        
        /**
         * WPSP Cron object
         */
        public $cron = null;
        
        /**
         * WPSP Ajax object
         */
        public $ajax = null;
        
        /**
         * WPSP Email object
         */
        public $emails = null;
        
        /**
         * WPSP function object
         */
        public $functions = null;

        /**
         * Main WPSP Instance.
         * Ensures only one instance of WPSP is loaded or can be loaded.
         */
        public static function instance() {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Cloning is forbidden.
         */
        public function __clone() {
            die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
        }

        /**
         * Un-serializing instances of this class is forbidden.
         */
        public function __wakeup() {
            die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
        }

        /**
         * WPSP Constructor.
         */
        public function __construct() {
            
						$this->define_constants();
						
						$installed_version = get_option( 'wp_support_plus_version' );
						if( $installed_version  ){
								
								add_action( 'init', array($this,'load_textdomain') );
								$this->includes();
								if (!wp_next_scheduled('daily_check_ticket_status')) {
										wp_schedule_event(time(), 'daily', 'daily_check_ticket_status');
								}
								add_action( 'daily_check_ticket_status', array($this->cron,'daily_check_ticket_status') );

								do_action('wp_support_plus_loaded');
								
            } else {
						
								include_once( WPSP_ABSPATH . 'includes/class-redirect-supportcandy.php' );
						
						}
						
        }
        
        /**
         * Define WPSP Constants.
         */
        private function define_constants() {
            $upload_dir = wp_upload_dir();

						$this->define('WPSP_STORE_URL', 'https://www.wpsupportplus.com');
            $this->define('WPSP_PLUGIN_FILE', __FILE__);
            $this->define('WPSP_ABSPATH', dirname(__FILE__) . '/');
            $this->define('WPSP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
            $this->define('WPSP_PLUGIN_BASENAME', plugin_basename(__FILE__));
            $this->define('WPSP_VERSION', $this->version);
        }
				
				function load_textdomain(){
						$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-support-plus-responsive-ticket-system' );
						load_textdomain( 'wp-support-plus-responsive-ticket-system', WP_LANG_DIR . '/wpsp/wp-support-plus-responsive-ticket-system-' . $locale . '.mo' );
						load_plugin_textdomain( 'wp-support-plus-responsive-ticket-system', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
		    }
        
        /**
         * Define constant if not already set.
         */
        private function define($name, $value) {
            if (!defined($name)) {
                define($name, $value);
            }
        }
        
        /**
         * What type of request is this?
         */
        private function is_request($type) {
            switch ($type) {
                case 'admin' :
                    return is_admin();
                case 'ajax' :
                    return defined('DOING_AJAX');
                case 'cron' :
                    return defined('DOING_CRON');
                case 'frontend' :
                    return (!is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
            }
        }
        
        /**
	 * Include required core files used in admin and on the frontend.
	 */
        public function includes() {
            
            include_once( WPSP_ABSPATH . 'includes/class-wpsp-install.php' );
            include_once( WPSP_ABSPATH . 'includes/class-wpsp-cron.php' );
            include_once( WPSP_ABSPATH . 'includes/class-wpsp-ajax.php' );
            include_once( WPSP_ABSPATH . 'includes/class-wpsp-emails.php' );
            include_once( WPSP_ABSPATH . 'includes/class-wpsp-functions.php' );
						
						if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
							include_once( WPSP_ABSPATH . 'includes/EDD_SL_Plugin_Updater.php' );
						}
            
            $this->set_class_objects();
            
            if ($this->is_request('admin')) {
                include_once( WPSP_ABSPATH . 'includes/admin/class-wpsp-admin.php' );
            }

            if ($this->is_request('frontend')) {
                include_once( WPSP_ABSPATH . 'includes/frontend/class-wpsp-frontend.php' );
                include_once( WPSP_ABSPATH . 'template/class-template-functions.php' );
            }
        }
        
        /**
         * Set all class objects
         */
        private function set_class_objects(){
            
            $this->cron      = new WPSP_Cron();
            $this->ajax      = new WPSP_Ajax();
            $this->emails    = new WPSP_Emails();
            $this->functions = new WPSP_Functions();
        }

    }

endif;

$GLOBALS['wpsupportplus'] = WP_Support_Plus::instance();
