<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$admin                = new WPSP_EN_Backend();
$email_types          = $admin->get_email_types();
$email_notification   = get_option('wpsp_email_notification') ? get_option('wpsp_email_notification') : array();
$wpsp_en_notification = get_option('wpsp_en_notification') ? get_option('wpsp_en_notification') : array();
?>

<div id="tab_container">

    <form id="wpsp_frm_email_notification_settings" method="post" action="">

      <input type="hidden" name="action" value="update"/>
      <input type="hidden" name="update_setting" value="settings_email_notification"/>
      <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>

        <table class="form-table">

            <tr class="wpsp_en_header">
              <th scope="row"><h3><?php _e( 'General Settings', 'wpsp-en' );?></h3></th>
              <td></td>
            </tr>

            <tr>
                <th scope="row"><?php _e( 'From Name', 'wpsp-en' );?></th>
                <td>
                  <input type="text" name="email_notification[from_name]" value="<?php echo isset($email_notification['from_name']) ? $email_notification['from_name'] : '';?>" />
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e( 'From Email', 'wpsp-en' );?></th>
                <td>
                  <input type="text" name="email_notification[from_email]" value="<?php echo isset($email_notification['from_email']) ? $email_notification['from_email'] : '';?>" />
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e( 'Reply-To', 'wpsp-en' );?></th>
                <td>
                  <input type="text" name="email_notification[reply_to]" value="<?php echo isset($email_notification['reply_to']) ? $email_notification['reply_to'] : '';?>" />
                </td>
            </tr>

						<tr>
                <th scope="row"><?php _e( 'Ignore-emails', 'wpsp-en' );?></th>
                <td>
                    <textarea name="email_notification[ignore_emails]" rows="7" cols="50"><?php echo isset($email_notification['ignore_emails']) ? $email_notification['ignore_emails'] : '';?></textarea>
                </td>
            </tr>

						<?php do_action('wpsp_en_general_settings',$email_notification) ?>

						<tr>
								<td colspan="2">
									<input id="submit" style="margin-left:-10px;" class="button button-primary" name="submit" value="<?php _e( 'Save Changes', 'wpsp-en' );?>" type="submit">
								</td>
						</tr>

						<tr class="wpsp_en_header">
              <th scope="row" colspan="2">
								<h3>
									<?php _e( 'Notifications', 'wpsp-en' );?>
									<a href="admin.php?page=wp-support-plus&setting=addons&section=email_notification&wpsp_en_action=add_notification" class="page-title-action"><?php _e( 'Add New', 'wpsp-en' );?></a>
								</h3>
							</th>
            </tr>

						<tr>
                <td colspan="2">
									<table id="wpsp_en_tbl_notifications" class="wp-list-table widefat fixed posts">
										<thead>
											<tr>
												<th class="wpsp_en_title_col" scope="col"><?php _e( 'Title', 'wpsp-en' );?></th>
												<th class="wpsp_en_type_col" scope="col"><?php _e( 'Type', 'wpsp-en' );?></th>
												<th scope="col"><?php _e( 'Actions', 'wpsp-en' );?></th>
											</tr>
										</thead>
										<tbody>

											<?php
											if($wpsp_en_notification){

												foreach ($wpsp_en_notification as $key => $value) {

													?>
													<tr>
														<td><?php echo $value['title'];?></td>
														<td><?php echo $email_types[$value['type']];?></td>
														<td>
															<a href="admin.php?page=wp-support-plus&setting=addons&section=email_notification&wpsp_en_action=edit_notification&en_id=<?php echo $key;?>"><?php _e('Edit','wpsp-en')?></a>&nbsp;|
															<a href="#" onclick="wpsp_en_clone(this)" data-href="admin.php?page=wp-support-plus&setting=addons&section=email_notification&wpsp_en_action=clone_notification&en_id=<?php echo $key;?>&nonce=<?php echo wp_create_nonce($key);?>"><?php _e('Clone','wpsp-en')?></a>&nbsp;|
															<a href="#" onclick="wpsp_en_delete(this)" data-href="admin.php?page=wp-support-plus&setting=addons&section=email_notification&wpsp_en_action=delete_notification&en_id=<?php echo $key;?>&nonce=<?php echo wp_create_nonce($key);?>" class="wpsp_en_delete"><?php _e('Delete','wpsp-en')?></a>
														</td>
													</tr>
													<?php

												}

											} else {

												echo '<tr><td style="text-align:center;" colspan="3">'.__( 'No Notifications Found', 'wpsp-en' ).'</td></tr>';

											}
											?>

										</tbody>
									</table>

                </td>
            </tr>

        </table>

    </form>

</div>
