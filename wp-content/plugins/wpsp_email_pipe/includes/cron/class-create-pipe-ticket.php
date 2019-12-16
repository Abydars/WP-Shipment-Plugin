<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Create_Pipe_Ticket' ) ) :

    final class WPSP_Create_Pipe_Ticket {

        var $is_create_ticket   = true;
        var $pipe_email;
        var $from_email;
        var $from_name;
        var $subject;
        var $body;
        var $attachments;
        var $status;
        var $priority;
        var $category;
        var $user_id            = 0;
        var $type               = 'guest';
        var $time;
        var $ticket_id          = 0;
				

        public function __construct($pipe_email, $from_email, $from_name, $subject, $body, $attachments ){
						global $wpsupportplus;

            $this->pipe_email   = $pipe_email;
            $this->from_email   = $from_email;
            $this->from_name    = $from_name;
            $this->subject      = htmlspecialchars($subject, ENT_QUOTES);
            $this->body         = htmlspecialchars($body, ENT_QUOTES);
            $this->attachments  = $attachments;
            $this->status       = $wpsupportplus->functions->get_default_status();
            $this->priority     = $wpsupportplus->functions->get_default_priority();
            $this->category     = $this->get_category_id();
            $this->time         = current_time('mysql', 1);
						
            $user = get_user_by( 'email', $this->from_email );
            if($user){
                $this->user_id      = $user->ID;
                $this->from_name    = $user->display_name;
                $this->type         = 'user';
            }

            $this->ticket_id    = $this->get_ticket_id();

            $this->check_is_allow_ticket();

        }

        public function check_is_allow_ticket(){
						global $wpdb,$wpsupportplus,$current_user;

            $email_piping = get_option('wpsp_email_pipe_settings');
            
            //check in block emails
            $block_rules = explode(PHP_EOL, $email_piping['block_emails']);
					  foreach ( $block_rules as $rule ){

                $rule = trim($rule);
                if( $rule && fnmatch($rule, $this->from_email)){
                    $this->is_create_ticket = false;
                    break;
                }

            }
						
						//check in ignore emails subject
            $ignore_email_subjects = explode(PHP_EOL, $email_piping['ignore_email_subject']);
						foreach ( $ignore_email_subjects as $ignore_email_subject ){

                $ignore_email_subject = trim($ignore_email_subject);
                if($ignore_email_subject && fnmatch($ignore_email_subject, $this->subject)){
                    $this->is_create_ticket = false;
                    break;
                }

            }

            //check allowed user email setting
            if( $this->is_create_ticket && $email_piping['allowed_emails'] == 0 && $this->user_id == 0 ){
                $this->is_create_ticket = false;
            }

            //check if deleted ticket
            if( $this->is_create_ticket && $this->ticket_id ){
                $active = $wpdb->get_var( "select active from {$wpdb->prefix}wpsp_ticket where id=".$this->ticket_id );
                if( !$active ){
                    $this->is_create_ticket = false;
                }
            }

            //check for returned mail
            if( $this->is_create_ticket && $this->isReturnedEmail() ){
                $this->is_create_ticket = false;
            }
						
						
            $this->is_create_ticket = apply_filters( 'wpsp_ep_is_create_ticket', $this->is_create_ticket, $this );

        }

        public function isReturnedEmail(){

            global $wpdb, $wpsupportplus, $current_user;
						
						// Check noreply email addresses
            if ( preg_match('/not?[\-_]reply@/i', $this->from_email) ){
                return true;
            }

            // Check mailer daemon email addresses
            if ( preg_match('/mail(er)?[\-_]daemon@/i', $this->from_email) ){
                return true;
            }

            // Check autoreply subjects
            if ( preg_match('/^[\[\(]?Auto(mat(ic|ed))?[ \-]?reply/i', $this->subject) ){
                return true;
            }

            // Check out of office subjects
            if ( preg_match('/^Out of Office/i', $this->subject) ){
                return true;
            }

            // Check delivery failed email subjects
            if (
                preg_match('/DELIVERY FAILURE/i', $this->subject) ||
                preg_match('/Undelivered Mail Returned to Sender/i', $this->subject) ||
                preg_match('/Delivery Status Notification \(Failure\)/i', $this->subject) ||
                preg_match('/Returned mail\: see transcript for details/i', $this->subject)
            )
            {
                return true;
            }

            // Check Delivery failed message
            if ( preg_match('/postmaster@/i', $this->from_email) && preg_match('/Delivery has failed to these recipients/i', $this->body) ){
                return true;
            }
												
						return false;

        }

        public function get_category_id(){

            $email_piping   = get_option('wpsp_email_pipe_settings');
            return $email_piping['email_categories'][$this->pipe_email];
        }

				public function get_ticket_id(){
            
						global $wpsupportplus, $wpdb;
						
						$ticket_label = $wpsupportplus->functions->get_ticket_lable();
						$prefix       = $wpsupportplus->functions->get_ticket_id_prefix();
						if(strpos($prefix,'$')){
							$prefix = str_replace("$",'\$',$prefix);
							preg_match_all("/".$ticket_label." ".$prefix."[0-9]+/i", $this->subject, $matches);
						} else{
							preg_match_all("/".$ticket_label." ".$prefix."[0-9]+/i", $this->subject, $matches);
						}
            
						$ticket_id = isset($matches[0][0]) ? $matches[0][0] : '';
						
            if($ticket_id){
                if(strpos($prefix,'\$')){
									$prefix = str_replace('\$',"$",$prefix);
									$prefix_str = $ticket_label.' '.$prefix; 
									$ticket_id  = substr($ticket_id, strlen($prefix_str));
								}else{
									$prefix_str = $ticket_label.' '.$prefix; 
									$ticket_id  = substr($ticket_id, strlen($prefix_str));
								}
            } else {
                $ticket_id = 0;
            }
						return $ticket_id;
        }

        public function create_ticket(){

            global $wpsupportplus, $wpdb;

            $values = array(
                'subject'       => $this->subject,
                'created_by'    => $this->user_id,
                'updated_by'    => $this->user_id,
                'guest_name'    => $this->from_name,
                'guest_email'   => $this->from_email,
                'status_id'     => $this->status,
                'cat_id'        => $this->category,
                'priority_id'   => $this->priority,
                'type'          => $this->type,
                'agent_created' => 0,
                'create_time'   => $this->time,
                'update_time'   => $this->time
            );

            if( !$wpsupportplus->functions->get_ticket_id_sequence() ){

                $id = 0;
                do {
                    $id = rand(11111111, 99999999);
                    $sql = "select id from {$wpdb->prefix}wpsp_ticket where id=" . $id;
                    $result = $wpdb->get_var($sql);
                } while ($result);

                $values['id'] = $id;
            }

            $values = apply_filters( 'wpsp_create_ticket_values', $values );

            include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';

            $ticket_oprations = new WPSP_Ticket_Operations();
            $ticket_id = $ticket_oprations->create_new_ticket($values);

            $values = array(
                'ticket_id'         => $ticket_id,
                'body'              => $this->body,
                'attachment_ids'    => $this->attachments,
                'create_time'       => $this->time,
                'created_by'        => $this->user_id,
                'guest_name'        => $this->from_name,
                'guest_email'       => $this->from_email
            );
            $values = apply_filters('wpsp_create_ticket_thread_values', $values);

            $ticket_oprations->create_new_thread($values);

            do_action( 'wpsp_after_create_ticket', $ticket_id );
        }

        public function reply_ticket(){

            global $wpdb, $wpsupportplus;
													
            $user                   = get_userdata($this->user_id);
            $agent_reply_status     = $wpsupportplus->functions->get_agent_reply_status();
            $customer_reply_status  = $wpsupportplus->functions->get_customer_reply_status();

            $ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$this->ticket_id );

            include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';

            $ticket_oprations = new WPSP_Ticket_Operations();
						$reply_body =  $this->body;
						$signature='';
						$signature = get_user_meta($this->user_id,'wpsp_agent_signature',true);
						
						if($signature && $wpsupportplus->functions->is_staff($user)){
							$signature='<br>---<br>' . stripcslashes(htmlspecialchars_decode($signature, ENT_QUOTES));
							$reply_body.= $signature;
						}
						
            $values = array(
                'ticket_id'         => $this->ticket_id,
                'body'              => $reply_body,
                'attachment_ids'    => $this->attachments,
                'create_time'       => $this->time,
                'created_by'        => $this->user_id,
                'guest_name'        => $this->from_name,
                'guest_email'       => $this->from_email
            );
            $values = apply_filters('wpsp_create_ticket_thread_values', $values);

            $thread_id = $ticket_oprations->create_new_thread($values);

            $values = array(
                'update_time' => $this->time,
								'updated_by'	=> $this->user_id
            );
            $ticket_oprations->change_ticket_fields( $values, $this->ticket_id );

						if($agent_reply_status != '' && $user && $wpsupportplus->functions->is_staff($user) && mb_strtolower($this->from_email) != mb_strtolower($ticket->guest_email)){
                $ticket_oprations->change_status($agent_reply_status, $this->ticket_id, $this->from_name, $this->from_email, $this->user_id);
           	}

          	if($customer_reply_status != '' && mb_strtolower($this->from_email) == mb_strtolower($ticket->guest_email)){
                $ticket_oprations->change_status($customer_reply_status, $this->ticket_id, $this->from_name, $this->from_email, $this->user_id);
           	}

            do_action( 'wpsp_after_ticket_reply', $this->ticket_id, $thread_id );

      }
		}

endif;
