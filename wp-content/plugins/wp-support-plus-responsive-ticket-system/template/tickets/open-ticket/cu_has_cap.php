<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$ticket_cap_list = apply_filters('wpsp_cap_list', array(
        
    'bulk_delete_ticket'    => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/bulk_delete_ticket.php',
    'bulk_assign_agent'     => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/bulk_assign_agent.php',
    'bulk_change_status'    => WPSP_ABSPATH . 'template/tickets/open-ticket/ticket-cap/bulk_change_status.php',
));

if(isset($ticket_cap_list[$cap])){
    include $ticket_cap_list[$cap];
}
