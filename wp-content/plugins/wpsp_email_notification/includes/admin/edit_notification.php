<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$wpsp_en_notification = get_option('wpsp_en_notification');
$en_id                = isset( $_REQUEST['en_id'] ) ? intval($_REQUEST['en_id']) : 'NA';

// exit if invalid id
if( !is_numeric($en_id) ) {
  wp_redirect( 'admin.php?page=wp-support-plus&setting=addons&section=email_notification' );
  exit();
}

global $wpsupportplus;

$admin        = new WPSP_EN_Backend();
$email_types  = $admin->get_email_types();
$notification = $wpsp_en_notification[$en_id];

?>
<div id="tab_container">

    <form id="wpsp_frm_en_edit" method="post" action="admin.php?page=wp-support-plus&setting=addons&section=email_notification&wpsp_en_action=update_notification">

			<input type="hidden" name="en_id" value="<?php echo $en_id?>">

			<table class="form-table">

          <tr>
            <th scope="row"><h3><?php _e( 'Edit Notification', 'wpsp-en' );?></h3></th>
            <td></td>
          </tr>

          <tr>
              <th scope="row"><?php _e( 'Title', 'wpsp-en' );?></th>
              <td>
                <input type="text" class="wpsp_en_full_width" name="wpsp_en_notification[title]" value="<?php echo isset($notification['title']) ? $notification['title'] : ''?>" />
              </td>
          </tr>

          <tr>
              <th scope="row"><?php _e( 'Type', 'wpsp-en' );?></th>
              <td>
								<select name="wpsp_en_notification[type]">

									<?php foreach ($email_types as $key => $value):

										$selected = isset($notification['type']) && $notification['type'] == $key ? 'selected="selected"' : ''; ?>
										<option <?php echo $selected;?> value="<?php echo $key?>"><?php echo $value?></option>

									<?php endforeach; ?>

								</select>
              </td>
          </tr>

					<tr>
						<th scope="row"><?php _e( 'Subject', 'wpsp-en' );?></th>
						<td>
							<input type="text" class="wpsp_en_full_width" name="wpsp_en_notification[subject]" value="<?php echo isset($notification['subject']) ? $notification['subject'] : ''?>" />
						</td>
					</tr>

					<tr>
						<th scope="row"><?php _e( 'Description', 'wpsp-en' );?></th>
						<td>
							<?php
			            $editor_id = 'wpsp_en_desc';
			            $settings  = array('textarea_name' => 'wpsp_en_notification[description]','editor_height' =>'200','media_buttons' => true);
			            $content   =  isset($notification['description']) ? stripslashes($notification['description']) : '';
			            wp_editor( $content, $editor_id, $settings );
			        ?>
							<p class="wpsp_list_template_tags">

									<b><?php _e('Template Tags', 'wpsp-en')?></b><br>
			            {ticket_id} - <?php _e('Ticket ID','wp-support-plus-responsive-ticket-system');?><br>
			            {ticket_subject} - <?php _e('Ticket Subject','wp-support-plus-responsive-ticket-system');?><br>
			            {ticket_description} - <?php _e('Ticket description given while creating ticket. If used, description attachments(if any) will get attached to mail.','wp-support-plus-responsive-ticket-system');?><br>
			            {ticket_status} - <?php _e('Ticket status','wp-support-plus-responsive-ticket-system');?><br>
			            {ticket_category} - <?php _e('Ticket Category','wp-support-plus-responsive-ticket-system');?><br>
			            {ticket_priority} - <?php _e('Ticket Priority','wp-support-plus-responsive-ticket-system');?><br>
			            {customer_name} - <?php _e('Name of ticket creator','wp-support-plus-responsive-ticket-system');?><br>
			            {customer_email} - <?php _e('Email of ticket creator','wp-support-plus-responsive-ticket-system');?><br>
			            {time_created} - <?php _e('Ticket create time','wp-support-plus-responsive-ticket-system');?><br>
			            {agent_created} - <?php _e('Name of the agent who have created ticket on behalf of user. Empty if ticket created by user himself.','wp-support-plus-responsive-ticket-system');?><br>
			            {assigned_agent} - <?php _e('Name(s) of agent assigned to ticket.','wp-support-plus-responsive-ticket-system');?><br>
									{reply_user_name} - <?php _e('Name of user posted last reply.','wp-support-plus-responsive-ticket-system');?><br>
									{reply_user_email} - <?php _e('Email of user posted last reply.','wp-support-plus-responsive-ticket-system');?><br>
									{reply_description} - <?php _e('Last reply description.  If used, reply description attachments(if any) will get attached to mail.','wp-support-plus-responsive-ticket-system');?><br>
									{ticket_history} - <?php _e('History of communication in ticket.','wp-support-plus-responsive-ticket-system');?><br>
									{updated_by} - <?php _e('Name of user doing current activity.','wp-support-plus-responsive-ticket-system');?><br>
									{ticket_url} - <?php _e('URL of the ticket.','wp-support-plus-responsive-ticket-system');?><br>

			            <?php do_action( 'print_custom_fields_template_tags', 'create_ticket' );?>
			            <?php $wpsupportplus->functions->print_custom_fields_template_tags();?>
			        </p>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php _e( 'Recipients', 'wpsp-en' );?></th>
						<td>
							<?php
							$recipients =  isset($notification['recipients']) && is_array($notification['recipients']) ? $notification['recipients'] : array();
							?>
							<table id="wpsp_en_tbl_recepients" class="wp-list-table widefat fixed posts" style="width:50%;">
								<thead>
									<tr>
										<th class="col_1" scope="col"><?php _e( 'Enable', 'wpsp-en' );?></th>
										<th class="col_2" scope="col"><?php _e( 'Role', 'wpsp-en' );?></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="col_1">
											<?php $checked = in_array('customer', $recipients) ? 'checked="checked"' : '';?>
											<input type="checkbox" <?php echo $checked;?> name="wpsp_en_notification[recipients][]" value="customer">
										</td>
										<td class="col_2"><?php _e('Customer', 'wpsp-en');?></td>
									</tr>
									<tr>
										<td class="col_1">
											<?php $checked = in_array('agent', $recipients) ? 'checked="checked"' : '';?>
											<input type="checkbox" <?php echo $checked;?> name="wpsp_en_notification[recipients][]" value="agent">
										</td>
										<td class="col_2"><?php _e('Assigned Agent', 'wpsp-en');?></td>
									</tr>
									<tr>
										<td class="col_1">
											<?php $checked = in_array('supervisor', $recipients) ? 'checked="checked"' : '';?>
											<input type="checkbox" <?php echo $checked;?> name="wpsp_en_notification[recipients][]" value="supervisor">
										</td>
										<td class="col_2"><?php _e('Supervisor', 'wpsp-en');?></td>
									</tr>
									<tr>
										<td class="col_1">
											<?php $checked = in_array('administrator', $recipients) ? 'checked="checked"' : '';?>
											<input type="checkbox" <?php echo $checked;?> name="wpsp_en_notification[recipients][]" value="administrator">
										</td>
										<td class="col_2"><?php _e('Administrator', 'wpsp-en');?></td>
									</tr>
								<tr>
									<td class="col_1">
										<?php $checked = in_array('all_agents', $recipients) ? 'checked="checked"' : '';?>
										<input type="checkbox" <?php echo $checked;?> name="wpsp_en_notification[recipients][]" value="all_agents">
									</td>
									<td class="col_2"><?php _e('All Agents', 'wpsp-en');?></td>
									</tr>
									<?php do_action('wpsp_en_after_edit_recipients',$recipients);?>

								</tbody>
							</table>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php _e( 'Conditions', 'wpsp-en' );?></th>
						<td>
							<button type="button" onclick="wpsp_en_show_add_condition();" class="button button-secondery" name=""><?php echo _e('Add New', 'wpsp-en');?></button>
							<div id="wpsp_en_condition_add_container">
								<select id="wpsp_en_conditional_field" onchange="wpsp_en_conditional_field_set(this);">
									<option value=""><?php _e('Select Field', 'wpsp-en');?></option>
									<?php $conditional_fields = $wpsupportplus->functions->get_conditional_fields();?>
									<?php foreach ($conditional_fields as $key => $value): ?>
										<option data-type="<?php echo $value['type']?>" value="<?php echo $key?>"><?php echo $value['label']?></option>
									<?php endforeach; ?>
								</select>
								<select id="wpsp_en_conditional_field_option" class="wpsp_en_conditional_values" onchange="wpsp_en_conditional_option_has_option(this);">
									<option value=""><?php _e('Select Option', 'wpsp-en');?></option>
								</select>
								<input id="wpsp_en_conditional_field_has_word" onkeyup="wpsp_en_conditional_has_word_has_char(this);" class="wpsp_en_conditional_values" type="text" placeholder="<?php _e('Has Word', 'wpsp-en');?>">
								<button type="button" id="wpsp_en_btn_add_condition" class="button button-primary wpsp_en_conditional_values" onclick="wpsp_en_set_add_condition();"><?php _e('Add', 'wpsp-en');?></button>
							</div>
							<div id="wpsp_en_condition_container">
								<?php $conditions =  isset($notification['condition']) && is_array($notification['condition']) ? $notification['condition'] : array();?>
								<?php foreach ($conditions as $key => $fields): ?>
									<?php foreach ($fields as $value): ?>
										<div class="wpsp_autocomplete_choice_item">
											<?php echo $conditional_fields[$key]['label']?> = "<?php echo $admin->get_option_value_label($key,$value,$conditional_fields)?>" <span onclick="wpsp_autocomplete_choice_item_delete(this)" class="dashicons dashicons-no-alt wpsp_autocomplete_choice_item_delete"></span>
											<input name="wpsp_en_notification[condition][<?php echo $key?>][]" value="<?php echo $value?>" type="hidden">
										</div>
									<?php endforeach; ?>
								<?php endforeach; ?>
							</div>
						</td>
					</tr>

					<tr>
              <th scope="row"><?php _e( 'Same condition field match relation', 'wpsp-en' );?></th>
              <td>
                <select name="wpsp_en_notification[same_condition_relation]">
									<option <?php echo isset($notification['same_condition_relation']) && $notification['same_condition_relation'] == 'OR' ? 'selected="selected"' : ''?> value="OR"><?php _e( 'Any one of them', 'wpsp-en' );?></option>
									<option <?php echo isset($notification['same_condition_relation']) && $notification['same_condition_relation'] == 'AND' ? 'selected="selected"' : ''?> value="AND"><?php _e( 'All of them', 'wpsp-en' );?></option>
								</select>
              </td>
          </tr>

					<tr>
              <th scope="row"><?php _e( 'Different condition field match relation', 'wpsp-en' );?></th>
              <td>
                <select name="wpsp_en_notification[diff_condition_relation]">
									<option <?php echo isset($notification['diff_condition_relation']) && $notification['diff_condition_relation'] == 'AND' ? 'selected="selected"' : ''?> value="AND"><?php _e( 'All of them', 'wpsp-en' );?></option>
									<option <?php echo isset($notification['diff_condition_relation']) && $notification['diff_condition_relation'] == 'OR' ? 'selected="selected"' : ''?> value="OR"><?php _e( 'Any one of them', 'wpsp-en' );?></option>
								</select>
              </td>
          </tr>

					<tr>
						<td colspan="2">
							<input id="submit" style="margin-left:-10px;" class="button button-primary" name="submit" value="<?php _e( 'Save Changes', 'wpsp-en' );?>" type="submit">
						</td>
					</tr>

      </table>

    </form>

</div>
