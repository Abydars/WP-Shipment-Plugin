<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $current_user, $wpdb;
if(!$wpsupportplus->functions->is_staff($current_user) && $wpsupportplus->functions->is_allow_to_view_customer_support_page()){
	  printf(__('<h4>Sorry, you do not have permission to view this page</h4>','wp-support-plus-responsive-ticket-system'));
}elseif($wpsupportplus->functions->is_agent($current_user) && $wpsupportplus->functions->is_allow_to_view_agent_support_page()){
	  printf(__('<h4>Sorry, you do not have permission to view this page</h4>','wp-support-plus-responsive-ticket-system'));
}elseif($wpsupportplus->functions->is_supervisor($current_user) && $wpsupportplus->functions->is_allow_to_view_supervisor_support_page()){
	  printf(__('<h4>Sorry, you do not have permission to view this page</h4>','wp-support-plus-responsive-ticket-system'));
}else{
		$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();
		if( $wpsp_user_session ){
    	include( WPSP_ABSPATH . 'template/tickets/logged-in.php' );
    } else {
    	include( WPSP_ABSPATH . 'template/header/sign-in.php' );
  	}
}
