<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb,$wpsupportplus;

$section_list_href = "admin.php?page=wp-support-plus&setting=general&section=support-page-menu";

$smid = isset($_REQUEST['sm']) ? intval(sanitize_text_field($_REQUEST['sm'])) : '';
if( empty($smid) ){
  die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$menu=$wpdb->get_row("select * from {$wpdb->prefix}wpsp_support_menu where id=".$smid);

?>
<div id="tab_container">

    <form id="wpsp_frm_general_spm_menu" method="post" action="#" onsubmit="return validate_edit_support_menu(this);">

        <table class="form-table">

            <tr>
                <th scope="row" colspan="2"><h3><?php _e('Add New Support Page Menu', 'wp-support-plus-responsive-ticket-system'); ?></h3></th>
            </tr>

            <tr>
              <th><?php _e('Name', 'wp-support-plus-responsive-ticket-system')?></th>
              <td>
                <input type="text" class="required" id="support_menu_name" name="wpsp_sp_menu[name]" value="<?php echo htmlentities($menu->name);?>">
              </td>
            </tr>

            <tr>
              <th><?php _e('Menu Icon', 'wp-support-plus-responsive-ticket-system')?></th>
              <td>
                <img id="wpsp_support_menu_img" style="width: 100px;" src="<?php echo $menu->icon;?>"/><br>
                <button class="wpsp_btn" type="button" onclick="wpsp_upload_sp_menu_logo();"><?php _e('Upload Logo', 'wp-support-plus-responsive-ticket-system'); ?></button>
								<button class="wpsp_btn" type="button" onclick="wpsp_reset_sp_menu_logo();"><?php _e('Clear Logo', 'wp-support-plus-responsive-ticket-system'); ?></button><br/>
                <input id="wpsp_support_menu_img_url" value="<?php echo $menu->icon?>"  type="hidden" name="wpsp_sp_menu[img_url]" />
              </td>
            </tr>

            <tr>
              <th><?php _e('Menu URL', 'wp-support-plus-responsive-ticket-system')?></th>
              <td>
                <input type="text" class="required"  id="support_page_menu_url" name="wpsp_sp_menu[url]" value="<?php echo $menu->redirect_url;?>">
              </td>
            </tr>
        </table>

        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="update_setting" value="support_page_menu_update"/>
        <input type="hidden" name="wpsp_sp_menu[id]" value="<?php echo htmlentities($smid);?>"/>
        <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>

        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">
            <a href="<?php echo htmlentities($section_list_href);?>" class="button button-primary" type="button"> <?php _e('Cancel','wp-support-plus-responsive-ticket-system')?></a>				
        </p>

    </form>

</div>
<script>
function wpsp_upload_sp_menu_logo(){
    file_frame = wp.media.frames.file_frame = wp.media({
        title: "Select Menu Logo",
        button: {
            text: "Set Menu logo"
        },
        multiple: false
    });
    file_frame.on('select', function () {
        image_data = file_frame.state().get('selection').first().toJSON();
        wpspjq('#wpsp_support_menu_img').attr('src',image_data.url);
        wpspjq('#wpsp_support_menu_img_url').val(image_data.url);
    });
    file_frame.open();
}

function wpsp_reset_sp_menu_logo(){
	wpspjq('#wpsp_support_menu_img').attr('src', '');
	wpspjq('#wpsp_support_menu_img_url').val('');
}
			
function validate_edit_support_menu(obj){
	var flag=true;
	if( wpspjq.trim(wpspjq('#support_menu_name').val()) ==""){
			flag=false;
	}
  if( wpspjq.trim(wpspjq('#support_page_menu_url').val()) ==""){
			flag=false;
	}
	if(!flag){
			alert(wpsp_admin.required);
	}
	
	return flag;
}
</script>
