<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $wpdb,$cu;
$cu=wp_get_current_user();
$usermeta=array();
$usermeta=get_user_meta($cu->ID,'wpsp_stick_ticket',true);
$stick_ticket_id=array();
$stick_ticket_id=get_option('wpsp_stick_ticket_id');
$stickTicketSettings=get_option( 'wpsp_stick_ticket_settings' );
$advancedSettings=get_option( 'wpsp_advanced_settings' );
$generalSettings=get_option( 'wpsp_general_settings' );
$sql_status="select * from {$wpdb->prefix}wpsp_custom_status";
$custom_statusses=$wpdb->get_results($sql_status);

/********************************************************************/

$advancedSettingsTicketList=get_option( 'wpsp_advanced_settings_ticket_list_order' );
$subCharLength=get_option( 'wpsp_ticket_list_subject_char_length' );

$advancedSettingsFieldOrder=get_option( 'wpsp_advanced_settings_field_order' );
$default_labels=$advancedSettingsFieldOrder['default_fields_label'];

$advancedSettings=get_option( 'wpsp_advanced_settings' );

$dateFormat = get_option( 'wpsp_ticket_list_date_format' );
$customFields = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsp_custom_fields" );
$customFieldSql='';
foreach ($customFields as $field){
	$customFieldSql.='t.cust'.$field->id.',';
}
$sql="select t.id,t.type,t.subject,t.status,c.name as category,c.id as cat_id,t.active,t.assigned_to,t.priority,t.created_by,t.guest_name,t.agent_created,cs.color,cp.color as pcolor,cp.name as pname,".$customFieldSql."
		TIMESTAMPDIFF(MONTH,t.update_time,UTC_TIMESTAMP()) as date_modified_month,
		TIMESTAMPDIFF(DAY,t.update_time,UTC_TIMESTAMP()) as date_modified_day,
		TIMESTAMPDIFF(HOUR,t.update_time,UTC_TIMESTAMP()) as date_modified_hour,
 		TIMESTAMPDIFF(MINUTE,t.update_time,UTC_TIMESTAMP()) as date_modified_min,
 		TIMESTAMPDIFF(SECOND,t.update_time,UTC_TIMESTAMP()) as date_modified_sec,
		t.create_time as create_date, t.update_time as update_date  		
		FROM {$wpdb->prefix}wpsp_ticket t 
		INNER JOIN {$wpdb->prefix}wpsp_catagories c ON t.cat_id=c.id  
		LEFT JOIN {$wpdb->prefix}wpsp_custom_status cs ON t.status=cs.name 
		LEFT JOIN {$wpdb->prefix}wpsp_custom_priority cp ON t.priority=cp.name ";


$where=' ';
if($cu->has_cap('manage_options')){
    if(!empty($stick_ticket_id)){
        $where="WHERE t.id IN (". implode(',',$stick_ticket_id). ") AND t.active='1'";
    }
}
else if($cu->has_cap('manage_support_plus_ticket')&& !($cu->has_cap('manage_support_plus_agent')) && $stickTicketSettings['wpsp_allow_user_to_stick_ticket']==0)
    {
        if(($usermeta)){
            $where.="WHERE (t.assigned_to RLIKE '(^|,)".$cu->ID."(,|$)' OR t.assigned_to='0' OR t.created_by='".$cu->ID."' OR t.ticket_type=1) AND (t.id IN (". implode(',',$usermeta). ") AND t.active='1')" ;
        }   
    }
