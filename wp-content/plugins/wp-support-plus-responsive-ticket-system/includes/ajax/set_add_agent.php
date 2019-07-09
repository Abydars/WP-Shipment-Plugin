<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Check nonce
 */
if( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce']) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$agent_id   = intval(sanitize_text_field($_POST['wpsp_agent']['agent_id']));
$role       = intval(sanitize_text_field($_POST['wpsp_agent']['role']));

$values = apply_filters('wpsp_set_add_user', array(
    'user_id'       => $agent_id,
    'role'          => $role
));

$wpdb->insert( $wpdb->prefix.'wpsp_users', $values );

if($wpdb->insert_id){
    
    $user = get_userdata($agent_id);
    
    $user->remove_cap('wpsp_agent');
    $user->remove_cap('wpsp_supervisor');
    $user->remove_cap('wpsp_administrator');
    
    switch ($role){
        case 1: $user->add_cap('wpsp_agent');
            break;
        case 2: $user->add_cap('wpsp_supervisor');
            break;
        case 3: $user->add_cap('wpsp_administrator');
            break;
    }
}
if(!empty($_POST['wpsp_agent']['selected_category_id'])){
	foreach ($_POST['wpsp_agent']['selected_category_id'] as $category_id) {
			$agent_id_array       = array();
			$agent_id_array[]     = $agent_id;
			$supervisors          = $wpdb->get_row("select supervisor from {$wpdb->prefix}wpsp_catagories WHERE id=".intval($category_id));
			
			if($supervisors->supervisor == ''){
					$assigned_supervisors = array();
			}else{
					$assigned_supervisors = unserialize($supervisors->supervisor);
		  }
	    
			$supervisor_id  = array_merge($agent_id_array,$assigned_supervisors);
			$values = array(
					'supervisor' => serialize($supervisor_id)
			);
			$wpdb->update( $wpdb->prefix.'wpsp_catagories', $values, array('id'=>$category_id) );
	}
}

//assign tickets to this user those are created by him as guest
$agent = get_user_by('id', $agent_id);
$tickets = $wpdb->get_results( "select * from {$wpdb->prefix}wpsp_ticket where created_by = 0 AND guest_email = ".'"'.$agent->user_email.'"' );

if( !empty($tickets) ){
	
	$values=array(
		 'created_by' => $agent_id,
		 'updated_by' => $agent_id,
		 'type'				=> 'user'
	);
	foreach($tickets as $ticket){
		$wpdb->update($wpdb->prefix.'wpsp_ticket', $values, array('id'=>$ticket->id));
	}
}


do_action('wpsp_after_add_new_agent', $wpdb->insert_id);
