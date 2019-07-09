<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

add_thickbox();

$nonce = wp_create_nonce();

$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_ticket_form_order ORDER BY load_order");
?>

<div id="tab_container" style="margin-top: 50px;">
    
    <h1 class="wp-heading-inline"><?php _e('Create New Ticket Form','wp-support-plus-responsive-ticket-system')?></h1>
    <a href="admin.php?page=wp-support-plus&setting=ticket-form&section=custom-fields" class="page-title-action" target="_blank"><?php _e('Add New Form Field','wp-support-plus-responsive-ticket-system')?></a>
    <form action="" onsubmit="return validate_wpsp_admin_popup_form(this);">
        
        <table class="wp-list-table widefat fixed striped pages">

            <thead>
                <tr>
                    <th class="column-primary" style="width: 50px;"><?php _e('Order','wp-support-plus-responsive-ticket-system')?></th>
                    <th class="column-primary"><?php _e('Name','wp-support-plus-responsive-ticket-system')?></th>
                    <th class="column-primary"><?php _e('Status','wp-support-plus-responsive-ticket-system')?></th>
                    <th class="column-primary"><?php _e('Full Width','wp-support-plus-responsive-ticket-system')?></th>
                    <?php do_action('wpsp_form_manage_setting_tbl_header')?>
                </tr>
            </thead>

            <tbody id="wpsp_sortable">
            <?php
            $counter = 0;
            foreach ( $results as $field ) {

                ?>
                <tr class="sectionsid" id="<?php echo $field->id?>">
                    <td style="width: 50px;">
                        <span class="dashicons dashicons-move wpsp_pointer"></span>
                    </td>
                    <td><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></td>
                    <td>
                        <select name="field_status[<?php echo $field->id?>]">
                            <option <?php echo $field->status ? 'selected="selected"' : ''?> value="1"><?php _e('Enable','wp-support-plus-responsive-ticket-system')?></option>
                            <option <?php echo $field->status ? '' : 'selected="selected"'?> value="0"><?php _e('Disable','wp-support-plus-responsive-ticket-system')?></option>
                        </select>
                    </td>
                    <td>
                        <select name="field_full_width[<?php echo $field->id?>]">
                            <option <?php echo $field->full_width ? 'selected="selected"' : ''?> value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
                            <option <?php echo $field->full_width ? '' : 'selected="selected"'?> value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
                        </select>
                    </td>
                    <?php do_action( 'wpsp_form_manage_setting_tbl_val', $field )?>
                </tr>
                <?php

            }
            ?>
            </tbody>

        </table><br>
        
        <input type="hidden" name="action" value="wpsp_save_form_management" />
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('form_management')?>" />

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