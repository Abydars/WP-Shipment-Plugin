<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Ajax' ) ) :
    
    /**
     * Ajax class for WPSP.
     * @class WPSP_Ajax
     */
    class WPSP_Ajax {
        
        /**
         * Constructor
         */
        public function __construct(){
            
            $ajax_events = array(
                'upgrade'                   => false,
                'installation'              => false,
                'create_support_page'       => false,
                'select_support_page'       => false,
                'get_add_category'          => false,
                'set_add_category'          => false,
                'get_edit_category'         => false,
                'set_edit_category'         => false,
                'get_delete_category'       => false,
                'set_delete_category'       => false,
                'save_table_order'          => false,
                'get_add_status'            => false,
                'set_add_status'            => false,
                'get_edit_status'           => false,
                'set_edit_status'           => false,
                'get_delete_status'         => false,
                'set_delete_status'         => false,
                'get_add_priority'          => false,
                'set_add_priority'          => false,
                'get_edit_priority'         => false,
                'set_edit_priority'         => false,
                'get_delete_priority'       => false,
                'set_delete_priority'       => false,
                'get_add_custom_field'      => false,
                'set_add_custom_field'      => false,
                'get_edit_custom_field'     => false,
                'set_edit_custom_field'     => false,
                'get_delete_custom_field'   => false,
                'set_delete_custom_field'   => false,
                'save_form_management'      => false,
                'save_list_settings'        => false,
                'get_add_agent'             => false,
                'search_users_for_add_agent'=> false,
                'set_add_agent'             => false,
                'get_edit_agent'            => false,
                'set_edit_agent'            => false,
                'get_delete_agent'          => false,
                'set_delete_agent'          => false,
                'autocomplete'              => true,
                'signin'                    => true,
                'guest_signin'              => true,
                'upload_image'              => true,
                'upload_file'               => true,
                'create_tkt_cng_cat'        => true,
                'save_ticket_filter'        => false,
                'delete_ticket_filter'      => false,
                'apply_ticket_filter'       => true,
                'get_tickets'               => true,
                'ticket_reply'              => true,
                'add_ticket_note'           => false,
                'get_change_ticket_status'  => false,
                'set_change_ticket_status'  => false,
                'get_change_raised_by'      => false,
                'set_change_raised_by'      => false,
                'get_assign_agent'          => false,
                'set_assign_agent'          => false,
                'get_agent_fields'          => false,
                'get_ticket_fields'         => false,
                'set_ticket_fields'         => false,
                'set_agent_fields'          => false,
                'get_edit_thread'           => false,
                'set_edit_thread'           => false,
                'get_delete_thread'         => false,
                'set_delete_thread'         => false,
                'get_edit_subject'          => false,
                'set_edit_subject'          => false,
                'get_delete_ticket'         => false,
                'set_delete_ticket'         => false,
                'get_clone_ticket'          => false,
                'set_clone_ticket'          => false,
                'get_delete_bulk_ticket'    => false,
                'set_delete_bulk_ticket'    => false,
                'get_bulk_assign_agent'     => false,
                'set_bulk_assign_agent'     => false,
                'get_bulk_change_status'    => false,
                'set_bulk_change_status'    => false,
                'get_close_ticket'          => true,
                'set_close_ticket'          => true,      
								'get_delete_custom_menu'    => false,
								'set_delete_custom_menu'    => false,
								'get_delete_support_menu'  	=> false,
								'set_delete_support_menu'		=> false,
								'customize_reset_default'		=> true,
								'set_agent_setting'					=> false,
								'save_ticket_widget'        => false,
								'get_ticket_created'        => false,
								'get_restore_ticket'				=> false,
								'set_restore_ticket'				=> false,
								'get_permanent_delete_ticket' => false,
								'set_permanent_delete_ticket' => false,
								'get_user_biography'        => false,
								'save_ticket_widget'        => false,
								'get_new_thread'            => false,
								'set_new_thread'            => false,
								'get_captcha_code'          => true,
								'submit_ticket'             => true
						);
            
            foreach ($ajax_events as $ajax_event => $nopriv) {
								add_action('wp_ajax_wpsp_' . $ajax_event, array($this, $ajax_event));
                if ($nopriv) {
                    add_action('wp_ajax_nopriv_wpsp_' . $ajax_event, array($this, $ajax_event));
                }
            }
        }
        
        /**
         * Upgrade process
         */
        public function upgrade(){
            include WPSP_ABSPATH . 'includes/ajax/upgrade.php';
            die();
        }
        
        /**
         * Installation steps
         */
        public function installation(){

            global $wpdb, $current_user, $wpsupportplus;

            $nonce  = isset( $_POST['nonce'] ) ? sanitize_text_field($_POST['nonce']) : '';
            $step   = isset( $_POST['current_step'] ) ? intval(sanitize_text_field($_POST['current_step'])) : 1;

            if( !wp_verify_nonce( $nonce, $current_user->ID ) || !$current_user->has_cap('manage_options') || !$step ){
                die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
            }
            
            update_option('wpsp_installation',$step);
            
        }
        
        /**
         * Create support page
         */
        public function create_support_page(){
            
            global $wpdb, $current_user, $wpsupportplus;

            $nonce  = isset( $_POST['nonce'] ) ? sanitize_text_field($_POST['nonce']) : '';
            $title  = isset( $_POST['page_title'] ) ? sanitize_text_field($_POST['page_title']) : 'Support';

            if( !wp_verify_nonce( $nonce, $current_user->ID ) || !$current_user->has_cap('manage_options') ){
                die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
            }
            
            $my_post = array(
                'post_type'     => 'page',
                'post_title'    => $title,
                'post_content'  => '[wp_support_plus]',
                'post_status'   => 'publish'
            );
            $page_id = wp_insert_post( $my_post );
            
            $general_settings                   = get_option( 'wpsp_settings_general' );
            $general_settings['support_page']   = $page_id;
            
            update_option('wpsp_settings_general', $general_settings);
						
						//custom menu slider 
						
						$support_icon = WPSP_PLUGIN_URL.'asset/images/icons/agent.png' ;
						$support_url  = get_permalink( $page_id );
						
						$values       = array(
							'menu_text'    => "Tickets",
							'redirect_url' => $support_url,
							'menu_icon'    => $support_icon,
							'load_order'   => "1"
						);
						
						$wpdb->insert($wpdb->prefix.'wpsp_panel_custom_menu',$values);
						
						//support page menu
						$values       = array(
							'name'         => "Tickets",
							'redirect_url' => $support_url,
							'icon'         => $support_icon,
							'load_order'   => "1"
						);
						$wpdb->insert($wpdb->prefix.'wpsp_support_menu',$values);
						
            update_option('wpsp_installation', 4 );
            
        }
        
        /**
         * Select support page in installation
         */
        public function select_support_page(){
            
            global $wpdb, $current_user, $wpsupportplus;

            $nonce      = isset( $_POST['nonce'] ) ? sanitize_text_field($_POST['nonce']) : '';
            $page_id    = isset( $_POST['page_id'] ) ? sanitize_text_field($_POST['page_id']) : 0;

            if( !wp_verify_nonce( $nonce, $current_user->ID ) || !$current_user->has_cap('manage_options') || !$page_id ){
                die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
            }
            
            $general_settings                   = get_option( 'wpsp_settings_general' );
            $general_settings['support_page']   = $page_id;
            
            update_option('wpsp_settings_general', $general_settings);
						
						//custom menu slider ticket menu
						
						$support_icon = WPSP_PLUGIN_URL.'asset/images/icons/agent.png' ;
						$support_url  = get_permalink( $page_id );
						
						$values       = array(
							'menu_text'    => "Tickets",
							'redirect_url' => $support_url,
							'menu_icon'    => $support_icon,
							'load_order'   => "1"
						);
						
						$wpdb->insert($wpdb->prefix.'wpsp_panel_custom_menu',$values);
						
						//support page menu
						$values       = array(
							'name'         => "Tickets",
							'redirect_url' => $support_url,
							'icon'         => $support_icon,
							'load_order'   => "1"
						);
						$wpdb->insert($wpdb->prefix.'wpsp_support_menu',$values);
						
            update_option('wpsp_installation', 4 );
        }

        /**
         * Print add category form on popup
         */
        public function get_add_category(){
            include WPSP_ABSPATH . 'includes/ajax/get_add_category.php';
            die();
        }
        
        /**
         * Create new category
         */
        public function set_add_category(){
            include WPSP_ABSPATH . 'includes/ajax/set_add_category.php';
            die();
        }
        
        /**
         * Edit category form
         */
        public function get_edit_category(){
            include WPSP_ABSPATH . 'includes/ajax/get_edit_category.php';
            die();
        }
        
        /**
         * Update category in database
         */
        public function set_edit_category(){
            include WPSP_ABSPATH . 'includes/ajax/set_edit_category.php';
            die();
        }
        
        /**
         * Delete category confirmation
         */
        public function get_delete_category(){
            include WPSP_ABSPATH . 'includes/ajax/get_delete_category.php';
            die();
        }
        
        /**
         * Delete category
         */
        public function set_delete_category(){
            include WPSP_ABSPATH . 'includes/ajax/set_delete_category.php';
            die();
        }
        
        /**
         * Save sortable table order to DB
         */
        public function save_table_order(){
            include WPSP_ABSPATH . 'includes/ajax/save_table_order.php';
            die();
        }
        
        /**
         * Get add status popup
         */
        public function get_add_status(){
            include WPSP_ABSPATH . 'includes/ajax/get_add_status.php';
            die();
        }
        
        /**
         * Add new status
         */
        public function set_add_status(){
            include WPSP_ABSPATH . 'includes/ajax/set_add_status.php';
            die();
        }
        
        /**
         * Edit status pop-up
         */
        public function get_edit_status(){
            include WPSP_ABSPATH . 'includes/ajax/get_edit_status.php';
            die();
        }
        
        /**
         * Update status to database
         */
        public function set_edit_status(){
            include WPSP_ABSPATH . 'includes/ajax/set_edit_status.php';
            die();
        }
        
        
        /**
         * Get delete status confirmation and server side validation
         */
        public function get_delete_status(){
            include WPSP_ABSPATH . 'includes/ajax/get_delete_status.php';
            die();
        }
        
        /**
         * Delete status from database
         */
        public function set_delete_status(){
            include WPSP_ABSPATH . 'includes/ajax/set_delete_status.php';
            die();
        }
        
        /**
         * Add new Priority Pop-up
         */
        public function get_add_priority(){
            include WPSP_ABSPATH . 'includes/ajax/get_add_priority.php';
            die();
        }
        
        /**
         * Add new Priority to database
         */
        public function set_add_priority(){
            include WPSP_ABSPATH . 'includes/ajax/set_add_priority.php';
            die();
        }
        
        /**
         * Edit priority Pop-up
         */
        public function get_edit_priority(){
            include WPSP_ABSPATH . 'includes/ajax/get_edit_priority.php';
            die();
        }
        
        /**
         * Set priority in database
         */
        public function set_edit_priority(){
            include WPSP_ABSPATH . 'includes/ajax/set_edit_priority.php';
            die();
        }
        
        /**
         * Delete priority pop-up
         */
        public function get_delete_priority(){
            include WPSP_ABSPATH . 'includes/ajax/get_delete_priority.php';
            die();
        }
        
        /**
         * Delete priority in database
         */
        public function set_delete_priority(){
            include WPSP_ABSPATH . 'includes/ajax/set_delete_priority.php';
            die();
        }
        
        /**
         * Add new custom field pop-up
         */
        public function get_add_custom_field(){
            include WPSP_ABSPATH . 'includes/ajax/get_add_custom_field.php';
            die();
        }
        
        /**
         * Insert custom field to database
         */
        public function set_add_custom_field(){
            include WPSP_ABSPATH . 'includes/ajax/set_add_custom_field.php';
            die();
        }
        
        /**
         * Get edit custom field pop-up
         */
        public function get_edit_custom_field(){
            include WPSP_ABSPATH . 'includes/ajax/get_edit_custom_field.php';
            die();
        }
        
        /**
         * update custom field in database
         */
        public function set_edit_custom_field(){
            include WPSP_ABSPATH . 'includes/ajax/set_edit_custom_field.php';
            die();
        }
        
        /**
         * Get delete custom field pop-up
         */
        public function get_delete_custom_field(){
            include WPSP_ABSPATH . 'includes/ajax/get_delete_custom_field.php';
            die();
        }
        
        /**
         * Delete custom field from db
         */
        public function set_delete_custom_field(){
            include WPSP_ABSPATH . 'includes/ajax/set_delete_custom_field.php';
            die();
        }
        
        /**
         * Form management save changes to DB
         */
        public function save_form_management(){
            include WPSP_ABSPATH . 'includes/ajax/save_form_management.php';
            die();
        }
        
        /**
         * Save list settings in DB
         */
        public function save_list_settings(){

            include WPSP_ABSPATH . 'includes/ajax/save_list_settings.php';
            die();
        }
     	/**
				 * Save Widget settings in DB
				 */
				public function save_ticket_widget(){
					include WPSP_ABSPATH . 'includes/ajax/save_ticket_widget.php';
					die();
				}
        
        /**
         * Add agent pop-up load
         */
        public function get_add_agent(){
            include WPSP_ABSPATH . 'includes/ajax/get_add_agent.php';
            die();
        }
        
        /**
         * Search users for add agent
         */
        public function search_users_for_add_agent(){
            include WPSP_ABSPATH . 'includes/ajax/search_users_for_add_agent.php';
            die();
        }
        
        /**
         * Set add agent
         */
        public function set_add_agent(){
            include WPSP_ABSPATH . 'includes/ajax/set_add_agent.php';
            die();
        }
        
        /**
         * Get edit agent
         */
        public function get_edit_agent(){
            include WPSP_ABSPATH . 'includes/ajax/get_edit_agent.php';
            die();
        }
        
        /**
         * Update agent
         */
        public function set_edit_agent(){
            include WPSP_ABSPATH . 'includes/ajax/set_edit_agent.php';
            die();
        }
        
        /**
         * Delete agent confirm
         */
        public function get_delete_agent(){
            include WPSP_ABSPATH . 'includes/ajax/get_delete_agent.php';
            die();
        }
        
        /**
         * Delete an agent
         */
        public function set_delete_agent(){
            include WPSP_ABSPATH . 'includes/ajax/set_delete_agent.php';
            die();
        }
        
        /**
         * All auto-complete services served by this
         */
        public function autocomplete(){
            include WPSP_ABSPATH . 'includes/ajax/autocomplete/index.php';
            die();
        }
        
        /**
         * user sign in
         */
        public function signin(){
            include WPSP_ABSPATH . 'includes/ajax/user-login/signin.php';
            die();
        }
        
        /**
         * user sign in
         */
        public function guest_signin(){
            include WPSP_ABSPATH . 'includes/ajax/user-login/guest_signin.php';
            die();
        }
        
        /**
         * Upload image via Tiny MCE
         */
        public function upload_image(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-form/upload_image.php';
            die();
        }
        
        /**
         * Upload file
         */
        public function upload_file(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-form/upload_file.php';
            die();
        }
        
        /**
         * Create ticket change category
         * return category ids dependant on selected category to show on form
         */
        public function create_tkt_cng_cat(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-form/create_tkt_cng_cat.php';
            die();
        }
        
        /**
         * Save ticket filter
         */
        public function save_ticket_filter(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-list/save_ticket_filter.php';
            die();
        }
        
        /**
         * Delete ticket filter
         */
        public function delete_ticket_filter(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-list/delete_ticket_filter.php';
            die();
        }
        
        /**
         * Apply ticket filter
         */
        public function apply_ticket_filter(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-list/apply_ticket_filter.php';
            die();
        }
        
        /**
         * Get ticket list
         */
        public function get_tickets(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-list/get_tickets.php';
            die();
        }
        
        /**
         * Ticket reply
         */
        public function ticket_reply(){
            
            include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';
            $ticket_oprations = new WPSP_Ticket_Operations();
            $ticket_oprations->reply_ticket();
            die();
            
        }
        
        /**
         * Ticket add note
         */
        public function add_ticket_note(){
            
            include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';
            $ticket_oprations = new WPSP_Ticket_Operations();
            $ticket_oprations->add_ticket_note();
            die();
            
        }
        
        /**
         * change ticket status form
         */
        public function get_change_ticket_status(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_change_ticket_status.php';
            die();
        }
        
        /**
         * Set change ticket status
         */
        public function set_change_ticket_status(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_change_ticket_status.php';
            die();
        }
        
        /**
         * Get change raised by
         */
        public function get_change_raised_by(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_change_raised_by.php';
            die();
        }
        
        /**
         * Set change raised by
         */
        public function set_change_raised_by(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_change_raised_by.php';
            die();
        }
        
        /**
         * Get assigned agent
         */
        public function get_assign_agent(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_assign_agent.php';
            die();
        }
        
        /**
         * Set assigned agent
         */
        public function set_assign_agent(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_assign_agent.php';
            die();
        }
        
        /**
         * get agent fields
         */
        public function get_agent_fields(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_agent_fields.php';
            die();
        }
        
        /**
         * set agent fields1
         */
        public function set_agent_fields(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_agent_fields.php';
            die();
        }

        /**
         * get ticket fields
         */
        public function get_ticket_fields(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_ticket_fields.php';
            die();
        }
        
        /**
         * set ticket fields
         */
        public function set_ticket_fields(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_ticket_fields.php';
            die();
        }
        
        /**
         * get_edit_thread
         */
        public function get_edit_thread(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_edit_thread.php';
            die();
        }
        
        /**
         * set_edit_thread
         */
        public function set_edit_thread(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_edit_thread.php';
            die();
        }
        
				/**
         * get_new_thread 
         */
				 public function get_new_thread(){
					  include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_new_thread.php';
						die();
				 }
				 /**
          * set_new_thread 
          */
					public function set_new_thread(){
						include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_new_thread.php';
						die();
					}
        /**
         * get_delete_thread
         */
        public function get_delete_thread(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_delete_thread.php';
            die();
        }
        
        /**
         * set_delete_thread
         */
        public function set_delete_thread(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_delete_thread.php';
            die();
        }
        
        /**
         * get_edit_subject
         */
        public function get_edit_subject(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_edit_subject.php';
            die();
        }
        
        /**
         * set_edit_subject
         */
        public function set_edit_subject(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_edit_subject.php';
            die();
        }
        
        /**
         * get_delete_ticket
         */
        public function get_delete_ticket(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_delete_ticket.php';
                die();
        }
        
        /*
         * get clone ticket
         */
        public function get_clone_ticket(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_clone_ticket.php';
               die();
        }
        
        /**
         * get close ticket
         */
        public function get_close_ticket(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_close_ticket.php';
            die();
        }
        
        /**
         * get_delete_ticket
         */
        public function get_delete_bulk_ticket(){            
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_delete_bulk_ticket.php';
            die();
        }
        
        /**
         * set_delete_ticket
         */
        public function set_delete_bulk_ticket(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_delete_bulk_ticket.php';
            die();
        }
        
        /**
         * get_bulk_assign_agent
         */
        public function get_bulk_assign_agent(){            
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_bulk_assign_agent.php';
            die();
        }
        
        /**
         * set_bulk_assign_agent
         */
        public function set_bulk_assign_agent(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_bulk_assign_agent.php';
            die();
        }
        
        /**
         * get_bulk_change_status
         */
        public function get_bulk_change_status(){            
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_bulk_change_status.php';
            die();
        }
        
         /**
         * set_bulk_change_status
         */
        public function set_bulk_change_status(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_bulk_change_status.php';
            die();
        }
        
        /**
         * set_delete_ticket
         */
        public function set_delete_ticket(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_delete_ticket.php';
            die();
        }

        /*
         * set clone ticket
         */
        public function set_clone_ticket(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_clone_ticket.php';
             die();
        }
        
        /**
         * set close ticket
         */
        public function set_close_ticket(){
            include WPSP_ABSPATH . 'includes/ajax/ticket-individual/set_close_ticket.php';
            die();
        }
				/**
				 * get user biography field
				 */
				public function get_user_biography(){
						include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_user_biography.php';
						die();
				}
 				/**
				*	get delete custom menu
				*/
				public function get_delete_custom_menu(){
            include WPSP_ABSPATH . 'includes/ajax/get_delete_custom_menu.php';
            die();
        }
				/**
				*	set delete custom menu
				*/
				public function set_delete_custom_menu(){
            include WPSP_ABSPATH . 'includes/ajax/set_delete_custom_menu.php';
            die();
        }
				/**
				*	get delete support page menu
				*/
				public function get_delete_support_menu(){
            include WPSP_ABSPATH . 'includes/ajax/get_delete_support_menu.php';
            die();
        }
				/**
				*	set delete support page menu
				*/
				public function set_delete_support_menu(){
            include WPSP_ABSPATH . 'includes/ajax/set_delete_support_menu.php';
            die();
        }
				
				public function customize_reset_default(){
						include WPSP_ABSPATH . 'includes/ajax/customize_reset_default.php';
						die();
				}
				
				public function set_agent_setting(){
						include WPSP_ABSPATH . 'includes/ajax/wpsp_set_agent_setting.php';
						die();
				}
				
				public function get_ticket_created(){
					include WPSP_ABSPATH . 'includes/ajax/ticket-individual/get_ticket_created.php';
					die();
				}
				
				public function get_restore_ticket(){
						include WPSP_ABSPATH . 'includes/ajax/get_restore_ticket.php';
						die();
				}
				
				public function set_restore_ticket(){
						include WPSP_ABSPATH . 'includes/ajax/set_restore_ticket.php';
						die();
				}
				
				public function get_permanent_delete_ticket(){
						include WPSP_ABSPATH . 'includes/ajax/get_permanent_delete_ticket.php';
						die();
				}
				
				public function set_permanent_delete_ticket(){
						include WPSP_ABSPATH . 'includes/ajax/set_permanent_delete_ticket.php';
						die();
				}
				
				public function get_captcha_code(){
						include WPSP_ABSPATH . 'includes/ajax/get_captcha_code.php';
						die();
				}
				public function submit_ticket(){
						include WPSP_ABSPATH . 'includes/ajax/submit_ticket.php';
						die();
				}
				
		}
    
endif;