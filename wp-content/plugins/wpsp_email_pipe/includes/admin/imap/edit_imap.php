<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$email_piping = get_option('wpsp_email_pipe_settings');
$imap_connections  = get_option('wpsp_ep_imap_connections') ? get_option('wpsp_ep_imap_connections') : array();

$connection =  isset($imap_connections[$_REQUEST['id']]) ? $imap_connections[$_REQUEST['id']] : array();

if (!$connection) {
  die();
}
?>

<div id="tab_container">

    <form id="wpsp_frm_ep_edit_frm" method="post" action="#" onsubmit="return false;">

        <table class="form-table">

            <tr>
                <th scope="row" colspan="2"><h3><?php _e('Edit IMAP Connection', 'wpsp_emailpipe'); ?></h3></th>
            </tr>

            <tr>
              <th><?php _e('Email Address', 'wpsp_emailpipe')?></th>
              <td>
                <input type="text" class="required" name="wpsp_imap_connection[email]" value="<?php echo $connection['email']?>">
              </td>
            </tr>

            <tr>
              <th><?php _e('Encryption', 'wpsp_emailpipe')?></th>
              <td>
                <select name="wpsp_imap_connection[encryption]">
                  <option <?php echo  $connection['encryption']=='none' ? 'selected="selected"' : ''?> value="none"><?php _e('None', 'wpsp_emailpipe')?></option>
                  <option <?php echo  $connection['encryption']=='ssl' ? 'selected="selected"' : ''?> value="ssl"><?php _e('SSL', 'wpsp_emailpipe')?></option>
                </select>
              </td>
            </tr>

            <tr>
              <th><?php _e('Incoming Mail Server', 'wpsp_emailpipe')?></th>
              <td>
                <input type="text" class="required" name="wpsp_imap_connection[mail_server]" value="<?php echo $connection['mail_server']?>">
              </td>
            </tr>

            <tr>
              <th><?php _e('Incoming Mail Server Port', 'wpsp_emailpipe')?></th>
              <td>
                <select name="wpsp_imap_connection[server_port]">
                  <option <?php echo  $connection['server_port']=='143' ? 'selected="selected"' : ''?> value="143">143</option>
                  <option <?php echo  $connection['server_port']=='993' ? 'selected="selected"' : ''?> value="993">993</option>
                </select>
              </td>
            </tr>

            <tr>
              <th><?php _e('Username', 'wpsp_emailpipe')?></th>
              <td>
                <input type="text" class="required" name="wpsp_imap_connection[username]" value="<?php echo $connection['username']?>">
              </td>
            </tr>

            <tr>
              <th><?php _e('Password', 'wpsp_emailpipe')?></th>
              <td>
                <input type="password" class="required" name="wpsp_imap_connection[password]" value="<?php echo $connection['password']?>">
              </td>
            </tr>

            <?php do_action('wpsp_after_ep_imap_edit')?>

         </table>

         <input type="hidden" name="action" value="wpsp_ep_add_imap">
         <input type="hidden" name="nonce" value="<?php echo wp_create_nonce()?>">

        <div class="submit">
            <button class="wpsp_ep_save_pipe_connection button button-primary" type="button" onclick="wpsp_ep_add_imap();"><?php _e('Test & Save Connection', 'wpsp_emailpipe')?></button><br>
            <img class="wpsp_ep_save_pipe_connection_loder" src="<?php echo WPSP_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>" alt="">
            <div class="wpsp_ep_save_pipe_connection_msg"></div>
        </div>

    </form>

</div>
