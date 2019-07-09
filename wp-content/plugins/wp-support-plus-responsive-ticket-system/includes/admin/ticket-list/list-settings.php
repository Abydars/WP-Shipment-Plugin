<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

add_thickbox();

$nonce = wp_create_nonce();

$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_ticket_list_order ORDER BY load_order");
?>

<div id="tab_container" style="margin-top: 50px;">
    
    <h1 class="wp-heading-inline"><?php _e('Ticket List Settings','wp-support-plus-responsive-ticket-system')?></h1>
    
    <form action="" onsubmit="return validate_wpsp_admin_popup_form(this);">
        
        <table class="wp-list-table widefat fixed striped pages">

            <thead>
                <tr>
                    <th class="column-primary" style="width: 50px;"><?php _e('Order','wp-support-plus-responsive-ticket-system')?></th>
                    <th class="column-primary"><?php _e('Name','wp-support-plus-responsive-ticket-system')?></th>
                    <th class="column-primary"><?php _e('Customer View','wp-support-plus-responsive-ticket-system')?></th>
                    <th class="column-primary"><?php _e('Agent View','wp-support-plus-responsive-ticket-system')?></th>
                    <th class="column-primary"><?php _e('Customer Filter','wp-support-plus-responsive-ticket-system')?></th>
                    <th class="column-primary"><?php _e('Agent Filter','wp-support-plus-responsive-ticket-system')?></th>
										<th class="column-primary"><?php _e('Customer Tickets','wp-support-plus-responsive-ticket-system')?></th>
                </tr>
            </thead>

            <tbody id="wpsp_sortable">
            <?php
            $filter_unavailable = array('subject','updated_time','date_updated','deleted_ticket');
						$filter_unavailable = apply_filters('wpsp_ticket_list_unavailable_filter', $filter_unavailable);

						$view_unavailable   = array('deleted_ticket');
						$view_unavailable   = apply_filters('wpsp_ticket_list_unavailable_view', $view_unavailable);
						
            $counter = 0;
            foreach ( $results as $field ) {

                ?>
                <tr class="sectionsid" id="<?php echo $field->id?>">
                    <td style="width: 50px;">
                        <span class="dashicons dashicons-move wpsp_pointer"></span>
                    </td>
                    <td><?php echo $wpsupportplus->functions->get_ticket_list_column_label($field->field_key)?></td>
                    
										<?php if(!in_array($field->field_key, $view_unavailable)){?>
											
												<td>
		                        <select name="customer_visible[<?php echo $field->id?>]">
		                            <option <?php echo $field->customer_visible ? 'selected="selected"' : ''?> value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
		                            <option <?php echo $field->customer_visible ? '' : 'selected="selected"'?> value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
		                        </select>
		                    </td>
		                    <td>
		                        <select name="agent_visible[<?php echo $field->id?>]">
		                            <option <?php echo $field->agent_visible ? 'selected="selected"' : ''?> value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
		                            <option <?php echo $field->agent_visible ? '' : 'selected="selected"'?> value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
		                        </select>
		                    </td>
											
										<?php }else{?>
                        
                        <td></td>
                        <td></td>
                        
                    <?php }?>
										
                    <?php if(!in_array($field->field_key, $filter_unavailable)){?>
                        
                        <td>
                            <select name="customer_filter[<?php echo $field->id?>]">
                                <option <?php echo $field->customer_filter ? 'selected="selected"' : ''?> value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
                                <option <?php echo $field->customer_filter ? '' : 'selected="selected"'?> value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
                            </select>
                        </td>
                        <td>
                            <select name="agent_filter[<?php echo $field->id?>]">
                                <option <?php echo $field->agent_filter ? 'selected="selected"' : ''?> value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
                                <option <?php echo $field->agent_filter ? '' : 'selected="selected"'?> value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
                            </select>
                        </td>
												
                    <?php }else{?>
                        
                        <td></td>
                        <td></td>
                        
                    <?php }?>
										<?php if(!in_array($field->field_key, $view_unavailable)){?>
												<td>
												 <select name="customer_ticket[<?php echo $field->id?>]">
														 <option <?php echo $field->customer_ticket ? 'selected="selected"' : ''?> value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
														 <option <?php echo $field->customer_ticket ? '' : 'selected="selected"'?> value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
												 </select>
										 	</td>
										<?php }else{?>
											<td></td>
										<?php }?>
                </tr>
                <?php

            }
            ?>
            </tbody>

        </table><br>
        
        <input type="hidden" name="action" value="wpsp_save_list_settings" />
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('list_settings')?>" />

        <button id="wpsp_setting_submit_btn" class="button button-primary"><?php _e('Save Changes','wp-support-plus-responsive-ticket-system')?></button><br><br>
    
    </form>
    
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        
        wpspjq("#wpsp_sortable").sortable({
            items: "tr",
            cursor: 'move',
            opacity: 0.6,
            update: function() {
                
            }
        });
        
    });
    
    function validate_wpsp_admin_popup_form(obj){

        wpsp_save_form_management(obj);
        return false;
    }
    
    function wpsp_save_form_management(obj){
        
        wpspjq('#wpsp_setting_submit_btn').text("<?php _e('Please Wait ...','wp-support-plus-responsive-ticket-system')?>");
        
        var order = wpspjq("#wpsp_sortable").sortable("toArray");
        var dataform = new FormData(obj);
        dataform.append('load_order', order);
        
        wpspjq.ajax({
            url: wpsp_admin.ajax_url,
            type: 'POST',
            data: dataform,
            processData: false,
            contentType: false
        })
        .done(function (response) {
            window.location.reload();
        });
    }
</script>