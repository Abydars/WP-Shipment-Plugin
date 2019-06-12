<?php
/**
 * Plugin Name: WP Support Plus Email Notifications
 * Plugin URI: https://www.wpsupportplus.com/
 * Description: Send email notifications for  WP Support Plus
 * Version: 1.0.6
 * Author: Pradeep Makone
 * Author URI: http://profiles.wordpress.org/pradeepmakone07/
 * Text Domain: wpsp-en
 * Domain Path: /lang
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( class_exists('WP_Support_Plus') ) {
    $GLOBALS['WPSP_EN'] = new WPSP_EN();
}

final class WPSP_EN {

    /*
     * Constructor
     */
    public function __construct() {

				$this->define_constants();
        add_action( 'init', array($this,'load_textdomain') );
        $this->include_files();

				//plugin updator
        add_action('admin_init',array($this,'plugin_updator'));

    }

    function define_constants(){

				define( 'WPSP_EN_PLUGIN_FILE', __FILE__ );
				define( 'WPSP_EN_URL', plugin_dir_url( __FILE__ ) );
        define( 'WPSP_EN_DIR', plugin_dir_path( __FILE__ ) );
        define( 'WPSP_EN_VERSION', '1.0.6');
				define( 'WPSP_EN_STORE_ID', '16285' );

    }

    /*
     * Textdomain loaded for this customization.
     */
	 function load_textdomain(){

 			 $locale = apply_filters( 'plugin_locale', get_locale(), 'wpsp-en' );
 			 load_textdomain( 'wpsp-en', WP_LANG_DIR . '/wpsp/wpsp-en-' . $locale . '.mo' );
 			 load_plugin_textdomain( 'wpsp-en', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
 	 	}

    function include_files(){

        include_once( WPSP_EN_DIR . 'includes/class-wpsp-install.php' );
				include( WPSP_EN_DIR.'includes/admin/admin.php' );
        include( WPSP_EN_DIR.'includes/frontend.php' );
				include( WPSP_EN_DIR.'includes/class-process-wpsp-emails.php' );

        $backend  = new WPSP_EN_Backend();
        $frontend = new WPSP_EN_Frontend();

				if (is_admin()) {

					add_action( 'admin_init', array($backend, 'wpsp_en_actions') );
					add_action( 'admin_enqueue_scripts', array($backend, 'loadScripts') );
					add_filter( 'wpsp_addon_submenus_sections', array( $backend, 'add_submenus_section') );
					add_action( 'wpsp_setting_update', array( $backend, 'setting_update') );

					// AJAX Calls
					add_action( 'wp_ajax_wpsp_en_get_field_options', array( $backend, 'get_field_options') );

					// License related actions
					add_filter( 'wpsp_addon_count', array( $backend, 'wpsp_addon_count') );
					add_action( 'wpsp_addon_license_setting', array( $backend, 'license_setting') );
					add_action( 'wpsp_setting_update', array( $backend, 'license_setting_update') );

        }

				// Email actions
				add_action( 'wpsp_after_create_ticket', array( $frontend, 'create_new_ticket' ), 99, 1 );
				add_action( 'wpsp_after_ticket_reply', array( $frontend, 'reply_ticket' ), 99, 2 );
				add_action( 'wpsp_after_change_ticket_status', array( $frontend, 'change_status' ), 99, 1 );
				add_action( 'wpsp_after_change_assign_agent', array( $frontend, 'assign_agent' ), 99, 3 );
				add_action( 'wpsp_after_delete_ticket', array( $frontend, 'delete_ticket' ), 99, 1 );
				add_action( 'wpsp_after_ticket_add_note',array( $frontend, 'add_note' ), 99, 2 );

    }

		function plugin_updator(){

				$license_key = get_option( 'wpsp_license_key_email_notification' );
				if ($license_key) {
					$edd_updater = new EDD_SL_Plugin_Updater( WPSP_STORE_URL, __FILE__, array(
									'version' => WPSP_EN_VERSION,
									'license' => $license_key,
									'item_id' => WPSP_EN_STORE_ID,
									'author'  => 'Pradeep Makone',
									'url'     => site_url()
					) );
				}

    }

}
?>
