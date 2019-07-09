<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $current_user, $wpdb;

$loading_html = '<div class="wpsp_filter_loading_icon"><img src="'.WPSP_PLUGIN_URL.'asset/images/ajax-loader@2x.gif"></div>';

$wpsp_data = array(
    'ajax_url'                             => admin_url( 'admin-ajax.php' ),
    'date_format'                          => $wpsupportplus->functions->get_date_format(),
    'support_url'                          => $wpsupportplus->functions->get_support_page_url(),
    'lbl_please_wait'                      => __('Please wait...','wp-support-plus-responsive-ticket-system'),
    'attachment_icon'                      => WPSP_PLUGIN_URL.'asset/images/icons/attachment.png',
    'attachment_cancel_icon'               => WPSP_PLUGIN_URL . 'asset/images/icons/close.png',
    'current_user_id'                      => $current_user->ID,
    'current_user_name'                    => $current_user->display_name,
    'attachment_tooltip'                   => __('Attach Files','wp-support-plus-responsive-ticket-system'),
    'lbl_attachment_file_type_not_allowed' => __("'.exe', '.php' files are not allowed!",'wp-support-plus-responsive-ticket-system'),
    'lbl_enter_required'                   => __("Required field should not be empty!",'wp-support-plus-responsive-ticket-system'),
    'lbl_wrong_email'                      => __("Incorrect Email Address!",'wp-support-plus-responsive-ticket-system'),
    'lbl_wrong_url'                        => __("Incorrect URL!",'wp-support-plus-responsive-ticket-system'),
    'lbl_are_you_sure'                     => __("Are you sure?",'wp-support-plus-responsive-ticket-system'),
    'lbl_enter_filter_name'                => __("Please enter filter name!",'wp-support-plus-responsive-ticket-system'),
    'lbl_default_filter_can_not_deleted'   => __("Default filter can not be deleted!",'wp-support-plus-responsive-ticket-system'),
    'lbl_public_filter_can_not_deleted'    => __("You do not have permission to delete public filter!",'wp-support-plus-responsive-ticket-system'),
    'is_agent'                             => $wpsupportplus->functions->is_agent($current_user),
    'is_supervisor'                        => $wpsupportplus->functions->is_supervisor($current_user),
    'is_administrator'                     => $wpsupportplus->functions->is_administrator($current_user),
    'loading_html'                         => $loading_html,
    'lbl_guest_confirm'                    => __("WARNING: Anyone having your email address can access your guest tickets. Please avoid sharing any sensitive data.",'wp-support-plus-responsive-ticket-system'),
    'ticket_thread_body_height'            => apply_filters('ticket_thread_body_height', 100 ),
    'lbl_view_more'                        => __("View More ...",'wp-support-plus-responsive-ticket-system'),
    'lbl_view_less'                        => __("View Less ...",'wp-support-plus-responsive-ticket-system'),
    'lbl_reply_body_empty'                 => __("Reply can not be empty!",'wp-support-plus-responsive-ticket-system'),
    'lbl_note_body_empty'                  => __("Note can not be empty!",'wp-support-plus-responsive-ticket-system'),
    'lbl_choose_reg_user'                  => __("Please choose registered user!",'wp-support-plus-responsive-ticket-system'),
    'lbl_name_or_email_empty'              => __("Name or Email not given!",'wp-support-plus-responsive-ticket-system'),
    'wpspAttachMaxFileSize'                => $wpsupportplus->functions->get_attachment_size(),
    'wpspAttachFileSizeExeeded'            => __('File Size limit exceeded! Allowed limit:','wp-support-plus-responsive-ticket-system').' '.$wpsupportplus->functions->get_attachment_size().__('MB','wp-support-plus-responsive-ticket-system'),
		'hide_filters'												 => __('Hide Filters','wp-support-plus-responsive-ticket-system'),
		'show_filters'												 => __('Show Filters','wp-support-plus-responsive-ticket-system'),
);

echo json_encode($wpsp_data);