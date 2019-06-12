<?php
/**
 * Plugin Name: WP Support Plus Email Pipe
 * Plugin URI: https://www.wpsupportplus.com/email-piping/
 * Description: Email Piping add-on for WP Support Plus
 * Version: 2.0.6
 * Author: Pradeep Makone
 * Author URI: http://profiles.wordpress.org/pradeepmakone07/
 * Text Domain: wpsp_emailpipe
 * Domain Path: /lang
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class WPSupportPlusEmailPipe{

    public function __construct() {

        $this->define_constants();
        add_action('init', array($this, 'load_textdomain'));
        $this->include_files();

        /*
         * Cron setup
         */
        add_filter('cron_schedules',array( $this, 'wpsp_cron_schedule'));
        if (!wp_next_scheduled('wpsp_imap_pipe_loader')) {
            wp_schedule_event(time(), 'wpsp1min', 'wpsp_imap_pipe_loader');
        }

        /*
         * Pipe actions
         */
        include( WPSP_PIPE_PLUGIN_DIR.'includes/class-pipe-cron.php' );
        $cron=new WPSPPipeCron();
        add_action( 'wpsp_imap_pipe_loader', array( $cron, 'pipe'));

				//remove piped emails from email notification senders array
				add_filter( 'wpsp_en_mailer_emails', array( $this, 'remove_piped_en' ), 10, 2 );
				
				//plugin updator
        add_action('admin_init',array($this,'plugin_updator'));

    }

    private function define_constants() {
        define( 'WPSP_EP_PLUGIN_FILE', __FILE__ );
				define( 'WPSP_PIPE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        define( 'WPSP_PIPE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        define( 'WPSP_PIPE_VERSION', '2.0.6' );
				define( 'WPSP_EP_STRORE_ID', '41' );
    }

    function load_textdomain(){
        $locale = apply_filters( 'plugin_locale', get_locale(), 'wpsp_emailpipe' );
        load_textdomain( 'wpsp_emailpipe', WP_LANG_DIR . '/wpsp/wpsp_emailpipe-' . $locale . '.mo' );
        load_plugin_textdomain( 'wpsp_emailpipe', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );
    }

    function include_files(){

        include_once( WPSP_PIPE_PLUGIN_DIR . 'includes/class-wpsp-install.php' );
        include_once( WPSP_PIPE_PLUGIN_DIR . 'includes/class-wpsp-ajax.php' );
        if ($this->is_request('admin')) {
            include_once( WPSP_PIPE_PLUGIN_DIR . 'includes/class-wpsp-admin.php' );
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

    function wpsp_cron_schedule($schedules){
        if(!isset($schedules["wpsp1min"])){
            $schedules["wpsp1min"] = array(
                'interval' => 1*60,
                'display' => __('Once every minute')
            );
        }
        return $schedules;
    }

		public function remove_piped_en( $emails, $mailer ){

			$imap_connections  = get_option('wpsp_ep_imap_connections');
			$gmail_connections = get_option('wpsp_ep_gmail_connections');

      if (is_array($imap_connections)){
				foreach ($imap_connections as $connection){
					if (in_array($connection['email'], $emails)) {
		        unset($emails[array_search($connection['email_address'],$emails)]);
		      }
				}
     }
		 
		 if (is_array($gmail_connections)){
				foreach ($gmail_connections as $connection){
					if (in_array($connection['email_address'], $emails)) {
		        unset($emails[array_search($connection['email_address'],$emails)]);
		      }
				}
     }
			return $emails;

		}
		
		function plugin_updator(){
				
				$license_key = get_option( 'wpsp_license_key_emailpipe' );
				if ($license_key) {
					$edd_updater = new EDD_SL_Plugin_Updater( WPSP_STORE_URL, __FILE__, array(
									'version' => WPSP_PIPE_VERSION,
									'license' => $license_key,
									'item_id' => WPSP_EP_STRORE_ID,
									'author'  => 'Pradeep Makone',
									'url'     => site_url()
					) );
				}
				
    }

}

if(class_exists('WP_Support_Plus')){
    $GLOBALS['WPSupportPlusEmailPipe'] =new WPSupportPlusEmailPipe();
}
?>
