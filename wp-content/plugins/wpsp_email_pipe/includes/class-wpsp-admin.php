<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Email_Pipe_Admin' ) ) :
    
    class WPSP_Email_Pipe_Admin {

        /**
         * Constructor
         */
        public function __construct() {
            
            add_action( 'admin_init', array( $this, 'add_pipe_connection') );
            add_action( 'admin_enqueue_scripts', array( $this, 'loadScripts') );
            add_filter( 'wpsp_addon_submenus_sections', array( $this, 'add_pipe_section') );
						
						// License related actions
            add_filter( 'wpsp_addon_count', array( $this, 'wpsp_addon_count') );
						add_action( 'wpsp_addon_license_setting', array( $this, 'license_setting') );
						add_action( 'wpsp_setting_update', array( $this, 'setting_update') );
        }
        
        /**
         * Load admin style and script
         */
        public function loadScripts(){
            
            if( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'wp-support-plus' ) :
                wp_enqueue_script( 'wpsp-ep-admin', WPSP_PIPE_PLUGIN_URL.'asset/js/admin.js?version='.WPSP_PIPE_VERSION );
                wp_enqueue_style ( 'wpsp-ep-admin-css', WPSP_PIPE_PLUGIN_URL . 'asset/css/admin.css?version='.WPSP_PIPE_VERSION );
                
            endif;
        }
        
        /**
         * Adds new pipe section under add-on which will serve settings for email piping there.
         */
        public function add_pipe_section($addon_sections){
            
            $addon_sections['email_piping'] = array(
                'label' => __('Email Piping','wpsp_emailpipe'),
                'file'  => WPSP_PIPE_PLUGIN_DIR . 'includes/admin/email_piping.php'
            );
            return $addon_sections;
        }
        
        /**
         * Add pipe connection
         */
        public function add_pipe_connection(){
            
            if( isset($_GET['action']) && $_GET['action']=='add_gmail_connection' ){
                include WPSP_PIPE_PLUGIN_DIR . 'includes/admin/gmail_connection.php';
            }
            
        }
				
				public function wpsp_addon_count($addon_count){
					
						return ++$addon_count;
					
				}
				
				public function license_setting(){
						
						include WPSP_PIPE_PLUGIN_DIR . 'includes/admin/license_setting.php';
						
				}
        
        public function setting_update(){
            
            $setting = sanitize_text_field( $_REQUEST['update_setting'] );
            
						if ( $setting === 'settings_email_piping' ){
                update_option( 'wpsp_email_pipe_settings', $_POST['email_piping'] );
            }
						
						if ( $setting === 'settings_addon_licenses' ){
                include WPSP_PIPE_PLUGIN_DIR . 'includes/admin/license_update.php';
            }
						
        }


    }
    
endif;


new WPSP_Email_Pipe_Admin();
