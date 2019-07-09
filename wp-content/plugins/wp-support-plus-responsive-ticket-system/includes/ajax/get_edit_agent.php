<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$agent_id = isset($_POST['load_id']) ? sanitize_text_field($_POST['load_id']) : 0;
$nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $agent_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$role = $wpdb->get_var("select role from {$wpdb->prefix}wpsp_users where user_id=".$agent_id);

?>

<form action="" onsubmit="return validate_wpsp_admin_popup_form(this);">
    
    <div id="wpsp_popup_form_error" class="error notice" style="display: none;">
        <p></p>
    </div>
    
    <div class="wpsp_popup_form_element">
        <strong><?php _e('Role','wp-support-plus-responsive-ticket-system');?></strong>:<br>
        <select name="role">
            <option <?php echo $role && $role==1 ? 'selected="selected"' : ''?> value="1"><?php _e('Agent','wp-support-plus-responsive-ticket-system')?></option>
            <option <?php echo $role && $role==2 ? 'selected="selected"' : ''?> value="2"><?php _e('Supervisor','wp-support-plus-responsive-ticket-system')?></option>
            <option <?php echo $role && $role==3 ? 'selected="selected"' : ''?> value="3"><?php _e('Administrator','wp-support-plus-responsive-ticket-system')?></option>
        </select>
    </div><br>
		
		<div id="wpsp_edit_agent_categories_div" style="display:none;">
				<strong><?php _e('Assigned Categories','wp-support-plus-responsive-ticket-system')?>:<br></strong>
				<?php
				global $wpdb; 
				$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_catagories ORDER BY load_order ");
				?>
				<table>
						<?php 
						foreach ($results as $result) { 
							
								$supervisor= $result->supervisor;
								$supervisor_id_array=unserialize($supervisor);
								if (in_array($agent_id, $supervisor_id_array)){
										$categories = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_catagories where supervisor=" ."'$supervisor'");
										foreach ($categories as $category ) {
												if($result->id == $category->id){
														$checked = 'checked ="checked"';
												}
										}
					      }else{
								 		$checked ='';
							  }
							?>
						<tr>
								<td>
									<input <?php echo $checked?> type="checkbox" name="wpsp_edit_agent_supervisor_categories[]" value="<?php echo htmlentities($result->id);?>" />
								</td>
								<td>
									<?php 
											$category_name = $result->name;
											echo $category_name ;
									 }
									 ?>
								</td>
						</tr>
				</table>
		</div>
    
    <input type="hidden" name="action" value="wpsp_set_edit_agent" />
    <input type="hidden" name="load_id" value="<?php echo htmlentities($agent_id)?>" />
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce($agent_id)?>" />
    
    <hr class="wpsp_admin_popup_footer_saperator">
    <button class="button button-primary" type="submit"><?php _e('Submit','wp-support-plus-responsive-ticket-system')?></button>
    <button class="button" type="button" onclick="wpsp_close_admin_popup();"><?php _e('Cancel','wp-support-plus-responsive-ticket-system')?></button>
    
</form>

<script>
jQuery(document).ready(function(){
    wpspjq('.wpsp_color_picker').wpColorPicker();
		
		wpspjq('select[name="role"]').on('change', function(){    
				if(this.value == 2){
						wpspjq('#wpsp_edit_agent_categories_div').show();
				}else{
						wpspjq('#wpsp_edit_agent_categories_div').hide();
				}    
    });
		
		<?php if($role == 2){?>
							wpspjq('#wpsp_edit_agent_categories_div').show();
		<?php }else{ ?>
							wpspjq('#wpsp_edit_agent_categories_div').hide();
		<?php } ?>
});
    
function validate_wpsp_admin_popup_form(obj){
    
    var error_flag = false;
    
    if(!error_flag){
        wpsp_admin_submit_popup(obj);
    } else {
        wpspjq('#wpsp_popup_form_error').show();
    }
    
    return false;
}
</script>