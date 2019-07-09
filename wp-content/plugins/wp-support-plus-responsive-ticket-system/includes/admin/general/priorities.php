<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

add_thickbox();

$nonce = wp_create_nonce();

$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_priority ORDER BY load_order");
?>

<div id="tab_container" style="margin-top: 50px;">
    
    <h1 class="wp-heading-inline">
        <?php _e('Priorities','wp-support-plus-responsive-ticket-system')?>
        <button onclick="wpsp_admin_load_popup('get_add_priority',0,'<?php echo $nonce?>','<?php _e('Add New Priority','wp-support-plus-responsive-ticket-system')?>', 300, 300, 110)" class="page-title-action"><?php _e('Add New','wp-support-plus-responsive-ticket-system')?></button>
    </h1>
    
    <table class="wp-list-table widefat fixed striped pages">
        
        <thead>
            <tr>
                <th class="column-primary" style="width: 50px;"><?php _e('Order','wp-support-plus-responsive-ticket-system')?></th>
                <th class="column-primary"><?php _e('Name','wp-support-plus-responsive-ticket-system')?></th>
                <th class="column-primary"><?php _e('Color','wp-support-plus-responsive-ticket-system')?></th>
                <th class="column-primary"><?php _e('Action','wp-support-plus-responsive-ticket-system')?></th>
                <?php do_action('wpsp_priority_setting_tbl_header')?>
            </tr>
        </thead>
        
        <tbody id="wpsp_sortable">
        <?php
        $counter = 0;
        foreach ( $results as $priority ) {
            
            ?>
            <tr class="sectionsid" id="<?php echo $priority->id?>">
                <td style="width: 50px;">
                    <span class="dashicons dashicons-move wpsp_pointer"></span>
                </td>
                <td>
                    <span class="wpsp_admin_label" style="background-color:<?php echo $priority->color?>;"><?php echo stripcslashes($priority->name)?></span>
                </td>
                <td><?php echo $priority->color?></td>
                <td><?php $wpsupportplus->functions->get_priority_actions( $priority )?></td>
                <?php do_action( 'wpsp_priority_setting_tbl_val', $priority )?>
            </tr>
            <?php
            
        }
        ?>
        </tbody>
        
    </table><br>
    
    <button class="button button-primary" onclick="wpsp_save_table_order('priority','<?php echo wp_create_nonce('priority')?>');"><?php _e('Save Order','wp-support-plus-responsive-ticket-system')?></button>
    
    <div id="wpsp-admin-popup-content" style="display:none;">
        <div>
            <div id="wpsp-admin-popup-body" style="display: none;"></div>
            <div id="wpsp-admin-popup-wait" style="text-align: center;">
                <img src="<?php echo WPSP_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>" />
            </div>
        </div>
    </div>
    
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
</script>