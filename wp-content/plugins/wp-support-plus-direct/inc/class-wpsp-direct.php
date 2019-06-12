<?php

class WPSP_Direct {
	public function __construct() {
		add_action('wp_ajax_createNewTicket', array($this, 'createNewTicket'));
		add_action('wp_ajax_nopriv_createNewTicket', array($this, 'createNewTicket'));
		add_action('wp_ajax_set_assign_agent', array($this, 'set_assign_agent'));
		add_action('wp_ajax_nopriv_set_assign_agent', array($this, 'set_assign_agent'));
		add_action('wp_ajax_replyTicket', array($this, 'reply_ticket'));
		add_action('wp_ajax_nopriv_replyTicket', array($this, 'reply_ticket'));
		
		add_action('admin_menu', array($this, 'render_admin_menu'), 1000);
	}
	
	public function render_admin_menu() {
		add_menu_page( 
			__('Support Tickets', 'support-tickets'),
			'Support Tickets',
			'manage_support_plus_ticket',
			'support-tickets',
			array($this, 'render_support_tickets'),
			'',
			30
		);
	}
	
	public function render_support_tickets() {
		?>
		<script>
			jQuery(function($) {
				$("#wpbody-content *:not(iframe)").remove();
				$("#wpbody-content iframe").css('height', $(window).height() - 120);
				$("#collapse-button").trigger("click");
			});
		</script>
		<iframe src="<?php bloginfo('url'); ?>/support-tickets/?page=tickets&section=ticket-list" width="100%" height="500" border="0"></iframe>
		<?php
	}
	
