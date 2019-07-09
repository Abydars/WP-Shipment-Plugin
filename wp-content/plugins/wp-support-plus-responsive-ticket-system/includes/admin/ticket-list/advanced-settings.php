<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<?php  
global $wpsupportplus;
?>
<div id="tab_container">

    <form method="post" action="">

        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="update_setting" value="advanced_settings"/>
        <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>
        
        <table class="form-table">

            <tr>
                <th scope="row"><h3><?php _e('Advanced Settings', 'wp-support-plus-responsive-ticket-system'); ?></h3></th>
                <td></td>
            </tr>

            <tr>
							  <th scope="row"><?php _e('Subject Character Length', 'wp-support-plus-responsive-ticket-system'); ?></th>
	              <td>
	                   <input type="text" id="wpsp_sub_char_length" name="advanced_settings[sub_char_length]" value="<?php echo $wpsupportplus->functions->get_sub_char_length(); ?>"><br>
                </td>
						</tr>
						<tr>
								<th scope="row"><?php _e('Raised By Character Length','wp-support-plus-responsive-ticket-system');?></th>
							<td>
									<input type="text" id="wpsp_raised_char_length" name="advanced_settings[raised_char_length]" value="<?php echo $wpsupportplus->functions->get_raised_char_length(); ?>"><br>
							</td>
						</tr>
				</table>

        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">											
        </p>

    </form>

</div>