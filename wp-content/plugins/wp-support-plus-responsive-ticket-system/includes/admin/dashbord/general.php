<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$dashbord_general = $wpsupportplus->functions->get_dashbord_general();

$statuses = $wpsupportplus->functions->get_wpsp_statuses();
?>

<div id="tab_container">
    
    <form method="post" action="">
        
        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="update_setting" value="dashbord_general"/>
        <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>
        
        <table class="form-table">
            
            <tr>
                <th scope="row"><?php _e('Statuses to Show', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    
                    <?php
                    foreach ($statuses as $status){
                        $checked = isset($dashbord_general['statuses']) && in_array($status->id, $dashbord_general['statuses']) ? 'checked="checked"' : '';
                        ?>
                        <input <?php echo $checked?> type="checkbox" name="dashbord_general[statuses][]" value="<?php echo htmlentities($status->id)?>" />
                        <span class="wpsp_admin_label" style="background-color:<?php echo $status->color?>;"><?php echo htmlentities($status->name)?></span><br><br>
                        <?php
                    }
                    ?>
                </td>
            </tr>
						
						<tr>
								<th scope="row"><?php _e('Dashborad to Hide', 'wp-support-plus-responsive-ticket-system'); ?></th>
								<td>

										<?php
										$checked = $wpsupportplus->functions->is_allow_to_view_customer_dashboard_page() ? 'checked="checked"' : '';
										?>
										<input <?php echo $checked?> type="checkbox" name="dashbord_general[allow_to_view_customer]" value="1" />
										<?php _e('Customer', 'wp-support-plus-responsive-ticket-system'); ?></br>
										
										<?php
										$checked = $wpsupportplus->functions->is_allow_to_view_agent_dashboard_page() ? 'checked="checked"' : '';
										?>
										<input <?php echo $checked?> type="checkbox" name="dashbord_general[allow_to_view_agent]" value="1" />
										<?php _e('Agent', 'wp-support-plus-responsive-ticket-system'); ?></br>
										
										<?php
										$checked = $wpsupportplus->functions->is_allow_to_view_supervisor_dashboard_page() ? 'checked="checked"' : '';
										?>
										<input <?php echo $checked?> type="checkbox" name="dashbord_general[allow_to_view_supervisor]" value="1" />
										<?php _e('Supervisor', 'wp-support-plus-responsive-ticket-system'); ?></br>
										
										<?php
										$checked = $wpsupportplus->functions->is_allow_to_view_administrator_dashboard_page() ? 'checked="checked"' : '';
										?>
										<input <?php echo $checked?> type="checkbox" name="dashbord_general[allow_to_view_administrator]" value="1" />
										<?php _e('Administrator', 'wp-support-plus-responsive-ticket-system'); ?>
								</td>
						</tr>
            
        </table>
        
        <?php do_action('wpsp_after_dashbord_settings')?>
        
        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">
        </p>
        
    </form>
    
</div>

<script>
    
</script>
