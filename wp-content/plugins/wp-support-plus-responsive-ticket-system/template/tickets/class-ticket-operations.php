<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Ticket_Operations' ) ) :
    
    class WPSP_Ticket_Operations {
    
        /**
         * Create ticket to DB
         */
        public function create_new_ticket($values){
            
            global $wpdb, $current_user, $wpsupportplus;

            $wpdb->insert($wpdb->prefix . 'wpsp_ticket', $values);

            $ticket_id = $wpdb->insert_id;
            
            return $ticket_id;
        }
        
        /**
         * Create new thread
         */
        public function create_new_thread( $values ){
            
            global $wpdb;
            $wpdb->insert($wpdb->prefix . 'wpsp_ticket_thread', $values);
            
            return $wpdb->insert_id;

        }

        /**
         * Reply ticket to DB
         */
        public function reply_ticket(){
            
            $ticket_thread = 0;
            include WPSP_ABSPATH . 'template/tickets/ticket-operations/reply_ticket.php';
            if($ticket_thread){
                echo 'Success';
            } else {
                echo 'No Success';
            }
            
        }
        
        /**
         * Reply ticket to DB
         */
        public function add_ticket_note(){
            
            $ticket_thread = 0;
            include WPSP_ABSPATH . 'template/tickets/ticket-operations/add_ticket_note.php';
            if($ticket_thread){
                echo 'Success';
            } else {
                echo 'No Success';
            }
            
        }
        
        /**
         * Change ticket status
         */
        public function change_category( $cat_id, $ticket_id ){
            
            global $wpdb;
            $current_cat_id = $wpdb->get_var("select cat_id from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id);
            if( $cat_id == $current_cat_id ) return;
            
            include WPSP_ABSPATH . 'template/tickets/ticket-operations/change_category.php';
            
        }
        
        /**
         * Change ticket priority
         */
        public function change_priority( $priority_id, $ticket_id ){
            
            global $wpdb;
            $current_priority_id = $wpdb->get_var("select priority_id from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id);
            if( $priority_id == $current_priority_id ) return;
            
            include WPSP_ABSPATH . 'template/tickets/ticket-operations/change_priority.php';
            
        }
        
        /**
         * Change ticket status
         */
        public function change_status( $status_id, $ticket_id, $guest_name='', $guest_email='', $user_id=0 ){
            
            global $wpdb;
            $current_status_id = $wpdb->get_var("select status_id from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id);
            if( $status_id == $current_status_id ) return;
            
            include WPSP_ABSPATH . 'template/tickets/ticket-operations/change_status.php';
        }
        
        /**
         * Change raised by
         */
        public function change_raised_by( $user_id, $user_name, $user_email, $ticket_id ){
            
            global $wpdb;
            
            $ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

            /**
             * If database values matches as change raised by, do not update DB
             * die used to exit because it is ajax response
             */
            if( $ticket->created_by == $user_id && $ticket->guest_name == $user_name && $ticket->guest_email == $user_email ){
                return;
            }
            
            include WPSP_ABSPATH . 'template/tickets/ticket-operations/change_raised_by.php';
        }
        
        /**
         * Change assign to
         */
        public function change_assign_agent( $assigned_agents, $ticket_id ){
            
            include WPSP_ABSPATH . 'template/tickets/ticket-operations/change_assign_agent.php';
        }
        
        /**
         * Change ticket fields
         */
        public function change_ticket_fields( $values, $ticket_id ){
            
            global $wpdb;
            $wpdb->update($wpdb->prefix . 'wpsp_ticket', $values, array('id' => $ticket_id));
        }
        
        /**
         * Change thread fields
         */
        public function change_thread_fields( $values, $thread_id ){
            
            global $wpdb;
            $wpdb->update($wpdb->prefix . 'wpsp_ticket_thread', $values, array('id' => $thread_id));
        }
        
        /**
         * Delete thread
         */
        public function delete_thread( $thread_id ){
            
            global $wpdb;
            $wpdb->delete($wpdb->prefix . 'wpsp_ticket_thread', array('id' => $thread_id));
        }
                                                         
    }      
endif;
