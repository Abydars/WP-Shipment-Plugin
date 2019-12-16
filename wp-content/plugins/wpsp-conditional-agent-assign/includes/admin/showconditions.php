<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $current_user, $WPSP_CAA;
if (!$current_user->has_cap('manage_options')) die();
$wpsp_caa_conditions = get_option( 'wpsp_caa_conditions' );
?>
<br>
<button type="button" class="btn btn-primary" onclick="wpsp_add_condition()"><?php _e("+ Add New Condition",'wpsp-caa');?></button><br>
<div id="CagentDisplayTableContainer">
    <table class="wp-list-table widefat fixed striped" style="width: 97%">
        
        <tr>
            <th><?php _e('Label','wpsp-caa');?></th>
            <th><?php _e('Assigned Agents','wpsp-caa');?></th>
            <th><?php _e('Description','wpsp-caa');?></th>
            <th><?php _e('Action','wpsp-caa');?></th>
        </tr>
        
            
        <?php if($wpsp_caa_conditions):?>
        <?php foreach($wpsp_caa_conditions as $key => $condition ):?>
        <tr>
            <td id="wpsp_caa_cond_name_<?php echo $key?>"><?php echo stripcslashes($condition['label'])?></td>
            <td><?php echo $WPSP_CAA->getAgentNameStringByIDS($condition['agents'])?></td>
            <td><?php echo stripcslashes($condition['caa_desc'])?></td>
            <td><img alt="Delete" title="Delete" onclick="wpsp_caa_delete_condition(<?php echo $key?>);" src="<?php echo WCE_PLUGIN_URL.'asset/images/delete.png';?>" /></td>
        </tr>
        <?php endforeach;?>
        <?php endif;?>
        
        
        <?php if(!$wpsp_caa_conditions):?>
        <tr><td colspan="4"><?php _e('No Conditions Found','wpsp-caa');?></td></tr>
        <?php endif;?>
    
    </table> 
</div> 