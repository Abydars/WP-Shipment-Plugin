<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$email_piping      = get_option('wpsp_email_pipe_settings');
$gmail_connections = get_option('wpsp_ep_gmail_connections') ? get_option('wpsp_ep_gmail_connections') : array();
$imap_connections  = get_option('wpsp_ep_imap_connections') ? get_option('wpsp_ep_imap_connections') : array();
$categories           = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_catagories ORDER BY load_order");

?>

<div id="tab_container">

    <form id="wpsp_frm_email_piping_settings" method="post" action="">

        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="update_setting" value="settings_email_piping"/>
        <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>

        <table class="form-table">

            <tr>
                <th scope="row"><h3><?php _e('General', 'wpsp_emailpipe'); ?></h3></th>
                <td></td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Block Emails', 'wpsp_emailpipe'); ?></th>
                <td>

                    <textarea name="email_piping[block_emails]" style="width: 300px; height: 100px;"><?php echo isset($email_piping['block_emails']) ? $email_piping['block_emails'] : ''?></textarea><br>
                    <small><i><?php _e('Please insert one email per line. You can insert email pattern like *@paypal.com, admin@*, etc.','wpsp_emailpipe');?></i></small>

                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Ignore emails having these words in subject', 'wpsp_emailpipe'); ?></th>
                <td>

                    <textarea name="email_piping[ignore_email_subject]" style="width: 300px; height: 100px;"><?php echo isset($email_piping['ignore_email_subject']) ? $email_piping['ignore_email_subject'] : ''?></textarea><br>
                      <small><i><?php _e('Please add one pattern per line. You can use patterns like abc,xyz,*abc,xyz* etc.','wpsp_emailpipe');?></i></small>

                </td>
            </tr>
            

            <tr>
                <th scope="row"><?php _e('Allowed User Emails', 'wpsp_emailpipe'); ?></th>
                <td>

                    <select name="email_piping[allowed_emails]">
                        <option <?php echo isset($email_piping['allowed_emails']) && $email_piping['allowed_emails'] == 1 ? 'selected="selected"' : ''?> value="1"><?php _e('Anyone (includding guest users)', 'wpsp_emailpipe'); ?></option>
                        <option <?php echo isset($email_piping['allowed_emails']) && $email_piping['allowed_emails'] == 0 ? 'selected="selected"' : ''?> value="0"><?php _e('Registered Users Only', 'wpsp_emailpipe'); ?></option>
                    </select>

                </td>
            </tr>

						<tr>
                <th scope="row"><h3><?php _e('IMAP', 'wpsp_emailpipe'); ?></h3></th>
                <td></td>
            </tr>

						<tr>
                <th scope="row"><?php _e('Pipe Accounts', 'wpsp_emailpipe'); ?></th>
                <td>

                    <a href="<?php echo admin_url( 'admin.php' ).'?page=wp-support-plus&setting=addons&section=email_piping&ep_action=add_imap'; ?>" class="button"><?php _e('+ Add New', 'wpsp_emailpipe'); ?></a><br><br>

                    <table class="wp-list-table widefat fixed posts wpsp_settings_tbl wpsp_settings_tbl_half">
                      <thead>
                        <tr>
                          <th><?php _e('Email Account', 'wpsp_emailpipe');?></th>
                          <th><?php _e('Action', 'wpsp_emailpipe');?></th>
                        </tr>
                      </thead>

                      <?php foreach ($imap_connections as $key => $connection): ?>
                        <tr>
                          <td><?php echo $connection['email']?></td>
                          <td>
														<a href="<?php echo admin_url( 'admin.php' ).'?page=wp-support-plus&setting=addons&section=email_piping&ep_action=edit_imap&id='.$key;?>">Edit</a>&nbsp;|
														<a href="#" onclick="wpsp_ep_delete_imap_connection(<?php echo $key?>,'<?php echo $connection['email']?>','<?php echo wp_create_nonce($key)?>')" class="wpsp_en_delete">Delete</a>
													</td>
                        </tr>
                      <?php endforeach; ?>

                    </table>

                </td>
            </tr>

            <tr>
                <th scope="row"><h3><?php _e('Google Mail', 'wpsp_emailpipe'); ?></h3></th>
                <td></td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Client Secret', 'wpsp_emailpipe'); ?></th>
                <td>
                    <small><i><?php _e('You need to create Google Developer app. Please <a href="https://developers.google.com/gmail/api/quickstart/php" target="_blank">follow step 1 here</a> and upload client_secret.json file below.','wpsp_emailpipe');?></i></small><br><br>
                    <input type="file" onchange="wpsp_pipe_gmail_app_config('<?php echo wp_create_nonce();?>');" name="gmail_client_secret"/><br>
                    <input type="hidden" name="email_piping[gmail_client_secret]" value="<?php echo isset($email_piping['gmail_client_secret']) ? $email_piping['gmail_client_secret'] : ''?>" />
                    <strong id="gmail_client_secret"><?php echo isset($email_piping['gmail_client_secret']) ? $email_piping['gmail_client_secret'] : ''?></strong>
                </td>
            </tr>

            <tr>
                <th scope="row"><?php _e('Pipe Accounts', 'wpsp_emailpipe'); ?></th>
                <td>

                    <a href="<?php echo admin_url( 'admin.php' ).'?page=wp-support-plus&setting=addons&section=email_piping&action=add_gmail_connection'; ?>" class="button"><?php _e('+ Add New', 'wpsp_emailpipe'); ?></a><br>

                    <div id="gmail_pipe_account_container" style="padding-top: 20px;">
                        <?php
                        foreach ($gmail_connections as $connection){
                            ?>
                            <div class="wpsp_autocomplete_choice_item">
                                <?php echo $connection['email_address']?> <span onclick="wpsp_ep_delete_gmail_connection(this, '<?php echo $connection['email_address']?>','<?php echo wp_create_nonce()?>')" class="dashicons dashicons-no-alt wpsp_autocomplete_choice_item_delete"></span>
                                <input type="hidden" name="gmail_connection[]" value="<?php echo $connection['email_address']?>">
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                </td>
            </tr>

            <tr>
                <th scope="row" style="vertical-align: top;"><h3><?php _e('Assign Categories', 'wpsp_emailpipe'); ?></h3></th>
                <td style="padding-top: 35px;">

                    <table class="wp-list-table widefat fixed posts wpsp_settings_tbl wpsp_settings_tbl_half">
                      <thead>
                        <tr>
                            <th><?php _e('Email', 'wpsp_emailpipe'); ?></th>
                            <th><?php _e('Category', 'wpsp_emailpipe'); ?></th>
                        </tr>
                      </thead>

                        <?php
                           if(isset($email_piping['email_categories'])){
                              foreach ($email_piping['email_categories'] as $key => $value){
                                  ?>
                                  <tr>
                                      <td><?php echo $key?></td>
                                      <td>
                                          <select name="email_piping[email_categories][<?php echo $key?>]" >
                                          <?php
                                          foreach ( $categories as $category ) {

                                              $selected = $category->id == $email_piping['email_categories'][$key] ? 'selected="selected"' : '';
                                              ?>
                                              <option <?php echo $selected?> value="<?php echo $category->id?>"><?php echo $category->name?></option>
                                              <?php

                                          }
                                          ?>
                                          </select>
                                      </td>
                                  </tr>
                                  <?php
                                 }
                             }
                        ?>

                    </table>

                </td>
            </tr>

         </table>

        <?php do_action('wpsp_after_ep_settings_overview')?>

        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wpsp_emailpipe'); ?>" type="submit">
        </p>

    </form>

</div>
