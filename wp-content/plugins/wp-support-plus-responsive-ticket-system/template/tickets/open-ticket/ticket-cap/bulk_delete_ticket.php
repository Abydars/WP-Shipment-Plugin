<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $current_user;
$wpsp_user_session = $this->get_current_user_session();

$agent_settings = $this->get_agent_settings();


if($this->is_administrator($current_user)){

    $flag = true;

} else if($this->is_supervisor($current_user)){

    if( $agent_settings['supervisor_allow_delete_ticket'] ){

        $flag = true;

    }

} else if($this->is_agent($current_user)){

    if(  $agent_settings['agent_allow_delete_ticket'] ){

        $flag = true;

    }

}

$flag = apply_filters( 'wpsp_ticket_cap_bulk_delete_ticket', $flag );
