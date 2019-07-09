<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$ticket_id  = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : 0 ;
$nonce      = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '' ;

$ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

/**
 * Check nonce
 */
if( !wp_verify_nonce( $nonce, $ticket_id ) || !$wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'change_fields' ) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$sql = "SELECT c.id as id, c.field_type as field_type, c.field_options as field_options FROM {$wpdb->prefix}wpsp_custom_fields c "
        . "INNER JOIN {$wpdb->prefix}wpsp_ticket_form_order f "
        . "ON c.id=f.field_key "
        . "WHERE 1=1 "
        . "AND c.isVarFeild=0 "
        . "AND ( "
                . "c.field_categories = 0 OR c.field_categories RLIKE '(^|,)".$ticket->cat_id."(,|$)'"
            . " ) "
        . "ORDER BY f.load_order ASC ";

$ticket_fields = $wpdb->get_results( $sql );

if( $ticket_fields ) {

    $values = array();
    
    foreach ( $ticket_fields as $custom_field ){

        $col_name = 'cust'.$custom_field->id;
        
        switch ($custom_field->field_type){
            case 1:
            case 2:
            case 4:
            case 9:
            case 10:
                
                if( isset($_POST[$col_name]) ){
                    $values[$col_name] = sanitize_text_field($_POST[$col_name]);
                } else {
                    $values[$col_name] = '';
                }
                break;
            
            case 3:
                
                if( isset($_POST[$col_name]) && is_array($_POST[$col_name]) ){
                    $values[$col_name] = sanitize_text_field( implode('|||', $_POST[$col_name] ) );
                } else {
                    $values[$col_name] = '';
                }
                break;
                
            case 5: 
                
                if( isset($_POST[$col_name]) ){
                    $values[$col_name] = wp_kses_post($_POST[$col_name]);
                } else {
                    $values[$col_name] = '';
                }
                break;
                
            case 6: 
                
                if( isset($_POST[$col_name]) && $_POST[$col_name] ){
                    
                    $save_value = sanitize_text_field($_POST[$col_name]);
                    
                    $format = str_replace('dd','d',$wpsupportplus->functions->get_date_format());
                    $format = str_replace('mm','m',$format);
                    $format = str_replace('yy','Y',$format);

                    $date       = date_create_from_format($format, $save_value);
                    $save_value = $date->format('Y-m-d H:i:s');
                    
                    $values[$col_name] = $save_value;
                    
                } else {
                    $values[$col_name] = '';
                }
                break;
            
            case 8: 
                
								$col_name = 'cust_'.$custom_field->id;
                if( isset($_POST[$col_name]) && is_array($_POST[$col_name]) ){
                    
                    $save_value = array();
                    
                    foreach ( $_POST[$col_name] as $key => $val ){
                        $save_value[$key] = sanitize_text_field($val);
                    }
                    
                    foreach ( $save_value as $key => $attachment_id ){

                        $attachment_id = intval(sanitize_text_field($attachment_id));
                        if($attachment_id){
                            $wpdb->update($wpdb->prefix . 'wpsp_attachments', array('active' => 1), array('id' => $attachment_id));
                        } else {
                            unset($save_value[$key]);
                        }
                    }
                    
                    $col_name = 'cust'.$custom_field->id;
                    
                    if($save_value){
                        $values[$col_name] = implode('|||', $save_value);
                    } else {
                        $values[$col_name] = '';
                    }
                    
                } else {
                    $col_name = 'cust'.$custom_field->id;
                    $values[$col_name] = '';
                }
                break;
                
        }
        
        $values = apply_filters( 'wpsp_set_ticket_fields', $values, $ticket, $custom_field );
        

    }
    
    include_once WPSP_ABSPATH . 'template/tickets/class-ticket-operations.php';

    $ticket_oprations = new WPSP_Ticket_Operations();

    $ticket_oprations->change_ticket_fields( $values, $ticket_id );
    

} else {

    _e('No Ticket fields','wp-support-plus-responsive-ticket-system');
}
