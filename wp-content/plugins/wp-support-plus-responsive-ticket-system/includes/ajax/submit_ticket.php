<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb, $current_user, $wpsupportplus,$is_wpsp_template;

    $wpsp_user_session = $wpsupportplus->functions->get_current_user_session();

    $user_id            = isset($_POST['user_id']) ? sanitize_text_field($_POST['user_id']) : 0;
    $guest_name         = isset($_POST['guest_name']) ? sanitize_text_field($_POST['guest_name']) : '';
    $guest_email        = isset($_POST['guest_email']) ? sanitize_text_field($_POST['guest_email']) : '';
    $agent_created      = isset($_POST['agent_created']) ? intval(sanitize_text_field($_POST['agent_created'])) : 0;
    $subject            = isset($_POST['subject']) ? htmlspecialchars_decode(wp_kses_post($_POST['subject'])) : apply_filters( 'wpsp_create_ticket_subject', __('No Subject', 'wp-support-plus-responsive-ticket-system') );
    $description        = isset($_POST['description']) ? wp_kses_post($_POST['description']) : apply_filters( 'wpsp_create_ticket_description', __('No Description', 'wp-support-plus-responsive-ticket-system') );
    $category           = isset($_POST['category']) ? intval(sanitize_text_field($_POST['category'])) : $wpsupportplus->functions->get_default_category();
    $priority           = isset($_POST['priority']) ? intval(sanitize_text_field($_POST['priority'])) : $wpsupportplus->functions->get_default_priority();
    $status             = $wpsupportplus->functions->get_default_status();
    $time               = current_time('mysql', 1);
    $type               = $user_id ? 'user' : 'guest' ;
    $ticket_user        = get_userdata($user_id);
		$ip_address					= isset($_SERVER) && isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'';
		
		if (strlen($ip_address)>28) {
			$ip_address = '';
		}
		
		/**
     * Check nonce
     */
		 $cap=$wpsupportplus->functions-> toggle_button_disable_captcha();
		
		 $captcha=false;
		 if($cap=='all'){
		 	$captcha=true;	
		}else if( !$user_id && $cap=='guest'){
		 	$captcha=true;
		 }
		 
		 if($captcha){
				 $captcha_key =  isset($_COOKIE) && isset($_COOKIE['wpsp_secure_code']) ? intval($_COOKIE['wpsp_secure_code']) : 0;

				 if( !isset($_POST['captcha_code']) || !wp_verify_nonce($_POST['captcha_code'],$captcha_key) ){
				     die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
				 }
				 setcookie('wpsp_secure_code','123');
		 }
    /**
     * If agent created is other than current user, don't allow
     */
    if( $current_user->ID != $agent_created && true === false ){
        die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
    }

    /**
     * Apply user name and email to guest
     */
    if( !$guest_email || !$guest_name ){
        $guest_email    = $wpsp_user_session['email'];
        $guest_name     = $wpsp_user_session['name'];
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
        'updated_by'    => $current_user->ID,
        'guest_name'    => $guest_name,
        'guest_email'   => $guest_email,
        'status_id'     => $status,
        'cat_id'        => $category,
        'priority_id'   => $priority,
        'type'          => $type,
        'agent_created' => $agent_created,
        'create_time'   => $time,
        'update_time'   => $time,
				'ip_address'    => $ip_address
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
		 $signature = get_user_meta($user_id,'wpsp_agent_signature',true);
		 if($signature){
		 	$signature='<br>---<br>' . stripcslashes(htmlspecialchars_decode($signature, ENT_QUOTES));
		 	$description.= $signature;
		 }
		 
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
		
		do_action('wpsp_sla_checkpoint', $ticket_id);
		
		$guest_ticket_redirect = $wpsupportplus->functions->get_guest_ticket_redirect();
		$guest_ticket_redirect_url= $wpsupportplus->functions->get_guest_ticket_redirect_url();
    
    $thankyou_url = $wpsupportplus->functions->get_support_page_url(array('page'=>'tickets','section'=>'create-ticket','action'=>'thankyou','ticket_id'=>$ticket_id));
		
		$redirect_url = $guest_ticket_redirect==1 ? $guest_ticket_redirect_url : $thankyou_url;
		
		?>
		
		<?php
		
		if ($is_wpsp_template) {
			wpsp_print_page_inline_script();
		} else {
			add_action('wp_footer', 'wpsp_print_page_inline_script', 900000000000 );
		}

		function wpsp_print_page_inline_script(){
			?>
			<script>
					jQuery(document).ready(function(){
							window.location.href = redirect_url;
					});
			</script>
			<?php
		}
echo json_encode($redirect_url);
