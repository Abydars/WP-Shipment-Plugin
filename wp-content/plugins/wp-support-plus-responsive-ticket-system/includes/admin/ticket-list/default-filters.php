<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$default_filters = $wpsupportplus->functions->get_default_filters();

$statuses = $wpsupportplus->functions->get_wpsp_statuses();
$list_keys = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_ticket_list_order ORDER BY load_order");
?>

<div id="tab_container">
    
    <form method="post" action="">
        
        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="update_setting" value="default_filters"/>
        <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>
        
        <table class="form-table">
            
            <tr>
                <th scope="row"><h3><?php _e('Customer View', 'wp-support-plus-responsive-ticket-system'); ?></h3></th>
                <td></td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Hide Selected Statuses', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    
                    <?php
                    foreach ($statuses as $status){
                        $checked = isset($default_filters['customer_hide_statuses']) && in_array($status->id, $default_filters['customer_hide_statuses']) ? 'checked="checked"' : '';
                        ?>
                        <input <?php echo $checked?> type="checkbox" name="default_filters[customer_hide_statuses][]" value="<?php echo $status->id?>" />
                        <span class="wpsp_admin_label" style="background-color:<?php echo $status->color?>;"><?php echo $status->name?></span><br><br>
                        <?php
                    }
                    ?>
                    <small><i><?php _e('Selected statuses will be hidden if no filter is active for customer view. If customer use status filter manually on ticket list, this setting will not take effect and tickets will be filtered as per customer selection.','wp-support-plus-responsive-ticket-system');?></i></small>
                    
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Order By', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <select name="default_filters[customer_orderby]">
                    <?php
                    foreach ($list_keys as $field){
                        $selected = $default_filters['customer_orderby'] == $field->join_compare ? 'selected="selected"' : '';
                        ?>
                        <option <?php echo $selected?> value="<?php echo $field->join_compare?>"><?php echo $wpsupportplus->functions->get_ticket_list_column_label($field->field_key)?></option>
                        <?php
                    }
                    ?>
                    </select>
                    <select name="default_filters[customer_orderby_order]">
                        <option <?php echo $default_filters['customer_orderby_order'] == 'ASC'? 'selected="selected"' : ''?> value="ASC">ASC</option>
                        <option <?php echo $default_filters['customer_orderby_order'] == 'DESC'? 'selected="selected"' : ''?> value="DESC">DESC</option>
                    </select>
                    <br>
                    <small><i><?php _e('Tickets are loaded on selected order for customer view by default. If customer apply manual filter, this setting will not take effect.','wp-support-plus-responsive-ticket-system');?></i></small>
                    
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Number of Tickets', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <select name="default_filters[customer_par_page_tickets]">
                        <option <?php echo $default_filters['customer_par_page_tickets'] == 10  ? 'selected="selected"' : ''?> value="10"><?php _e('10', 'wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $default_filters['customer_par_page_tickets'] == 20  ? 'selected="selected"' : ''?> value="20"><?php _e('20', 'wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $default_filters['customer_par_page_tickets'] == 30  ? 'selected="selected"' : ''?> value="30"><?php _e('30', 'wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $default_filters['customer_par_page_tickets'] == 40  ? 'selected="selected"' : ''?> value="40"><?php _e('40', 'wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $default_filters['customer_par_page_tickets'] == 50  ? 'selected="selected"' : ''?> value="50"><?php _e('50', 'wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $default_filters['customer_par_page_tickets'] == 100 ? 'selected="selected"' : ''?> value="100"><?php _e('100', 'wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $default_filters['customer_par_page_tickets'] == 500 ? 'selected="selected"' : ''?> value="500"><?php _e('500', 'wp-support-plus-responsive-ticket-system')?></option>
                    </select>
                    <br>
                    <small><i><?php _e('Number of Tickets shown per page for customer view.','wp-support-plus-responsive-ticket-system');?></i></small>
                    
                </td>
            </tr>
            
            <tr>
                <th scope="row"><h3><?php _e('Agent View', 'wp-support-plus-responsive-ticket-system'); ?></h3></th>
                <td></td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Hide Selected Statuses', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    
                    <?php
                    foreach ($statuses as $status){
                        $checked = isset($default_filters['agent_hide_statuses']) && in_array($status->id, $default_filters['agent_hide_statuses']) ? 'checked="checked"' : '';
                        ?>
                        <input <?php echo $checked?> type="checkbox" name="default_filters[agent_hide_statuses][]" value="<?php echo htmlentities($status->id)?>" />
                        <span class="wpsp_admin_label" style="background-color:<?php echo $status->color?>;"><?php echo htmlentities($status->name)?></span><br><br>
                        <?php
                    }
                    ?>
                    <small><i><?php _e('Selected statuses will be hidden if no filter is active for agent view. If agent use status filter manually on ticket list, this setting will not take effect and tickets will be filtered as per customer selection.','wp-support-plus-responsive-ticket-system');?></i></small>
                    
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Order By', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <select name="default_filters[agent_orderby]">
                    <?php
                    foreach ($list_keys as $field){
                        $selected = $default_filters['agent_orderby'] == $field->join_compare ? 'selected="selected"' : '';
                        ?>
                        <option <?php echo $selected?> value="<?php echo $field->join_compare?>"><?php echo $wpsupportplus->functions->get_ticket_list_column_label($field->field_key)?></option>
                        <?php
                    }
                    ?>
                    </select>
                    <select name="default_filters[agent_orderby_order]">
                        <option <?php echo $default_filters['agent_orderby_order'] == 'ASC'? 'selected="selected"' : ''?> value="ASC">ASC</option>
                        <option <?php echo $default_filters['agent_orderby_order'] == 'DESC'? 'selected="selected"' : ''?> value="DESC">DESC</option>
                    </select>
                    <br>
                    <small><i><?php _e('Tickets are loaded on selected order for agent view by default. If agent apply manual filter, this setting will not take effect.','wp-support-plus-responsive-ticket-system');?></i></small>
                    
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Number of Tickets', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <select name="default_filters[agent_par_page_tickets]">
                        <option <?php echo $default_filters['agent_par_page_tickets'] == 10  ? 'selected="selected"' : ''?> value="10"><?php _e('10', 'wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $default_filters['agent_par_page_tickets'] == 20  ? 'selected="selected"' : ''?> value="20"><?php _e('20', 'wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $default_filters['agent_par_page_tickets'] == 30  ? 'selected="selected"' : ''?> value="30"><?php _e('30', 'wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $default_filters['agent_par_page_tickets'] == 40  ? 'selected="selected"' : ''?> value="40"><?php _e('40', 'wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $default_filters['agent_par_page_tickets'] == 50  ? 'selected="selected"' : ''?> value="50"><?php _e('50', 'wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $default_filters['agent_par_page_tickets'] == 100 ? 'selected="selected"' : ''?> value="100"><?php _e('100', 'wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $default_filters['agent_par_page_tickets'] == 500 ? 'selected="selected"' : ''?> value="500"><?php _e('500', 'wp-support-plus-responsive-ticket-system')?></option>
                    </select>
                    <br>
                    <small><i><?php _e('Number of Tickets shown per page for agent view.','wp-support-plus-responsive-ticket-system');?></i></small>
                    
                </td>
            </tr>
						<tr>
							<th scope="row"><h3><?php _e('Ticket List', 'wp-support-plus-responsive-ticket-system'); ?></h3></th>
							<td></td>
						</tr>
						<tr>
								<th>
									<?php _e('Ticket List Filter', 'wp-support-plus-responsive-ticket-system'); ?>
									<br /><small><i><?php _e('On page load','wp-support-plus-responsive-ticket-system');?></i></small>
								</th>
								<td>
									<?php
									$ticket_filter = $default_filters['ticket_list_filter'];
									$checked = $ticket_filter == 1 ? 'checked="checked"' : '';
									?>
									<input <?php echo $checked?> type="radio" name="default_filters[ticket_list_filter]" value="1" />
									<?php _e('Enable','wp-support-plus-responsive-ticket-system')?>&nbsp;&nbsp;

									<?php
									$checked = $ticket_filter == 0 ? 'checked="checked"' : '';
									?>
									<input <?php echo $checked?> type="radio" name="default_filters[ticket_list_filter]" value="0" />
									<?php _e('Disable','wp-support-plus-responsive-ticket-system')?><br>
								</td>
						</tr>
    	</table>
        
        <?php do_action('wpsp_after_default_filter_settings')?>
        
        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">
        </p>
        
    </form>
    
</div>

<script>
    
</script>
