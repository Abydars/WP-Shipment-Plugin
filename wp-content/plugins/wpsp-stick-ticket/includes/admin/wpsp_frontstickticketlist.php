<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $current_user,$wpdb;
$current_user=wp_get_current_user();
$stick_ticket_id=array();
$stick_ticket_id=get_option('wpsp_stick_ticket_id');
$advancedSettings=get_option( 'wpsp_advanced_settings' );
$generalSettings=get_option( 'wpsp_general_settings' );
$stickTicketSettings=get_option( 'wpsp_stick_ticket_settings' );
$advancedSettingsTicketList=get_option( 'wpsp_advanced_settings_ticket_list_order' );
$subCharLength=get_option( 'wpsp_ticket_list_subject_char_length' );
$usermeta=array();
$usermeta=get_user_meta($current_user->ID,'wpsp_stick_ticket',true);
$advancedSettingsFieldOrder=get_option( 'wpsp_advanced_settings_field_order' );
$default_labels=$advancedSettingsFieldOrder['default_fields_label'];

$customFields = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsp_custom_fields" );
$customFieldSql='';
foreach ($customFields as $field){
	$customFieldSql.='t.cust'.$field->id.',';
}
$dateFormat = get_option( 'wpsp_ticket_list_date_format' );
$sql="select t.id,t.type,t.subject,t.status,c.name as category,c.id as cat_id,t.assigned_to,t.active,t.priority,t.created_by,t.guest_email,t.guest_name,cs.color,cp.color as pcolor,cp.name as pname,".$customFieldSql."  
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
if($current_user->has_cap('manage_options'))
{
    if(!empty($stick_ticket_id)){
        $where="WHERE (t.ticket_type=1 OR t.ticket_type=0) AND t.id IN (". implode(',',$stick_ticket_id). ")";
    }
}
else if( $current_user->has_cap('manage_support_plus_ticket') && !($current_user->has_cap('manage_support_plus_agent')) && $stickTicketSettings['wpsp_allow_user_to_stick_ticket']==0)
{   if(!empty($usermeta)){
        $where="WHERE (t.assigned_to RLIKE '(^|,)".$current_user->ID."(,|$)' OR t.assigned_to='0' OR t.created_by='".$current_user->ID."' OR t.ticket_type=1) AND t.id IN (". implode(',',$usermeta). ")";
    }
}
else if(!($current_user->has_cap('manage_support_plus_agent')) && $current_user->has_cap('manage_support_plus_ticket') && $stickTicketSettings['wpsp_allow_user_to_stick_ticket']==1){
    if(!empty($stick_ticket_id) ){
        $where="WHERE (t.assigned_to RLIKE '(^|,)".$current_user->ID."(,|$)' OR t.assigned_to='0' OR t.created_by='".$current_user->ID."' OR t.ticket_type=1) AND t.id IN (". implode(',',$stick_ticket_id). ")";
    }
}else if(!($current_user->has_cap('manage_options')) && $current_user->has_cap('manage_support_plus_agent') && $stickTicketSettings['wpsp_allow_user_to_stick_ticket']==0){
    if(!empty($usermeta)){
        $where="WHERE (t.ticket_type=1 OR t.ticket_type=0) AND t.id IN (". implode(',',$usermeta). ")";
    }
}else if(!($current_user->has_cap('manage_options')) && $current_user->has_cap('manage_support_plus_agent') && $stickTicketSettings['wpsp_allow_user_to_stick_ticket']==1){
    if(!empty($stick_ticket_id)){
        $where="WHERE (t.ticket_type=1 OR t.ticket_type=0) AND t.id IN (". implode(',',$stick_ticket_id). ")";
    }
}
if($where!=' '){
    $where.=" AND t.active='1' ";
    $sql.=$where;
    $tickets = $wpdb->get_results($sql);
        foreach ($tickets as $ticket){
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
                    /* END CLOUGH I.T. SOLUTIONS MODIFICATION
                    */
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

                    $css='background-color: '.$stickTicketSettings['stick_ticket_color'].' !important; cursor:pointer;';
                    echo "<tr class='wpsp_custom_tr_front' style='$css' onclick='openTicket(".$ticket->id.");'>";
                    do_action('wpsp_after_tr_in_frontend',$ticket, 'stick');
                    foreach($advancedSettingsTicketList['frontend_ticket_list'] as $frontend_ticket_field_key => $frontend_ticket_field_value){
                            if($frontend_ticket_field_value==1){
                                    if(is_numeric($frontend_ticket_field_key)){
                                            $field = $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}wpsp_custom_fields WHERE id='".$frontend_ticket_field_key."'" );

                                            if($field->isVarFeild && $current_user->has_cap('manage_support_plus_ticket')){
                                                 $value='cust'.$frontend_ticket_field_key;
                                                 ?><td><?php echo $ticket->{$value};?></td><?php
                                            } else if($field->isVarFeild==0){
                                                 $value='cust'.$frontend_ticket_field_key;
                                                 ?><td><?php echo $ticket->{$value};?></td><?php
                                            }
                                    }
                                    else {
                                            switch($frontend_ticket_field_key){
                                                    case 'id': echo "<td>".__($ticket->id,'wpsp-stick-ticket')."</td>";
                                                                            break;
                                                    case 'st': echo "<td><span class='label label-".$status_color."' style='font-size: 13px;".$style."'>".__(ucfirst($ticket->status),'wpsp-stick-ticket')."<span></td>";
                                                                            break;
                                                    case 'sb': $str_dots=""; 
                                                            if(strlen(stripcslashes(htmlspecialchars_decode($ticket->subject,ENT_QUOTES))) > $subCharLength['frontend'])
                                                            {
                                                                    $str_dots="...";
                                                            }
                                                            echo "<td title='".stripslashes(htmlspecialchars_decode($ticket->subject))."'>".substr(stripcslashes(htmlspecialchars_decode($ticket->subject,ENT_QUOTES)), 0,$subCharLength['frontend']).$str_dots."</td>";
                                                                            break;
                                                    case 'ct': echo "<td class='category'>".__($ticket->category,'wpsp-stick-ticket')."</td>";
                                                                            break;
                                                    case 'at': if($current_user->has_cap('manage_support_plus_ticket')){
                                                                                    echo "<td>".__($agent_name,'wpsp-stick-ticket')."</td>";
                                                                            }
                                                                            break;
                                                    case 'rb': if($current_user->has_cap('manage_support_plus_ticket')){
                                                                                    echo "<td>".__($raised_by,'wpsp-stick-ticket')."</td>";
                                                                            }
                                                                            break;
                                                    case 'pt': echo "<td class='priority'><span class='label label-".$priority_color."' style='font-size: 13px;background-color:".$priority_color."'>".__(($ticket->pname),'wpsp-stick-ticket')."</span></td>";
                                                    break;
                                                    case 'ut': echo "<td>".__($modified,'wpsp-stick-ticket')."</td>";
                                                    break;
                                                    case 'cdt': 
                                                            if($dateFormat['cdt_frontend']=="")
                                                            {
                                                                    $cdt=date_i18n( get_option( 'date_format' ), strtotime( get_date_from_gmt( $ticket->create_date, 'Y-m-d H:i:s') ) ) . ' ' . get_date_from_gmt( $ticket->create_date, 'H:i:s');
                                                            }
                                                            else
                                                            {
                                                                    $cdt=date_i18n( $dateFormat['cdt_frontend'], strtotime( get_date_from_gmt( $ticket->create_date, $dateFormat['cdt_frontend']) ) );
                                                            }
                                                            echo "<td>".__($cdt,'wpsp-stick-ticket')."</td>";
                                                    break;
                                                    case 'udt': 
                                                            if($dateFormat['udt_frontend']=="")
                                                            {
                                                                    $udt=date_i18n( get_option( 'date_format' ), strtotime( get_date_from_gmt( $ticket->update_date, 'Y-m-d H:i:s') ) ). ' ' . get_date_from_gmt( $ticket->update_date, 'H:i:s');
                                                            }
                                                            else
                                                            {
                                                                    $udt=date_i18n( $dateFormat['udt_frontend'], strtotime( get_date_from_gmt( $ticket->create_date, $dateFormat['udt_frontend']) ) );
                                                            }
                                                            echo "<td>".__($udt,'wpsp-stick-ticket')."</td>";
                                                    break;
                                                default:                                                     
                                                        do_action('wpsp_add_td_in_ticket_list',$ticket,$frontend_ticket_field_key);                                                    
                                                        break;
                                            }
                                    }
                            }
                    }
                    echo "</tr>";
        }
}
?>
