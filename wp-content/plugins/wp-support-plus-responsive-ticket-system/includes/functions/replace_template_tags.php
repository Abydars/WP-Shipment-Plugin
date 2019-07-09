<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb,$wpsupportplus;

$ticket_id = $ticket->id;

include_once WPSP_ABSPATH . 'template/tickets/open-ticket/class-fields-formatting.php';
$fields_format = new WPSP_Ticket_Field_Formatting( $ticket_id, $ticket );

// ticket id
$str = preg_replace('/{ticket_id}/', $ticket_id, $str);

// ticket subject
$str = preg_replace('/{ticket_subject}/', stripslashes(htmlspecialchars_decode($ticket->subject,ENT_QUOTES)), $str);

// description
$thread      = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}wpsp_ticket_thread WHERE ticket_id=$ticket_id AND is_note=0 ORDER BY create_time ASC LIMIT 0,1");
$description = stripslashes(htmlspecialchars_decode($thread->body,ENT_QUOTES));
if( $thread->attachment_ids && apply_filters('wpsp_enable_mail_attachments',1) ){
	$description .= '<strong>'.__('Attachments:', 'wp-support-plus-responsive-ticket-system').'</strong><br>';
	$attachments  = explode(',', $thread->attachment_ids);
	$count        = 0;
	foreach ($attachments as $attachment_id) {
		$attachment   = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_attachments WHERE id=".$attachment_id);
		$download_url = $this->get_support_page_url().'?wpsp_attachment='.$attachment_id.'&dc='.time();
		$description .= ++$count.' . <a href="'.$download_url.'" target="_blank">'.$attachment->filename.'</a><br>';
	}

}
$description=str_replace("$",'\$',$description);
$str = preg_replace('/{ticket_description}/', $description, $str);

// ticket status
$status = $wpdb->get_var( "select name from {$wpdb->prefix}wpsp_custom_status where id=".$ticket->status_id );
$custom_status_localize = get_option('wpsp_custom_status_localize');
$str = preg_replace('/{ticket_status}/', $custom_status_localize['label_'.$ticket->status_id], $str);

// ticket category
$category = $wpdb->get_var( "select name from {$wpdb->prefix}wpsp_catagories where id=".$ticket->cat_id );
$custom_category_localize = get_option('wpsp_custom_category_localize');
$str = preg_replace('/{ticket_category}/', $custom_category_localize['label_'.$ticket->cat_id], $str);

// ticket priority
$priority = $wpdb->get_var( "select name from {$wpdb->prefix}wpsp_custom_priority where id=".$ticket->priority_id );
$custom_priority_localize = get_option('wpsp_custom_priority_localize');
$str = preg_replace('/{ticket_priority}/', $custom_priority_localize['label_'.$ticket->priority_id], $str);

// customer name
$str = preg_replace('/{customer_name}/', $ticket->guest_name, $str);

// customer email
$str = preg_replace('/{customer_email}/', $ticket->guest_email, $str);

// time created
$date = date_i18n( $this->get_display_date_format(), strtotime( get_date_from_gmt( $ticket->create_time, 'Y-m-d H:i:s') ) ) ;
$str = preg_replace('/{time_created}/', $date, $str);

// agent created
$agent_created = '';
if( $ticket->agent_created != 0 ){
    $agent          = get_userdata($ticket->agent_created);
    $agent_created  = $agent->display_name;
}
$str = preg_replace('/{agent_created}/', $agent_created, $str);

// assigned agents
$assigned_agents    = '';
$agents             = explode(',', $ticket->assigned_to);
if(count($agents) == 1 && $agents[0] == 0 ){
    $assigned_agents = __('None','wp-support-plus-responsive-ticket-system');
} else {
    $html = array();
    foreach ( $agents as $agent_id ){
        $agent  = get_userdata($agent_id);
        $html[] = $agent->display_name;
    }
    $assigned_agents = implode(', ', $html);
}
$str = preg_replace('/{assigned_agent}/', $assigned_agents, $str);

//updated by
$updated_by = '';
if( $ticket->updated_by != 0  ){
	$update_user = get_userdata( $ticket->updated_by );
	$updated_by  = $update_user->display_name;
}
$str = preg_replace('/{updated_by}/', $updated_by, $str);

//ticket url
$ticket_url = $this->get_support_page_url().'?page=tickets&section=ticket-list&action=open-ticket&id='.$ticket_id;
$str = preg_replace('/{ticket_url}/', '<a href="'.$ticket_url.'" target="_blank">'.$ticket_url.'</a>', $str);

// reply related fields_format
$reply = $wpdb->get_row("SELECT * FROM {$wpdb->prefix}wpsp_ticket_thread WHERE ticket_id=$ticket_id AND (is_note=0 OR is_note=1) ORDER BY create_time DESC LIMIT 0,1");
$str = preg_replace('/{reply_user_name}/', $reply->guest_name, $str);
$str = preg_replace('/{reply_user_email}/', $reply->guest_email, $str);
$description = stripslashes(htmlspecialchars_decode($reply->body,ENT_QUOTES));
if($reply->attachment_ids && apply_filters('wpsp_reply_enable_mail_attachments',1)){
	$description .= '<strong>'.__('Attachments:', 'wp-support-plus-responsive-ticket-system').'</strong><br>';
	$attachments  = explode(',', $reply->attachment_ids);
	$count        = 0;
	foreach ($attachments as $attachment_id) {
		$attachment   = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_attachments WHERE id=".$attachment_id);
		$download_url = $this->get_support_page_url().'?wpsp_attachment='.$attachment_id.'&dc='.time();
		$description .= ++$count.' . <a href="'.$download_url.'" target="_blank">'.$attachment->filename.'</a><br>';
	}
}
$description=str_replace("$",'\$',$description);
$str = preg_replace('/{reply_description}/', $description, $str);

// ticket history
$history_str = '';
$history     = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpsp_ticket_thread WHERE ticket_id=$ticket_id AND is_note=0 ORDER BY create_time DESC LIMIT 1,100");

foreach ($history as $reply) {
	
	$date         = date_i18n( $wpsupportplus->functions->get_display_date_format(), strtotime( get_date_from_gmt( $reply->create_time, 'Y-m-d H:i:s') ) ) ;
  $history_str .= sprintf( __( 'On %1$s, %2$s wrote:', 'wp-support-plus-responsive-ticket-system' ), $date, $reply->guest_name );
	$history_str .= '<blockquote>';
	$history_str .= stripslashes(htmlspecialchars_decode($reply->body,ENT_QUOTES));
	$history_str .= '</blockquote>';
}
$history_str=str_replace("$",'\$',$history_str);
$str = preg_replace('/{ticket_history}/', $history_str, $str);


//custom fields
$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_fields");
foreach ( $results as $custom_field ) {

    ob_start();
    if( $this->is_new_line_field_type($custom_field->field_type) ){
        echo '<br>';
    }
    $fields_format->get_field_val($custom_field);
    $field_html = ob_get_clean();
    $str = preg_replace('/{cust'.$custom_field->id.'}/', $field_html, $str);
}

$str = apply_filters( 'wpsp_replace_template_tags', $str, $ticket_id, $ticket );
