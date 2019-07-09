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

?>

<form id="wpsp_frm_add_category" action="" onsubmit="return validate_wpsp_admin_popup_form(this);">
    
    <div id="wpsp_popup_form_error" class="error notice" style="display: none;">
        <p></p>
    </div>
    
    <div class="wpsp_popup_form_element">
        <strong><?php _e('Category Name','wp-support-plus-responsive-ticket-system')?>:</strong><br>
        <input style="width: 298px;" type="text" name="cat_name" value=""/>
    </div>
    
    <div id="wpsp_auto_supervisor_container" class="wpsp_popup_form_element wpsp_autocomplete_container">
        <strong><?php _e('Supervisor (Optional)','wp-support-plus-responsive-ticket-system')?>:</strong><br>
        <ul class="wpsp_autocomplete_search_container">
            <input type="text" autocomplete="off" id="wpsp_auto_supervisor" class="wpsp_autocomplete" placeholder="<?php _e('Search Supervisors...','wp-support-plus-responsive-ticket-system');?>"/>
        </ul>
        <div class="wpsp_autocomplete_choosen_drop">
            <ul class="wpsp_autocomplete_choosen_results"></ul>
        </div>
        <div class="wpsp_autocomplete_choosen_choices"></div>
    </div>
    
    <?php do_action('wpsp_get_add_category');?>
    
    <input type="hidden" name="action" value="wpsp_set_add_category" />
    <input type="hidden" id="wpsp_nonce" name="nonce" value="<?php echo wp_create_nonce()?>" />
    
    <div style="width: 100%; float: left; margin-top: 30px;">
        <button class="button button-primary" type="submit"><?php _e('Submit','wp-support-plus-responsive-ticket-system')?></button>
        <button class="button" type="button" onclick="wpsp_close_admin_popup();"><?php _e('Cancel','wp-support-plus-responsive-ticket-system')?></button>
    </div>
    
</form>

<script>
    jQuery(document).ready(function(){
        
        wpspjq('#wpsp_frm_add_category').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) { 
              e.preventDefault();
              return false;
            }
        });
        
        wpspjq('.wpsp_autocomplete').focus(function(){
            var input_id = wpspjq(this).attr('id');
            wpspjq('#'+input_id+'_container').find('.wpsp_autocomplete_choosen_results').html(wpsp_admin.filter_loading_html);
            wpspjq('#'+input_id+'_container').find('.wpsp_autocomplete_choosen_drop').show();
            wpsp_get_autocomplete_results(input_id);
        });
        
        wpspjq('body').click(function(event){
            if( !(event.target.nodeName === 'LI' || wpspjq(event.target).hasClass('wpsp_autocomplete')) ){
                wpspjq(document).find('.wpsp_autocomplete_choosen_drop').hide();
                wpspjq('.wpsp_autocomplete').val('');
            }
        });
        
        wpspjq('.wpsp_autocomplete').keyup(function(evt){
            var input_id = wpspjq(this).attr('id');
            if( evt.keyCode == 40 ){
                if(wpspjq('#'+input_id+'_container').find('.heightligted').next().is('.active-result')){
                    var next_result = wpspjq('#'+input_id+'_container').find('.heightligted').next();
                    wpspjq('#'+input_id+'_container').find('.active-result').removeClass('heightligted');
                    wpspjq(next_result).addClass('heightligted');
                }
                return;
            }

            if( evt.keyCode == 38 ){
                if(wpspjq('#'+input_id+'_container').find('.heightligted').prev().is('.active-result')){
                    var prev_result = wpspjq('#'+input_id+'_container').find('.heightligted').prev();
                    wpspjq('#'+input_id+'_container').find('.active-result').removeClass('heightligted');
                    wpspjq(prev_result).addClass('heightligted');
                }
                return;
            }

            if( evt.keyCode == 13 ){
                wpspjq('#'+input_id+'_container').find('.heightligted').trigger('click');
                return;
            }
            
            wpspjq('#'+input_id+'_container').find('.wpsp_autocomplete_choosen_drop').show();
            var keywords = wpspjq(this).val().trim();
            wpsp_get_autocomplete_results( input_id, keywords );
            
        });
        
    });
    
    function validate_wpsp_admin_popup_form(obj){

        var error_flag = false;

        if( !error_flag && wpspjq('input[name=cat_name]').val().trim() == '' ){
            wpspjq('#wpsp_popup_form_error p').text("<?php _e('Please enter category name!','wp-support-plus-responsive-ticket-system')?>");
            wpspjq('input[name=cat_name]').val('');
            wpspjq('input[name=cat_name]').focus();
            error_flag = true;
        }

        <?php do_action('validate_wpsp_get_add_category');?>

        if(!error_flag){
            wpsp_admin_submit_popup(obj);
        } else {
            wpspjq('#wpsp_popup_form_error').show();
        }

        return false;
    }
    
</script>