else if($cu->has_cap('manage_support_plus_ticket') && (!$cu->has_cap('manage_support_plus_agent')) && $stickTicketSettings['wpsp_allow_user_to_stick_ticket']==1){
    if(!empty($stick_ticket_id)){
            $where.="WHERE (t.assigned_to RLIKE '(^|,)".$cu->ID."(,|$)' OR t.assigned_to='0' OR t.created_by='".$cu->ID."' OR t.ticket_type=1) AND (t.id IN (". implode(',',$stick_ticket_id). ") AND t.active='1')" ;
           
    }
}else if(!($cu->has_cap('manage_options')) && $cu->has_cap('manage_support_plus_agent') && $stickTicketSettings['wpsp_allow_user_to_stick_ticket']==0){
    if(!empty($usermeta)){
        $where="WHERE t.id IN (". implode(',',$usermeta). ") AND t.active='1'";
    }
}else if(!($cu->has_cap('manage_options')) && $cu->has_cap('manage_support_plus_agent') && $stickTicketSettings['wpsp_allow_user_to_stick_ticket']==1){
    if(!empty($stick_ticket_id)){
        $where="WHERE t.id IN (". implode(',',$stick_ticket_id). ") AND t.active='1'";
    }
}
if($where!=' ' && $filter!='deleted'){
    $sql.=$where;
    $tickets = $wpdb->get_results($sql);
    foreach ($tickets as $ticket){
        $assign_to=array();
        $assign_to=explode(',',$ticket->assigned_to);

            $raised_by='';
            if($ticket->type=='user'){
                    $user=get_userdata( $ticket->created_by );
                    $raised_by=$user->display_name;
            }
            else{
                    $raised_by=$ticket->guest_name;
            }

            $modified='';
            if ($ticket->date_modified_month) $modified=$ticket->date_modified_month.' '.__('months ago','wpsp-stick-ticket');
            else if ($ticket->date_modified_day) $modified=$ticket->date_modified_day.' '.__('days ago','wpsp-stick-ticket');
            else if ($ticket->date_modified_hour) $modified=$ticket->date_modified_hour.' '.__('hours ago','wpsp-stick-ticket');
            else if ($ticket->date_modified_min) $modified=$ticket->date_modified_min.' '.__('minutes ago','wpsp-stick-ticket');
            else $modified=$ticket->date_modified_sec.' '.__('seconds ago','wpsp-stick-ticket');


            $priority_color='';
            switch ($ticket->priority){
                    case 'high': $priority_color=$ticket->pcolor;break;
                    case 'medium': $priority_color=$ticket->pcolor;break;
                    case 'normal': $priority_color=$ticket->pcolor;break;

                    case 'low': $priority_color=$ticket->pcolor;break;
                    default :
                            $priority_color=$ticket->pcolor;
                            break;
            }
            $agent_name='';
            if($ticket->assigned_to=='0'){
                    $agent_name="None";
            }
            else {
                    $assigned_users=explode(',', $ticket->assigned_to);
                    $u_display_names=array();
                    foreach ($assigned_users as $user){
                            $userdata=get_userdata($user);
                            $u_display_names[]=$userdata->display_name;
                    }
                    $agent_name=implode(',',$u_display_names);
            }
            $agent_created='';
            if($ticket->agent_created!='0'){
                    $user=get_userdata( $ticket->agent_created);
                    $agent_created=$user->display_name;
            }

            $css='background-color: '.$stickTicketSettings['stick_ticket_color'].' !important; cursor:pointer;';
            $disabled='';
            if($stick_ticket_id && ($cu->has_cap('manage_options'))){
                if(array_search($ticket->id, $stick_ticket_id)> -1){
                   $disabled='disabled'; 
                }
            }else if(($cu->has_cap('manage_support_plus_ticket')) && $stickTicketSettings['wpsp_allow_user_to_stick_ticket']==0){
                if($usermeta){
                    if(array_search($ticket->id, $usermeta)> -1 ){
                       $disabled='disabled'; 
                    }
                }
            }
            else if(($cu->has_cap('manage_support_plus_ticket')) && $stickTicketSettings['wpsp_allow_user_to_stick_ticket']==1 && $stick_ticket_id){
                if(array_search($ticket->id,$stick_ticket_id)>-1){
                     $disabled='disabled'; 
                }
            }

            echo "<tr class='wpsp_custom_tr'  style='$css' onclick='if(link)openTicket(".$ticket->id.");'>"; 

            if(is_admin()){
                if($_POST['filter_by_status'] != 'deleted'){
                    echo "<td onmouseover='link=false;' onmouseout='link=true;'><input id='".$ticket->id."' type='checkbox' class='bulk_action_checkbox' onchange='wpspCheckBulkActionVisibility();' name='selected[]' value='".$ticket->id."' ".$disabled."/></td>";
                }
            }
            do_action('wpsp_after_checkbox_td_in_getticketsbyfilter',$ticket, 'stick');

            foreach($advancedSettingsTicketList['backend_ticket_list'] as $backend_ticket_field_key => $backend_ticket_field_value){
                if($backend_ticket_field_value==1){
                                if(is_numeric($backend_ticket_field_key)){
                                        $customFields = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsp_custom_fields WHERE id='".$backend_ticket_field_key."'" );
                                        foreach($customFields as $field)
                                        {
                                            $value='cust'.$backend_ticket_field_key;
                                        ?><td><?php echo $ticket->{$value};?></td><?php
                                }
                        }
                        else
                        {
                                switch($backend_ticket_field_key){
                                        case 'id': echo "<td>".__($ticket->id,'wpsp-stick-ticket')." </td>";
                                        break;
                                        case 'st': 
                                            $status_color='';
                                            $style = '';
                                            switch ($ticket->status){
                                                    case 'open': 
                                                            $style = ( $ticket->color != NULL && $ticket->color != '' ) ? ' background-color:' . $ticket->color . ' !important;' : '';
                                                            $status_color='danger';
                                                            break;
                                                    case 'pending': 
                                                            $style = ( $ticket->color != NULL && $ticket->color != '' ) ? ' background-color:' . $ticket->color . ' !important;' : '';
                                                            $status_color='warning';
                                                            break;
                                                    case 'closed': 
                                                            $style = ( $ticket->color != NULL && $ticket->color != '' ) ? ' background-color:' . $ticket->color . ' !important;' : '';
                                                            $status_color='success';
                                                            break;
                                                    default :
                                                            $style = ( $ticket->color != NULL && $ticket->color != '' ) ? ' background-color:' . $ticket->color . ' !important;' : '';
                                                            $status_color='info';
                                                            break;
                                            }
                                            echo "<td><span class='label label-".$status_color."' style='font-size: 13px;".$style."'>".__(ucfirst($ticket->status),'wpsp-stick-ticket')."<span></td>";
                                        break;
                                        case 'sb':$str_dots=""; 
                                                if(strlen(stripcslashes(htmlspecialchars_decode($ticket->subject,ENT_QUOTES))) > $subCharLength['backend'])
                                                {
                                                        $str_dots="...";
                                                }
                                                echo "<td title='".stripslashes(htmlspecialchars_decode($ticket->subject))."'>".substr(stripcslashes(htmlspecialchars_decode($ticket->subject,ENT_QUOTES)), 0,$subCharLength['backend']).$str_dots."</td>";
                                        break;
                                        case 'rb': echo "<td>".__($raised_by,'wpsp-stick-ticket')."</td>";
                                        break;
                                        case 'ty': echo "<td>".__(ucfirst($ticket->type),'wpsp-stick-ticket')."</td>";
                                        break;
                                        case 'ct': echo "<td>".__($ticket->category,'wpsp-stick-ticket')."</td>";
                                        break;
                                        case 'at': echo "<td>".__($agent_name,'wpsp-stick-ticket')."</td>";
                                        break;
                                        case 'pt': echo "<td><span class='label label-".$priority_color."' style='font-size: 13px;background-color:".$priority_color."'>".__(($ticket->pname),'wpsp-stick-ticket')."</span></td>";
                                        break;
                                        case 'ut': echo "<td>".__($modified,'wpsp-stick-ticket')."</td>";
                                        break;
                                        case 'cdt': 
                                                if($dateFormat['cdt_backend']=="")
                                                {
                                                        $cdt=date_i18n( get_option( 'date_format' ), strtotime( get_date_from_gmt( $ticket->create_date, 'Y-m-d H:i:s') ) ) . ' ' . get_date_from_gmt( $ticket->create_date, 'H:i:s');
                                                }
                                                else
                                                {
                                                        $cdt=date_i18n( $dateFormat['cdt_backend'], strtotime( get_date_from_gmt( $ticket->create_date, $dateFormat['cdt_backend']) ) );
                                                }
                                                echo "<td>".__($cdt,'wpsp-stick-ticket')."</td>";
                                        break;
                                        case 'udt': 
                                                if($dateFormat['udt_backend']=="")
                                                {
                                                        $udt=date_i18n( get_option( 'date_format' ), strtotime( get_date_from_gmt( $ticket->update_date, 'Y-m-d H:i:s') ) ) . ' ' . get_date_from_gmt( $ticket->update_date, 'H:i:s');
                                                }
                                                else
                                                {
                                                        $udt=date_i18n( $dateFormat['udt_backend'], strtotime( get_date_from_gmt( $ticket->update_date, $dateFormat['udt_backend']) ) );
                                                }
                                                echo "<td>".__($udt,'wpsp-stick-ticket')."</td>";
                                        break;
                                        case 'acd': echo "<td>".__($agent_created,'wpsp-stick-ticket')."</td>";
                                        break;
                                        default:                                                         
                                                do_action('wpsp_add_td_in_ticket_list',$ticket,$backend_ticket_field_key);                                                         
                                                break;
                                }
                        }
                }
            }
        echo "</tr>";
    }
}
?>
