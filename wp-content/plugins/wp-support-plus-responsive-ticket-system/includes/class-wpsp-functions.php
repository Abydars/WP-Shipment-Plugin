<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Functions' ) ) :

    /**
     * Functions class for WPSP.
     * @class WPSP_Functions
     */
    class WPSP_Functions {

        /**
         * General Settings
         */
        public $settings_general = null;

        /**
         * Support Button settings
         */
        public $support_btn = null;

      
				/**
				* Default filters for ticket list
				*/
				public $default_filters = null;
				/**
				 * Default Widgets 
				 */
				 public $default_widget = null;
			/**
         * Agent settings
         */
        public $agent_settings = null;

        /**
         * Supervisor categories if current user is supervisor
         */
        public $supervisor_categories = array();

        /*
         * Custom CSS Setting
         */
        public $custom_css = null;

        /*
         * Footer Text Setting
         */
        public $footer_text=null;

        /*8
         * Thank You Page Setting
         */
        public $thank_you_page = null;

        /**
         * Dashbord General
         */
        public $dashbord_general = null;

        public $general_advanced_settings= null;
        /**
         * Constructor
         */
        public function __construct(){

            $this->load_settings();

        }
        
        public $custom_login = null; 
				
				/**
         * Load settings
         */
        public function load_settings(){
            $this->settings_general     = get_option('wpsp_settings_general');
            $this->support_btn          = get_option('wpsp_settings_support_btn');
            $this->default_filters      = get_option('wpsp_ticket_list_default_filters');
						$this->default_widget				= get_option('wpsp_ticket_widget_order');
            $this->agent_settings       = get_option('wpsp_agent_settings');
            $this->custom_css           = get_option('wpsp_custom_css');
            $this->footer_text          = get_option('wpsp_text_footer');
            $this->thank_you_page       = get_option('wpsp_thank_you_page');
            $this->dashbord_general     = get_option('wpsp_dashbord_general');
						$this->advanced_settings    = get_option('wpsp_ticket_list_advanced_settings');
						$this->general_settings_advanced    = get_option('wpsp_general_settings_advanced');

        }

        /**
         * Return WP pages array ID => Name
         */
        public function get_wp_page_list(){

            $args = array(
                'sort_order' => 'asc',
                'sort_column' => 'post_title',
                'post_type' => 'page',
                'post_status' => 'publish'
            );
            $pages = get_pages( $args );

            return $pages;
        }

        /**
         * Return WPSP Categories database results
         */
        public function get_wpsp_categories(){

            global $wpdb;
            $results = $wpdb->get_results( "select * from {$wpdb->prefix}wpsp_catagories ORDER BY load_order" );

            return $results;
        }

        /**
         * Return WPSP statuses database results
         */
        public function get_wpsp_statuses(){

            global $wpdb;
            $results = $wpdb->get_results( "select * from {$wpdb->prefix}wpsp_custom_status ORDER BY load_order" );

            return $results;
        }

        /**
         * Return WPSP priorities database results
         */
        public function get_wpsp_priorities(){

            global $wpdb;
            $results = $wpdb->get_results( "select * from {$wpdb->prefix}wpsp_custom_priority ORDER BY load_order" );

            return $results;
        }

        /**
         * Returns support page id
         */
        public function get_support_page_id(){

            if( $this->settings_general && isset($this->settings_general['support_page']) ) {
								//fix for WPML plugin
								$page_id = apply_filters( 'wpml_object_id', $this->settings_general['support_page'], 'post', true );
                return $page_id;
            } else {
                return 0;
            }
        }
				
				public function get_role_theme_integration(){
						if( $this->settings_general && isset($this->settings_general['theme_integration']) ) {
								return $this->settings_general['theme_integration'];
						} else {
								
								$theme_integration = array(
									'customer'      => 1,
									'agent'         => 1,
									'supervisor'    => 1,
									'administrator' => 1
								);
								return $theme_integration;
						}
				}
				
				public function load_bootstrap(){
						if( $this->settings_general && isset($this->settings_general['load_bootstrap']) ) {
								return $this->settings_general['load_bootstrap'];
						} else {
								
								return 1;
						}
				}

        /**
         * Return support page URL
         */
        public function get_support_page_url( $args = array() ){

            $support_page_id = $this->get_support_page_id();
          
            if( $support_page_id ){
                
								$url = get_permalink( $support_page_id );
								if ( $args ) {
									$arguments = array();
									foreach ($args as $key => $value) {
										$arguments[] = $key.'='.$value;
									}
									$arguments = implode('&', $arguments);
									$url = preg_match('/\?/', $url) ? $url.'&'.$arguments : $url.'?'.$arguments;
								}
								return $url;
							
            } else {
                return get_home_url();
            }
        }

        /**
         * Returns default category
         */
        public function get_default_category(){

            if( $this->settings_general && isset($this->settings_general['default_category']) ) {

                return $this->settings_general['default_category'];
            } else {
                return 1;
            }
        }

        /**
         * Returns default status
         */
        public function get_default_status(){

            if( $this->settings_general && isset($this->settings_general['default_status']) ) {

                return $this->settings_general['default_status'];
            } else {
                return 1;
            }
        }

        /**
         * Returns default priority
         */
        public function get_default_priority(){

            if( $this->settings_general && isset($this->settings_general['default_priority']) ) {

                return $this->settings_general['default_priority'];
            } else {
                return 1;
            }
        }

        /**
         * Return customer reply status
         */
        public function get_customer_reply_status() {

            if( $this->settings_general && isset($this->settings_general['customer_reply_status']) ) {

                return $this->settings_general['customer_reply_status'];
            } else {
                return '';
            }
        }

        /**
         * Return true or false whether Close Button allowed or not for customer
         */
        public function is_close_btn_allowed() {

            if( $this->settings_general && isset($this->settings_general['allow_cust_close']) ) {
                return true;
            } else {
                return false;
            }
        }

        /**
         * Return support button settings
         */
        public function get_support_btn_settings(){

            $support_btn_settings = array();
						
						$support_btn_settings['allow_support_btn']  = $this->support_btn && isset($this->support_btn['allow_support_btn']) ? $this->support_btn['allow_support_btn'] : '1';
            $support_btn_settings['support_btn_label']  = $this->support_btn && isset($this->support_btn['support_btn_label']) ? $this->support_btn['support_btn_label'] : 'Help-Desk';
						$support_btn_settings['img_url']						= $this->support_btn && isset($this->support_btn['img_url'])? $this->support_btn['img_url'] : WPSP_PLUGIN_URL.'asset/images/icons/agent.png';
						
            return apply_filters('wpsp_get_support_btn_settings' , $support_btn_settings);

        }

        /**
         * Return default filter settings for ticket list
         */
        public function get_default_filters(){

            $default_filters = array(
                'customer_hide_statuses'        => array(),
                'customer_orderby'              => 't.update_time',
                'customer_orderby_order'        => 'ASC',
                'customer_par_page_tickets'     => 20,
                'agent_hide_statuses'           => array(),
                'agent_orderby'                 => 't.update_time',
                'agent_orderby_order'           => 'ASC',
                'agent_par_page_tickets'        => 20,
								'ticket_list_filter'						=> 1
            );
            if($this->default_filters){
                $default_filters = $this->default_filters;
            }
            return $default_filters;
        }

				/**
				 * Return default widget settings 
				 */
				public function get_default_ticket_widget(){
					
					$default_widget= array(
						'ticket-status' 			=> 'Ticket Status',
						'raised-by' 					=> 'Raised By',
						'assigned-agent'			=> 'Assigned Agents',
						'agent-only-fields'  	=> 'Agent Only Fields',
						'ticket-fields'			  => 'Ticket Fields'
					
					);
					
					if($this->default_widget){
						$default_widget = $this->default_widget;
						}
						return $default_widget;
				}
				/**
         * Return Dashbord General
         */

        public function get_dashbord_general(){

            $dashbord_settings = array(
                'statuses'        => array(1,2,3)
             );
	            if($this->dashbord_general){
	                $dashbord_settings = $this->dashbord_general;
	            }
            return $dashbord_settings;
        }
				
				/**
	 			* Return Allow Customer to view Dashboard page
	 			*/
	 		 public function is_allow_to_view_customer_dashboard_page() {
			 		
					if( $this->dashbord_general && isset($this->dashbord_general['allow_to_view_customer']) ) {
								 return $this->dashbord_general['allow_to_view_customer'];
					 } else {
							 return false;
					 }
	 		 }
	 		 
				/**
				 * Return  Allow Agent to view Dashboard page
				 */
				public function is_allow_to_view_agent_dashboard_page() {

						if( $this->dashbord_general && isset($this->dashbord_general['allow_to_view_agent']) ) {
								return $this->dashbord_general['allow_to_view_agent'];
						} else {
								return false;
						}
				}
				
				/**
				 * Return Allow Supervisor to view Dashboard page
				 */
				public function is_allow_to_view_supervisor_dashboard_page() {

						if( $this->dashbord_general && isset($this->dashbord_general['allow_to_view_supervisor']) ) {
								return $this->dashbord_general['allow_to_view_supervisor'];
						} else {
								return false;
						}
				}
				
				/**
				 * Return Allow Supervisor to view Dashboard page
				 */
				public function is_allow_to_view_administrator_dashboard_page() {

						if( $this->dashbord_general && isset($this->dashbord_general['allow_to_view_administrator']) ) {
								return $this->dashbord_general['allow_to_view_administrator'];
						} else {
								return false;
						}
				}


        /**
         * Return agent settings
         */
        public function get_agent_settings(){

            $agent_settings = array(
                'agent_allow_assign_ticket'                             => 0,
                'agent_allow_delete_ticket'                             => 0,
                'agent_allow_change_raised_by'                          => 0,
                'supervisor_allow_delete_ticket'                        => 0,
                'supervisor_allow_change_raised_by'                     => 0
            );
            if($this->agent_settings){
                $agent_settings = $this->agent_settings;
            }
            return $agent_settings;
        }

        /**
         * Return close button status
         */
        public function get_close_btn_status() {

            if( $this->settings_general && isset($this->settings_general['close_ticket_status']) ) {

                return $this->settings_general['close_ticket_status'];
            } else {
                return '';
            }
        }

        /**
         * Return ticket id sequence
         */
        public function get_ticket_id_sequence(){

            if( $this->settings_general && isset($this->settings_general['ticket_id_sequence']) ) {
                return $this->settings_general['ticket_id_sequence'];
            } else {
                return 1;
            }
        }
				
				public function get_default_login(){

            if( $this->settings_general && isset($this->settings_general['default_login']) ) {
                return $this->settings_general['default_login'];
            } else {
                return 1;
            }
        }
				
				/**
				 * Return true or false whether Close Button allowed or not for customer
				 */
				public function is_enable_default_login() {

						if( $this->settings_general && isset($this->settings_general['enable_default_login']) ) {
								  return $this->settings_general['enable_default_login'];
						} else {
								return false;
						}
				}


        /**
         * Get date format
         */
        public function get_date_format() {

            if( $this->settings_general && isset($this->settings_general['date_format']) ) {
                return $this->settings_general['date_format'];
            } else {
                return 'yy-mm-dd';
            }
        }

        /**
         * Get display date format
         */
        public function get_display_date_format() {

            if( $this->settings_general && isset($this->settings_general['display_date_format']) ) {
                return $this->settings_general['display_date_format'];
            } else {
                return 'Y/m/d H:i:s';
            }
        }

        /**
         * Get custom date format
         */
        public function get_custom_date_format() {

            if( $this->settings_general && isset($this->settings_general['custom_date_format']) ) {
                return $this->settings_general['custom_date_format'];
            } else {
                return 'Y/m/d';
            }
        }

        /**
         * Get display date format
         */
        public function is_open_support_page_new_tab() {

            if( $this->settings_general && isset($this->settings_general['support_btn_new_tab']) ) {
                return $this->settings_general['support_btn_new_tab'];
            } else {
                return 0;
            }
        }

        /*
         * Attachment Max Size
         */
        public function get_attachment_size(){
            if($this->settings_general && isset($this->settings_general['attachment_size'])){
                return $this->settings_general['attachment_size'] ;
            } else {
              return 20;
            }
        }


        /*
         * Allow Guest Ticket
         */
        public function is_allow_guest_ticket(){

            if($this->settings_general && isset($this->settings_general['allow_guest_ticket'])){
                return $this->settings_general['allow_guest_ticket'];
            } else{
                return 1;
            }

        }

        /*
         * Allow staff to read all tickets
         */
        public function is_allow_staff_read_all_ticket(){

            if($this->settings_general && isset($this->settings_general['staff_read_all_ticket'])){
                return $this->settings_general['staff_read_all_ticket'];
            } else{
                return 0;
            }

        }

				/**
	 			* Return Allow to view Customer advance support page
	 			*/
	 		 public function is_allow_to_view_customer_support_page() {
			 		
					if( $this->settings_general && isset($this->settings_general['allow_to_view_customer']) ) {
								 return $this->settings_general['allow_to_view_customer'];
					 } else {
							 return false;
					 }
	 		 }
	 		 
				/**
				 * Return Allow to view Agent advance support page
				 */
				public function is_allow_to_view_agent_support_page() {

						if( $this->settings_general && isset($this->settings_general['allow_to_view_agent']) ) {
								return $this->settings_general['allow_to_view_agent'];
						} else {
								return false;
						}
				}
				
				/**
				 * Return Allow to view Supervisor advance support page
				 */
				public function is_allow_to_view_supervisor_support_page() {

						if( $this->settings_general && isset($this->settings_general['allow_to_view_supervisor']) ) {
								return $this->settings_general['allow_to_view_supervisor'];
						} else {
								return false;
						}
				}
				
       /**
         * Return Agent reply status
         */
        public function get_agent_reply_status() {

            if( $this->settings_general && isset($this->settings_general['agent_reply_status']) ) {

                return $this->settings_general['agent_reply_status'];
            } else {
                return '';
            }
        }

        /**
         * Reply Form Position
         */
        public function get_reply_form_position(){

            if($this->settings_general && isset($this->settings_general['reply_form_position'])){
                return $this->settings_general['reply_form_position'];
            } else{
                return 1;
            }
        }

				/**
				*  upload logo settings
				*/
			 public function get_upload_logo(){

					 if($this->settings_general && isset($this->settings_general['company_logo'])){
							 return $this->settings_general['company_logo'];
					 } else{
							 return WPSP_PLUGIN_URL . 'asset/images/default_company_logo.png';
					 }
			 }
			 
			 
			 /**
				 * Ticket Lable
				 */
			 public function get_ticket_lable(){

						if($this->settings_general && isset($this->settings_general['ticket_lable'])){
								return $this->settings_general['ticket_lable'];
						} else{
								return 'Ticket';
						}
				}
				
				
			 /**
         * Ticket ID Prefix
         */
      	public function get_ticket_id_prefix(){

            if($this->settings_general && isset($this->settings_general['ticket_id_prefix'])){
                return $this->settings_general['ticket_id_prefix'];
            } else{
                return '#';
            }
        }
       
			 
			  /**
         * Custom CSS Setting
         */
        public function get_custom_css(){

            if($this->custom_css && isset($this->custom_css['custom_css'])){
                return $this->custom_css['custom_css'];
            } else {
                return '';
            }
        }
				
				/**
         * Theme Integration Setting
         */
				public function get_theme_intrgration(){

            if( $this->custom_css && isset($this->custom_css['theme_intrgration']) ) {

                return $this->custom_css['theme_intrgration'];
            } else {
                return '';
            }
        }

        /*
         * Footer text
         */
        public function get_footer_text(){
            if($this->footer_text && isset($this->footer_text)){
                return $this->footer_text;
            } else {
                return '';
            }
        }

        /**
         * Thank You Page Title
         */
        public function get_thank_you_page_title(){
						if($this->thank_you_page && isset($this->thank_you_page['title'])){
								return $this->thank_you_page['title'];
						} else {
								return '';
						}
        }

        /**
         * Thank You Page Body
         */
        public function get_thank_you_page_body(){
						if($this->thank_you_page && isset($this->thank_you_page['body'])){
								return $this->thank_you_page['body'];
						} else {

							$thank_you = '<p>Dear {customer_name},</p><p>Thank you for creating ticket. Our agent will shortly look after this and get back as soon as possible.</p>';
							return $thank_you;
						}
        }
				
				/**
				* Guest Ticket Redirect
				*/
			 public function get_guest_ticket_redirect(){
				
						if( $this->thank_you_page && isset($this->thank_you_page['guest_ticket_redirect']) ) {
								return true;
						} else {
								return false;
						}
			 }

     
			 /**
 				* Guest Ticket Redirect URL
 				*/
 			 public function get_guest_ticket_redirect_url(){

 					 if($this->thank_you_page && isset($this->thank_you_page['guest_ticket_redirect_url'])){
 							 return $this->thank_you_page['guest_ticket_redirect_url'];
 					 } else{
 							 return '';
 					 }
 			 }


        /**
         * Print custom field template tags
         */
        public function print_custom_fields_template_tags(){

            include WPSP_ABSPATH . 'includes/functions/print_custom_fields_template_tags.php';
        }

        /**
         * Replace templates tag
         */
        public function replace_template_tags( $str, $ticket ){

            include WPSP_ABSPATH . 'includes/functions/replace_template_tags.php';
            return $str;
        }

        /**
         * Print category action icons
         */
        public function get_category_actions( $category ){

            $nonce = wp_create_nonce($category->id);
            ?>
            <span class="dashicons dashicons-edit wpsp_pointer" onclick="wpsp_admin_load_popup('get_edit_category',<?php echo $category->id?>,'<?php echo $nonce?>','<?php _e('Edit Category','wp-support-plus-responsive-ticket-system')?>', 300, 400, 100)"></span>
            <?php
            if( $category->id != 1 ){
                echo '&nbsp;&nbsp;';
                ?>
                <span class="dashicons dashicons-trash wpsp_pointer" onclick="wpsp_admin_load_popup('get_delete_category',<?php echo $category->id?>,'<?php echo $nonce?>','<?php _e('Delete Category','wp-support-plus-responsive-ticket-system')?>', 300, 200, 63)"></span>
                <?php
            }
        }

        /**
         * Sanitize integer array
         */
        public function sanitize_integer_array($input_arr){

            $int_array = array();
            foreach($input_arr as $key => $val){
                if (is_array($val)) {
										$int_array[$key] = $this->sanitize_integer_array($val);
								} else {
										$int_array[$key] = sanitize_text_field(intval($val));
								}
            }
            return $int_array;
        }

        /**
         * Sanitize string array
         */
        public function sanitize_string_array($input_arr){

            $str_array = array();
						foreach($input_arr as $key => $val){
        			if (is_array($val)) {
									$str_array[$key] = $this->sanitize_string_array($val);
							} else {
									$str_array[$key] = sanitize_text_field($val);
							}
				    }
						return $str_array;
        }
				
				/**
				 * Sanitize string Area array
				 */
				public function sanitize_string_area_array($input_arr){
            
						$str_array = array();
						foreach($input_arr as $key => $val){
							if (is_array($val)) {
									$str_array[$key] = $this->sanitize_string_area_array($val);
							} else {
									$str_array[$key] = sanitize_textarea_field($val);
							}
						}
						return $str_array;
				}

        /**
         * Print status action icons
         */
        public function get_status_actions($status){

            $nonce = wp_create_nonce($status->id);
            ?>
            <span class="dashicons dashicons-edit wpsp_pointer" onclick="wpsp_admin_load_popup('get_edit_status',<?php echo $status->id?>,'<?php echo $nonce?>','<?php _e('Edit Status','wp-support-plus-responsive-ticket-system')?>', 300, 200, 63)"></span>
            <?php
            if( $status->id != 1 ){
                echo '&nbsp;&nbsp;';
                ?>
                <span class="dashicons dashicons-trash wpsp_pointer" onclick="wpsp_admin_load_popup('get_delete_status',<?php echo $status->id?>,'<?php echo $nonce?>','<?php _e('Delete Status','wp-support-plus-responsive-ticket-system')?>', 300, 200, 63)"></span>
                <?php
            }
        }

        /**
         * Print priority action icons
         */
        public function get_priority_actions($priority){

            $nonce = wp_create_nonce($priority->id);
            ?>
            <span class="dashicons dashicons-edit wpsp_pointer" onclick="wpsp_admin_load_popup('get_edit_priority',<?php echo $priority->id?>,'<?php echo $nonce?>','<?php _e('Edit Priority','wp-support-plus-responsive-ticket-system')?>', 300, 200, 63)"></span>
            <?php
            if( $priority->id != 1 ){
                echo '&nbsp;&nbsp;';
                ?>
                <span class="dashicons dashicons-trash wpsp_pointer" onclick="wpsp_admin_load_popup('get_delete_priority',<?php echo $priority->id?>,'<?php echo $nonce?>','<?php _e('Delete Priority','wp-support-plus-responsive-ticket-system')?>', 300, 200, 63)"></span>
                <?php
            }
        }

        /**
         * Return custom fields array
         */
        public function get_custom_fields(){

            $custom_fields = array(
                1 => __('Text Field', 'wp-support-plus-responsive-ticket-system'),
                2 => __('Drop-Down', 'wp-support-plus-responsive-ticket-system'),
                3 => __('Checkbox', 'wp-support-plus-responsive-ticket-system'),
                4 => __('Radio Button', 'wp-support-plus-responsive-ticket-system'),
                5 => __('Textarea', 'wp-support-plus-responsive-ticket-system'),
                6 => __('Date', 'wp-support-plus-responsive-ticket-system'),
                8 => __('Attachment', 'wp-support-plus-responsive-ticket-system'),
                9 => __('URL', 'wp-support-plus-responsive-ticket-system'),
                10 => __('Email', 'wp-support-plus-responsive-ticket-system')
            );
            return apply_filters('wpsp_custom_fields', $custom_fields);
        }

        /**
         * Checks whether given custom field type can be inserted in ticket list
         */
        public function is_allowed_ticket_list($type){

            $allowed_types = apply_filters( 'wpsp_list_allowed_custom_fields', array(
                1,2,3,4,6,10
            ));

            return in_array($type, $allowed_types);
        }

        /**
         * Return Custom field type name
         */
        public function get_custom_field_type_name($type){

            $custom_fields = $this->get_custom_fields();
            return $custom_fields[$type];
        }

        /**
         * Print custom field action icons
         */
        public function get_custom_field_actions($field){

            $nonce = wp_create_nonce($field->id);
            ?>
            <span class="dashicons dashicons-edit wpsp_pointer" onclick="wpsp_admin_load_popup('get_edit_custom_field',<?php echo $field->id?>,'<?php echo $nonce?>','<?php _e('Edit Custom Field','wp-support-plus-responsive-ticket-system')?>', 600, 600, 250)"></span>&nbsp;&nbsp;
            <span class="dashicons dashicons-trash wpsp_pointer" onclick="wpsp_admin_load_popup('get_delete_custom_field',<?php echo $field->id?>,'<?php echo $nonce?>','<?php _e('Delete Custom Field','wp-support-plus-responsive-ticket-system')?>', 300, 200, 63)"></span>
            <?php
        }

        /**
         * Get category names for custom fields.
         * Called for showing name in custom field table
         */
        public function get_custom_field_category_names($field){

            global $wpdb;
            $category_names = array();
            if($field->field_categories && $field->field_categories != '0'){
                $categories = $wpdb->get_results("select name from {$wpdb->prefix}wpsp_catagories WHERE id IN({$field->field_categories}) ORDER BY load_order");
                foreach ($categories as $category){
                    $category_names[] = $category->name;
                }
                $category_names = implode(', ', $category_names);
            } else {
                $category_names = 'None';
            }
            return $category_names;
        }

        /**
         * Returns label for ticket form
         * This is used for translation support for WPML
         */
        public function get_ticket_form_label($key){
            if(is_numeric($key)){
                $custom_fields_localize = get_option('wpsp_custom_fields_localize');
                return $custom_fields_localize['label_'.$key];
            } else {
                $default_labels = array(
                    'ds' => __('Subject', 'wp-support-plus-responsive-ticket-system'),
                    'dd' => __('Description', 'wp-support-plus-responsive-ticket-system'),
                    'dc' => __('Category', 'wp-support-plus-responsive-ticket-system'),
                    'dp' => __('Priority', 'wp-support-plus-responsive-ticket-system')
                );
                return $default_labels[$key];
            }
        }

        /**
         * Return label for Ticket List column
         * This is used for translation support for WPML
         */
        public function get_ticket_list_column_label($key){
            if(is_numeric($key)){
                $custom_fields_localize = get_option('wpsp_custom_fields_localize');
								return htmlspecialchars_decode( stripslashes($custom_fields_localize['label_'.$key]));
            } else {
                $default_labels = array(
										'deleted_ticket'    => __('Trashed Tickets', 'wp-support-plus-responsive-ticket-system'),
                    'id'                => __('ID', 'wp-support-plus-responsive-ticket-system'),
                    'status'            => __('Status', 'wp-support-plus-responsive-ticket-system'),
                    'subject'           => __('Subject', 'wp-support-plus-responsive-ticket-system'),
                    'raised_by'         => __('Raised By', 'wp-support-plus-responsive-ticket-system'),
                    'user_type'         => __('User Type', 'wp-support-plus-responsive-ticket-system'),
                    'category'          => __('Category', 'wp-support-plus-responsive-ticket-system'),
                    'assigned_agent'    => __('Assigned Agent', 'wp-support-plus-responsive-ticket-system'),
                    'priority'          => __('Priority', 'wp-support-plus-responsive-ticket-system'),
                    'date_created'      => __('Date Created', 'wp-support-plus-responsive-ticket-system'),
                    'date_updated'      => __('Date Updated', 'wp-support-plus-responsive-ticket-system'),
                    'created_agent'     => __('Agent Created', 'wp-support-plus-responsive-ticket-system')										
                );
								$default_labels=apply_filters('wpsp_ticket_list_column_label', $default_labels,$key);
                return $default_labels[$key];
            }
        }

        /**
         * Return custom field type
         */
        public function get_custom_field_type($field_id){

            global $wpdb;
            $type = $wpdb->get_var( "select field_type from {$wpdb->prefix}wpsp_custom_fields where id=".$field_id );
            return $type;
        }

        /**
         * Custom field type print new line in ticket list
         */
        public function is_new_line_field_type($type){

            $new_line_field_type = apply_filters('wpsp_new_line_field_type', array( 5,8 ));
            return in_array($type, $new_line_field_type);
        }

        /**
         * Get header menu
         * This is applicable for -> 1. Support Button AND 2. Support Page header menu
         */
        public function get_header_menu(){

            $header_menu = array(

                'tickets' => array(
                    'icon'  => WPSP_PLUGIN_URL.'asset/images/icons/ticket.png',
                    'label' => __('Tickets','wp-support-plus-responsive-ticket-system')
                )
            );

            return apply_filters( 'wpsp_header_menu', $header_menu );

        }

        /**
         * return whether current user is agent or not
         */
        public function is_agent($user){
            return $user->has_cap('wpsp_agent');
        }

        /**
         * return whether current user is supervisor
         */
        public function is_supervisor($user){
            return $user->has_cap('wpsp_supervisor');
        }

        /**
         * return whether current user is administrator
         */
        public function is_administrator($user){
            return $user->has_cap('wpsp_administrator');
        }

        /**
         * return whether current user is belong to staff
         */
        public function is_staff($user){

            if( $this->is_agent($user) || $this->is_supervisor($user) || $this->is_administrator($user) ){
                return true;
            } else {
                return false;
            }
        }

        /**
         * Return filter based on current user logged in
         */
        public function get_ticket_filters(){

            global $current_user, $wpdb;

            $filters = array();
            if( $this->is_agent($current_user) || $this->is_supervisor($current_user) ){ // agent filters

                $filters = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_ticket_list_order WHERE agent_filter=1 AND field_key	!= 'deleted_ticket' ORDER BY load_order");
            } else if($this->is_administrator($current_user)) {
							
								$filters = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_ticket_list_order WHERE agent_filter=1 ORDER BY load_order");
						}else { // customer filters

                $filters = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_ticket_list_order WHERE customer_filter=1 AND field_key != 'deleted_ticket' ORDER BY load_order");
            }

            return $filters;
        }

        /**
         * Return current filter
         */
        public function get_current_ticket_filter_applied(){

            global $current_user, $wpdb;

            $default_filters = $this->get_default_filters();
            $filters = array();
            $default_orderby    = '';
            $default_order      = '';
            if( $this->is_staff($current_user) ){ // agent filters

                $filters = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_ticket_list_order WHERE agent_filter=1 ORDER BY load_order");
                $default_orderby    = $default_filters['agent_orderby'];
                $default_order      = $default_filters['agent_orderby_order'];

            } else { // customer filters

                $filters = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_ticket_list_order WHERE customer_filter=1 ORDER BY load_order");
                $default_orderby    = $default_filters['customer_orderby'];
                $default_order      = $default_filters['customer_orderby_order'];

            }

            $ticket_filter = array(
                'page'          => 1,
                's'             => '',
                'order_by'      => $default_orderby,
                'orderby_order' => $default_order
            );

            if( isset($_COOKIE['wpsp_ticket_filters']) && $_COOKIE['wpsp_ticket_filters'] ){
                $ticket_filter = json_decode(base64_decode($_COOKIE['wpsp_ticket_filters']),TRUE);
            }

            return $ticket_filter;

        }

        /**
         * Return ticket select fields
         */
        public function get_tickets_select_field_list(){

            global $wpsupportplus, $wpdb, $current_user;

            $sql        = '';
            $response   = array();

            if( $this->is_staff($current_user) ){

                $sql = "select * from {$wpdb->prefix}wpsp_ticket_list_order where agent_visible=1 ORDER BY load_order";
            } else {

                $sql = "select * from {$wpdb->prefix}wpsp_ticket_list_order where customer_visible=1 ORDER BY load_order";
            }

            $list_keys = $wpdb->get_results($sql);

            foreach ( $list_keys as $key ){

                $response[] = $key->join_match.' as field_'.$key->field_key;
            }

            $response = implode(', ', $response);

            return $response;

        }

        /**
         * return supervisor category array
         */
        public function get_supervisor_categories($user_id){

            global $wpdb, $current_user;

            $categories = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_catagories");
            $cat_arr = array();
            foreach ( $categories as $category ){
                $supervisor=array();
								if(isset($category->supervisor)){
									$supervisor = unserialize($category->supervisor);
								}
                
                if( in_array($user_id, $supervisor) ){
                    $cat_arr[] = $category->id;
                }
            }

            return $cat_arr;

        }

        /**
         * Return ticket list restrict rules
         */
        public function get_ticket_list_restrict_rules(){

            global $wpdb, $current_user,$wpsupportplus;
						$public_mode_tickets= $wpsupportplus->functions->make_ticket_as_public();
						
            $ticket_restrict_rules = array();

            if( $this->is_administrator($current_user) ){

                $ticket_restrict_rules[] = '1=1';

            }
            else if( $this->is_supervisor($current_user) ){
								if($public_mode_tickets){
									  $ticket_restrict_rules[] = '1=1';
								}else{
										$ticket_restrict_rules[] = "t.guest_email='".$current_user->user_email."'";
		                $ticket_restrict_rules[] = "t.assigned_to RLIKE '(^|,)".$current_user->ID."(,|$)'";

		                if(!$this->supervisor_categories){
		                    $this->supervisor_categories = $this->get_supervisor_categories($current_user->ID);
		                }

		                $supervisor_categories = $this->supervisor_categories;

		                if($supervisor_categories){
		                    $ticket_restrict_rules[] = 't.cat_id IN ('. implode(',', $supervisor_categories).')';
		                }
								}
                

            }
            else if( $this->is_agent($current_user) ){
								if($public_mode_tickets){
										$ticket_restrict_rules[] = '1=1';
								}else{
									$ticket_restrict_rules[] = "t.guest_email='".$current_user->user_email."'";
	                $ticket_restrict_rules[] = "t.assigned_to RLIKE '(^|,)".$current_user->ID."(,|$)'";
								}
                
            }
            else {
								$wpsp_user_session = $this->get_current_user_session();
								if( isset($wpsp_user_session) && $wpsp_user_session['type']){
										if($public_mode_tickets){
											$ticket_restrict_rules[] = '1=1';
										}else{
											$ticket_restrict_rules[] = "t.guest_email='".$wpsp_user_session['email']."'";
										}
								}else{
										$ticket_restrict_rules[] = "t.guest_email='".$wpsp_user_session['email']."'";
								}
						}

            return apply_filters( 'wpsp_ticket_restrict_rules', $ticket_restrict_rules );

        }

        /**
         * Return ticket filter table joins
         */
        public function get_ticket_filter_joins(){

            global $wpdb;

            $joins = array(

                array(
                    'type'      => 'LEFT',
                    'table'     => $wpdb->prefix.'wpsp_custom_status',
                    'char'      => 's',
                    'on'        => 't.status_id=s.id'
                ),
                array(
                    'type'      => 'LEFT',
                    'table'     => $wpdb->prefix.'wpsp_catagories',
                    'char'      => 'c',
                    'on'        => 't.cat_id=c.id'
                ),
                array(
                    'type'      => 'LEFT',
                    'table'     => $wpdb->prefix.'wpsp_custom_priority',
                    'char'      => 'p',
                    'on'        => 't.priority_id=p.id'
                )

            );

            return apply_filters( 'wpsp_ticket_filter_joins', $joins );
        }

        /**
         * return public filters array
         */
        public function get_public_filters(){

            $filters = get_option('wpsp_public_ticket_filters');
            if( $filters === false ){
                $filters = array();
            }

            return $filters;
        }

        /**
         * return private filters array
         */
        public function get_private_filters(){

            global $current_user;

            $filters = array();
            if($this->is_staff($current_user)){
                $filters = get_user_meta($current_user->ID, 'wpsp_private_ticket_filters', TRUE);
                if(!$filters){
                    $filters = array();
                }
            }

            return $filters;
        }

        /**
         * Current user session
         */
        public function get_current_user_session(){

            global $current_user;
						$wpsp_user_session = array();
						if( is_user_logged_in() ){
								$wpsp_user_session = array(
										'type'  => 1,
										'name'  => $current_user->display_name,
										'email' => $current_user->user_email
								);
								
								if (isset($_COOKIE['wpsp_user_session'])){
                    $wpsp_user_session_temp = json_decode(base64_decode($_COOKIE['wpsp_user_session']),TRUE);
                    if($wpsp_user_session_temp['email'] != $wpsp_user_session['email'] ){
                        @setcookie("wpsp_user_session", base64_encode(json_encode($wpsp_user_session)), 0, COOKIEPATH);
                    }
                } else {
                    @setcookie("wpsp_user_session", base64_encode(json_encode($wpsp_user_session)), 0, COOKIEPATH);
                }
																
						} else if (isset($_COOKIE['wpsp_user_session'])) {
								$wpsp_user_session = json_decode(base64_decode($_COOKIE['wpsp_user_session']),TRUE);
						}
						return $wpsp_user_session;

        }

        public function cu_has_cap_ticket( $ticket, $cap ){

            $flag = false;
            include WPSP_ABSPATH . 'template/tickets/open-ticket/cu_has_cap_ticket.php';
            return $flag;
        }

        public function cu_has_cap( $cap ){

            $flag = false;
            include WPSP_ABSPATH . 'template/tickets/open-ticket/cu_has_cap.php';
            return $flag;
        }

        public function tz_offset_to_hrs($offset){

            $time       = explode('.', $offset);
            $hour       = $time[0];
            $negetive   = $hour < 0 ? true : false;
            $hour       = abs($hour) < 10 ? '0'.abs($hour) : abs($hour);
            $hour       = $negetive ? '-'.$hour : '+'.$hour;

            return isset($time[1]) ? $hour.':'.($time[1]*6) : $hour.':00' ;
        }


				public function get_conditional_fields(){

						global $wpdb;

						$conditional_fields = array(
							'subject'=> array(
								'label' => __('Subject', 'wp-support-plus-responsive-ticket-system'),
								'type'  => 'text'
							),
							'status_id'=> array(
								'label' => __('Status', 'wp-support-plus-responsive-ticket-system'),
								'type'  => 'drop-down'
							),
							'cat_id'=> array(
								'label' => __('Category', 'wp-support-plus-responsive-ticket-system'),
								'type'  => 'drop-down'
							),
							'priority_id'=> array(
								'label' => __('Priority', 'wp-support-plus-responsive-ticket-system'),
								'type'  => 'drop-down'
							)
						);

						$allowed_types = $this->get_conditional_allowed_cust_field_type();

						$custom_fields = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_fields");
						foreach ($custom_fields as $field) {
							if ( isset($allowed_types[$field->field_type]) ) {
								$conditional_fields[$field->id] = array(
									'label' => $field->label,
									'type'  => $allowed_types[$field->field_type]
								);
							}
						}

						return $conditional_fields;

				}

				public function get_conditional_allowed_cust_field_type(){

					$allowed_types = apply_filters( 'wpsp_conditional_allowed_types', array(
						'1'  => 'text',
						'2'  => 'drop-down',
						'3'  => 'drop-down',
						'4'  => 'drop-down',
						'5'  => 'text',
						'9'  => 'text',
						'10' => 'text'
					));
					return $allowed_types;
				}
			
				public function get_custom_slider_menus(){
					
						global $wpdb;
						$menu=$wpdb->get_results("select * from {$wpdb->prefix}wpsp_panel_custom_menu ORDER BY load_order");
						return $menu;
				}
				
				public function get_support_page_menus(){
					
						global $wpdb;
						$menu=$wpdb->get_results("select * from {$wpdb->prefix}wpsp_support_menu ORDER BY load_order");
						return $menu;
				}
				
				public function is_license_expired( $expiry_date ){
					
						$expiry_date  = new DateTime($expiry_date);
						$current_time = new DateTime('NOW');
						$interval     = $current_time->diff($expiry_date);
						
						if ( $interval->invert == 1 ) {
								return true;
						} else {
								return false;
						}
					
				}
				
				public function get_customize_general(){
						
						$general = get_option('wpsp_customize_general');
						if( $general === false ){
								
								$general = array(
									'header'         => file_get_contents(WPSP_ABSPATH . 'includes/admin/customize/css/header.css'),
									'secondery_menu' => file_get_contents(WPSP_ABSPATH . 'includes/admin/customize/css/secondery_menu.css'),
									'body'           => file_get_contents(WPSP_ABSPATH . 'includes/admin/customize/css/body.css'),
									'footer'         => file_get_contents(WPSP_ABSPATH . 'includes/admin/customize/css/footer.css'),
									'buttons'        => file_get_contents(WPSP_ABSPATH . 'includes/admin/customize/css/buttons.css'),
								);
								
						}
						
						return $general;
						
				}
				
				public function get_customize_ticket_list(){
						
						$customize_ticket_list = get_option('wpsp_customize_ticket_list');
						if( $customize_ticket_list === false ){
								
								$customize_ticket_list = array(
									'filter_sidebar'         => file_get_contents(WPSP_ABSPATH . 'includes/admin/customize/css/filter_sidebar.css'),
									'ticket_list_table'      => file_get_contents(WPSP_ABSPATH . 'includes/admin/customize/css/ticket_list_table.css'),
									'open_ticket_widget'     => file_get_contents(WPSP_ABSPATH . 'includes/admin/customize/css/open_ticket_widget.css'),
									'ticket_log'             => file_get_contents(WPSP_ABSPATH . 'includes/admin/customize/css/ticket_log.css'),
								);
								
						}
						
						return $customize_ticket_list;
						
				}
				
				public function is_integrate_theme(){
						
						global $wpdb, $wpsupportplus, $current_user;
						
						$theme_integration = $this->get_role_theme_integration();
						
						if ( $this->is_administrator($current_user) && $theme_integration['administrator'] ) {
							return true;
						} else if ( $this->is_supervisor($current_user) && $theme_integration['supervisor'] ) {
							return true;
						} else if ( $this->is_agent($current_user) && $theme_integration['agent'] ) {
							return true;
						} else if ( !$this->is_staff($current_user) && $theme_integration['customer'] ) {
							return true;
						} else {
							return false;
						}
						
				}
				
				public function get_allow_powered_by_text(){

            if($this->settings_general && isset($this->settings_general['allow_powered_by_text'])){
								return $this->settings_general['allow_powered_by_text'];
            } else{
                return 1;
            }

        }
				
				/**
				 * Return Make Ticket As Public
				 */
				public function make_ticket_as_public() {

						if( $this->settings_general && isset($this->settings_general['make_ticket_as_public']) ) {
								return $this->settings_general['make_ticket_as_public'];
						} else {
								return 0;
						}
				}
				
				public function get_sub_char_length(){

            if($this->advanced_settings && isset($this->advanced_settings['sub_char_length'])){
								return $this->advanced_settings['sub_char_length'];
            } else{
                return 25;
            }

        }
				
				public function get_raised_char_length(){
					
					if($this->advanced_settings && isset($this->advanced_settings['raised_char_length'])){
							return $this->advanced_settings['raised_char_length'];
					}
				 	else{
							return 25;
					}
				}
				
				public function toggle_button_signin()
					 {
						 if( $this->general_settings_advanced && isset($this->general_settings_advanced['signin']) ) 
	   					{
	 								return $this->general_settings_advanced['signin'];
	 						}
							else 
						  {
	 							 return 1;
	 					  }
	 			  }
					
					public function toggle_button_signout()
 					{
 						if( $this->general_settings_advanced && isset($this->general_settings_advanced['signout']) ) 
 						 {
 								 return $this->general_settings_advanced['signout'];
 						 }
 						 else 
 						 {
 								return 1;
 						 }
 				 }
 				 

				public function toggle_button_signup()
				 {
					 if( $this->general_settings_advanced && isset($this->general_settings_advanced['signup']) ) 
   					{
 								return $this->general_settings_advanced['signup'];
 						}
						else 
					  {
 							 return 1;
 					  }
 			  }
				
				/**
         * Return Customer ticket select fields
         */
        public function get_user_all_tickets_select_field_list(){

            global $wpsupportplus, $wpdb, $current_user;

            $sql        = '';
            $response   = array();

            $sql = "select * from {$wpdb->prefix}wpsp_ticket_list_order where customer_ticket=1 ORDER BY load_order";
            

            $list_keys = $wpdb->get_results($sql);

            foreach ( $list_keys as $key ){

                $response[] = $key->join_match.' as field_'.$key->field_key;
            }

            $response = implode(', ', $response);

            return $response;

        }
				
				public function get_custom_login_redirect_url(){

						if($this->settings_general && isset($this->settings_general['custom_login'])){
								return $this->settings_general['custom_login'];
						} else{
								return '';
						}
				}
				
				/**
         * Allow Customer to reply closed tickets
         */
				public function toggle_button_reply_closed_tickets(){
					
					 if( $this->general_settings_advanced && isset($this->general_settings_advanced['reply_closed_tickets']) ) 
						{
								return $this->general_settings_advanced['reply_closed_tickets'];
						}
						else 
						{
							 return 1;
						}
				}
				
				/**
         * Enable/disable captcha
         */
				public function toggle_button_disable_captcha(){
					
					 if( $this->general_settings_advanced && isset($this->general_settings_advanced['captcha_status']) ) 
						{
								return $this->general_settings_advanced['captcha_status'];
						}
						else 
						{
							 return 'all';
						}
				}
		}

endif;