	public function createNewTicket() {
		global $wpdb, $wpsupportplus;

		$user_id            = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : 0;
		$guest_name         = isset($_POST['guest_name']) ? sanitize_text_field($_POST['guest_name']) : '';
		$guest_email        = isset($_POST['guest_email']) ? sanitize_text_field($_POST['guest_email']) : '';
		$agent_created      = isset($_POST['agent_created']) ? intval(sanitize_text_field($_POST['agent_created'])) : 0;
		$subject            = isset($_POST['subject']) ? wp_kses_post($_POST['subject']) : apply_filters( 'wpsp_create_ticket_subject', __('No Subject', 'wp-support-plus-responsive-ticket-system') );
		$description        = isset($_POST['description']) ? wp_kses_post($_POST['description']) : apply_filters( 'wpsp_create_ticket_description', __('No Description', 'wp-support-plus-responsive-ticket-system') );
		$category           = isset($_POST['category']) ? intval(sanitize_text_field($_POST['category'])) : $wpsupportplus->functions->get_default_category();
		$priority           = isset($_POST['priority']) ? intval(sanitize_text_field($_POST['priority'])) : $wpsupportplus->functions->get_default_priority();
		$status             = $wpsupportplus->functions->get_default_status();
		$time               = current_time('mysql', 1);
		$type               = $user_id ? 'user' : 'guest' ;
		$ticket_user        = get_userdata($user_id);

		/**
		 * Check nonce
		 */
		$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;
		if( !wp_verify_nonce($nonce) ){
			die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
		}

		if( $user_id ){
			$user = get_userdata($user_id);
			$guest_name  = $user->display_name;
			$guest_email = $user->user_email;
		}

		/**
		 * If ticket is created by current user, agent created should not come into picture and should be 0
		 */
		if( $user_id == $agent_created ){
			$agent_created = 0;
		}

		$values = array(
			'subject'       => htmlspecialchars($subject, ENT_QUOTES),
			'created_by'    => $user_id,
			'updated_by'    => 0,
			'guest_name'    => $guest_name,
			'guest_email'   => $guest_email,
			'status_id'     => $status,
			'cat_id'        => $category,
			'priority_id'   => $priority,
			'type'          => $type,
			'agent_created' => $agent_created,
			'create_time'   => $time,
			'update_time'   => $time
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

		/**
		 * Insert custom fields to DB
		 */
		$sql = "SELECT f.field_key as id, c.field_type as type, c.field_categories as categories "
				. "FROM {$wpdb->prefix}wpsp_ticket_form_order f "
				. "INNER JOIN {$wpdb->prefix}wpsp_custom_fields c ON f.field_key = c.id "
				. "WHERE f.status = 1 ";
		$form_fields = $wpdb->get_results($sql);
		foreach ( $form_fields as $field ){

			$categories = explode(',', $field->categories);
			if( in_array(0, $categories) || in_array($category, $categories) ){

				if( isset($_POST['cust_'.$field->id]) && is_array($_POST['cust_'.$field->id]) ){

					$save_value = array();

					foreach ( $_POST['cust_'.$field->id] as $key => $val ){
						$save_value[$key] = sanitize_text_field($val);
					}

					if( $field->type == 8 && $save_value ){

						foreach ( $save_value as $key => $attachment_id ){

							$attachment_id = intval(sanitize_text_field($attachment_id));
							if($attachment_id){
								$wpdb->update($wpdb->prefix . 'wpsp_attachments', array('active' => 1), array('id' => $attachment_id));
							} else {
								unset($save_value[$key]);
							}
						}

					}

					if($save_value){
						$values['cust'.$field->id] = implode('|||', $save_value);
					}

				}

				if( isset($_POST['cust_'.$field->id]) && !is_array($_POST['cust_'.$field->id]) ){

					$save_value = sanitize_text_field($_POST['cust_'.$field->id]);

					if( $field->type == 5 && $save_value ){

						$save_value = wp_kses_post($_POST['cust_'.$field->id]);

					}

					if( $field->type == 6 && $save_value ){

						$format = str_replace('dd','d',$wpsupportplus->functions->get_date_format());
						$format = str_replace('mm','m',$format);
						$format = str_replace('yy','Y',$format);

						$date       = date_create_from_format($format, $save_value);
						$save_value = $date->format('Y-m-d H:i:s');

					}

					$values['cust'.$field->id] = $save_value;

				}
			}
		}

		$values = apply_filters( 'wpsp_create_ticket_values', $values );

		include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';

		$ticket_oprations = new WPSP_Ticket_Operations();

		$ticket_id = $ticket_oprations->create_new_ticket($values);

		/**
		 * Attachments for description
		 */
		$attachments = isset($_POST['desc_attachment']) && is_array($_POST['desc_attachment']) ? $_POST['desc_attachment'] : array();
		foreach ($attachments as $key => $attachment_id) {

			$attachment_id = intval(sanitize_text_field($attachment_id));
			if ($attachment_id) {
				$wpdb->update($wpdb->prefix . 'wpsp_attachments', array('active' => 1), array('id' => $attachment_id));
			} else {
				unset($attachments[$key]);
			}
		}
		$attachments = implode(',', $attachments);

		/**
		 * Insert thread to DB
		 */
		$values = array(
			'ticket_id'         => $ticket_id,
			'body'              => htmlspecialchars($description, ENT_QUOTES),
			'attachment_ids'    => $attachments,
			'create_time'       => $time,
			'created_by'        => $user_id,
			'guest_name'        => $guest_name,
			'guest_email'       => $guest_email
		);
		$values = apply_filters('wpsp_create_ticket_thread_values', $values);

		$ticket_oprations->create_new_thread($values);

		do_action( 'wpsp_after_create_ticket', $ticket_id );
		exit();
	}
	
	public function set_assign_agent() {
		global $wpdb, $wpsupportplus, $current_user;

		$ticket_id  = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : 0 ;
		$nonce      = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '' ;

		$ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

		/**
		 * Check nonce
		 */
		if( !wp_verify_nonce( $nonce, $ticket_id ) ){
			die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
		}

		$agents  = isset($_POST['assigned_agents']) && is_array($_POST['assigned_agents']) ? $_POST['assigned_agents'] : array() ;

		$assigned_agents = array();
		foreach( $agents as $agent ){
			
			$agent = intval(sanitize_text_field($agent)) ? intval(sanitize_text_field($agent)) : 0;
			if ($agent){
				$assigned_agents[] = $agent;
			}
			
		}

		include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';

		$ticket_oprations = new WPSP_Ticket_Operations();

		$ticket_oprations->change_assign_agent( $assigned_agents, $ticket_id );
        die();
	}
	
	public function reply_ticket() {
		global $wpdb, $wpsupportplus;

		$ticket_id              = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : 0;
		$created_by             = isset($_POST['user_id']) ? intval(sanitize_text_field($_POST['user_id'])) : 0;
		$reply_body             = isset($_POST['reply_body']) ? wp_kses_post($_POST['reply_body']) : '';
		$nonce                  = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
		$agent_reply_status     = $wpsupportplus->functions->get_agent_reply_status();
		$customer_reply_status  = $wpsupportplus->functions->get_customer_reply_status();

		$ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

		/**
		 * Check nonce
		 */
		if( !wp_verify_nonce( $nonce, $ticket_id ) ){
			die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
		}

		/**
		 * Reply body should not be empty
		 */
		if( !$reply_body ){
			die('Reply body empty');
		}

		$time           = current_time('mysql', 1);
		
		if( $created_by ){
			$user = get_userdata($created_by);
			$guest_name  = $user->display_name;
			$guest_email = $user->user_email;
		}

		/**
		 * Attachments for description
		 */
		$attachments = isset($_POST['desc_attachment']) && is_array($_POST['desc_attachment']) ? $_POST['desc_attachment'] : array();

		foreach ( $attachments as $key => $attachment_id ){

			$attachment_id = intval(sanitize_text_field($attachment_id));
			if($attachment_id){
				$wpdb->update($wpdb->prefix . 'wpsp_attachments', array('active' => 1), array('id' => $attachment_id));
			} else {
				unset($attachments[$key]);
			}
		}
		$attachments = implode(',', $attachments);

		/**
		 * Insert thread to DB
		 */
		$values = array(
			'ticket_id'         => $ticket_id,
			'body'              => htmlspecialchars($reply_body, ENT_QUOTES),
			'attachment_ids'    => $attachments,
			'create_time'       => $time,
			'created_by'        => $created_by,
			'guest_name'        => $guest_name,
			'guest_email'       => $guest_email
		);
		$values = apply_filters('wpsp_reply_ticket_thread_values', $values);

		$thread_id = $this->create_new_thread($values);
		
		/**
		 * Update ticket
		 */
		$values = array(
			'update_time' => $time,
				'updated_by'	=> $created_by
		);
		$this->change_ticket_fields( $values, $ticket_id );

		if($agent_reply_status != '' && $wpsupportplus->functions->is_staff($current_user) && $guest_email != $ticket->guest_email){
			$this->change_status($agent_reply_status, $ticket_id);
		}

		if($customer_reply_status != '' && $guest_email == $ticket->guest_email){
			$this->change_status($customer_reply_status, $ticket_id);
		}
		
		do_action( 'wpsp_after_ticket_reply', $ticket_id, $thread_id );
	}
	
	public function change_status( $status_id, $ticket_id, $guest_name='', $guest_email='', $user_id=0 ){
            
		global $wpdb;
		$current_status_id = $wpdb->get_var("select status_id from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id);
		if( $status_id == $current_status_id ) return;
		
		include WPSP_ABSPATH . 'template/tickets/ticket-operations/change_status.php';
	}
	
	public function create_new_thread( $values ){
            
		global $wpdb;
		$wpdb->insert($wpdb->prefix . 'wpsp_ticket_thread', $values);
		
		return $wpdb->insert_id;

	}
	
	/**
	 * Change ticket fields
	 */
	public function change_ticket_fields( $values, $ticket_id ){
		
		global $wpdb;
		$wpdb->update($wpdb->prefix . 'wpsp_ticket', $values, array('id' => $ticket_id));
	}
}