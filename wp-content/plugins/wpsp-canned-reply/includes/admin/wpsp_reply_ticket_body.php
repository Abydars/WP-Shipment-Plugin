<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

global $wpdb;
$cu = wp_get_current_user();
$wpsp_canned_reply_template = get_option('wpsp_canned_reply_template');
$ticketStatus = __(ucfirst($reply->status), 'wp-support-plus-responsive-ticket-system');
$priority = __($reply->priority, 'wp-support-plus-responsive-ticket-system');
$wpsp_subject = stripslashes(htmlspecialchars_decode($reply->subject, ENT_QUOTES));
$ctCategoryName = $wpdb->get_var("SELECT name FROM {$wpdb->prefix}wpsp_catagories where id=" . $reply->cat_id);
$ctCategoryName = __($ctCategoryName, 'wp-support-plus-responsive-ticket-system');
$customFields = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpsp_custom_fields");
$ctCustomField = array();
foreach ($customFields as $field) {
    $cust_alice = 'cust' . $field->id;
    if ($field->field_type == '5') {
        $ctCustomField['cust' . $field->id] = nl2br($reply->$cust_alice);
    } else {
        $ctCustomField['cust' . $field->id] = $reply->$cust_alice;
    }
}

$customerName = '';
$customerEmail = '';
if ($reply->created_by) {
    $user = get_userdata($reply->created_by);
    $customerName = $user->display_name;
    $customerEmail = $user->user_email;
} else {
    $customerName = $reply->guest_name;
    $customerEmail = $reply->guest_email;
}

foreach ($wpsp_canned_reply_template['templates'] as $ct_key => $ct_val) {
    switch ($ct_key) {
        case 'reply_by_name':
            $reply_body = str_replace('{reply_by_name}', $cu->display_name, $reply_body);
            break;
        case 'reply_by_email':
            $reply_body = str_replace('{reply_by_email}', $cu->user_email, $reply_body);
            break;
        case 'ticket_status':
            $reply_body = str_replace('{ticket_status}', $ticketStatus, $reply_body);
            break;
        case 'customer_name':
            $reply_body = str_replace('{customer_name}', $customerName, $reply_body);
            break;
        case 'customer_email':
            $reply_body = str_replace('{customer_email}', $customerEmail, $reply_body);
            break;
        case 'ticket_id':
            $reply_body = str_replace('{ticket_id}', $reply->id, $reply_body);
            break;
        case 'ticket_subject':
            $reply_body = str_replace('{ticket_subject}', $wpsp_subject, $reply_body);
            break;
        case 'ticket_category':
            $reply_body = str_replace('{ticket_category}', $ctCategoryName, $reply_body);
            break;
        case 'ticket_priotity':
            $reply_body = str_replace('{ticket_priotity}', $priority, $reply_body);
            break;
        case 'ticket_url':
            $reply_body = str_replace('{ticket_url}', $wpsp_open_ticket_page_url, $reply_body);
            break;
        case 'time_created':
            $reply_body = str_replace('{time_created}', $reply->ticket->create_time, $reply_body);
            break;
        default:
            break;
    }
}
foreach ($ctCustomField as $etFieldKey => $etFieldVal) {
         $reply_body = str_replace('{' . $etFieldKey . '}', $etFieldVal, $reply_body);
}
?>