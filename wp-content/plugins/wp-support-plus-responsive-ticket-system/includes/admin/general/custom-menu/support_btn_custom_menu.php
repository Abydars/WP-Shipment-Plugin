<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_thickbox();
$section_add_href="admin.php?page=wp-support-plus&setting=general&section=support-button&action=add";
$section_edit_href="admin.php?page=wp-support-plus&setting=general&section=support-button&action=edit";

?>
<div id="tab_container">
    
    <form method="post" action="">
        
        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="update_setting" value="settings_support_btn"/>
        <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>
        
        <table class="form-table">
            
            <tr>
                <th scope="row"><h3><?php _e('Support Button', 'wp-support-plus-responsive-ticket-system'); ?></h3></th>
                <td></td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Show Support Button', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    
                    <?php
                    $checked = $support_btn_settings['allow_support_btn'] ? 'checked="checked"' : '';
                    ?>
                    <input <?php echo $checked?> type="checkbox" name="btn_settings[allow_support_btn]" value="1" />
                    
                    <input type="text" name="btn_settings[support_btn_label]" value="<?php echo $support_btn_settings['support_btn_label']?>"/><br>
                    
                    <small><i><?php _e('If enabled, it will show Support Button on front-end website. When visitor click this button, it will open Support Page selected above in new tab.','wp-support-plus-responsive-ticket-system');?></i></small>
                    
                </td>
            </tr>
            
						<tr>
              <th><?php _e('Icon', 'wp-support-plus-responsive-ticket-system')?></th>
              <td>
								
                <img id="wpsp_support_img" style="width: 100px;" src="<?php echo $support_btn_settings['img_url']?>" /><br>
								<button class="wpsp_btn" type="button" onclick="wpsp_upload_support_logo();"><?php _e('Upload Logo', 'wp-support-plus-responsive-ticket-system'); ?></button><br>
								
                <input id="wpsp_support_img_url" value="<?php echo $support_btn_settings['img_url']?>" type="hidden" name="btn_settings[img_url]" />
								
              </td>
            </tr>
						
        </table>
				<h3 class="wp-heading-inline">
		        <?php _e('Custom Menu Slider','wp-support-plus-responsive-ticket-system')?>
		    </h3>
        <?php
				global $wpsupportplus;
				
				$slider_menu=$wpsupportplus->functions->get_custom_slider_menus();
				
				?>
				
				<a href="<?php echo $section_add_href;?>" class="button button-primary"  style="margin-bottom:5px;float:right;" type="button"> <?php _e('Add New','wp-support-plus-responsive-ticket-system')?></a>				
        
				<table class="wp-list-table widefat fixed striped pages">
		        
		        <thead>
		            <tr>
		                <th class="column-primary" style="width: 50px;"><?php _e('Order','wp-support-plus-responsive-ticket-system')?></th>
		                <th class="column-primary"><?php _e('Icon','wp-support-plus-responsive-ticket-system')?></th>
		                <th class="column-primary"><?php _e('Title','wp-support-plus-responsive-ticket-system')?></th>
		                <th class="column-primary"><?php _e('Action','wp-support-plus-responsive-ticket-system')?></th>
		                <?php do_action('wpsp_custom_menu_setting_tbl_header')?>
		            </tr>
		        </thead>
		        
		        <tbody id="wpsp_sortable">
		        <?php
		        $counter = 0;
						$menu_order=array();
		        foreach ( $slider_menu as $menu ) {
								
								$nonce = wp_create_nonce($menu->id);
		            $menu_order[]=$menu->id;
		            ?>
		            <tr class="sectionsid" id="<?php echo $menu->id?>">
		                <td style="width: 50px;">
		                    <span class="dashicons dashicons-move wpsp_pointer"></span>
		                </td>
										<td>
												<img src="<?php echo $menu->menu_icon;?>" style="height:35px;width:35px;" alt="<?php echo stripcslashes($menu->menu_text)?>">
										</td>
		                <td>
		                    <span class=""><?php echo stripcslashes($menu->menu_text)?></span>
		                </td>
		                <td>
												<a href="<?php echo $section_edit_href.'&cs='.$menu->id;?>" class="dashicons dashicons-edit wpsp_pointer"></a>				
												<span class="dashicons dashicons-trash wpsp_pointer" onclick="wpsp_admin_load_popup('get_delete_custom_menu',<?php echo $menu->id?>,'<?php echo $nonce?>','<?php _e('Delete Custom Menu','wp-support-plus-responsive-ticket-system')?>', 300, 200, 63)"> </span>
										</td>
		                <?php do_action( 'wpsp_custom_menu_setting_tbl_val', $menu )?>
		            </tr>
		            <?php
		            
		        }
		        ?>
		        </tbody>
		        
		    </table><br>
				
        <?php do_action('wpsp_after_support_btn_settings')?>
        
        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">
						<input type="hidden" id="custom_menu_sort_order" name="custom_menu_sort_order" value="<?php echo implode(',', $menu_order);?>"/>
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
								wpspjq('#custom_menu_sort_order').val(order);
            }
        });
        
    });
		
		function wpsp_upload_support_logo(){
		    file_frame = wp.media.frames.file_frame = wp.media({
		        title: "Select Logo",
		        button: {
		            text: "Set logo"
		        },
		        multiple: false
		    });
		    file_frame.on('select', function () {
		        image_data = file_frame.state().get('selection').first().toJSON();
		        wpspjq('#wpsp_support_img').attr('src',image_data.url);
		        wpspjq('#wpsp_support_img_url').val(image_data.url);
		    });
		    file_frame.open();
		}

</script>