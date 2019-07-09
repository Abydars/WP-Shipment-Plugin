<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$agent_settings = $wpsupportplus->functions->get_agent_settings();

?>

<div id="tab_container">
    
    <form method="post" action="">
        
        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="update_setting" value="agent_general_settings"/>
        <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>
        
        <table class="form-table">
            
            <tr>
                <th scope="row"><h3><?php _e('Agent', 'wp-support-plus-responsive-ticket-system'); ?></h3></th>
                <td></td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Allow Assign Ticket', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <select name="agent_settings[agent_allow_assign_ticket]">
                        <option <?php echo $agent_settings['agent_allow_assign_ticket'] ? 'selected="selected"' : ''?> value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $agent_settings['agent_allow_assign_ticket'] ? '' : 'selected="selected"'?> value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
                    </select><br>
                    <small><i><?php _e('If set yes, an agent will able to assign ticket assigned to him to some other agent who is assigned to ticket category.','wp-support-plus-responsive-ticket-system');?></i></small>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Allow Delete Ticket', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <select name="agent_settings[agent_allow_delete_ticket]">
                        <option <?php echo $agent_settings['agent_allow_delete_ticket'] ? 'selected="selected"' : ''?> value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $agent_settings['agent_allow_delete_ticket'] ? '' : 'selected="selected"'?> value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
                    </select><br>
                    <small><i><?php _e('If set yes, an agent will able to delete ticket assigned to him.','wp-support-plus-responsive-ticket-system');?></i></small>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Allow Change Raised By', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <select name="agent_settings[agent_allow_change_raised_by]">
                        <option <?php echo $agent_settings['agent_allow_change_raised_by'] ? 'selected="selected"' : ''?> value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $agent_settings['agent_allow_change_raised_by'] ? '' : 'selected="selected"'?> value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
                    </select><br>
                    <small><i><?php _e('If set yes, an agent will able to change raised by of ticket assigned to him.','wp-support-plus-responsive-ticket-system');?></i></small>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><h3><?php _e('Supervisor', 'wp-support-plus-responsive-ticket-system'); ?></h3></th>
                <td></td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Allow Delete Ticket', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <select name="agent_settings[supervisor_allow_delete_ticket]">
                        <option <?php echo $agent_settings['supervisor_allow_delete_ticket'] ? 'selected="selected"' : ''?> value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $agent_settings['supervisor_allow_delete_ticket'] ? '' : 'selected="selected"'?> value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
                    </select><br>
                    <small><i><?php _e('If set yes, supervisor will able to delete tickets of assigned categories','wp-support-plus-responsive-ticket-system');?></i></small>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Allow Change Raised By', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <select name="agent_settings[supervisor_allow_change_raised_by]">
                        <option <?php echo $agent_settings['supervisor_allow_change_raised_by'] ? 'selected="selected"' : ''?> value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $agent_settings['supervisor_allow_change_raised_by'] ? '' : 'selected="selected"'?> value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
                    </select><br>
                    <small><i><?php _e('If set yes, supervisor will able to change raised by of tickets of assigned categories.','wp-support-plus-responsive-ticket-system');?></i></small>
                </td>
            </tr>
            
            
        </table>
        
        <?php do_action('wpsp_after_support_btn_settings')?>
        
        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">
        </p>
        
    </form>
    
</div>

<script>
    
</script>
