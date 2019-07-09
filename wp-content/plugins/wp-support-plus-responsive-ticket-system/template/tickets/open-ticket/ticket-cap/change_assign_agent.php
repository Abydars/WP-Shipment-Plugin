<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user;
$wpsp_user_session = $this->get_current_user_session();

$agent_settings = $this->get_agent_settings();

$assigned_agents = explode(',', $ticket->assigned_to);

if($this->is_administrator($current_user)){
    
    $flag = true;
    
} else if($this->is_supervisor($current_user)){
    
    $supervisor_categories = $this->supervisor_categories;
    
    if( in_array( $ticket->cat_id, $supervisor_categories ) || in_array( $current_user->ID, $assigned_agents )){
        
        $flag = true;
        
    }
    
} else if($this->is_agent($current_user)){
    
    if( in_array( $current_user->ID, $assigned_agents ) && $agent_settings['agent_allow_assign_ticket'] ){
        
        $flag = true;
        
    }
    
}

$flag = apply_filters( 'wpsp_ticket_cap_change_assign_agent', $flag, $ticket );
