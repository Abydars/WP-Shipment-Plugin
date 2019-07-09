<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

add_thickbox();

$nonce = wp_create_nonce();

$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_fields");
?>

<div id="tab_container" style="margin-top: 50px;">
    
    <h1 class="wp-heading-inline">
        <?php _e('Custom Fields','wp-support-plus-responsive-ticket-system')?>
        <button onclick="wpsp_admin_load_popup('get_add_custom_field',0,'<?php echo $nonce?>','<?php _e('Add New Custom Field','wp-support-plus-responsive-ticket-system')?>', 600, 600, 250)" class="page-title-action"><?php _e('Add New','wp-support-plus-responsive-ticket-system')?></button>
    </h1>
    
    <table class="wp-list-table widefat fixed striped pages">
        
        <thead>
            <tr>
                <th class="column-primary"><?php _e('Name','wp-support-plus-responsive-ticket-system')?></th>
                <th class="column-primary"><?php _e('Type','wp-support-plus-responsive-ticket-system')?></th>
                <th class="column-primary"><?php _e('Agent Only','wp-support-plus-responsive-ticket-system')?></th>
                <th class="column-primary"><?php _e('Required','wp-support-plus-responsive-ticket-system')?></th>
                <th class="column-primary"><?php _e('Assigned Categories','wp-support-plus-responsive-ticket-system')?></th>
                <th class="column-primary"><?php _e('Action','wp-support-plus-responsive-ticket-system')?></th>
							  <?php do_action('wpsp_cust_field_setting_tbl_header')?>
            </tr>
        </thead>
        
        <tbody id="wpsp_sortable">
        <?php
        foreach ( $results as $field ) {
            
            ?>
            <tr class="sectionsid">
                <td><?php echo htmlspecialchars_decode( stripslashes($field->label))?></td>
                <td><?php echo $wpsupportplus->functions->get_custom_field_type_name( $field->field_type )?></td>
                <td><?php echo $field->isVarFeild ? __('Yes', 'wp-support-plus-responsive-ticket-system') : __('No', 'wp-support-plus-responsive-ticket-system')?></td>
                <td><?php echo $field->required ? __('Yes', 'wp-support-plus-responsive-ticket-system') : __('No', 'wp-support-plus-responsive-ticket-system')?></td>
                <td><?php echo $wpsupportplus->functions->get_custom_field_category_names( $field )?></td>
                <td><?php $wpsupportplus->functions->get_custom_field_actions( $field )?></td>
								<?php do_action( 'wpsp_cust_field_setting_tbl_val', $field )?>
            </tr>
            <?php
            
        }
        
        if(!$results){
            echo '<tr class="sectionsid"><td colspan="6" style="text-align: center;">'.__('No Custom Fields found!','wp-support-plus-responsive-ticket-system').'</td></tr>';
        }
        
        ?>
        </tbody>
        
    </table><br>
    
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
        
        
        
    });
</script>