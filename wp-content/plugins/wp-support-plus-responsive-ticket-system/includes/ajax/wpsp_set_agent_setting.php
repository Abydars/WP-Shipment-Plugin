<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb,$current_user, $wpsupportplus;
$signature    = isset($_POST['signature']) ? htmlspecialchars($_POST['signature'],ENT_QUOTES) : '';

if ( $wpsupportplus->functions->is_staff($current_user) ){
    update_user_meta($current_user->ID,'wpsp_agent_signature',sanitize_text_field($signature));
}
