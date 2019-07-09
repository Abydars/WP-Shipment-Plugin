<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $current_user;
$this->supervisor_categories = $this->get_supervisor_categories($current_user->ID);
$ticket_cap_list = apply_filters('wpsp_ticket_cap_list', array(

    'read'                  => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/read.php',
    'close_ticket'          => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/close_ticket.php',
    'delete_ticket'         => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/delete_ticket.php',
    'clone_ticket'          => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/clone_ticket.php',
    'edit_subject'          => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/edit_subject.php',
    'post_reply'            => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/post_reply.php',
    'add_note'              => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/add_note.php',
    'view_log'              => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/view_log.php',
    'view_note'             => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/view_note.php',
    'edit_thread'           => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/edit_thread.php',
    'delete_thread'         => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/delete_thread.php',
    'thread_email'          => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/thread_email.php',
    'change_status'         => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/change_status.php',
    'view_raised_by'        => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/view_raised_by.php',
    'change_raised_by'      => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/change_raised_by.php',
    'view_raised_by_email'  => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/view_raised_by_email.php',
    'view_assign_agent'     => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/view_assign_agent.php',
    'change_assign_agent'   => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/change_assign_agent.php',
    'view_agent_fields'     => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/view_agent_fields.php',
    'change_agent_fields'   => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/change_agent_fields.php',
    'change_fields'         => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/change_fields.php',
		'restore_ticket'        => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/restore_ticket.php',
		'permanent_delete_ticket' => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/permanent_delete_ticket.php',
		'new_thread'						=> WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/new_thread.php'
));

if(isset($ticket_cap_list[$cap])){
    include $ticket_cap_list[$cap];
}
