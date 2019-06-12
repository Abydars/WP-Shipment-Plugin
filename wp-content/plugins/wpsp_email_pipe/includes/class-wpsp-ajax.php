<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_EP_Ajax' ) ) :

    /**
     * Ajax class for WPSP.
     * @class WPSP_EP_Ajax
     */
    class WPSP_EP_Ajax {

        /**
         * Constructor
         */
        public function __construct(){

            $ajax_events = array(
                'upload_google_app_config'      => false,
                'delete_gmail_pipe_connection'  => false,
								'add_imap'  										=> false,
								'delete_imap_pipe_connection'   => false,
            );

            foreach ($ajax_events as $ajax_event => $nopriv) {
                add_action('wp_ajax_wpsp_ep_' . $ajax_event, array($this, $ajax_event));
                if ($nopriv) {
                    add_action('wp_ajax_nopriv_wpsp_ep_' . $ajax_event, array($this, $ajax_event));
                }
            }
        }

        /**
         * Upload google config
         */
        public function upload_google_app_config(){

            include  WPSP_PIPE_PLUGIN_DIR . 'includes/ajax/upload_google_app_config.php';
            die();
        }

        /**
         * Delete gmail pipe connection
         */
        public function delete_gmail_pipe_connection(){

            include  WPSP_PIPE_PLUGIN_DIR . 'includes/ajax/delete_gmail_pipe_connection.php';
            die();
        }

				public function add_imap(){
					include  WPSP_PIPE_PLUGIN_DIR . 'includes/ajax/set_add_imap.php';
					die();
				}

				public function delete_imap_pipe_connection(){
					include  WPSP_PIPE_PLUGIN_DIR . 'includes/ajax/delete_imap_pipe_connection.php';
					die();
				}


    }

endif;

new WPSP_EP_Ajax();
