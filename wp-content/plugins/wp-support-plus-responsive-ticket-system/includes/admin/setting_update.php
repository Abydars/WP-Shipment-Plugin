<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if (!check_admin_referer('wpbdp_tab_general_section_general')) {
    exit;
}

global $wpsupportplus, $wpdb, $current_user;

if( isset($_REQUEST['update_setting']) ) :
    
    $setting = sanitize_text_field( $_REQUEST['update_setting'] );
    
    switch ( $setting ) {
        
        case 'settings_general':
            update_option( 'wpsp_settings_general', $wpsupportplus->functions->sanitize_string_array($_POST['general_settings']) );
            break;
        
				case 'default_filters':
            update_option( 'wpsp_ticket_list_default_filters', $wpsupportplus->functions->sanitize_string_array($_POST['default_filters']) );
            break;
        
				case 'default_widget' :
						update_option( 'wpsp_ticket_widget_order',$wpsupportplus->functions->sanitize_string_array($_POST['default_widget']) );
				    break;  
        
				case 'agent_general_settings':
            update_option( 'wpsp_agent_settings', $wpsupportplus->functions->sanitize_string_array($_POST['agent_settings'] ));
            break;
        
        case 'custom_css':
            update_option('wpsp_custom_css', $wpsupportplus->functions->sanitize_string_area_array($_POST['custom_css']));
            break;
        
        case 'footer_text':
            update_option('wpsp_text_footer', wp_kses_post($_POST['footer_text']));
            break;
        
        case 'thank_you_page':
            update_option('wpsp_thank_you_page', wp_kses_post($_POST['thank_you_page']));
            break;
        
        case 'dashbord_general':
            update_option( 'wpsp_dashbord_general', $wpsupportplus->functions->sanitize_string_array($_POST['dashbord_general']));
            break;
						
				case 'customize_general_settings':
						update_option( 'wpsp_customize_general', $wpsupportplus->functions->sanitize_string_area_array($_POST['customize_general']));
						break;
						
				case 'customize_ticket_list_settings':
						update_option( 'wpsp_customize_ticket_list', $wpsupportplus->functions->sanitize_string_area_array($_POST['customize_ticket_list']));
						break;
				
				case 'advanced_settings':
						update_option( 'wpsp_ticket_list_advanced_settings', $wpsupportplus->functions->sanitize_string_array($_POST['advanced_settings'] ));
						break;
				
				case 'general_settings_advanced':
						update_option( 'wpsp_general_settings_advanced', $wpsupportplus->functions->sanitize_string_array($_POST['general_advanced_settings']) );
					  break;	
        }
    
    $wpsupportplus->functions->load_settings();
    
endif;

