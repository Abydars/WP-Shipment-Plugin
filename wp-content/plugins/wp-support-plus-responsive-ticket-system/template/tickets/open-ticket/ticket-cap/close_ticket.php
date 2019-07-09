<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user;

$wpsp_user_session = $this->get_current_user_session();

$assigned_agents = explode(',', $ticket->assigned_to);

$wpsp_settings_general= get_option('wpsp_settings_general');
$allow_cust_close = isset($wpsp_settings_general['allow_cust_close']) ? $wpsp_settings_general['allow_cust_close'] : 0;

if($this->is_administrator($current_user)){
    
    $flag = true;
    
} else if($this->is_supervisor($current_user)){
    
    $supervisor_categories = $this->supervisor_categories;
    
    if( in_array( $ticket->cat_id, $supervisor_categories ) || in_array( $current_user->ID, $assigned_agents )){
        
        $flag = true;
        
    } else if( $ticket->created_by == $current_user->ID && $allow_cust_close==1){
        
        $flag = true;
        
    }
    
} else if($this->is_agent($current_user)){
    
    if( in_array( $current_user->ID, $assigned_agents ) ){
        
        $flag = true;
        
    } else if( $ticket->created_by == $current_user->ID && $allow_cust_close==1){
        
        $flag = true;
        
    }
    
} else if($allow_cust_close==1){
        
    if( $ticket->guest_email == $wpsp_user_session['email'] ){
        
        $flag = true;
    }
}
        
$flag = apply_filters( 'wpsp_ticket_cap_close_ticket', $flag, $ticket );
