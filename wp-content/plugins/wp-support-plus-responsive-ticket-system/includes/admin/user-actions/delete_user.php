<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;
$user = get_userdata($user_id);

/**
 * If user is supervisor, remove him from categories he assigned
 */
if( $user->has_cap('wpsp_supervisor') ){
    $categories = $wpdb->get_results("select id, supervisor from {$wpdb->prefix}wpsp_catagories");
    foreach ($categories as $category){
        $existing_supervisors = $wpdb->get_var("select supervisor from {$wpdb->prefix}wpsp_catagories where id=".$category->id);
        $existing_supervisors = $existing_supervisors ? unserialize($existing_supervisors) : array();
        if(in_array($user->ID, $existing_supervisors)){
            $user->remove_cap('wpsp_cat_'.$category->id);
            unset($existing_supervisors[array_search($user->ID, $existing_supervisors)]);
            $values = array(
                'supervisor'    => serialize($existing_supervisors)
            );
            $wpdb->update( $wpdb->prefix.'wpsp_catagories', $values, array('id'=>$category->id) );
        }
    }
}

/**
 * Delete user from agents
 */
$wpdb->delete( $wpdb->prefix.'wpsp_users', array('user_id'=>$user->ID) );
$user->remove_cap('wpsp_agent');
$user->remove_cap('wpsp_supervisor');
$user->remove_cap('wpsp_administrator');