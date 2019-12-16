<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_EN_Install' ) ) :

    /**
     * WPSP installation and updating class
     */
    class WPSP_EN_Install {

        /**
         * Constructor
         */
        public function __construct() {

            register_activation_hook( WPSP_EN_PLUGIN_FILE, array($this,'install') );
            register_deactivation_hook( WPSP_EN_PLUGIN_FILE, array($this,'deactivate') );
            $this->check_version();
        }

        /**
         * Check version of WPSP
         */
        private function check_version(){

            $installed_version = get_option( 'wpsp_en_version' );
            if( $installed_version != WPSP_EN_VERSION ){
                $this->install();
            }

            // last version where upgrade check done
            $upgrade_version = get_option( 'wpsp_en_upgrade_version' );
            if( $upgrade_version != WPSP_EN_VERSION ){
                $this->upgrade();
                update_option( 'wpsp_en_upgrade_version', WPSP_EN_VERSION );
            }

        }

        /**
         * Install WPSP
         */
        function install(){

            $this->create_tables();
            update_option( 'wpsp_en_version', WPSP_EN_VERSION );
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

            $upgrade_version = get_option( 'wpsp_en_upgrade_version' ) ? get_option( 'wpsp_en_upgrade_version' ) : 0;

            //Version 1.0.0
            if( $upgrade_version < '1.0.0' ){

								// general settings
								$emailSettings = get_option( 'wpsp_email_notification_settings' );
								if ($emailSettings) {
									
										$email_notification = array(
											'from_name'  => $emailSettings['default_from_name'],
											'from_email' => $emailSettings['default_from_email'],
											'reply_to'   => $emailSettings['default_reply_to'],
										);
										update_option( 'wpsp_email_notification', $email_notification );
									
								}
								
								$wpsp_en_notification = array();
								
								// create new ticket
								$wpsp_et_create_new_ticket = get_option( 'wpsp_et_create_new_ticket' );
								if ($wpsp_et_create_new_ticket) {
									
										$notification = array(
												'title'                   => 'Create Ticket Customer Notification',
												'type'                    => 'crt_tkt',
												'subject'                 => stripcslashes($wpsp_et_create_new_ticket['success_subject']),
												'description'             => stripcslashes($wpsp_et_create_new_ticket['success_body']),
												'recipients'              => array('customer'),
												'same_condition_relation' => 'OR',
												'diff_condition_relation' => 'AND'
										);
										$wpsp_en_notification[] = $notification;
										
										$recepients = array();
										if( $wpsp_et_create_new_ticket['staff_to_notify']['administrator'] ){
												$recepients[] = 'administrator';
										}
										if( $wpsp_et_create_new_ticket['staff_to_notify']['supervisor'] ){
												$recepients[] = 'supervisor';
										}
										if( $wpsp_et_create_new_ticket['staff_to_notify']['assigned_agent'] ){
												$recepients[] = 'agent';
										}
										if($wpsp_et_create_new_ticket['staff_to_notify']['all_agents']){
											$recepients[] = 'all_agents';
										}
										$notification = array(
												'title'                   => 'Create Ticket Staff Notification',
												'type'                    => 'crt_tkt',
												'subject'                 => stripcslashes($wpsp_et_create_new_ticket['staff_subject']),
												'description'             => stripcslashes($wpsp_et_create_new_ticket['staff_body']),
												'recipients'              => $recepients,
												'same_condition_relation' => 'OR',
												'diff_condition_relation' => 'AND'
										);
										$wpsp_en_notification[] = $notification;
									
								} else {
									
										$description = 'Dear {customer_name},'.PHP_EOL.PHP_EOL.'We have received your ticket and would like to thank you for writing to us. One of our colleagues will get back to you shortly.'.PHP_EOL.PHP_EOL.'Your ticket id is #{ticket_id}. You will get email notification just after we reply in your ticket but in case notification failed, you can reach your ticket on below link anytime to check its status or for further communication.'.PHP_EOL.PHP_EOL.'{ticket_url}';
										$notification = array(
												'title'                   => 'Create Ticket Customer Notification',
												'type'                    => 'crt_tkt',
												'subject'                 => '{ticket_subject}',
												'description'             => $description,
												'recipients'              => array('customer'),
												'same_condition_relation' => 'OR',
												'diff_condition_relation' => 'AND'
										);
										$wpsp_en_notification[] = $notification;
										
										$description = '<strong>{customer_name}</strong> wrote:'.PHP_EOL.PHP_EOL.'{ticket_description}'.PHP_EOL.PHP_EOL.'{ticket_url}';
										$notification = array(
												'title'                   => 'Create Ticket Staff Notification',
												'type'                    => 'crt_tkt',
												'subject'                 => '{ticket_subject}',
												'description'             => $description,
												'recipients'              => array('administrator','supervisor','agent'),
												'same_condition_relation' => 'OR',
												'diff_condition_relation' => 'AND'
										);
										$wpsp_en_notification[] = $notification;
									
								}
								
								// Reply Ticket
								$wpsp_et_reply_ticket = get_option( 'wpsp_et_reply_ticket' );
								if ($wpsp_et_reply_ticket) {
									
										$recepients = array();
										if( $wpsp_et_reply_ticket['notify_to']['administrator'] ){
												$recepients[] = 'administrator';
										}
										if( $wpsp_et_reply_ticket['notify_to']['supervisor'] ){
												$recepients[] = 'supervisor';
										}
										if( $wpsp_et_reply_ticket['notify_to']['assigned_agent'] ){
												$recepients[] = 'agent';
										}
										if( $wpsp_et_reply_ticket['notify_to']['customer'] ){
												$recepients[] = 'customer';
										}
										if( $wpsp_et_reply_ticket['notify_to']['all_agents'] ){
												$recepients[] = 'all_agents';
										}
										$notification = array(
												'title'                   => 'Reply Ticket Notification',
												'type'                    => 'rep_tkt',
												'subject'                 => stripcslashes($wpsp_et_reply_ticket['reply_subject']),
												'description'             => stripcslashes($wpsp_et_reply_ticket['reply_body']),
												'recipients'              => $recepients,
												'same_condition_relation' => 'OR',
												'diff_condition_relation' => 'AND'
										);
										$wpsp_en_notification[] = $notification;
									
								} else {
									
										$description = '<strong>{reply_user_name}</strong> wrote:'.PHP_EOL.PHP_EOL.'{reply_description}'.PHP_EOL.PHP_EOL.'{ticket_url}'.PHP_EOL.PHP_EOL.'{ticket_history}';
										$notification = array(
												'title'                   => 'Reply Ticket Notification',
												'type'                    => 'rep_tkt',
												'subject'                 => '{ticket_subject}',
												'description'             => $description,
												'recipients'              => array('administrator','supervisor','agent','customer'),
												'same_condition_relation' => 'OR',
												'diff_condition_relation' => 'AND'
										);
										$wpsp_en_notification[] = $notification;
									
								}
								
								// Change Status
								$wpsp_et_change_ticket_status = get_option( 'wpsp_et_change_ticket_status' );
								if ($wpsp_et_change_ticket_status) {
									
										$recepients = array();
										if( $wpsp_et_change_ticket_status['notify_to']['administrator'] ){
												$recepients[] = 'administrator';
										}
										if( $wpsp_et_change_ticket_status['notify_to']['supervisor'] ){
												$recepients[] = 'supervisor';
										}
										if( $wpsp_et_change_ticket_status['notify_to']['assigned_agent'] ){
												$recepients[] = 'agent';
										}
										if( $wpsp_et_change_ticket_status['notify_to']['customer'] ){
												$recepients[] = 'customer';
										}
										if( $wpsp_et_change_ticket_status['notify_to']['all_agents'] ){
												$recepients[] = 'all_agents';
										}
										$notification = array(
												'title'                   => 'Change status email notification',
												'type'                    => 'cng_sts',
												'subject'                 => stripcslashes($wpsp_et_change_ticket_status['mail_subject']),
												'description'             => stripcslashes($wpsp_et_change_ticket_status['mail_body']),
												'recipients'              => $recepients,
												'same_condition_relation' => 'OR',
												'diff_condition_relation' => 'AND'
										);
										$wpsp_en_notification[] = $notification;
									
								} else {
									
										$description  = '{updated_by} changed status of the ticket.'.PHP_EOL.PHP_EOL;
										$description .= '<strong>Below are details of current status of ticket:</strong>'.PHP_EOL.PHP_EOL;
										$description .= '<strong>Status :</strong> {ticket_status}'.PHP_EOL;
										$description .= '<strong>Category :</strong> {ticket_category}'.PHP_EOL;
										$description .= '<strong>Priority :</strong> {ticket_priority}'.PHP_EOL.PHP_EOL;
										$description .= '{ticket_url}';
										
										$notification = array(
												'title'                   => 'Change status email notification',
												'type'                    => 'cng_sts',
												'subject'                 => '{ticket_subject}',
												'description'             => $description,
												'recipients'              => array('administrator','supervisor','agent','customer'),
												'same_condition_relation' => 'OR',
												'diff_condition_relation' => 'AND'
										);
										$wpsp_en_notification[] = $notification;
									
								}
								
								// Assign agent
								$wpsp_et_change_ticket_assign_agent = get_option( 'wpsp_et_change_ticket_assign_agent' );
								if ($wpsp_et_change_ticket_assign_agent) {
									
										$recepients = array();
										if( $wpsp_et_change_ticket_assign_agent['notify_to']['administrator'] ){
												$recepients[] = 'administrator';
										}
										if( $wpsp_et_change_ticket_assign_agent['notify_to']['supervisor'] ){
												$recepients[] = 'supervisor';
										}
										if( $wpsp_et_change_ticket_assign_agent['notify_to']['assigned_agent'] ){
												$recepients[] = 'agent';
										}
										if( $wpsp_et_change_ticket_assign_agent['notify_to']['customer'] ){
												$recepients[] = 'customer';
										}
										if( $wpsp_et_change_ticket_status['notify_to']['all_agents'] ){
												$recepients[] = 'all_agents';
										}
										$notification = array(
												'title'                   => 'Assign agent email notification',
												'type'                    => 'asgn_agnt',
												'subject'                 => stripcslashes($wpsp_et_change_ticket_assign_agent['mail_subject']),
												'description'             => stripcslashes($wpsp_et_change_ticket_assign_agent['mail_body']),
												'recipients'              => $recepients,
												'same_condition_relation' => 'OR',
												'diff_condition_relation' => 'AND'
										);
										$wpsp_en_notification[] = $notification;
									
								} else {
									
										$description  = '{updated_by} assigned ticket to {assigned_agent}.'.PHP_EOL.PHP_EOL;
										$description .= '<strong>Below are details of the ticket:</strong>'.PHP_EOL.PHP_EOL;
										$description .= '<strong>Subject :</strong> {ticket_subject}'.PHP_EOL.PHP_EOL;
										$description .= '<strong>Description : </strong>'.PHP_EOL.PHP_EOL;
										$description .= '{ticket_description}'.PHP_EOL.PHP_EOL;
										$description .= '{ticket_url}';
										
										$notification = array(
												'title'                   => 'Assign agent email notification',
												'type'                    => 'asgn_agnt',
												'subject'                 => '{ticket_subject}',
												'description'             => $description,
												'recipients'              => array('administrator','supervisor','agent'),
												'same_condition_relation' => 'OR',
												'diff_condition_relation' => 'AND'
										);
										$wpsp_en_notification[] = $notification;
									
								}
								
								// Delete Ticket
								$wpsp_et_delete_ticket = get_option( 'wpsp_et_delete_ticket' );
								if ($wpsp_et_delete_ticket) {
									
										$recepients = array();
										if( $wpsp_et_delete_ticket['notify_to']['administrator'] ){
												$recepients[] = 'administrator';
										}
										if( $wpsp_et_delete_ticket['notify_to']['supervisor'] ){
												$recepients[] = 'supervisor';
										}
										if( $wpsp_et_delete_ticket['notify_to']['assigned_agent'] ){
												$recepients[] = 'agent';
										}
										if( $wpsp_et_delete_ticket['notify_to']['customer'] ){
												$recepients[] = 'customer';
										}
										if( $wpsp_et_change_ticket_status['notify_to']['all_agents'] ){
												$recepients[] = 'all_agents';
										}
										$notification = array(
												'title'                   => 'Delete Ticket email notification',
												'type'                    => 'del_tkt',
												'subject'                 => stripcslashes($wpsp_et_delete_ticket['mail_subject']),
												'description'             => stripcslashes($wpsp_et_delete_ticket['mail_body']),
												'recipients'              => $recepients,
												'same_condition_relation' => 'OR',
												'diff_condition_relation' => 'AND'
										);
										$wpsp_en_notification[] = $notification;
									
								} else {
									
										$description  = '{updated_by} deleted ticket #{ticket_id}.'.PHP_EOL.PHP_EOL;
										$description .= 'Ticket can not accept further replies. Please create new ticket if you need.';
										
										$notification = array(
												'title'                   => 'Delete Ticket email notification',
												'type'                    => 'del_tkt',
												'subject'                 => '{ticket_subject}',
												'description'             => $description,
												'recipients'              => array('administrator','supervisor','agent','customer'),
												'same_condition_relation' => 'OR',
												'diff_condition_relation' => 'AND'
										);
										$wpsp_en_notification[] = $notification;
									
								}
								
								update_option( 'wpsp_en_notification', $wpsp_en_notification );

            }

        }


    }

endif;

new WPSP_EN_Install();
