<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;
$logo_path = $wpsupportplus->functions->get_upload_logo();
?>

<div id="tab_container">

    <form method="post" action="">

        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="update_setting" value="settings_general"/>
        <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>

        <table class="form-table">

            <tr>
                <th scope="row"><h3><?php _e('General Settings', 'wp-support-plus-responsive-ticket-system'); ?></h3></th>
                <td></td>
            </tr>

						<tr>
                <th scope="row"><?php _e('Choose Logo', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                     	<img id="wpsp_company_logo_img" src="<?php echo $logo_path?>" style="width: 100px;" /><br>
					            <button class="wpsp_btn" type="button" onclick="wpsp_upload_company_logo_dashboard();"><?php _e('Upload Logo', 'wp-support-plus-responsive-ticket-system'); ?></button><br>
					            <input id="wpsp_company_logo_url"  type="hidden" name="general_settings[company_logo]" value="<?php echo htmlentities($logo_path)?>" />
											<small><i><?php _e('Applicable on Stand-Alone interface only.','wp-support-plus-responsive-ticket-system');?></i></small>
								</td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Support Page', 'wp-support-plus-responsive-ticket-system'); ?></th>
								 <td>

                    <select name="general_settings[support_page]">

                        <option value=""></option>

                        <?php
                        foreach ( $wpsupportplus->functions->get_wp_page_list() as $page ) :

                            $selected = $wpsupportplus->functions->get_support_page_id() == $page->ID ? 'selected="selected"' : '';
                            echo '<option '.$selected.' value="'.$page->ID.'">'.$page->post_title.'</option>';

                        endforeach;
                        ?>

                    </select>
									 <i><small><a href="<?php echo $wpsupportplus->functions->get_support_page_url();?>" target="_blank"><?php _e('Click here to go to support page','wp-support-plus-responsive-ticket-system')?></a></small></i>
										<br>
										
                    <small><i><?php _e('This page will be used as helpdesk page on front-end. All users ( customer, agent, supervisor, administrator ) will able to access helpdesk on same page depending on their role. It is require to have shortcode [wp_support_plus].','wp-support-plus-responsive-ticket-system');?></i></small>
								  </td>
								
            </tr>
				
						<?php
						$theme_integration = $wpsupportplus->functions->get_role_theme_integration();
						?>
						<tr>
                <th scope="row"><?php _e('Theme Integration', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
									<table class="wp-list-table widefat fixed posts wpsp_settings_tbl wpsp_settings_tbl_half">
										<thead>
											<tr>
												<th><?php _e('Role', 'wp-support-plus-responsive-ticket-system');?></th>
												<th><?php _e('Action', 'wp-support-plus-responsive-ticket-system');?></th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td><?php _e('Customer', 'wp-support-plus-responsive-ticket-system');?></td>
												<td>
													<select name="general_settings[theme_integration][customer]">
														<option value="0" <?php echo !$theme_integration['customer'] ? 'selected="selected"' : ''?>><?php _e('No', 'wp-support-plus-responsive-ticket-system');?></option>
														<option value="1" <?php echo $theme_integration['customer'] ? 'selected="selected"' : ''?>><?php _e('Yes', 'wp-support-plus-responsive-ticket-system');?></option>
													</select>
												</td>
											</tr>
											<tr>
												<td><?php _e('Agent', 'wp-support-plus-responsive-ticket-system');?></td>
												<td>
													<select name="general_settings[theme_integration][agent]">
														<option value="0" <?php echo !$theme_integration['agent'] ? 'selected="selected"' : ''?>><?php _e('No', 'wp-support-plus-responsive-ticket-system');?></option>
														<option value="1" <?php echo $theme_integration['agent'] ? 'selected="selected"' : ''?>><?php _e('Yes', 'wp-support-plus-responsive-ticket-system');?></option>
													</select>
												</td>
											</tr>
											<tr>
												<td><?php _e('Supervisor', 'wp-support-plus-responsive-ticket-system');?></td>
												<td>
													<select name="general_settings[theme_integration][supervisor]">
														<option value="0" <?php echo !$theme_integration['supervisor'] ? 'selected="selected"' : ''?>><?php _e('No', 'wp-support-plus-responsive-ticket-system');?></option>
														<option value="1" <?php echo $theme_integration['supervisor'] ? 'selected="selected"' : ''?>><?php _e('Yes', 'wp-support-plus-responsive-ticket-system');?></option>
													</select>
												</td>
											</tr>
											<tr>
												<td><?php _e('Administrator', 'wp-support-plus-responsive-ticket-system');?></td>
												<td>
													<select name="general_settings[theme_integration][administrator]">
														<option value="0" <?php echo !$theme_integration['administrator'] ? 'selected="selected"' : ''?>><?php _e('No', 'wp-support-plus-responsive-ticket-system');?></option>
														<option value="1" <?php echo $theme_integration['administrator'] ? 'selected="selected"' : ''?>><?php _e('Yes', 'wp-support-plus-responsive-ticket-system');?></option>
													</select>
												</td>
											</tr>
										</tbody>
									</table>
									<small><i><?php _e("Decides to integrate or have Stand-Alone interface for your support page. Select YES only for the group you want to see the theme Integrated Interface.",'wp-support-plus-responsive-ticket-system');?></i></small>
                </td>
            </tr>
						
						<tr>
                <th scope="row"><?php _e('Load Bootstrap', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>

                    <select name="general_settings[load_bootstrap]">
                        <option <?php echo $wpsupportplus->functions->load_bootstrap() ? 'selected="selected"' : ''?> value="1"><?php _e('Yes', 'wp-support-plus-responsive-ticket-system'); ?></option>
                        <option <?php echo $wpsupportplus->functions->load_bootstrap() ? '' : 'selected="selected"'?> value="0"><?php _e('No', 'wp-support-plus-responsive-ticket-system'); ?></option>
                    </select>
										<small><i><?php _e('Applicable on theme integration page only.','wp-support-plus-responsive-ticket-system');?></i></small>

                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Open support page in new window?', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>

                    <select name="general_settings[support_btn_new_tab]">
                        <option <?php echo $wpsupportplus->functions->is_open_support_page_new_tab() ? 'selected="selected"' : ''?> value="1"><?php _e('Yes', 'wp-support-plus-responsive-ticket-system'); ?></option>
                        <option <?php echo $wpsupportplus->functions->is_open_support_page_new_tab() ? '' : 'selected="selected"'?> value="0"><?php _e('No', 'wp-support-plus-responsive-ticket-system'); ?></option>
                    </select>

                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Default Category', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>

                    <select name="general_settings[default_category]">

                        <?php
                        foreach ( $wpsupportplus->functions->get_wpsp_categories() as $category ) :

                            $selected = $wpsupportplus->functions->get_default_category() == $category->id ? 'selected="selected"' : '';
                            echo '<option '.$selected.' value="'.$category->id.'">'.$category->name.'</option>';

                        endforeach;
                        ?>

                    </select><br>

                    <small><i><?php _e('This category will be default category for new ticket. If category is not present in ticket form or ticket created via email piping, this category will be used as new ticket category.','wp-support-plus-responsive-ticket-system');?></i></small>

                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Default Ticket Status', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>

                    <select name="general_settings[default_status]">

                        <?php
                        foreach ( $wpsupportplus->functions->get_wpsp_statuses() as $status ) :

                            $selected = $wpsupportplus->functions->get_default_status() == $status->id ? 'selected="selected"' : '';
                            echo '<option '.$selected.' value="'.$status->id.'">'.$status->name.'</option>';

                        endforeach;
                        ?>

                    </select><br>

                    <small><i><?php _e('This status will be default status for new ticket.','wp-support-plus-responsive-ticket-system');?></i></small>

                </td>
            </tr>
						
						<tr>
                <th scope="row"><?php _e('Close Status', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>

									<select name="general_settings[close_ticket_status]">
											<?php
											foreach ( $wpsupportplus->functions->get_wpsp_statuses() as $status ) :

													$selected = $wpsupportplus->functions->get_close_btn_status() == $status->id ? 'selected="selected"' : '';
													echo '<option '.$selected.' value="'.$status->id.'">'.$status->name.'</option>';

											endforeach;
											?>
									</select><br>

                    <small><i><?php _e('Selected status will be considered as closed status.','wp-support-plus-responsive-ticket-system');?></i></small>

                </td>
            </tr>
						
						<tr>
                <th scope="row"><?php _e('Default Ticket Priority', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>

                    <select name="general_settings[default_priority]">

                        <?php
                        foreach ( $wpsupportplus->functions->get_wpsp_priorities() as $priority ) :

                            $selected = $wpsupportplus->functions->get_default_priority() == $priority->id ? 'selected="selected"' : '';
                            echo '<option '.$selected.' value="'.$priority->id.'">'.$priority->name.'</option>';

                        endforeach;
                        ?>

                    </select><br>

                    <small><i><?php _e('This priority will be default priority for new ticket.','wp-support-plus-responsive-ticket-system');?></i></small>

                </td>
            </tr>
						
						<tr>
                <th scope="row"><?php _e('Ticket Status after Agent Reply','wp-support-plus-responsive-ticket-system');?></th>
                <td>

                    <select name="general_settings[agent_reply_status]">
                        <option value=""><?php _e('Current Status','wp-support-plus-responsive-ticket-system')?></option>

                        <optgroup label="<?php _e('Status List','wp-support-plus-responsive-ticket-system')?>"></optgroup>
                            <?php
                            foreach ( $wpsupportplus->functions->get_wpsp_statuses() as $status ) :

                                $selected = $wpsupportplus->functions->get_agent_reply_status() == $status->id ? 'selected="selected"' : '';
                                echo '<option '.$selected.' value="'.$status->id.'">'.$status->name.'</option>';
                            endforeach;
                            ?>
                    </select><br>

                     <small><i><?php _e('As name suggest, this setting will change ticket status to selected when support agent reply to ticket. Current Status will not change status of ticket in this case.','wp-support-plus-responsive-ticket-system');?></i></small>
                </td>
            </tr>
						
            <tr>
                <th scope="row"><?php _e('Ticket Status after Customer Reply', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>

                    <select name="general_settings[customer_reply_status]">

                        <option value=""><?php _e('Current Status','wp-support-plus-responsive-ticket-system')?></option>

                        <optgroup label="<?php _e('Status List','wp-support-plus-responsive-ticket-system')?>"></optgroup>
                        <?php
                        foreach ( $wpsupportplus->functions->get_wpsp_statuses() as $status ) :

                            $selected = $wpsupportplus->functions->get_customer_reply_status() == $status->id ? 'selected="selected"' : '';
                            echo '<option '.$selected.' value="'.$status->id.'">'.$status->name.'</option>';

                        endforeach;
                        ?>

                    </select><br>

                    <small><i><?php _e('As name suggest, this setting will change ticket status to selected when ticket creator reply to ticket. Current Status will not change status of ticket in this case.','wp-support-plus-responsive-ticket-system');?></i></small>

                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Allow Customer to Close Ticket', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>

                    <?php
                    $checked = $wpsupportplus->functions->is_close_btn_allowed() ? 'checked="checked"' : '';
                    ?>
                    <input <?php echo $checked?> type="checkbox" name="general_settings[allow_cust_close]" value="1" />

                    <small><i><?php _e('If enabled, it will show Close Ticket button to open ticket view for customer. ','wp-support-plus-responsive-ticket-system');?></i></small>

                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Ticket ID', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>

                    <?php
                    $ticket_sequence = $wpsupportplus->functions->get_ticket_id_sequence();
                    $checked = $ticket_sequence == 1 ? 'checked="checked"' : '';
                    ?>
                    <input <?php echo $checked?> type="radio" name="general_settings[ticket_id_sequence]" value="1" />
                    <?php _e('Sequential','wp-support-plus-responsive-ticket-system')?>&nbsp;&nbsp;

                    <?php
                    $checked = $ticket_sequence == 0 ? 'checked="checked"' : '';
                    ?>
                    <input <?php echo $checked?> type="radio" name="general_settings[ticket_id_sequence]" value="0" />
                    <?php _e('Random','wp-support-plus-responsive-ticket-system')?><br>

                    <small><i><?php _e('This is applicable for Ticket id being created for new ticket.','wp-support-plus-responsive-ticket-system');?></i></small>

                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Reply Form Position','wp-support-plus-responsive-ticket-system')?></th>
                <td>
                    <select name="general_settings[reply_form_position]">
                        <option <?php echo $wpsupportplus->functions->get_reply_form_position()? 'selected="selected"' : '' ?> value="1"><?php _e('Top','wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $wpsupportplus->functions->get_reply_form_position()? '' : 'selected="selected"' ?> value="0"><?php _e('Bottom','wp-support-plus-responsive-ticket-system')?></option>
                    </select><br>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Calender Date Format', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>

                    <?php
                    $date_formats = apply_filters('wpsp_date_format',array(
                        'dd-mm-yy',
                        'mm-dd-yy',
                        'yy-mm-dd',
                        'yy-dd-mm',
                        'mm-yy-dd',
                        'dd-yy-mm'
                    ));
                    ?>
                    <select name="general_settings[date_format]">
                        <?php
                        foreach ( $date_formats as $date_format ) {

                            $selected = $wpsupportplus->functions->get_date_format() == $date_format ? 'selected="selected"' : '';
                            echo '<option '.$selected.' value="'.$date_format.'">'.$date_format.'</option>';

                        }
                        ?>
                    </select><br>

                    <small><i><?php _e('This format will be applicable for input date fields (datepicker) for WP Support Plus','wp-support-plus-responsive-ticket-system');?></i></small>

                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('System Date Format', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>

                    <input type="text" name="general_settings[display_date_format]" value="<?php echo $wpsupportplus->functions->get_display_date_format()?>" ><br>

                    <small><i><?php _e('This format will be applicable for default date fields for Support Plus','wp-support-plus-responsive-ticket-system');?></i></small>

                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Custom Date Format', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>

                    <input type="text" name="general_settings[custom_date_format]" value="<?php echo $wpsupportplus->functions->get_custom_date_format()?>" ><br>

                    <small><i><?php _e('This format will be applicable for custom date fields for Support Plus','wp-support-plus-responsive-ticket-system');?></i></small>

                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Attachment Max Size(MB)','wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <input type="text" name="general_settings[attachment_size]" value="<?php echo $wpsupportplus->functions->get_attachment_size(); ?>"><br>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Allow Guest Ticket','wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <select name="general_settings[allow_guest_ticket]">
                        <option <?php echo $wpsupportplus->functions->is_allow_guest_ticket()? 'selected="selected"' : '' ?> value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $wpsupportplus->functions->is_allow_guest_ticket()? '' : 'selected="selected"' ?> value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
                    </select><br>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Allow Support Staff to read all ticket','wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <select name="general_settings[staff_read_all_ticket]">
                        <option <?php echo $wpsupportplus->functions->is_allow_staff_read_all_ticket()? 'selected="selected"' : '' ?> value="1"><?php _e('Yes','wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo $wpsupportplus->functions->is_allow_staff_read_all_ticket()? '' : 'selected="selected"' ?> value="0"><?php _e('No','wp-support-plus-responsive-ticket-system')?></option>
                    </select><br>
                </td>
            </tr>
						
						<tr>
								<th scope="row"><?php _e('Restrict to view support page', 'wp-support-plus-responsive-ticket-system'); ?></th>
								<td>

										<?php
										$checked = $wpsupportplus->functions->is_allow_to_view_customer_support_page() ? 'checked="checked"' : '';
										?>
										<input <?php echo $checked?> type="checkbox" name="general_settings[allow_to_view_customer]" value="1" />
										<?php _e('Customer', 'wp-support-plus-responsive-ticket-system'); ?></br>
										
										<?php
										$checked = $wpsupportplus->functions->is_allow_to_view_agent_support_page() ? 'checked="checked"' : '';
										?>
										<input <?php echo $checked?> type="checkbox" name="general_settings[allow_to_view_agent]" value="1" />
										<?php _e('Agent', 'wp-support-plus-responsive-ticket-system'); ?></br>
										
										<?php
										$checked = $wpsupportplus->functions->is_allow_to_view_supervisor_support_page() ? 'checked="checked"' : '';
										?>
										<input <?php echo $checked?> type="checkbox" name="general_settings[allow_to_view_supervisor]" value="1" />
										<?php _e('Supervisor', 'wp-support-plus-responsive-ticket-system'); ?>
								</td>
						</tr>

						<tr>
							  <th scope="row"><?php _e('Ticket Label', 'wp-support-plus-responsive-ticket-system'); ?></th>
	              <td>
	                   <input type="text"  name="general_settings[ticket_lable]" value="<?php echo $wpsupportplus->functions->get_ticket_lable(); ?>"><br>
                </td>
						</tr>
						
						<tr>
							  <th scope="row"><?php _e('Ticket ID Prefix', 'wp-support-plus-responsive-ticket-system'); ?></th>
	              <td>
	                   <input type="text" id="wpsp_ticket_id_prefix" name="general_settings[ticket_id_prefix]" value="<?php echo $wpsupportplus->functions->get_ticket_id_prefix(); ?>"><br>

										 <small><i><?php _e('If you set SRN, [Ticket #123456] will become [Ticket SRN123456] whereever applicable. Please don\'t leave blank.','wp-support-plus-responsive-ticket-system');?></i></small>
								</td>
						</tr>
						
						<tr>
							<th scope="row"><?php _e('Default Login', 'wp-support-plus-responsive-ticket-system'); ?></th>
							<td>	
								<?php
								$checked = $wpsupportplus->functions->is_enable_default_login() ? 'checked="checked"' : '';
								?>
								<input <?php echo $checked?> type="checkbox" name="general_settings[enable_default_login]" value="1" />
								<?php _e('Disable Default Login','wp-support-plus-responsive-ticket-system')?><br />
															
								<?php
								$default_login = $wpsupportplus->functions->get_default_login();
								$checked = $default_login == 1 ? 'checked="checked"' : '';
								?>
								<input <?php echo $checked?> type="radio" name="general_settings[default_login]" value="1" />
								<?php _e('Default Support Plus Login Module','wp-support-plus-responsive-ticket-system')?><br />

								<?php
								$checked = $default_login == 0 ? 'checked="checked"' : '';
								$style =  $default_login == 0 ? 'display:block;' :'display:none;';
								?>
								
								<label><input id="wp_login_link"  <?php echo $checked?> type="radio" name="general_settings[default_login]" value="0"  /></label>
								<?php _e('WP Login Link','wp-support-plus-responsive-ticket-system')?><br>								
								 
								 <div id="custom_login" style="<?php echo $style;?>">
								 		<?php _e('Custom Url','wp-support-plus-responsive-ticket-system')?>
							 			<input  <?php  $wpsupportplus->functions->get_custom_login_redirect_url();?>type="text" style="width:500px;" name="general_settings[custom_login]" value="<?php echo $wpsupportplus->functions->get_custom_login_redirect_url(); ?>"><br>
								 </div>
								 
								 </td>
							</tr>

						<tr>
							<th scope="row"><?php _e('Allow Powered by','wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
										<select name="general_settings[allow_powered_by_text]">
												<option <?php echo $wpsupportplus->functions->get_allow_powered_by_text()? 'selected="selected"' : '' ?> value="1"><?php _e('Enable','wp-support-plus-responsive-ticket-system')?></option>
												<option <?php echo $wpsupportplus->functions->get_allow_powered_by_text()? '' : 'selected="selected"' ?> value="0"><?php _e('Disable','wp-support-plus-responsive-ticket-system')?></option>
										</select><br>
                </td>
            </tr>
						
						<tr>
							<th scope="row"><?php _e('Public Ticket Mode','wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
									 <select name="general_settings[make_ticket_as_public]">
											<option <?php echo $wpsupportplus->functions->make_ticket_as_public() ? 'selected="selected"' : '' ?> value="1"><?php _e('Enable','wp-support-plus-responsive-ticket-system')?></option>
											<option <?php echo $wpsupportplus->functions->make_ticket_as_public() ? '' : 'selected="selected"' ?> value="0"><?php _e('Disable','wp-support-plus-responsive-ticket-system')?></option>
									 </select><br>
									
									 <small><i><?php _e('If you enable this setting then all tickets will be visible to all users and they can reply to each others tickets.','wp-support-plus-responsive-ticket-system');?></i></small>
								</td>
					  </tr>

				</table>

        <?php do_action('wpsp_after_general_settings')?>

        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">											
        </p>

    </form>

</div>

<script>
function wpsp_upload_company_logo_dashboard(){
    file_frame = wp.media.frames.file_frame = wp.media({
        title: "Select Company Logo",
        button: {
            text: "Set Company logo"
        },
        multiple: false
    });
    file_frame.on('select', function () {
        image_data = file_frame.state().get('selection').first().toJSON();
        wpspjq('#wpsp_company_logo_img').attr('src',image_data.url);
        wpspjq('#wpsp_company_logo_url').val(image_data.url);
    });
    file_frame.open();
}

jQuery(document).ready(function() {
   wpspjq('input[type="radio"][name="general_settings[default_login]"]').click(function() {
       if(this.value==0)
			 		wpspjq('#custom_login').show();
			 else
			 		wpspjq('#custom_login').hide();   
   });
});
</script>
