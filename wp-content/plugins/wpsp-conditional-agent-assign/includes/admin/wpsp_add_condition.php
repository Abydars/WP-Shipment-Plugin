<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb,$cu;
$cu = wp_get_current_user();
if (!$cu->has_cap('manage_options')) exit; // Exit if current user is not admin
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
?>
<form id="wpsp_caa_form" name="wpsp_caa_form" class="createCategoryContainer" method="post" onsubmit="setCondition(event,this);">
    <div class="wpsp_caa_setting_divs">
        
        <h4><?php echo __('Add New Condition','wpsp-caa') ?><a class="backtoconditionlist" href="#" onclick="showConditionList();"> Back To List</a></h4>
        <hr>
        
        <table>
            <tr>
                <td style="width: 50%;vertical-align: top;border-right: 1px solid #c3c3c3;">
                    <table id="wpsp_caa_tbl">
            
                        <tr>
                            <th><?php _e('Name','wpsp-caa');?>:</th>
                            <td><input type="text" id="wpsp_caa[label]" name="wpsp_caa[label]" value=""></td>
                        </tr>

                        <tr>
                            <th><?php _e('Assign Agent(s)','wpsp-caa');?>:</th>
                            <td>
                                <?php foreach ($agents as $agent) :?>
                                <div class="wpsp_caa_option_single">
                                    <input type="checkbox" onchange="wpsp_reset_ca_rule_text();" name="wpsp_caa[agents][]" value="<?php echo $agent->ID;?>" /> <?php echo $agent->data->display_name;?>
                                </div>
                                <?php endforeach;?>
                            </td>
                        </tr>
                        
                        <tr>
                            <th><h4><?php _e('Rules','wpsp-caa');?>:</h4></th>
                            <td></td>
                        </tr>

                        <?php if(in_array('dc',$advancedSettingsFieldOrder['display_fields'])): ?>
                        <tr>
                            <th>Category:</th>
                            <td>
                                <input type="hidden" class="caa_rule" value="create_ticket_category" />
                                <span style="display:none;" id="create_ticket_category_name">Category</span>
                                <input type="radio" onclick="wpsp_ca_apply_toggle('create_ticket_category',0);" name="wpsp_caa[rules][create_ticket_category][status]" value="0" checked="checked" /> <?php _e('Not Apply','wpsp-caa');?>&nbsp;&nbsp;
                                <input type="radio" onclick="wpsp_ca_apply_toggle('create_ticket_category',1);" name="wpsp_caa[rules][create_ticket_category][status]" value="1" /> <?php _e('Apply','wpsp-caa');?>
                                <div id="create_ticket_category_options_container" style="width:100%; float: left; display: none;">
                                    <?php $count = 0;?>
                                    <?php foreach ($categories as $category) :?>
                                    <div class="wpsp_caa_option_single">
                                        <input type="checkbox" onchange="wpsp_reset_ca_rule_text();" name="wpsp_caa[rules][create_ticket_category][options][]" value="<?php echo $category->id;?>" /> <span id="create_ticket_category_<?php echo $count++;?>"><?php echo stripcslashes($category->name);?></span>
                                    </div>
                                    <?php endforeach;?>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php if(in_array('dp',$advancedSettingsFieldOrder['display_fields'])): ?>
                        <tr>
                            <th>Priority:</th>
                            <td>
                                <input type="hidden" class="caa_rule" value="create_ticket_priority" />
                                <span style="display:none;" id="create_ticket_priority_name">Priority</span>
                                <input type="radio" onclick="wpsp_ca_apply_toggle('create_ticket_priority',0);" name="wpsp_caa[rules][create_ticket_priority][status]" value="0" checked="checked" /> <?php _e('Not Apply','wpsp-caa');?>&nbsp;&nbsp;
                                <input type="radio" onclick="wpsp_ca_apply_toggle('create_ticket_priority',1);" name="wpsp_caa[rules][create_ticket_priority][status]" value="1" /> <?php _e('Apply','wpsp-caa');?>
                                <div id="create_ticket_priority_options_container" style="width:100%; float: left; display: none;">
                                    <?php $count = 0;?>
                                    <?php foreach ($priorities as $priority) :?>
                                    <div class="wpsp_caa_option_single">
                                        <input type="checkbox" onchange="wpsp_reset_ca_rule_text();" name="wpsp_caa[rules][create_ticket_priority][options][]" value="<?php echo stripcslashes($priority->name);?>" /> <span id="create_ticket_priority_<?php echo $count++;?>"><?php echo stripcslashes($priority->name);?></span>
                                    </div>
                                    <?php endforeach;?>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($customFields as $field):?>
                        <?php if( in_array($field->id,$advancedSettingsFieldOrder['display_fields']) && $field->field_options ):
                                $field_options=unserialize($field->field_options);
                            ?>
                        <tr>
                            <th><?php echo $field->label;?>:</th>
                            <td>
                                <input type="hidden" class="caa_rule" value="cust<?php echo $field->id;?>" />
                                <span style="display:none;" id="cust<?php echo $field->id;?>_name"><?php echo $field->label;?></span>
                                <input type="radio" onclick="wpsp_ca_apply_toggle('cust<?php echo $field->id;?>',0);" name="wpsp_caa[rules][cust<?php echo $field->id;?>][status]" value="0" checked="checked" /> <?php _e('Not Apply','wpsp-caa');?>&nbsp;&nbsp;
                                <input type="radio" onclick="wpsp_ca_apply_toggle('cust<?php echo $field->id;?>',1);" name="wpsp_caa[rules][cust<?php echo $field->id;?>][status]" value="1" /> <?php _e('Apply','wpsp-caa');?>
                                <div id="cust<?php echo $field->id;?>_options_container" style="width:100%; float: left; display: none;">
                                    <?php $count = 0;?>
                                    <?php foreach ($field_options as $field_option_key=>$field_option_value) :?>
                                    <div class="wpsp_caa_option_single">
                                        <input type="checkbox" onchange="wpsp_reset_ca_rule_text();" name="wpsp_caa[rules][cust<?php echo $field->id;?>][options][]" value="<?php echo stripcslashes($field_option_value);?>" /> <span id="cust<?php echo $field->id;?>_<?php echo $count++;?>"><?php echo stripcslashes($field_option_value);?></span>
                                    </div>
                                    <?php endforeach;?>
                                </div>
                            </td>
                        </tr>
                        <?php endif;?>
                        <?php endforeach;?>

                    </table>
                </td>
                <td style="width: 50%; vertical-align: top;padding-left: 20px;">
                    <h4><?php _e('Description','wpsp-caa');?></h4>
                    <div id="wpsp_caa_description">No Rule Applied!</div>
                </td>
            </tr>
        </table>
        <input type="hidden" name="action" value="wpsp_conditional_agent_assign">
        <input type="submit" id="wpsp_ass_submit" class="btn btn-success" value="<?php _e('Submit Rule','wpsp-caa');?>"><br><br>
        <span style="color:red;"><i><?php echo __('* After creating the rule if custom field is disabled or deleted then rule will not be applied.','wpsp-caa');?></i></span>
        
    </div>
</form>
