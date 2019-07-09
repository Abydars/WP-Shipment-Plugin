<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$agent_id    = isset($_POST['load_id']) ? intval(sanitize_text_field($_POST['load_id'])) : 0;
$role        = isset($_POST['role']) ? intval(sanitize_text_field($_POST['role'])) : 0;
$nonce          = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $agent_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$agent = get_userdata($agent_id);
if( $agent->has_cap('wpsp_supervisor') && $role!=2 ){
    $categories = $wpdb->get_results("select id, supervisor from {$wpdb->prefix}wpsp_catagories");
    foreach ($categories as $category){
        $existing_supervisors = $wpdb->get_var("select supervisor from {$wpdb->prefix}wpsp_catagories where id=".$category->id);
        $existing_supervisors = $existing_supervisors ? unserialize($existing_supervisors) : array();
        if(in_array($agent->ID, $existing_supervisors)){
            $agent->remove_cap('wpsp_cat_'.$category->id);
            unset($existing_supervisors[array_search($agent->ID, $existing_supervisors)]);
            $values = array(
                'supervisor'    => serialize($existing_supervisors)
            );
            $wpdb->update( $wpdb->prefix.'wpsp_catagories', $values, array('id'=>$category->id) );
        }
    }
}


$values = apply_filters('wpsp_set_edit_agent', array(
    'role' => $role
));

$wpdb->update( $wpdb->prefix.'wpsp_users', $values, array('user_id'=>$agent_id) );
    
$agent->remove_cap('wpsp_agent');
$agent->remove_cap('wpsp_supervisor');
$agent->remove_cap('wpsp_administrator');

switch ($role){
    case 1: $agent->add_cap('wpsp_agent');
        break;
    case 2: $agent->add_cap('wpsp_supervisor');
        break;
    case 3: $agent->add_cap('wpsp_administrator');
        break;
}
$wpsp_edit_agent_supervisor_categories = isset($_POST['wpsp_edit_agent_supervisor_categories']) ? intval(sanitize_text_field($_POST['wpsp_edit_agent_supervisor_categories'])): array();
$categories = implode(',', $wpsp_edit_agent_supervisor_categories);
if(!empty($categories)){
		$agent_id_array       = array();
		$agent_id_array[]     = $agent_id;
		$supervisors          = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_catagories WHERE id IN($categories)");

		foreach($supervisors as $supervisor){

				if($supervisor->supervisor == ''){
						$assigned_supervisors = array();
				}else{
						$assigned_supervisors = unserialize($supervisor->supervisor);
				}
				$supervisors_id = array_merge($agent_id_array,$assigned_supervisors);
				$unique_supervisors_id = array_unique($supervisors_id);
				$values = array(
						'supervisor' => serialize($unique_supervisors_id)
				);
				$wpdb->update( $wpdb->prefix.'wpsp_catagories', $values, array('id'=>$supervisor->id) );
		}

		$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_catagories WHERE id NOT IN($categories)");

		foreach($results as $result){
			
				if($result->supervisor == ''){
						$assigned_supervisors = array();
				}else{
						$assigned_supervisors = unserialize($result->supervisor);
				}

				if(in_array($agent_id, $assigned_supervisors)){
						$merged_array = array_merge($agent_id_array,$assigned_supervisors); 
						$supervisor = array_unique($merged_array);
						$index_of_agent_id = array_search($agent_id,$assigned_supervisors);
						unset($supervisor[$index_of_agent_id]);
						$values = array(
								'supervisor' => serialize($supervisor)
						);
						$wpdb->update( $wpdb->prefix.'wpsp_catagories', $values, array('id'=>$result->id) );
				}
		}
}else{
		$agent_id_array       = array();
		$agent_id_array[]     = $agent_id;
		$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_catagories");

		foreach($results as $result){
			
				if($result->supervisor == ''){
						$assigned_supervisors = array();
				}else{
						$assigned_supervisors = unserialize($result->supervisor);
				}

				if(in_array($agent_id, $assigned_supervisors)){
						$merged_array = array_merge($agent_id_array,$assigned_supervisors); 
						$supervisor = array_unique($merged_array);
						$index_of_agent_id = array_search($agent_id,$assigned_supervisors);
						unset($supervisor[$index_of_agent_id]);
						$values = array(
								'supervisor' => serialize($supervisor)
						);
						$wpdb->update( $wpdb->prefix.'wpsp_catagories', $values, array('id'=>$result->id) );
				}
		}
}
do_action('wpsp_after_edit_agent', $agent_id);
