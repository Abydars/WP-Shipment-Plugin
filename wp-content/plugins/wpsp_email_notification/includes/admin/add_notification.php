<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$admin       = new WPSP_EN_Backend();
$email_types = $admin->get_email_types();
?>

<div id="tab_container">

    <form id="wpsp_frm_en_add" method="post" action="admin.php?page=wp-support-plus&setting=addons&section=email_notification&wpsp_en_action=insert_notification">

      <table class="form-table">

          <tr>
            <th scope="row"><h3><?php _e( 'Add Notification', 'wpsp-en' );?></h3></th>
            <td></td>
          </tr>

          <tr>
              <th scope="row"><?php _e( 'Title', 'wpsp-en' );?></th>
              <td>
                <input type="text" name="wpsp_en_notification[title]" />
              </td>
          </tr>

          <tr>
              <th scope="row"><?php _e( 'Type', 'wpsp-en' );?></th>
              <td>
								<select name="wpsp_en_notification[type]">
									<?php
									foreach ($email_types as $key => $value) {
										echo '<option value="'.$key.'">'.$value.'</option>';
									}
									?>
								</select>
              </td>
          </tr>

      </table>


      <p class="submit">
          <input id="submit" class="button button-primary" name="submit" value="<?php _e( 'Save & Continue', 'wpsp-en' );?>" type="submit">
      </p>

    </form>

</div>
