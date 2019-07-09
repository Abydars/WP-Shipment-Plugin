<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpsupportplus, $wpdb;

$sectionsid         = isset($_POST['load_order']) ? explode(',', sanitize_text_field($_POST['load_order'])) : array();
$order              = $wpsupportplus->functions->sanitize_string_array($sectionsid);
$nonce              = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;
 
 if( !wp_verify_nonce($nonce, 'ticket_widget') ){
     die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
 }

$widget=get_option('wpsp_ticket_widget_order');

$widget_order=array();
foreach ($order as $value): 
		$widget_order[$value]=$widget[$value];
endforeach;

update_option('wpsp_ticket_widget_order',$widget_order);
apply_filters('wpsp_ticket_widget_re_order',$widget_order);
?>