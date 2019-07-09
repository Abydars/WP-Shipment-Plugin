<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user;
$wpsp_user_session = $this->get_current_user_session();

if($this->is_administrator($current_user) && $ticket->active != 0){
    
    $flag = true;
    
}

$flag = apply_filters( 'wpsp_ticket_cap_delete_thread', $flag, $ticket );
