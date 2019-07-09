<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$customize_ticket_list = $wpsupportplus->functions->get_customize_ticket_list();

?>
<div id="tab_container">
    
    <form method="post" action="">
        
        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="update_setting" value="customize_ticket_list_settings"/>
        <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>
				<div style="clear:both;padding-top:10px;">
					In version series 9x you can choose to theme Integrate or have Stand-Alone interface for your support page.
					You can customize Stand-Alone interface using this custom css.
				</div>
        <table class="form-table">
            
            <tr>
                <th scope="row"><h3><?php _e('Ticket List CSS', 'wp-support-plus-responsive-ticket-system'); ?></h3></th>
                <td></td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Filter Sidebar', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <textarea name="customize_ticket_list[filter_sidebar]" rows="8" cols="80"><?php echo $customize_ticket_list['filter_sidebar'];?></textarea>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Ticket List Table', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <textarea name="customize_ticket_list[ticket_list_table]" rows="8" cols="80"><?php echo $customize_ticket_list['ticket_list_table'];?></textarea>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Open Ticket Widgets', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <textarea name="customize_ticket_list[open_ticket_widget]" rows="8" cols="80"><?php echo $customize_ticket_list['open_ticket_widget'];?></textarea>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Ticket Log', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <textarea name="customize_ticket_list[ticket_log]" rows="8" cols="80"><?php echo $customize_ticket_list['ticket_log'];?></textarea>
                </td>
            </tr>
            
        </table>
        
        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">
						<button type="button" class="button" onclick="wpsp_customize_reset_default('ticket_list');"><?php _e('Reset default', 'wp-support-plus-responsive-ticket-system'); ?></button>
        </p>
        
    </form>
    
</div>