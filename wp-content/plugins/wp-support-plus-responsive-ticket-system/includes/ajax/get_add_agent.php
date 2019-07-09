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

<form id="wpsp_agent_frm" action="" onsubmit="return validate_wpsp_admin_popup_form(this);">
    
    <div id="wpsp_popup_form_error" class="error notice" style="display: none;">
        <p></p>
    </div>
    
    <div class="wpsp_popup_form_element">
        <strong><?php _e('Agent','wp-support-plus-responsive-ticket-system')?>:<br></strong>
        <div class="wpsp-autocomplete-drop-down" id="wpsp-agent" >
            <span id="wpsp-agent-label"><?php _e('Select Agent','wp-support-plus-responsive-ticket-system')?></span>
            <input id="wpsp-agent-id" type="hidden" name="wpsp_agent[agent_id]" />
            <span style="float: right;" class="dashicons dashicons-arrow-down-alt2"></span>
        </div>
        <div class="wpsp-autocomplete-drop-down-panel">
            <input style="margin: 0;width: 100%;padding: 8px;" onkeyup="wpsp_search_keypress(event,this);" type="text" id="wpsp-autocomplete-search" placeholder="<?php _e('Search Agents...','wp-support-plus-responsive-ticket-system')?>" autocomplete="off" />
            <div id="wpsp-autocomplete-search-results"></div>
        </div>
    </div>
    
    <div class="wpsp_popup_form_element">
        <strong><?php _e('Role','wp-support-plus-responsive-ticket-system')?>:<br></strong>
        <select name="wpsp_agent[role]" id="wpsp_search_role_id">
            <option value="1"><?php _e('Agent','wp-support-plus-responsive-ticket-system')?></option>
            <option value="2"><?php _e('Supervisor','wp-support-plus-responsive-ticket-system')?></option>
            <option value="3"><?php _e('Administrator','wp-support-plus-responsive-ticket-system')?></option>
        </select>
    </div><br>
		<div id="wpsp_show_all_categories_div" style="display:none;">
				<strong><?php _e('Assign Category For Supervisor','wp-support-plus-responsive-ticket-system')?>:<br></strong>
				<?php
				global $wpdb; 
				$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_catagories ORDER BY load_order ");
				?>
				<table>
						<?php 
						foreach ($results as $result) { ?>
						<tr>
								<td>
									<input type="checkbox" name="wpsp_agent[selected_category_id][]" value="<?php echo htmlentities($result->id) ;?>" />
								</td>
								<td>
									<?php 
											$category_name = $result->name;
											echo htmlentities($category_name) ;
									 }
									 ?>
								</td>
						</tr>
				</table>
		</div>
    
    <?php do_action('wpsp_get_add_category');?>
    
    <input type="hidden" name="action" value="wpsp_set_add_agent" />
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce()?>" />
    
    <hr class="wpsp_admin_popup_footer_saperator">
    <button class="button button-primary" type="submit"><?php _e('Submit','wp-support-plus-responsive-ticket-system')?></button>
    <button class="button" type="button" onclick="wpsp_close_admin_popup();"><?php _e('Cancel','wp-support-plus-responsive-ticket-system')?></button>
    
</form>

<script>
    
    jQuery(document).ready(function(){
        wpspjq('.wpsp-autocomplete-drop-down-panel').hide();
        wpspjq('#wpsp_agent_frm').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) { 
              e.preventDefault();
              return false;
            }
        });
        wpspjq('#wpsp-agent').click(function(){
            wpspjq('#wpsp-agent-id').val('');
            wpspjq('#wpsp-autocomplete-search').val('');
            wpspjq('.wpsp-autocomplete-drop-down-panel').show();
            wpspjq('#wpsp-autocomplete-search').focus();
            wpsp_search_users_for_add_agent();
        });
    });
    
    function validate_wpsp_admin_popup_form(obj){
        
        var error_flag = false;
        
        if( !error_flag && wpspjq('#wpsp-agent-id').val().trim() == '' ){
            wpspjq('#wpsp_popup_form_error p').text('<?php _e('Please select an Agent!', 'wp-support-plus-responsive-ticket-system') ?>');
            error_flag = true;
        }
        
        if(!error_flag){
            wpsp_admin_submit_popup(obj);
        } else {
            wpspjq('#wpsp_popup_form_error').show();
        }
        
        return false;
    }
    
    function wpsp_aut_item_select(obj){
        
        wpspjq('.wpsp-autocomplete-result-item').removeClass('wpsp-autocomplete-result-item-selected');
        wpspjq(obj).addClass('wpsp-autocomplete-result-item-selected');
    }
    
    function wpsp_aut_choose_item(obj){
        
        var agent_id    = parseInt(wpspjq(obj).attr('id'));
        var agent_name  = wpspjq(obj).text();
        wpspjq('#wpsp-agent-id').val(agent_id);
        wpspjq('#wpsp-agent-label').text(agent_name);
        wpspjq('.wpsp-autocomplete-drop-down-panel').hide();
    }
    
    function wpsp_search_keypress(evt,t){
        
        if( evt.keyCode == 40 ){
            if(wpspjq('.wpsp-autocomplete-result-item-selected').next().is('.wpsp-autocomplete-result-item')){
                wpsp_aut_item_select(wpspjq('.wpsp-autocomplete-result-item-selected').next());
            }
            return;
        }
        
        if( evt.keyCode == 38 ){
            if(wpspjq('.wpsp-autocomplete-result-item-selected').prev().is('.wpsp-autocomplete-result-item')){
                wpsp_aut_item_select(wpspjq('.wpsp-autocomplete-result-item-selected').prev());
            }
            return;
        }
        
        if( evt.keyCode == 13 ){
            wpsp_aut_choose_item( wpspjq('.wpsp-autocomplete-result-item-selected') );
            return;
        }
        
        wpsp_search_users_for_add_agent(t.value);
        
    }
    
    function wpsp_search_users_for_add_agent( s = '' ){
        
        var data = {
            'action': 'wpsp_search_users_for_add_agent',
            's': s,
            'nonce' : wpspjq('input[name="nonce"]').val()
        };

        wpspjq.post(wpsp_admin.ajax_url, data, function(response) {
            wpspjq('#wpsp-autocomplete-search-results').html(response);
        });
    }
		
		jQuery('#wpsp_search_role_id').on('change', function() {
				if(this.value == 2){
						wpspjq('#wpsp_show_all_categories_div').show();
		    }else{
						wpspjq('#wpsp_show_all_categories_div').hide();
				}
    });
</script>
<style>
    .wpsp-autocomplete-drop-down{
        background: #f5f5f5 linear-gradient(to top, #f5f5f5, #fafafa) repeat scroll 0 0;
        border-color: #dfdfdf;
        border-style: solid;
        border-width: 1px;
        clear: both;
        margin: 5px 0 0 0;
        padding: 10px;
        cursor: pointer;
    }
    .wpsp-autocomplete-drop-down-panel{
        border-color: #dfdfdf;
        border-style: solid;
        border-width: 1px;
        clear: both;
        padding: 10px;
    }
    #wpsp-autocomplete-search-results{
        border-color: #dfdfdf;
        border-style: solid;
        border-width: 1px;
    }
    .wpsp-autocomplete-result-item{
        padding: 5px;
        margin: 0;
        cursor: pointer;
    }
    .wpsp-autocomplete-result-item-selected{
        background-color: #0073aa;
        color: #fff;
    }
</style>