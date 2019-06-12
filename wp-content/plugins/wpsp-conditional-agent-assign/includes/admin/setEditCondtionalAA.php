<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb;
$cu = wp_get_current_user();
$flag=TRUE;
if (!$cu->has_cap('manage_options')) exit; // Exit if current user is not admin
$category='';
$priority='';
if(isset($_POST['edit_caa_category'])){
    $category=$_POST['edit_caa_category'];
}
if(isset($_POST['caa_edit_priority'])){
    $priority=$_POST['caa_edit_priority'];
}

$category_array=array();
if(isset($_POST['wpsp_caa_edit_category_condition'])){
    $category_array[$category]=$_POST['wpsp_caa_edit_category_condition'];
}
if(isset($_POST['wpsp_caa_edit_priority_condition'])){
    $priority_array[$priority]=$_POST['wpsp_caa_edit_priority_condition'];
}

$wpsp_conditional_caa=get_option('wpsp_conditional_caa');
$customFields = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsp_custom_fields WHERE field_type=2" );
if ($_POST['conditional_assign_agent']!=NULL) {
        $assign_agent=sanitize_text_field(implode(',', $_POST['conditional_assign_agent']));
}
foreach($customFields as $field){
    if( isset($_POST['custom'.$field->id]) ){
        $cust_field_array[$field->id]=array('value'=>$_POST['custom'.$field->id],'condition'=>$_POST['wpsp_caa_edit_cust_'.$field->id.'_condition']);
    }
}
foreach($wpsp_conditional_caa as $caa=>$val){
    if($val['label']==$_POST['edit_label']){
       unset($wpsp_conditional_caa[$caa]);
       update_option('wpsp_conditional_caa',$wpsp_conditional_caa);
    }
}

$wpsp_conditional_caa[]=array(
    'label'=>$_POST['edit_label'],
    'category'=>$category_array,
    'priority'=>$priority_array,
    'custom_field'=>$cust_field_array,
    'assign_agent'=>$assign_agent
);
update_option('wpsp_conditional_caa',$wpsp_conditional_caa);
?>