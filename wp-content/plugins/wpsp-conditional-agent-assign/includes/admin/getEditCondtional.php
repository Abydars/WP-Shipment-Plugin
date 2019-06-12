<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $wpdb,$current_user;
if (!$current_user->has_cap('manage_support_plus_ticket'));
$wpsp_conditional_caa=get_option('wpsp_conditional_caa');
$cust_field_array=array();
$categories = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsp_catagories ORDER BY name" );
$priorities = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsp_custom_priority" );
$advancedSettingsFieldOrder=get_option( 'wpsp_advanced_settings_field_order' );
$default_labels = $advancedSettingsFieldOrder['default_fields_label'];
$generalSettings = get_option('wpsp_general_settings');
$advancedSettings=get_option( 'wpsp_advanced_settings' );
$roleManage=get_option( 'wpsp_role_management' );
$agents=array();
$value_array=array();
$condition_array=array();
$agents=array_merge($agents,get_users(array('orderby'=>'display_name','role'=>'wp_support_plus_agent')));
$agents=array_merge($agents,get_users(array('orderby'=>'display_name','role'=>'wp_support_plus_supervisor')));
$agents=array_merge($agents,get_users(array('orderby'=>'display_name','role'=>'administrator')));
foreach($roleManage['agents'] as $agentRole)
{
	$agents=array_merge($agents,get_users(array('orderby'=>'display_name','role'=>$agentRole)));
}
foreach($roleManage['supervisors'] as $supervisorRole)
{
	$agents=array_merge($agents,get_users(array('orderby'=>'display_name','role'=>$supervisorRole)));
}
$customFields = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsp_custom_fields WHERE field_type=2" );

$caa=sanitize_text_field($_POST['rule_id']);
$label=$wpsp_conditional_caa[$caa]['label'];
$custom_field=$wpsp_conditional_caa[$caa]['custom_field'];
$assign_agent=$wpsp_conditional_caa[$caa]['assign_agent'];
$cat_array=$wpsp_conditional_caa[$caa]['category'];
$priority_array=$wpsp_conditional_caa[$caa]['priority'];

$ass_agent=explode(',',$assign_agent);

