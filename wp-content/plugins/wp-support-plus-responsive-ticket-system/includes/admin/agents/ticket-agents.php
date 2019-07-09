<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

add_thickbox();

$nonce = wp_create_nonce();

include_once( WPSP_ABSPATH.'includes/admin/agents/class_agent_table.php' );
?>
<div id="tab_container" style="margin-top: 50px;">
    
    <h1 class="wp-heading-inline">
        <?php _e('Agents','wp-support-plus-responsive-ticket-system')?>
        <button onclick="wpsp_admin_load_popup('get_add_agent',0,'<?php echo $nonce?>','<?php _e('Add New Agent','wp-support-plus-responsive-ticket-system')?>', 400, 400, 150)" class="page-title-action"><?php _e('Add New','wp-support-plus-responsive-ticket-system')?></button>
    </h1>
    
    <?php
    $agent_table = new WPSP_Agent_List_Table();
    $agent_table->prepare_items();
    ?>
    <form method="get" action="<?php echo admin_url('admin.php')?>">
        <input type="hidden" name="page" value="wp-support-plus" />
        <input type="hidden" name="setting" value="agents" />
        <input type="hidden" name="section" value="agents" />
        <div class="wpsp_admin_list_table_filter">
            <strong><?php _e('Role','wp-support-plus-responsive-ticket-system');?></strong>:
            <select name="role">
                <option value=""><?php _e('All','wp-support-plus-responsive-ticket-system')?></option>
                <option <?php echo isset($_REQUEST['role']) && $_REQUEST['role']==1 ? 'selected="selected"' : ''?> value="1"><?php _e('Agent','wp-support-plus-responsive-ticket-system')?></option>
                <option <?php echo isset($_REQUEST['role']) && $_REQUEST['role']==2 ? 'selected="selected"' : ''?> value="2"><?php _e('Supervisor','wp-support-plus-responsive-ticket-system')?></option>
                <option <?php echo isset($_REQUEST['role']) && $_REQUEST['role']==3 ? 'selected="selected"' : ''?> value="3"><?php _e('Administrator','wp-support-plus-responsive-ticket-system')?></option>
            </select>
            <button type="submit" class="button button-primary"><?php _e('Apply','wp-support-plus-responsive-ticket-system')?></button>
            <a href="<?php echo admin_url('admin.php').'?page=wp-support-plus&setting=agents&section=agents'?>" class="button"><?php _e('Reset Filter','wp-support-plus-responsive-ticket-system')?></a>
            <?php $agent_table->search_box(__('Search','wp-support-plus-responsive-ticket-system'), 'wpsp-agent-search');?>
        </div>
    </form>
    <?php
    $agent_table->display();
    ?>
    
    <div id="wpsp-admin-popup-content" style="display:none;">
        <div>
            <div id="wpsp-admin-popup-body" style="display: none;"></div>
            <div id="wpsp-admin-popup-wait" style="text-align: center;">
                <img src="<?php echo WPSP_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>" />
            </div>
        </div>
    </div>
    
</div>
