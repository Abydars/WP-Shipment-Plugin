<?php 
global $wpdb;
$upload_dir = wp_upload_dir();
$path_to_export=$upload_dir['basedir'].'/wpsp_export_ticket.csv';
$url_to_export=$upload_dir['baseurl'].'/wpsp_export_ticket.csv';
$data1 =$_POST['from_date'];
$data2 =$_POST['to_date'];
$a="SELECT * FROM {$wpdb->prefix}wpsp_ticket WHERE create_time between '".$data1."' and '".$data2."'";
$result = $wpdb->get_results($a);
$filename=$path_to_export;
$fp=fopen($filename,"w");

$custome_fields=$wpdb->get_results("SELECT * FROM {$wpdb->prefix}wpsp_custom_fields");
$c_fields=array();
foreach ($custome_fields as $custome_field){
    $c_fields[]=array(
        'id'=>$custome_field->id,
        'name'=>$custome_field->label
    );
}
$export_colomn_name=array(
    __('Sr. No.','wpsp_export'),
    __('Ticket ID','wpsp_export'),
    __('Status','wpsp_export'),
    __('Subject','wpsp_export'),
    __('User Type','wpsp_export'),
    __('Created by Name','wpsp_export'),
    __('Created by Email','wpsp_export'),
    __('Assigned to','wpsp_export'),
    __('Priority','wpsp_export'),
    __('Create Time','wpsp_export'),
    __('Last Updated','wpsp_export'),
    __('Category','wpsp_export'),
);
foreach ($c_fields as $c_field){
    $export_colomn_name[]=$c_field['name'];
}
fputcsv($fp,$export_colomn_name);

$i=1;
foreach ($result as $row)
{
    $user_type=($row->created_by==0)?__('Guest','wpsp_export'):__('User','wpsp_export');
    
    $create_by_name='';
    $create_by_email='';
    if($row->created_by==0){
        $create_by_name=$row->guest_name;
        $create_by_email=$row->guest_email;
    } else {
        $user=get_userdata( $row->created_by );
        $create_by_name=$user->display_name;
        $create_by_email=$user->user_email;
    }
    
    $assign_to='';
    if($row->assigned_to==0){
        $assign_to=__('None','wpsp_export');
    } else {
        $assigned_users=explode(',', $row->assigned_to);
        $u_display_names=array();
        foreach ($assigned_users as $user){
                $userdata=get_userdata($user);
                $u_display_names[]=$userdata->display_name;
        }
        $assign_to=implode(',',$u_display_names);
    }
    
    $etCategoryName=$wpdb->get_var( "SELECT name FROM {$wpdb->prefix}wpsp_catagories where id=".$row->cat_id );
    $etCategoryName=__($etCategoryName,'wpsp_export');

    $export_colomn_value=array($i,$row->id,$row->status,stripcslashes(htmlspecialchars_decode($row->subject,ENT_QUOTES)),$user_type,$create_by_name,$create_by_email,$assign_to,$row->priority,$row->create_time,$row->update_time,$etCategoryName);
    foreach ($c_fields as $c_field){
        $value='cust'.$c_field['id'];
        $custom_filed_value=$row->{$value};
        if(!$row->{$value}){
            $custom_filed_value=__('Null','wpsp_export');
        }
        $export_colomn_value[]=$custom_filed_value;
    }
    fputcsv($fp,$export_colomn_value);
    $i++;
}
fclose($fp);
echo '{"url_to_export":"'.$url_to_export.'"}';
?>
 
