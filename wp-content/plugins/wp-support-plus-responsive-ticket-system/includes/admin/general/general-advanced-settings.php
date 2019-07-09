<?php
if ( ! defined( 'ABSPATH' ) )
{
	exit; // Exit if accessed directly
}
global $wpsupportplus,$wpdb;
$general_advanced_settings = get_option('wpsp_general_settings_advanced');
$agent_reply_status        = $wpsupportplus->functions->get_agent_reply_status();
$status_selected           = isset($general_advanced_settings['status'])? $general_advanced_settings['status']:array();
$auto_close_days           = isset($general_advanced_settings['selected_status_ticket_close'])? $general_advanced_settings['selected_status_ticket_close']:'';

?>

<div id="tab_container">

<form method="post" action="">
	
	<input type="hidden" name="action" value="update"/>
	<input type="hidden" name="update_setting" value="general_settings_advanced"/>
  <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>
 			<table class="form-table">
      
					<tr>
						<th scope="row"><?php _e('Sign In Button','wp-support-plus-responsive-ticket-system'); ?></th>
						<td>
							<select name="general_advanced_settings[signin]">
						 
						 		<option <?php echo $wpsupportplus->functions->toggle_button_signin() ? 'selected="selected"' : '' ?> value="1"><?php _e('Enable','wp-support-plus-responsive-ticket-system')?></option>
						 		<option <?php echo $wpsupportplus->functions->toggle_button_signin()? '' : 'selected="selected"' ?> value="0"><?php _e('Disable','wp-support-plus-responsive-ticket-system')?></option>
	
				  		</select><br>
							<small><i><?php _e('Applicable on Stand-Alone interface only.','wp-support-plus-responsive-ticket-system');?></i></small>
			   		</td>
					</tr>

					<tr>
						<th scope="row"><?php _e('Sign Out Button','wp-support-plus-responsive-ticket-system'); ?></th>
						<td>
							<select name="general_advanced_settings[signout]">
						 
						 		<option <?php echo $wpsupportplus->functions->toggle_button_signout() ? 'selected="selected"' : '' ?> value="1"><?php _e('Enable','wp-support-plus-responsive-ticket-system')?></option>
						 		<option <?php echo $wpsupportplus->functions->toggle_button_signout()? '' : 'selected="selected"' ?> value="0"><?php _e('Disable','wp-support-plus-responsive-ticket-system')?></option>
					
							</select><br>
							<small><i><?php _e('Applicable on Stand-Alone interface only.','wp-support-plus-responsive-ticket-system');?></i></small>
						</td>
					</tr>

				<tr>
					<th scope="row"><?php _e('Sign Up Button','wp-support-plus-responsive-ticket-system'); ?></th>
					<td>
						<select name="general_advanced_settings[signup]">
					 
					 		<option <?php echo $wpsupportplus->functions->toggle_button_signup() ? 'selected="selected"' : '' ?> value="1"><?php _e('Enable','wp-support-plus-responsive-ticket-system')?></option>
					 		<option <?php echo $wpsupportplus->functions->toggle_button_signup()? '' : 'selected="selected"' ?> value="0"><?php _e('Disable','wp-support-plus-responsive-ticket-system')?></option>

			  		</select><br>
						<small><i><?php _e('Applicable on Stand-Alone interface only.','wp-support-plus-responsive-ticket-system');?></i></small>
		   		</td>
				</tr>
				
				<tr>
					<th scope="row"><?php _e('Auto Close Ticket','wp-support-plus-responsive-ticket-system'); ?></th>
						<td>
							<?php _e('All selected status tickets will automatically get closed after','wp-support-plus-responsive-ticket-system'); ?>
							<input type ="text" id="wpsp_selected_status_ticket_close" value="<?php echo htmlentities($auto_close_days);?>" name="general_advanced_settings[selected_status_ticket_close]" size=4  /><?php _e('days.','wp-support-plus-responsive-ticket-system'); ?>
							<?php _e('Please leave blank to disable this feature.  ','wp-support-plus-responsive-ticket-system'); ?>
							<br />
							<?php
							 	global $wpdb;
								$status = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_status");
							 	foreach($status as $st){
									if(in_array($st->id,$status_selected)){
										$checked='checked="checked"';
									}else{
										$checked = "";
									}
									?>
									<input type ="checkbox" <?php echo $checked ?> name="general_advanced_settings[status][]" value="<?php echo htmlentities($st->id); ?>"/><?php echo htmlentities($st->name) ?><br />
									<?php
								}
							 ?>
						</td>
					</th>
				</tr>
				
				<tr>
					<th scope="row"><?php _e('Reply to Closed Tickets','wp-support-plus-responsive-ticket-system'); ?></th>
					<td>
					<select name="general_advanced_settings[reply_closed_tickets]">
						 
						 <option <?php echo $wpsupportplus->functions-> toggle_button_reply_closed_tickets() ? 'selected="selected"' : '' ?>value="1"><?php _e('Allowed','wp-support-plus-responsive-ticket-system')?></option>
						 <option <?php echo $wpsupportplus->functions-> toggle_button_reply_closed_tickets() ? '' : 'selected="selected"' ?>value="0"><?php _e('Not Allowed','wp-support-plus-responsive-ticket-system')?></option>

				  </select><br>
					<small><i><?php _e('Applicable for Subscriber role only.','wp-support-plus-responsive-ticket-system');?></i></small>
			   	</td>
				</tr>
				
				<tr>
					<th scope="row"><?php _e('Allow Captcha','wp-support-plus-responsive-ticket-system'); ?></th>
					<td>
						<?php
						$cap=$wpsupportplus->functions-> toggle_button_disable_captcha();
						?>
					<select name="general_advanced_settings[captcha_status]">
						 
						 <option <?php echo $cap=='all'? 'selected="selected"' : '' ?>value="all"><?php _e('All Users','wp-support-plus-responsive-ticket-system')?></option>
						 <option <?php echo $cap=='guest' ? 'selected="selected"' : '' ?>value="guest"><?php _e('Guest Users','wp-support-plus-responsive-ticket-system')?></option>
						 <option <?php echo $cap=='disable' ? 'selected="selected"':'' ?>value="disable"><?php _e('Disable','wp-support-plus-responsive-ticket-system')?></option>

				  </select><br>
					<small><i><?php _e('Enable/disable captcha on create ticket page.','wp-support-plus-responsive-ticket-system');?></i></small>
			   	</td>
				</tr>
     </table>

      <?php do_action('wpsp_after_general_settings')?>
					<p class="submit">
						<input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">											
	        </p>

</form>
</div>
