<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$status_id = isset($_POST['load_id']) ? sanitize_text_field($_POST['load_id']) : 0;
$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $status_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$status = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_custom_status where id=".$status_id);

?>

<form action="" onsubmit="return validate_wpsp_admin_popup_form(this);">
    
    <div id="wpsp_popup_form_error" class="error notice" style="display: none;">
        <p></p>
    </div>
    
    <div class="wpsp_popup_form_element">
        <?php _e('Status Name','wp-support-plus-responsive-ticket-system')?>:<br>
        <input class="wpsp_fullwidth" type="text" name="status_name" value="<?php echo htmlentities(stripcslashes($status->name), ENT_QUOTES)?>"/>
    </div>
    
    <div class="wpsp_popup_form_element">
        <?php _e('Choose Color','wp-support-plus-responsive-ticket-system')?>:<br>
        <input class="wpsp_color_picker" type="text" name="color" value="<?php echo $status->color?>"/>
    </div>
    
    <?php do_action('wpsp_get_edit_status', $status);?>
    
    <input type="hidden" name="action" value="wpsp_set_edit_status" />
    <input type="hidden" name="load_id" value="<?php echo $status_id?>" />
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce($status_id)?>" />
    
    <hr class="wpsp_admin_popup_footer_saperator">
    <button class="button button-primary" type="submit"><?php _e('Submit','wp-support-plus-responsive-ticket-system')?></button>
    <button class="button" type="button" onclick="wpsp_close_admin_popup();"><?php _e('Cancel','wp-support-plus-responsive-ticket-system')?></button>
    
</form>

<script>
jQuery(document).ready(function(){
    wpspjq('.wpsp_color_picker').wpColorPicker();
});
    
function validate_wpsp_admin_popup_form(obj){
    
    var error_flag = false;
    
    if( !error_flag && wpspjq('input[name=status_name]').val().trim() == '' ){
        wpspjq('#wpsp_popup_form_error p').text("<?php _e('Please enter status name!','wp-support-plus-responsive-ticket-system')?>");
        wpspjq('input[name=status_name]').val('');
        wpspjq('input[name=status_name]').focus();
        error_flag = true;
    }
    
    if( !error_flag && wpspjq('input[name=color]').val().trim() == '' ){
        wpspjq('#wpsp_popup_form_error p').text("<?php _e('Please choose color!','wp-support-plus-responsive-ticket-system')?>");
        wpspjq('input[name=color]').val('');
        wpspjq('input[name=color]').focus();
        error_flag = true;
    }
    
    <?php do_action('validate_wpsp_get_edit_status');?>
    
    if(!error_flag){
        wpsp_admin_submit_popup(obj);
    } else {
        wpspjq('#wpsp_popup_form_error').show();
    }
    
    return false;
}
</script>