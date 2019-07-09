<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$section_list_href="admin.php?page=wp-support-plus&setting=general&section=support-button";
?>
<div id="tab_container">

    <form id="wpsp_frm_general_cs_menu" method="post" action="#" onsubmit="return validate_add_custom_menu(this);">

        <table class="form-table">

            <tr>
                <th scope="row" colspan="2"><h3><?php _e('Add New Menu Slider', 'wp-support-plus-responsive-ticket-system'); ?></h3></th>
            </tr>

            <tr>
              <th><?php _e('Name', 'wp-support-plus-responsive-ticket-system')?></th>
              <td>
                <input type="text" class="required" id="custom_menu_name" name="wpsp_custom_menu[name]" value="">
              </td>
            </tr>

            <tr>
              <th><?php _e('Menu Icon', 'wp-support-plus-responsive-ticket-system')?></th>
              <td>
                <img id="wpsp_custom_menu_img" style="width: 100px;" /><br>
                <button class="wpsp_btn" type="button" onclick="wpsp_upload_custom_menu_logo();"><?php _e('Upload Logo', 'wp-support-plus-responsive-ticket-system'); ?></button><br>
                <input id="wpsp_custom_menu_img_url"  type="hidden" name="wpsp_custom_menu[img_url]" />
              </td>
            </tr>

            <tr>
              <th><?php _e('Menu URL', 'wp-support-plus-responsive-ticket-system')?></th>
              <td>
                <input type="text" class="required"  id="custom_menu_url" name="wpsp_custom_menu[url]" value="">
              </td>
            </tr>
        </table>

        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="update_setting" value="custom_menu_add"/>
        <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>

        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">
            <a href="<?php echo htmlentities($section_list_href);?>" class="button button-primary" type="button"> <?php _e('Cancel','wp-support-plus-responsive-ticket-system')?></a>				
        </p>

    </form>

</div>
<script>
function wpsp_upload_custom_menu_logo(){
    file_frame = wp.media.frames.file_frame = wp.media({
        title: "Select Custom Menu Logo",
        button: {
            text: "Set Custom Menu logo"
        },
        multiple: false
    });
    file_frame.on('select', function () {
        image_data = file_frame.state().get('selection').first().toJSON();
        wpspjq('#wpsp_custom_menu_img').attr('src',image_data.url);
        wpspjq('#wpsp_custom_menu_img_url').val(image_data.url);
    });
    file_frame.open();
}

function validate_add_custom_menu(obj){
	var flag=true;
	if( wpspjq.trim(wpspjq('#custom_menu_name').val()) ==""){
			flag=false;
	}
	if( wpspjq.trim(wpspjq('#wpsp_custom_menu_img_url').val()) ==""){
			flag=false;
	}
	if( wpspjq.trim(wpspjq('#custom_menu_url').val()) ==""){
			flag=false;
	}
	if(!flag){
			alert(wpsp_admin.required);
	}
	
	return flag;
}
</script>