<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Check nonce
 */
if( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce']) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$categories = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_catagories ORDER BY load_order");

?>

<form action="" onsubmit="return validate_wpsp_admin_popup_form(this);">
    
    <div id="wpsp_popup_form_error" class="error notice" style="display: none;">
        <p></p>
    </div>
    
    <div class="wpsp_popup_form_element">
        <b><?php _e('Field Label','wp-support-plus-responsive-ticket-system')?>:</b><br>
        <input class="wpsp_fullwidth" type="text" name="field_label" value=""/>
    </div>
		
		<div class="wpsp_popup_form_element">
			<b><?php _e('Instructions','wp-support-plus-responsive-ticket-system')?>:</b><br>
			<input class="wpsp_fullwidth" type="text" name="field_instructions" value=""/>
		</div>
    
    <div class="wpsp_popup_form_element">
        <b><?php _e('Choose Field Type','wp-support-plus-responsive-ticket-system')?>:</b><br>
        <select name="field_type">
            <option value=""></option>
            <?php
            foreach ($wpsupportplus->functions->get_custom_fields() as $key => $val){
                ?>
                <option value="<?php echo $key;?>"><?php echo $val;?></option>
                <?php
            }
            ?>
        </select>
    </div>
    
    <div class="wpsp_popup_form_element" style="display: none;">
        <b><?php _e('Enter Field Options','wp-support-plus-responsive-ticket-system')?>:</b><br>
        <textarea name="field_options"></textarea><br>
        <small><i><?php _e('Please make sure you enter one option per line')?></i></small>
    </div>
    
    <div class="wpsp_popup_form_element" style="">
        <b><?php _e('Agent Only','wp-support-plus-responsive-ticket-system')?>:</b><br>
        <select name="agent_only">
            <option value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
            <option value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
        </select><br>
        <small><i><?php _e('Agent Only fields not available for ticket form. These can be edited by agents for their internal use.','wp-support-plus-responsive-ticket-system')?></i></small>
    </div>
    
    <div class="wpsp_popup_form_element" style="display: none;">
        <b><?php _e('Required','wp-support-plus-responsive-ticket-system')?>:</b><br>
        <select name="required">
            <option value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
            <option value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
        </select><br>
        <small><i><?php _e('Required field to ticket form. If set Yes, user must have to fill or submit value for this field','wp-support-plus-responsive-ticket-system')?></i></small>
    </div>
    
    <div class="wpsp_popup_form_element" style="display: none;">
        <b><?php _e('Assign Categories (optional)','wp-support-plus-responsive-ticket-system')?>:</b><br>
        <select id="wpsp_categories_multi" name="category[]" size="4" multiple>
            
            <?php foreach ($categories as $category):?>
            <option value="<?php echo $category->id?>"><?php echo $category->name?></option>
            <?php endforeach;?>
            
        </select><br>
        <small><i><?php _e('If category is set, custom field will only visible when one of the selected category is choosen in create ticket form. If you want to show this all the time, please leave this empty. Use Ctrl+Click for select OR unselect value.','wp-support-plus-responsive-ticket-system')?></i></small>
    </div>
    
    <?php do_action('wpsp_get_add_custom_field');?>
    
    <input type="hidden" name="action" value="wpsp_set_add_custom_field" />
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce()?>" />
    
    <hr class="wpsp_admin_popup_footer_saperator">
    <button class="button button-primary" type="submit"><?php _e('Submit','wp-support-plus-responsive-ticket-system')?></button>
    <button class="button" type="button" onclick="wpsp_close_admin_popup();"><?php _e('Cancel','wp-support-plus-responsive-ticket-system')?></button>
    <br><br>
    
</form>

<script>
jQuery(document).ready(function(){
    
    wpspjq('select[name=field_type]').change(function(){
        var field_type = parseInt(wpspjq(this).val());
        if( field_type == 2 || field_type == 3 || field_type == 4 ){
            wpspjq('textarea[name=field_options]').parent().show();
        } else {
            wpspjq('textarea[name=field_options]').val('');
            wpspjq('textarea[name=field_options]').parent().hide();
        }
    });
    
    wpspjq('select[name=agent_only]').change(function(){
        if(wpspjq(this).val().toString()=='0'){
            wpspjq('select[name=required], #wpsp_categories_multi').parent().show();
        } else {
            wpspjq('#wpsp_categories_multi option').each(function(){
                wpspjq(this).prop('selected',false);
            });
            wpspjq('select[name=required]').val('0');
            wpspjq('select[name=required],#wpsp_categories_multi').parent().hide();
        }
    });
    
});

function validate_wpsp_admin_popup_form(obj){
    
    var error_flag = false;
    
    if( !error_flag && wpspjq('input[name=field_label]').val().trim() == '' ){
        wpspjq('#wpsp_popup_form_error p').text("<?php _e('Please enter Field Label!','wp-support-plus-responsive-ticket-system')?>");
        wpspjq('input[name=field_label]').val('');
        wpspjq('input[name=field_label]').focus();
        error_flag = true;
    }    
    
    if( !error_flag && wpspjq('select[name=field_type]').val().trim() == '' ){
        wpspjq('#wpsp_popup_form_error p').text('<?php _e('Please choose Field Type!','wp-support-plus-responsive-ticket-system')?>');
        wpspjq('select[name=field_type]').focus();
        error_flag = true;
    }
    
    var field_type = parseInt(wpspjq('select[name=field_type]').val());
    if( !error_flag && ( field_type == 2 || field_type == 3 || field_type == 4 ) && wpspjq('textarea[name=field_options]').val().trim() == '' ){
        wpspjq('#wpsp_popup_form_error p').text('<?php _e('Please insert Field Options!','wp-support-plus-responsive-ticket-system')?>');
        wpspjq('textarea[name=field_options]').focus();
        error_flag = true;
    }
    
    <?php do_action('validate_wpsp_get_add_custom_field');?>
    
    if(!error_flag){
        wpsp_admin_submit_popup(obj);
    } else {
        wpspjq('#wpsp_popup_form_error').show();
    }
    
    return false;
}
</script>