<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Template_Functions' ) ) :
    
    class WPSP_Template_Functions {
    
        /**
         * Print header
         */
        public function get_header(){
            include( WPSP_ABSPATH . 'template/header/header.php' );
        }
        
        /**
         * User information or login form in header
         */
        public function get_userinfo(){
            include( WPSP_ABSPATH . 'template/header/user_info.php' );
        }
        
        /**
         * Print footer
         */
        public function get_footer(){
            include( WPSP_ABSPATH . 'template/footer/footer.php' );
        }
        
        /**
         * Print JS data
         */
        public function js_data(){
            include( WPSP_ABSPATH . 'template/js_data.php' );
        }
        
        /**
         * Return if guest ticket is allowed
         */
        public function is_guest_tickets_allowed(){
            global $wpsupportplus, $wpdb;
            return $wpsupportplus->functions->is_allow_guest_ticket();
        }
        
        /**
         * Default page for support
         */
        public function default_support_page(){
            return apply_filters('wpsp_default_support_page','tickets');
        }
    
    }
    
endif;