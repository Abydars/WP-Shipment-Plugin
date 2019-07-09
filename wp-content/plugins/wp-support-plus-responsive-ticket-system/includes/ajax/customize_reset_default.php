<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$setting =  isset($_REQUEST['setting']) ? sanitize_text_field($_REQUEST['setting']) : '';

if ($current_user->has_cap('manage_options')) {
    
    switch ($setting) {
      
      case 'general':
        delete_option('wpsp_customize_general');
        break;
        
      case 'ticket_list':
        delete_option('wpsp_customize_ticket_list');
        break;
        
    }
    
    do_action('wpsp_after_customize_reset_default',$setting);
    
}