<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_thickbox();
$section_add_href="admin.php?page=wp-support-plus&setting=general&section=support-page-menu&action=add";
$section_edit_href="admin.php?page=wp-support-plus&setting=general&section=support-page-menu&action=edit";

?>
<div style="clear:both;"></div>
<div id="tab_container">
    
    <form method="post" action="">
        
        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="update_setting" value="support_page_menu"/>
        <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>
        
        <?php
				global $wpsupportplus;
				
				$support_menu=$wpsupportplus->functions->get_support_page_menus();
				
				?>
				
				<a href="<?php echo htmlentities($section_add_href);?>" class="button button-primary"  style="margin-bottom:5px;float:right;" type="button"> <?php _e('Add New','wp-support-plus-responsive-ticket-system')?></a>				
        
				<table class="wp-list-table widefat fixed striped pages">
		        
		        <thead>
		            <tr>
		                <th class="column-primary" style="width: 50px;"><?php _e('Order','wp-support-plus-responsive-ticket-system')?></th>
		                <th class="column-primary"><?php _e('Icon','wp-support-plus-responsive-ticket-system')?></th>
		                <th class="column-primary"><?php _e('Title','wp-support-plus-responsive-ticket-system')?></th>
		                <th class="column-primary"><?php _e('Action','wp-support-plus-responsive-ticket-system')?></th>
		                <?php do_action('wpsp_support_page_menu_setting_tbl_header')?>
		            </tr>
		        </thead>
		        
		        <tbody id="wpsp_sortable">
		        <?php
		        $counter = 0;
						$menu_order=array();
		        foreach ( $support_menu as $menu ) {
		            $menu_order[]=$menu->id;
								$nonce = wp_create_nonce($menu->id);
		            ?>
		            <tr class="sectionsid" id="<?php echo $menu->id?>">
		                <td style="width: 50px;">
		                    <span class="dashicons dashicons-move wpsp_pointer"></span>
		                </td>
										<td>
												<?php 
														if($menu->icon){
															?>
															<img src="<?php echo $menu->icon;?>" style="height:35px;width:35px;" alt="<?php echo stripcslashes($menu->name)?>">
															<?php
														}else{
															echo '-';
														}
												 ?>
												
										</td>
		                <td>
		                    <span class=""><?php echo stripcslashes($menu->name)?></span>
		                </td>
		                <td>
												<a href="<?php echo $section_edit_href.'&sm='.$menu->id;?>" class="dashicons dashicons-edit wpsp_pointer"></a>				
												<span class="dashicons dashicons-trash wpsp_pointer" onclick="wpsp_admin_load_popup('get_delete_support_menu',<?php echo $menu->id?>,'<?php echo $nonce?>','<?php _e('Delete Support Menu','wp-support-plus-responsive-ticket-system')?>', 300, 200, 63)"> </span>
										</td>
		                <?php do_action( 'wpsp_support_menu_setting_tbl_val', $menu )?>
		            </tr>
		            <?php
		            
		        }
		        ?>
		        </tbody>
		        
		    </table><br>
				
        <?php do_action('wpsp_after_support_page_menu_settings')?>
        
        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">
						<input type="hidden" id="support_menu_sort_order" name="support_menu_sort_order" value="<?php echo implode(',', $menu_order);?>"/>
        </p>
        
    </form>
    
</div>

<div id="wpsp-admin-popup-content" style="display:none;">
		<div>
				<div id="wpsp-admin-popup-body" style="display: none;"></div>
				<div id="wpsp-admin-popup-wait" style="text-align: center;">
						<img src="<?php echo WPSP_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>" />
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
                var order = wpspjq("#wpsp_sortable").sortable("toArray");
								wpspjq('#support_menu_sort_order').val(order);
            }
        });
        
    });
</script>