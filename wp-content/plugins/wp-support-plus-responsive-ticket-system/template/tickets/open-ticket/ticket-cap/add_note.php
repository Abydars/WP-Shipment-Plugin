<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user,$wpsupportplus;
$wpsp_user_session = $this->get_current_user_session();
$assigned_agents = explode(',', $ticket->assigned_to);
$public_mode_tickets= $wpsupportplus->functions->make_ticket_as_public();

if($this->is_administrator($current_user) && $ticket->active != 0){
    
    $flag = true;
    
} else if($this->is_supervisor($current_user) && $ticket->active != 0){
    if( $public_mode_tickets ){
				$flag = true;
		}else{
				$supervisor_categories = $this->supervisor_categories;
		    
		    if( in_array( $ticket->cat_id, $supervisor_categories ) || in_array( $current_user->ID, $assigned_agents ) || $this->is_allow_staff_read_all_ticket() ){
		        
		        $flag = true;
		        
		    }
		}
    
    
} else if($this->is_agent($current_user) && $ticket->active != 0){
    if( $public_mode_tickets ){
				$flag = true;
		}else{
				if( in_array( $current_user->ID, $assigned_agents ) || $this->is_allow_staff_read_all_ticket() ){
		        
		        $flag = true;
		        
		    }
		}
}

$flag = apply_filters( 'wpsp_ticket_cap_add_note', $flag, $ticket );
