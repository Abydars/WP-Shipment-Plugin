<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Pipe_Install' ) ) :

    /**
     * WPSP installation and updating class
     */
    class WPSP_Pipe_Install {

        /**
         * Constructor
         */
        public function __construct() {

            register_activation_hook( WPSP_EP_PLUGIN_FILE, array($this,'install') );
            register_deactivation_hook( WPSP_EP_PLUGIN_FILE, array($this,'deactivate') );
            $this->check_version();
        }

        /**
         * Check version of WPSP
         */
        private function check_version(){

            $installed_version = get_option( 'wpsp_email_pipe_version' );
            if( $installed_version != WPSP_PIPE_VERSION ){
                $this->install();
            }

            // last version where upgrade check done
            $upgrade_version = get_option( 'wpsp_ep_upgrade_version' );
            if( $upgrade_version != WPSP_PIPE_VERSION ){
                $this->upgrade();
                update_option( 'wpsp_ep_upgrade_version', WPSP_PIPE_VERSION );
            }

        }

        /**
         * Install WPSP
         */
        function install(){

            $this->create_tables();
            update_option( 'wpsp_email_pipe_version', WPSP_PIPE_VERSION );
        }

        /**
         * Deactivate WPSP actions
         */
        public function deactivate() {

        }

        /**
         * Create tables for WPSP
         */
        function create_tables(){

        }


        /**
         * Upgrade process begin
         */
        function upgrade(){

            $upgrade_version = get_option( 'wpsp_ep_upgrade_version' ) ? get_option( 'wpsp_ep_upgrade_version' ) : 0;

            //Version 2.0.0
            if( $upgrade_version < '2.0.0' ){

							$email_piping                   = get_option('wpsp_email_pipe_settings');
							$imap_connections               = get_option('wpsp_ep_imap_connections');
							$imap_pipe_list                 = get_option('wpsp_imap_pipe_list');
						  $ignore_incoming_emails         = get_option( 'wpsp_email_pipe_ignore_incomming_email' );
							$ignore_incoming_emails_subject = get_option( 'wpsp_email_pipe_ignore_incomming_email_subject');

							if( $email_piping === false ){
								$email_piping = array(
									'block_emails'        => $ignore_incoming_emails ? $ignore_incoming_emails : '',
									'ignore_email_subject'=> $ignore_incoming_emails_subject ? $ignore_incoming_emails_subject : '',
									'allowed_emails'      => 1,
									'gmail_client_secret' => '',
									'email_categories'    => array()
								);
							}

							if( $imap_connections === false ){
								$imap_connections = array();
							}

							if( $imap_pipe_list !== false ){
								foreach ($imap_pipe_list as $connection) {
									$imap_connections[] = array(
										'email'       => $connection['pipe_email'],
										'encryption'  => $connection['imap_encryption'],
										'mail_server' => $connection['imap_server'],
										'server_port' => $connection['imap_port'],
										'username'    => $connection['imap_username'],
										'password'    => $connection['imap_password'],
										'last_uid'    => $connection['last_mail_id']['id']
									);
									$email_piping['email_categories'][$connection['pipe_email']] = $connection['pipe_category'];
								}
							}

							update_option('wpsp_email_pipe_settings',$email_piping);
							update_option('wpsp_ep_imap_connections',$imap_connections);

            }

        }


    }

endif;

new WPSP_Pipe_Install();