?>
<form id="wpsp_caa_edit_form" name="wpsp_caa_edit_form" class="createCategoryContainer" method="post" onsubmit="setEditConditional();">
        <h4><?php echo __('Update Rule','wpsp-caa') ?> <a class="backtoconditionlist" href="#" onclick="showConditionList();"> Back To List</a></h4>
        <hr>
        <span class="label label-info wpsp_title_label hide_fields_support_plus"><?php _e('Name','wpsp-caa');?></span><code>*</code><br><br>
        <input type="text" id='edit_label' name="edit_label" class="wpsp_required" value="<?php echo $label ?>">
        <br><br>
        <?php   if(in_array('dc',$advancedSettingsFieldOrder['display_fields'])){ 
                    ?>
                    <span class="label label-info wpsp_title_label hide_fields_support_plus"><?php _e('Category','wpsp-caa');?></span><br><br>
                        <select id="edit_caa_category" name="edit_caa_category" class="hide_fields_support_plus">
                            <option value=""></option><?php 
                                    if(in_array("have", $cat_array)){
                                        $havechecked="checked";
                                    }else{
                                        $havechecked=" ";
                                    }
                                    if(in_array("have_not", $cat_array)){
                                        $havenotchecked="checked";
                                    }else{
                                        $havenotchecked=" ";
                                    }if(in_array("any", $cat_array)){
                                        $anychecked="checked";
                                    }else{
                                        $anychecked=" ";
                                    }
                            foreach ($categories as $category){
                                foreach($cat_array as $key=>$val){
                                    if($category->id==$key)
                                    {
                                            $selected='selected';
                                    }
                                    else
                                    {   
                                            $selected='';
                                    }
                                }
                                echo '<option value="'.$category->id.'"'.$selected.'>'.__(stripcslashes($category->name),'wpsp-caa').'</option>';
                            }
                            ?>
                        </select>
                            <input type="radio" name="wpsp_caa_edit_category_condition" value="have" <?php echo $havechecked ?>> <?php _e('Have','wpsp-caa');?>
                            <input type="radio" name="wpsp_caa_edit_category_condition" value="have_not" <?php echo $havenotchecked ?>> <?php _e('Have Not','wpsp-caa');?>
                            <input type="radio" name="wpsp_caa_edit_category_condition" value="any" <?php echo $anychecked ?>> <?php _e('Any','wpsp-caa');?>
                            <br><br>
        <?php   }
        if(in_array('dp',$advancedSettingsFieldOrder['display_fields'])){ 
            ?>
        <span class="label label-info wpsp_title_label hide_fields_support_plus"><?php _e('Priority','wpsp-caa');?></span><br><br>
            <select id="caa_edit_priority" name="caa_edit_priority" class="hide_fields_support_plus">
                <option value="" class="blankpri"></option>	
                <?php 
                if(in_array("have", $priority_array)){
                    $havechecked="checked";
                }else{
                    $havechecked=" ";
                }
                if(in_array("have_not", $priority_array)){
                    $havenotchecked="checked";
                }else{
                    $havenotchecked=" ";
                }if(in_array("any", $priority_array)){
                    $anychecked="checked";
                }else{
                    $anychecked=" ";
                }
                foreach ($priorities as $priority){
                    foreach($priority_array as $key=>$val){
                        if($key==$priority->name){
                            $selected='selected';
                        }
                        else
                        {
                            $selected='';
                        }
                    }
                        echo '<option value="'.($priority->name).'" '.$selected.'>'.__($priority->name,'wpsp-caa').'</option>';
                }
                ?>
            </select>
            <input type="radio" name="wpsp_caa_edit_priority_condition" value="have" <?php echo $havechecked ?>> <?php _e('Have','wpsp-caa');?>
            <input type="radio" name="wpsp_caa_edit_priority_condition" value="have_not" <?php echo $havenotchecked?>> <?php _e('Have Not','wpsp-caa');?>
            <input type="radio" name="wpsp_caa_edit_priority_condition" value="any" <?php echo $anychecked ?>> <?php _e('Any','wpsp-caa');?>
            <br><br>
        <?php }
        foreach ($customFields as $field){
            if(in_array($field->id,$advancedSettingsFieldOrder['display_fields'])){
                $field_array[]=$field->id;
                $value=$custom_field[$field->id]['value'];
                $condition=$custom_field[$field->id]['condition'];
                $havechecked='';
                $havenotchecked='';
                $anychecked='';
                if($condition=='have'){
                    $havechecked='checked';
                }else if($condition=='have_not'){
                    $havenotchecked='checked';
                }else if($condition=='any'){
                    $anychecked='checked';
                }
                ?>
                <?php $id="custom".$field->id;?>
                <span class="label label-info" style="font-size: 12px;"><?php echo $field->label;?></span><br><br>
                    <select id="<?php echo $id;?>" name="<?php echo $id;?>" class="cust_field_conditinal">
                        <option value=""></option>
                        <?php 
                        if($field->field_options==NULL)
                        {
                                $field_options=array();
                        }
                        else
                        {
                                $field_options=unserialize($field->field_options);
                        }
                        foreach ($field_options as $field_option_key=>$field_option_value){
                                if($field_option_value==$value){
                                    $selected='selected';
                                }
                                else
                                {
                                    $selected='';
                                }
                               
                                echo '<option value="'.$field_option_value.'"'.$selected.'>'.$field_option_value.'</option>';
                        }
                        ?>
                    </select>
                    <input type="radio" name="wpsp_caa_edit_cust_<?php echo $field->id ?>_condition" value="have" <?php echo $havechecked ?>> <?php _e('Have','wpsp-caa');?>
                    <input type="radio" name="wpsp_caa_edit_cust_<?php echo $field->id ?>_condition" value="have_not" <?php echo $havenotchecked ?>> <?php _e('Have Not','wpsp-caa');?>
                    <input type="radio" name="wpsp_caa_edit_cust_<?php echo $field->id ?>_condition" value="any" <?php echo $anychecked ?>> <?php _e('Any','wpsp-caa');?>
                    <br><br>
                    <?php 
            }
        } 
        $field_string=implode(',',$field_array);
        ?>
        <div id="wpsp_edit_default_assignee">
            <span class="label label-info" style="font-size: 12px;"><?php _e('Default Assignee','wpsp-caa');?> </span><br><br>
            <select id="conditional_assign_agent" name="conditional_assign_agent[]" class="wpsp_caa_select_multi" multiple="multiple" name="conditional_assign_agent[]">
                    <?php 
                    foreach ($agents as $agent){
                            if(in_array($agent->ID,$ass_agent)){
                                $selected='selected';
                            }
                            else
                            {
                                $selected='';
                            }
                            echo '<option value="'.$agent->ID.'" '.$selected.'>'.$agent->data->display_name.'</option>';
                    }
                    ?>
            </select><br/><br/>
        </div>
        <input type="hidden" id="field_string" value="<?php echo $field_string ?>">
        <input type="hidden" name="action" value="setEditConditional">
        <input type="submit" id="edit_caa_submit" onclick="setEditConditional();" class="btn btn-success" value="<?php _e('Save Settings','wpsp-caa');?>"><br><br>
        <span style="color:red;"><i><?php echo __('* After updating the rule if custom field is disabled or deleted then rule will not be applied.','wpsp-caa');?></i></span>
</form>