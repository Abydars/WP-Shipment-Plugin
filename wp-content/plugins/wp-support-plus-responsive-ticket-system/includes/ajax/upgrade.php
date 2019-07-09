<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

global $wpdb, $current_user, $wpsupportplus;

$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field($_POST['nonce']) : '';

if( !wp_verify_nonce( $nonce, $current_user->ID ) || !$current_user->has_cap('manage_options') ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$upgrade_step = get_option( 'wpsp_upgrade' );
$upgrade_step = $upgrade_step ? $upgrade_step : 0;

$upgrade_step = $upgrade_step+1;

switch ( $upgrade_step ){
    
    case 1 : // status id replace
        
        $statuses = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpsp_custom_status");
        foreach ( $statuses as $status ){
            $wpdb->update( $wpdb->prefix.'wpsp_ticket', array( 'status_id'=>$status->id ), array( 'status'=>$status->name ) );
            $wpdb->update( $wpdb->prefix.'wpsp_ticket_thread', array( 'body'=>$status->id ), array( 'is_note'=>3, 'body'=>$status->name ) );
        }
        $wpdb->query("UPDATE {$wpdb->prefix}wpsp_ticket_thread SET body='1' WHERE is_note=3 AND body IS NULL");
        update_option('wpsp_upgrade',1);
        break;
        
    case 2 : // priority id replace
        
        $priorities = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpsp_custom_priority");
        foreach ( $priorities as $priority ){
            $wpdb->update( $wpdb->prefix.'wpsp_ticket', array( 'priority_id'=>$priority->id ), array( 'priority'=> strtolower($priority->name) ) );
            $wpdb->update( $wpdb->prefix.'wpsp_ticket', array( 'priority_id'=>$priority->id ), array( 'priority'=>$priority->name ) );
            $wpdb->update( $wpdb->prefix.'wpsp_ticket_thread', array( 'body'=>$priority->id ), array( 'is_note'=>5, 'body'=>strtolower($priority->name) ) );
            $wpdb->update( $wpdb->prefix.'wpsp_ticket_thread', array( 'body'=>$priority->id ), array( 'is_note'=>5, 'body'=>$priority->name ) );
        }
        $wpdb->query("UPDATE {$wpdb->prefix}wpsp_ticket_thread SET body='1' WHERE is_note=5 AND body IS NULL");
        update_option('wpsp_upgrade',2);
        break;
        
    case 3: // convert guest tickets to user tickets who are registered user of the guest mail
        
        $results = $wpdb->get_results("SELECT DISTINCT t.guest_email as guest_email FROM {$wpdb->prefix}wpsp_ticket t INNER JOIN {$wpdb->base_prefix}users u ON t.guest_email=u.user_email WHERE t.created_by=0");
        foreach ( $results as $result ){
            
            $user = $wpdb->get_row("SELECT ID, user_email, display_name FROM {$wpdb->base_prefix}users WHERE user_email='".$result->guest_email."'");
            $wpdb->update( 
            
                $wpdb->prefix.'wpsp_ticket', 
                array( 
                    'guest_name'    => $user->display_name,
                    'created_by'    => $user->ID
                ), 
                array( 
                    'guest_email'=>$result->guest_email 
                ) 
            );
        }
        update_option('wpsp_upgrade',3);
        break;
        
    case 4: // guest name & email for tickets
        
        $created_users = $wpdb->get_results("SELECT DISTINCT created_by as id FROM {$wpdb->prefix}wpsp_ticket WHERE created_by<>0");
        foreach ( $created_users as $created_user ){
            
            $user = $wpdb->get_row("SELECT user_email, display_name FROM {$wpdb->base_prefix}users WHERE ID=".$created_user->id);
            $wpdb->update( 
            
                $wpdb->prefix.'wpsp_ticket', 
                array( 
                    'guest_name'    => $user->display_name,
                    'guest_email'   => $user->user_email
                ), 
                array( 
                    'created_by'=>$created_user->id 
                ) 
            );
        }
        update_option('wpsp_upgrade',4);
        break;
        
    case 5: // guest name & email for thread
        
        $created_users = $wpdb->get_results("SELECT DISTINCT created_by as id FROM {$wpdb->prefix}wpsp_ticket_thread WHERE created_by<>0");
        foreach ( $created_users as $created_user ){
            
            $user = $wpdb->get_row("SELECT user_email, display_name FROM {$wpdb->base_prefix}users WHERE ID=".$created_user->id);
            $wpdb->update( 
            
                $wpdb->prefix.'wpsp_ticket_thread', 
                array( 
                    'guest_name'    => $user->display_name,
                    'guest_email'   => $user->user_email
                ), 
                array( 
                    'created_by'=>$created_user->id 
                ) 
            );
        }
        update_option('wpsp_upgrade',5);
        break;
        
    case 6: // form order
        
        $counter = 0;
        $custom_fields_localize = array();
        $advancedSettingsFieldOrder=get_option( 'wpsp_advanced_settings_field_order' );
        $result = $wpdb->get_var("SELECT count(field_key) FROM {$wpdb->prefix}wpsp_ticket_form_order WHERE `field_key` IS NOT NULL");
        foreach($advancedSettingsFieldOrder['fields_order'] as $field_id){
          
            if( $field_id=='da' || $field_id=='dn' || $field_id=='de' ) continue;
          
                $counter++;
                $values = array(
                    'field_key'     => $field_id,
                    'status'        => in_array($field_id,$advancedSettingsFieldOrder['display_fields']) ? 1 : 0,
                    'full_width'    => 1,
                    'load_order'    => $counter
                );
                
                if($result == 0){
                    $wpdb->insert( $wpdb->prefix.'wpsp_ticket_form_order', $values );
                }
                
                if(is_numeric($field_id)){
                    $custom_fields_localize['label_' . $field_id] = $wpdb->get_var("select label from {$wpdb->prefix}wpsp_custom_fields where id=".$field_id);
                }
        }
        update_option('wpsp_custom_fields_localize', $custom_fields_localize);
        update_option('wpsp_upgrade',6);
        break;
    
    case 7: // list order data insert
        
        $sql='';
        $result = $wpdb->get_var("SELECT count(field_key) FROM {$wpdb->prefix}wpsp_ticket_list_order WHERE `field_key` IS NOT NULL");
        if($result != 0) {
            $results = $wpdb->get_results("select field_key from {$wpdb->prefix}wpsp_ticket_list_order");
            foreach ($results as $value) {
                $field_key_value[]=$value->field_key;
            }
            
            if (!in_array('id', $field_key_value)){
                $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                        . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                        . "VALUES "
                        . "( 'id', 't.id', 't.id', 'LIKE', 1, 1, 0, 1 )";
                $wpdb->query($sql);
            }
                
            if (!in_array('status', $field_key_value)){
                $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                        . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                        . "VALUES "
                        . "( 'status', 's.id', 's.name', 'LIKE', 1, 1, 1, 1 )";
                $wpdb->query($sql);
            }
                
            if (!in_array('subject', $field_key_value)){
                $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                        . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                        . "VALUES "
                        . "( 'subject', 't.subject', 't.subject', NULL, 1, 1, 0, 0 )";
                $wpdb->query($sql);
            }
                
            if (!in_array('raised_by', $field_key_value)){
                $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                        . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                        . "VALUES "
                        . "( 'raised_by', 't.guest_email', 't.guest_name', 'LIKE', 0, 1, 0, 1 )";
                $wpdb->query($sql);
            }
                
            if (!in_array('user_type', $field_key_value)){
                $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                        . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                        . "VALUES "
                        . "( 'user_type', 't.type', 't.type', 'LIKE', 0, 0, 0, 0 )";
                $wpdb->query($sql);
            }
                
            if (!in_array('category', $field_key_value)){
                $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                        . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                        . "VALUES "
                        . "( 'category', 'c.id', 'c.name', 'LIKE', 1, 1, 1, 1 )";
                $wpdb->query($sql);
            }
                
            if (!in_array('assigned_agent', $field_key_value)){
                $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                        . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                        . "VALUES "
                        . "( 'assigned_agent', 't.assigned_to', 't.assigned_to', 'LIKE', 0, 1, 0, 1 )";
                $wpdb->query($sql);
            }
                
            if (!in_array('priority', $field_key_value)){
                $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                        . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                        . "VALUES "
                        . "( 'priority', 'p.id', 'p.name', 'LIKE', 1, 1, 1, 1 )";
                $wpdb->query($sql);
            }
                
            if (!in_array('date_created', $field_key_value)){
                $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                        . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                        . "VALUES "
                        . "( 'date_created', 't.create_time', 't.create_time', 'BETWEEN', 0, 0, 1, 1 )";
                $wpdb->query($sql);
            }
                
            if (!in_array('date_updated', $field_key_value)){
                $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                        . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                        . "VALUES "
                        . "( 'date_updated', 't.update_time', 't.update_time', 'BETWEEN', 1, 1, 0, 0 )";
                $wpdb->query($sql);
            }
                
            if (!in_array('created_agent', $field_key_value)){
                $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                        . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                        . "VALUES "
                        . "( 'created_agent', 't.agent_created', 'u.user_login,u.display_name,u.user_email', 'LIKE', 0, 0, 0, 0 )";
                $wpdb->query($sql);
            }
            
            if (!in_array('deleted_ticket', $field_key_value)){
                $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                        . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                        . "VALUES "
                        . "( 'deleted_ticket', 'NULL', 'NULL', 'LIKE', 0, 0, 0, 1 )";
                $wpdb->query($sql);
            }

            $custom_fields = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_fields where isVarFeild=0");
            
            foreach ($custom_fields as $field){
                
                if($field->field_type == 6 && !in_array($field->id, $field_key_value)){
                            
                    $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                            . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                            . "VALUES "
                            . "( '".$field->id."', 't.cust".$field->id."', 't.cust".$field->id."','BETWEEN', 0, 0, 0, 1 )";
                            
                } else if(!in_array($field->id, $field_key_value)){
                  
                      $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                              . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                              . "VALUES "
                              . "( '".$field->id."', 't.cust".$field->id."', 't.cust".$field->id."','LIKE', 0, 0, 0, 1 )";
                  }
            }
        }else{
            $sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_list_order "
                  . "( field_key, join_match, join_compare, join_relation, customer_visible, agent_visible, customer_filter, agent_filter )"
                  . "VALUES "
                  . "( 'id', 't.id', 't.id', 'LIKE', 1, 1, 0, 1 ), "
                  . "( 'status', 's.id', 's.name', 'LIKE', 1, 1, 1, 1 ), "
                  . "( 'subject', 't.subject', 't.subject', NULL, 1, 1, 0, 0 ), "
                  . "( 'raised_by', 't.guest_email', 't.guest_name', 'LIKE', 0, 1, 0, 1 ), "
                  . "( 'user_type', 't.type', 't.type', 'LIKE', 0, 0, 0, 0 ), "
                  . "( 'category', 'c.id', 'c.name', 'LIKE', 1, 1, 1, 1 ), "
                  . "( 'assigned_agent', 't.assigned_to', 't.assigned_to', 'LIKE', 0, 1, 0, 1 ), "
                  . "( 'priority', 'p.id', 'p.name', 'LIKE', 1, 1, 1, 1 ), "
                  . "( 'date_created', 't.create_time', 't.create_time', 'BETWEEN', 0, 0, 1, 1 ), "
                  . "( 'date_updated', 't.update_time', 't.update_time', 'BETWEEN', 1, 1, 0, 0 ), "
                  . "( 'created_agent', 't.agent_created', 'u.user_login,u.display_name,u.user_email', 'LIKE', 0, 0, 0, 0 ), "
                  . "( 'deleted_ticket', 'NULL', 'NULL', 'LIKE', 0, 0, 0, 1 ) ";
                  
            $custom_fields = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_fields where isVarFeild=0");
            foreach ($custom_fields as $field){
              
              if($field->field_type == 6){
                  $sql .= ", ( '".$field->id."', 't.cust".$field->id."', 't.cust".$field->id."', 'BETWEEN', 0, 0, 0, 1 )";
              } else {
                  $sql .= ", ( '".$field->id."', 't.cust".$field->id."', 't.cust".$field->id."', 'LIKE', 0, 0, 0, 1 )";
              }
          }
        }
      
        $wpdb->query($sql);
        update_option('wpsp_upgrade',7);
        break;
        
    case 8 : // admin, supervisor, agent
        
        $agents         = get_users(array('role'=>'wp_support_plus_agent'));
        $supervisors    = get_users(array('role'=>'wp_support_plus_supervisor'));
        $admins         = get_users(array('role'=>'administrator'));
        
        $result = $wpdb->get_var("SELECT count(user_id) FROM {$wpdb->prefix}wpsp_users WHERE `user_id` IS NOT NULL");
        if($result != 0){
            $results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_users");
            $array = json_decode(json_encode($results), True);
            $c=count($array);
            $user_id_list=array();
            for ( $i=0; $i < $c; $i++){
                $user_id_list[]=$array[$i]['user_id'];
            }
            
            foreach ( $agents as $user ){
                
                $values = array(
                    'user_id'       => $user->ID,
                    'role'          => 1
                );
                
                if (!in_array($values['user_id'], $user_id_list)){
                    $wpdb->insert( $wpdb->prefix.'wpsp_users', $values );
                    $user->add_cap('wpsp_agent');
                }
            }
            
            foreach ( $supervisors as $user ){
                
                $values = array(
                    'user_id'       => $user->ID,
                    'role'          => 2
                );
                
                if (!in_array($values['user_id'], $user_id_list)){
                    $wpdb->insert( $wpdb->prefix.'wpsp_users', $values );
                    $user->add_cap('wpsp_supervisor');
                }
            }
            
            foreach ( $admins as $user ){
                
                $values = array(
                    'user_id'       => $user->ID,
                    'role'          => 3
                );
                
                if (!in_array($values['user_id'], $user_id_list)){
                    $wpdb->insert( $wpdb->prefix.'wpsp_users', $values );
                    $user->add_cap('wpsp_administrator');
                }
            }
       }else{
           foreach ( $agents as $user ){
              
              $values = array(
                  'user_id'       => $user->ID,
                  'role'          => 1
              );
              $wpdb->insert( $wpdb->prefix.'wpsp_users', $values );
              $user->add_cap('wpsp_agent');
          }
          
          foreach ( $supervisors as $user ){
              
              $values = array(
                  'user_id'       => $user->ID,
                  'role'          => 2
              );
              $wpdb->insert( $wpdb->prefix.'wpsp_users', $values );
              $user->add_cap('wpsp_supervisor');
          }
          
          foreach ( $admins as $user ){
              
              $values = array(
                  'user_id'       => $user->ID,
                  'role'          => 3
              );
              $wpdb->insert( $wpdb->prefix.'wpsp_users', $values );
              $user->add_cap('wpsp_administrator');
          }
       }
        update_option('wpsp_upgrade',8);
        break;
        
        
    case 9: // old settings copy
        
        $generalSettings    = get_option( 'wpsp_general_settings' );
        $advancedSettings   = get_option( 'wpsp_advanced_settings' );
    
        $new_general    = get_option( 'wpsp_settings_general' );
        
        $new_general['support_page']        =  $generalSettings['post_id'];
        $new_general['default_category']    =  $generalSettings['default_ticket_category'];
        $new_general['default_status']      =  $generalSettings['default_new_ticket_status'];
        $new_general['ticket_id_sequence']  =  $advancedSettings['ticketId'];
        $new_general['reply_form_position'] =  $advancedSettings['wpsp_reply_form_position'];
        $new_general['date_format']         =  $advancedSettings['datecustfield'];
        $new_general['attachment_size']     =  $advancedSettings['wpspAttachMaxFileSize'];
        $new_general['allow_guest_ticket']  =  $generalSettings['enable_guest_ticket'];
        $new_general['ticket_id_prefix']    =  $advancedSettings['wpsp_ticket_id_prefix'];
        $new_general['ticket_lable']        =  $advancedSettings['ticket_label_alice'][1];
      
        
        update_option('wpsp_settings_general', $new_general);
        update_option('wpsp_upgrade',9);
        break;
    
    case 10: // date custom field data upgrade
        
        $format = str_replace('dd','%d',$wpsupportplus->functions->get_date_format());
        $format = str_replace('mm','%m',$format);
        $format = str_replace('yy','%Y',$format);
        $format .= ' %H:%i:%s';
        
        $custom_fields = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_fields where field_type=6");
        foreach( $custom_fields as $field ){
            
            $sql = "UPDATE {$wpdb->prefix}wpsp_ticket SET cust".$field->id." = STR_TO_DATE( CONCAT(cust".$field->id.", ' ', '00:00:00'),'$format') WHERE cust".$field->id."<>''";
            $wpdb->query($sql);
        }
        update_option('wpsp_upgrade',10);
        break;
}

$no_of_steps = 10;
$percentage = ($upgrade_step/$no_of_steps)*100;

$response=array(
    'is_next'       => $upgrade_step < $no_of_steps ? 1 : 0 ,
    'percentage'    => ceil($percentage)
);

echo json_encode($response);
