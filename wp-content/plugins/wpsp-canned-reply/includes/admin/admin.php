<?php
final class WPSPCannedBackend {
    
    function loadScripts(){
        wp_enqueue_style('wpsp_canned_admin', WPSP_CANNED_URL . 'asset/css/admin.css?version='.WPSP_VERSION);
    }
    
    function canned_reply_template(){
        include WPSP_CANNED_DIR.'includes/admin/canned_reply_template.php';
    }
    
    function wpsp_reply_ticket_body($reply_body,$reply){
        include WPSP_CANNED_DIR.'includes/admin/wpsp_reply_ticket_body.php';
        return $reply_body;
    }
    
    function wpsp_in_createnewcustomfield_filter($last_id,$label){
        $wpsp_canned_reply_template=get_option( 'wpsp_canned_reply_template' );
	$wpsp_canned_reply_template['templates']['cust'.$last_id]= sanitize_text_field($label);
	update_option('wpsp_canned_reply_template',$wpsp_canned_reply_template);
    }
    
    function wpsp_in_deletecustomfield_filter($field_id){
        $wpsp_canned_reply_template=get_option( 'wpsp_canned_reply_template' );
	unset($wpsp_canned_reply_template['templates']['cust'.$field_id]);
	update_option('wpsp_canned_reply_template',$wpsp_canned_reply_template);
    }
    
    function wpsp_in_updatecustomfield_filter($field_id,$label){
        $wpsp_canned_reply_template=get_option( 'wpsp_canned_reply_template' );
        $wpsp_canned_reply_template['templates']['cust'.$field_id]=sanitize_text_field($label);
        update_option('wpsp_canned_reply_template',$wpsp_canned_reply_template);
    }
    
    function wpsp_editcanned_action(){
        include WPSP_CANNED_DIR.'includes/admin/canned_reply_template.php';
    }
}
?>