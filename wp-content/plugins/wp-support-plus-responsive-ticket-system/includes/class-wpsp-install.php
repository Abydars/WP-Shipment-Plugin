<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Install' ) ) :

    /**
     * WPSP installation and updating class
     */
    class WPSP_Install {

        /**
         * Constructor
         */
        public function __construct() {

            register_activation_hook( WPSP_PLUGIN_FILE, array($this,'install') );
            register_deactivation_hook( WPSP_PLUGIN_FILE, array($this,'deactivate') );
            $this->check_version();
        }

        /**
         * Check version of WPSP
         */
        private function check_version(){

            $installed_version = get_option( 'wp_support_plus_version' );
            if( $installed_version != WPSP_VERSION ){
                $this->install();
            }

            // last version where upgrade check done
            $upgrade_version = get_option( 'wp_support_plus_upgrade_version' );
            if( $upgrade_version != WPSP_VERSION ){
                $this->upgrade();
                update_option( 'wp_support_plus_upgrade_version', WPSP_VERSION );
            }

        }

        /**
         * Install WPSP
         */
        function install(){

            $this->create_tables();
            update_option( 'wp_support_plus_version', WPSP_VERSION );
				
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

            global $wpdb;

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

            $collate = 'CHARACTER SET utf8 COLLATE utf8_general_ci';

            $tables = "
                CREATE TABLE {$wpdb->prefix}wpsp_ticket (
                  id bigint(20) NOT NULL AUTO_INCREMENT,
                  subject varchar(200) NULL DEFAULT NULL,
                  created_by bigint(20),
                  updated_by INT NOT NULL DEFAULT '0',
                  guest_name TINYTEXT NULL DEFAULT NULL,
                  guest_email TINYTEXT NULL DEFAULT NULL,
                  type varchar(30) NULL DEFAULT NULL,
                  status_id integer,
                  cat_id integer,
                  create_time datetime,
                  update_time datetime,
                  assigned_to VARCHAR( 30 ) NULL DEFAULT '0',
                  priority_id integer,
									ip_address VARCHAR(30) NULL DEFAULT NULL,
                  agent_created INT NULL DEFAULT '0',
                  active int(11) DEFAULT 1,
                  PRIMARY KEY  (id),
                  KEY cat_id (cat_id),
                  KEY created_by (created_by),
                  KEY update_time (update_time),
                  KEY status_id (status_id),
                  KEY priority_id (priority_id),
                  KEY active (active)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}wpsp_ticket_thread (
                  id bigint(20) NOT NULL AUTO_INCREMENT,
                  ticket_id integer,
                  body LONGTEXT NULL DEFAULT NULL,
                  attachment_ids TINYTEXT NULL DEFAULT NULL,
                  create_time datetime,
                  created_by integer,
                  guest_name TINYTEXT NULL DEFAULT NULL,
                  guest_email TINYTEXT NULL DEFAULT NULL,
                  is_note INT NULL DEFAULT '0',
                  PRIMARY KEY  (id),
                  KEY ticket_id (ticket_id),
                  KEY create_time (create_time),
                  KEY created_by (created_by)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}wpsp_attachments (
                  id bigint(20) NOT NULL AUTO_INCREMENT,
                  filename TINYTEXT NULL DEFAULT NULL,
                  filetype TINYTEXT NULL DEFAULT NULL,
                  filepath TINYTEXT NULL DEFAULT NULL,
                  active integer(2) DEFAULT 1,
                  upload_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                  PRIMARY KEY  (id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}wpsp_custom_fields (
                  id integer NOT NULL AUTO_INCREMENT,
                  label varchar(200),
                  required INT NULL DEFAULT '0',
                  field_type INT NULL DEFAULT '1',
                  field_options TEXT NULL DEFAULT NULL,
                  field_categories varchar(100) DEFAULT '0',
                  isVarFeild INT DEFAULT 0,
                  PRIMARY KEY  (id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}wpsp_catagories (
                  id integer NOT NULL AUTO_INCREMENT,
                  name TINYTEXT NULL DEFAULT NULL,
                  supervisor TEXT NULL DEFAULT NULL,
                  load_order INT NULL DEFAULT '0',
                  PRIMARY KEY (id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}wpsp_custom_status (
                  id integer NOT NULL AUTO_INCREMENT,
                  name VARCHAR(50) NULL DEFAULT NULL,
                  color varchar(10),
                  load_order INT NULL DEFAULT '0',
                  PRIMARY KEY  (id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}wpsp_custom_priority (
                  id integer NOT NULL AUTO_INCREMENT,
                  name VARCHAR(50) NULL DEFAULT NULL,
                  color varchar(10),
                  load_order INT NULL DEFAULT '0',
                  PRIMARY KEY  (id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}wpsp_agent_assign_data (
                  id bigint(20) NOT NULL AUTO_INCREMENT,
                  ticket_id bigint(20) NOT NULL,
                  agent_id INT NOT NULL,
                  PRIMARY KEY  (id),
                  KEY ticket_id (ticket_id),
                  KEY agent_id (agent_id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}wpsp_ticket_form_order (
                  id int NOT NULL AUTO_INCREMENT,
                  field_key TINYTEXT NULL DEFAULT NULL,
                  status TINYINT NOT NULL,
                  full_width TINYINT NOT NULL,
                  load_order SMALLINT NOT NULL DEFAULT 0,
                  PRIMARY KEY  (id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}wpsp_ticket_list_order (
                  id int NOT NULL AUTO_INCREMENT,
                  field_key TINYTEXT NULL DEFAULT NULL,
                  join_match TINYTEXT NULL DEFAULT NULL,
                  join_compare TINYTEXT NULL DEFAULT NULL,
                  join_relation TINYTEXT NULL DEFAULT NULL,
                  customer_visible TINYINT NOT NULL DEFAULT 0,
                  agent_visible TINYINT NOT NULL DEFAULT 0,
                  customer_filter TINYINT NOT NULL DEFAULT 0,
                  agent_filter TINYINT NOT NULL DEFAULT 0,
                  load_order SMALLINT NOT NULL DEFAULT 0,
                  PRIMARY KEY  (id)
                ) $collate;
                CREATE TABLE {$wpdb->prefix}wpsp_users (
                  id bigint(20) NOT NULL AUTO_INCREMENT,
                  user_id bigint(20) NOT NULL DEFAULT 0,
                  role TINYINT NOT NULL DEFAULT 0,
                  PRIMARY KEY  (id)
                ) $collate;
								CREATE TABLE {$wpdb->prefix}wpsp_panel_custom_menu (
									id int NOT NULL AUTO_INCREMENT,
									menu_text varchar(50) NULL DEFAULT NULL,
									menu_icon varchar(200) NULL DEFAULT NULL,
									redirect_url varchar(200) NULL DEFAULT NULL,
									load_order INT NULL DEFAULT '0',
									PRIMARY KEY  (id)
								) $collate;
								CREATE TABLE {$wpdb->prefix}wpsp_support_menu (
									id int NOT NULL AUTO_INCREMENT,
									name varchar(50) NULL DEFAULT NULL,
									icon varchar(200) NULL DEFAULT NULL,
									redirect_url varchar(200) NULL DEFAULT NULL,
									load_order INT NULL DEFAULT '0',
									PRIMARY KEY  (id)
								) $collate;
            ";

            dbDelta( $tables );
        }


        /**
         * Upgrade process begin
         */
        function upgrade(){

            $upgrade_version = get_option( 'wp_support_plus_upgrade_version' ) ? get_option( 'wp_support_plus_upgrade_version' ) : 0;
						//Version 9.0.0
            if( $upgrade_version < '9.0.0' ){

                global $wpdb, $current_user;

                $coloums = $wpdb->get_results("SHOW COLUMNS FROM {$wpdb->prefix}wpsp_ticket like 'status'");
                if( !count($coloums) ) {  // insert table data only for fresh install

                    $sql = "INSERT INTO {$wpdb->prefix}wpsp_catagories "
                        . "( id, name, load_order )"
                        . "VALUES "
                        . "( 1, 'General', '1' ) ";

                    $wpdb->query($sql);

                    $sql = "INSERT INTO {$wpdb->prefix}wpsp_custom_status "
                        . "( name, color, load_order )"
                        . "VALUES "
                        . "( 'Open', '#d9534f', '1' ), "
                        . "( 'Pending', '#f0ad4e', '2' ), "
                        . "( 'Closed', '#5cb85c', '3' ) ";

                    $wpdb->query($sql);

                    $sql = "INSERT INTO {$wpdb->prefix}wpsp_custom_priority "
                        . "( name, color, load_order )"
                        . "VALUES "
                        . "( 'Normal', '#5cb85c', '1' ), "
                        . "( 'High', '#d9534f', '2' ), "
                        . "( 'Medium', '#f0ad4e', '3' ), "
                        . "( 'Low', '#5bc0de', '4' ) ";

                    $wpdb->query($sql);

                    $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_form_order "
                        . "( field_key, status, full_width, load_order )"
                        . "VALUES "
                        . "( 'ds', '1', '1', '1' ), "
                        . "( 'dd', '1', '1', '2' ), "
                        . "( 'dc', '1', '1', '3' ), "
                        . "( 'dp', '1', '1', '4' ) ";

                    $wpdb->query($sql);

                    $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                        . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter, load_order )"
                        . "VALUES "
                        . "( 'id', 't.id', 't.id', 'LIKE', 1, 1, 0, 1, 1 ), "
                        . "( 'status', 's.id', 's.name', 'LIKE', 1, 1, 1, 1, 2 ), "
                        . "( 'subject', 't.subject', 't.subject', NULL, 1, 1, 0, 0, 3 ), "
                        . "( 'raised_by', 't.guest_email', 't.guest_name', 'LIKE', 0, 1, 0, 1, 4 ), "
                        . "( 'user_type', 't.type', 't.type', 'LIKE', 0, 0, 0, 0, 4 ), "
                        . "( 'category', 'c.id', 'c.name', 'LIKE', 1, 1, 1, 1, 5 ), "
                        . "( 'assigned_agent', 't.assigned_to', 't.assigned_to', 'LIKE', 0, 1, 0, 1, 6 ), "
                        . "( 'priority', 'p.id', 'p.name', 'LIKE', 1, 1, 1, 1, 7 ), "
                        . "( 'date_created', 't.create_time', 't.create_time', 'BETWEEN', 0, 0, 1, 1, 8 ), "
                        . "( 'date_updated', 't.update_time', 't.update_time', 'BETWEEN', 1, 1, 0, 0, 9 ), "
                        . "( 'created_agent', 't.agent_created', 'u.user_login,u.display_name,u.user_email', 'LIKE', 0, 0, 0, 0, 10 )";

                    $wpdb->query($sql);

                    // insert admin agents
                    $admins = get_users(array('role'=>'administrator'));
                    foreach ( $admins as $user ){
                        $values = array(
                            'user_id'       => $user->ID,
                            'role'          => 3
                        );
                        $wpdb->insert( $wpdb->prefix.'wpsp_users', $values );
                        $user->add_cap('wpsp_administrator');
                    }

                }

                // General settings
                $settings_general = array(
                    'support_page'          => 0,
                    'default_category'      => 1,
                    'default_status'        => 1,
                    'default_priority'      => 1,
                    'customer_reply_status' => '',
                    'allow_cust_close'      => false,
                    'close_ticket_status'   => '',
                    'ticket_id_sequence'    => 1,
                    'date_format'           => 'yy-mm-dd',
                    'display_date_format'   => 'Y/m/d H:i:s',
                    'custom_date_format'    => 'Y/m/d',
                    'support_btn_new_tab'   => 0,
                    'attachment_size'       => 20,
                    'allow_guest_ticket'    => 1,
                    'staff_read_all_ticket' => 0,
                    'agent_reply_status'    => '',
                    'reply_form_position'   => 1
                );
                update_option( 'wpsp_settings_general', $settings_general );

                // Support button
                $support_btn = array(
                    'allow_support_btn' => 1,
                    'support_btn_label' => 'Help-Desk'
                );
                update_option( 'wpsp_settings_support_btn', $settings_general );

                // Default filters
                $default_filters = array(
                    'customer_hide_statuses'        => array(),
                    'customer_orderby'              => 't.update_time',
                    'customer_orderby_order'        => 'DESC',
                    'customer_par_page_tickets'     => 20,
                    'agent_hide_statuses'           => array(),
                    'agent_orderby'                 => 't.update_time',
                    'agent_orderby_order'           => 'DESC',
                    'agent_par_page_tickets'        => 20
                );
                update_option( 'wpsp_ticket_list_default_filters', $default_filters );
								
                // Agent settings
                $agent_settings = array(
                    'agent_allow_assign_ticket'                             => 0,
                    'agent_allow_delete_ticket'                             => 0,
                    'agent_allow_change_raised_by'                          => 0,
                    'agent_allow_read_only_unassigned_ticket'               => 1,
                    'supervisor_allow_delete_ticket'                        => 0,
                    'supervisor_allow_change_raised_by'                     => 0
                );
                update_option( 'wpsp_agent_settings', $agent_settings );

                // custom css
                update_option( 'wpsp_custom_css', '' );

                // footer text
                update_option( 'wpsp_text_footer', '' );
								
            }
						
						if( $upgrade_version < '9.0.1' ){
							 
							 global $wpdb;
							 $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_ticket CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
							 $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_ticket_thread CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
							 $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_attachments CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
							 $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_custom_fields CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
							 $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_catagories CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
							 $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_custom_status CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
							 $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_custom_priority CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
							 $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_agent_assign_data CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
							 $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_ticket_form_order CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
							 $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_ticket_list_order CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
							 $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_users CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
							 $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_panel_custom_menu CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
							 $wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_support_menu CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci;");
						}
						
						if( $upgrade_version < '9.0.2' ){
							
								global $wpsupportplus;
								$settings_general = get_option('wpsp_settings_general');
								$support_page_id = $settings_general['support_page'];
								if($support_page_id){
									$page = get_post($support_page_id);
									$contents = $page->post_content;
									if (!preg_match('/[wp_support_plus]/', $contents)) {
										$contents = $contents.PHP_EOL.'[wp_support_plus]';
										$attr = array(
									      'ID'           => $support_page_id,
									      'post_content' => $contents
									  );
										wp_update_post( $attr );
									}
								}
							
						}
						
						if( $upgrade_version < '9.0.4' ){
							
								/**
								 * Update translation option for custom status label in order to support WPML
								 */
								
								global $wpdb;
								$default_status = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_status");
								$custom_status_localize = get_option('wpsp_custom_status_localize');
								if (!$custom_status_localize) {
										$custom_status_localize = array();
								}
								foreach($default_status as $status){
									
										$custom_status_localize['label_'.$status->id] = $status->name;
										update_option('wpsp_custom_status_localize', $custom_status_localize);
								}
								
								/**
								 * Update translation option for custom category label in order to support WPML
								 */
								
								$default_category = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_catagories");
								$custom_category_localize = get_option('wpsp_custom_category_localize');
								if (!$custom_category_localize) {
										$custom_category_localize = array();
								}
								foreach($default_category as $category){
										
										$custom_category_localize['label_'.$category->id] = $category->name;
										update_option('wpsp_custom_category_localize', $custom_category_localize);
								}
								
								/**
								 * Update translation option for custom priority label in order to support WPML
								 */
								
								$default_priority = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_priority");
								$custom_priority_localize = get_option('wpsp_custom_priority_localize');
								if (!$custom_priority_localize) {
										$custom_priority_localize = array();
								}
								foreach($default_priority as $priority){
										
										$custom_priority_localize['label_'.$priority->id] = $priority->name;
										update_option('wpsp_custom_priority_localize', $custom_priority_localize);
								}
								
								/**
								 * Update translation option for custom fields label in order to support WPML
								 */
								
								$default_custom_fields = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_fields");
								$custom_fields_localize = get_option('wpsp_custom_fields_localize');
								if (!$custom_fields_localize) {
										$custom_fields_localize = array();
								}
								foreach($default_custom_fields as $custom_fields){
										
										$custom_fields_localize['label_'.$custom_fields->id] = $custom_fields->label;
										update_option('wpsp_custom_fields_localize', $custom_fields_localize);
								}
								
								/**
								 *	delete cookie for first use after this update for replacing serialize with json_decode
								 */
								 if ( isset($_COOKIE["wpsp_user_session"])) {
								 		unset($_COOKIE["wpsp_user_session"]);
										setcookie("wpsp_user_session", null, strtotime('-1 day'), COOKIEPATH);
								 }
								 if ( isset($_COOKIE["wpsp_ticket_filters"])) {
									 	unset($_COOKIE["wpsp_ticket_filters"]);
										setcookie("wpsp_ticket_filters", null, strtotime('-1 day'), COOKIEPATH);
								 }
								
						}
						
						if( $upgrade_version < '9.0.6' ){
								
								$wpsp_thank_you_page=get_option('wpsp_thank_you_page');
								if($wpsp_thank_you_page['body'] == ""){
										
										$thank_you = '<p>Dear {customer_name},</p><p>Thank you for creating ticket. Our agent will shortly look after this and get back as soon as possible.</p>';
										$wpsp_thank_you_page_body= array(
												'body' => $thank_you
										);
										update_option('wpsp_thank_you_page', $wpsp_thank_you_page_body);
								}
						}
						
						if ($upgrade_version < '9.0.7') {
							
							global $wpdb;
							//Widget Setting
							$default_widget=get_option('wpsp_ticket_widget_order');
							if(!$default_widget){
								$default_widget= array(
									'ticket-status' 			=> 'Ticket Status',
									'raised-by' 					=> 'Raised By',
									'assigned-agent'			=> 'Assigned Agents',
									'agent-only-fields'  	=> 'Agent Only Fields',
									'ticket-fields'			  => 'Ticket Fields'

								);
								update_option( 'wpsp_ticket_widget_order',$default_widget);
							}
							
							//Clone Ticket Issue Fix
							$wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_ticket_thread MODIFY ticket_id bigint(20)");
														
							//Customer Ticket
						 	$wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_ticket_list_order ADD customer_ticket TINYINT NOT NULL DEFAULT 0");
						 
						 	$values=array(
 								 'customer_ticket' => 1
							);
							
						 	$wpdb->query("UPDATE {$wpdb->prefix}wpsp_ticket_list_order SET customer_ticket='1' WHERE field_key='id' OR field_key='status' OR field_key='subject' OR field_key='category'");
							
						}
						
						if ($upgrade_version < '9.0.9') {
								//Instructions in custom field
								global $wpdb;
								$wpdb->query("ALTER TABLE {$wpdb->prefix}wpsp_custom_fields ADD instructions TEXT NULL DEFAULT NULL");
						}
						
						if ($upgrade_version < '9.0.9') {
									global $wpdb;
									$upgrade_step = get_option( 'wpsp_upgrade' );
									
									$result = $wpdb->get_var("SELECT count(field_key) FROM {$wpdb->prefix}wpsp_ticket_list_order WHERE field_key = 'deleted_ticket' ");
									if($result == 0){
											
											$sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
																	. "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
																	. " VALUES "
																	. "( 'deleted_ticket', 'NULL', 'NULL', 'LIKE', 0, 0, 0, 1 )";
											
											$wpdb->query($sql);
											
		 								 $wpsp_ticket_active_pre_value = get_option('wpsp_ticket_active_pre_value');
		 								 $wpsp_ticket_active_pre_value['ticket_active'] = 1;
		 								 update_option('wpsp_ticket_active_pre_value',$wpsp_ticket_active_pre_value );
								 }
						}
						
			}			
    }

endif;

new WPSP_Install();
