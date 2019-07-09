<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$field_id = isset($_POST['load_id']) ? sanitize_text_field($_POST['load_id']) : 0;
$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $field_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$field = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_custom_fields where id=".$field_id);

?>

<form action="" onsubmit="return validate_wpsp_admin_popup_form(this);">
    
    <div id="wpsp_popup_form_error" class="error notice" style="display: none;">
        <p></p>
    </div>
    
    <div class="wpsp_popup_form_element">
        <?php _e('Are you sure to delete this custom field?','wp-support-plus-responsive-ticket-system')?><br>
        <ul>
            <li><?php echo htmlentities($field->label)?></li>
        </ul>
        <small><i><?php _e('Please note, this will delete all data associated with this custom field and can not be undone!','wp-support-plus-responsive-ticket-system')?></i></small><br>
    </div>
    
    <?php do_action('wpsp_get_delete_custom_field', $field);?>
    
    <input type="hidden" name="action" value="wpsp_set_delete_custom_field" />
    <input type="hidden" name="load_id" value="<?php echo htmlentities($field_id)?>" />
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce($field_id)?>" />
    
    <hr class="wpsp_admin_popup_footer_saperator">
    <button class="button button-primary" type="submit"><?php _e('Submit','wp-support-plus-responsive-ticket-system')?></button>
    <button class="button" type="button" onclick="wpsp_close_admin_popup();"><?php _e('Cancel','wp-support-plus-responsive-ticket-system')?></button>
    
</form>

<script>
function validate_wpsp_admin_popup_form(obj){
    
    var error_flag = false;
        
    <?php do_action('validate_wpsp_get_delete_custom_field');?>
    
    if(!error_flag){
        wpsp_admin_submit_popup(obj);
    } else {
        wpspjq('#wpsp_popup_form_error').show();
    }
    
    return false;
}
</script>