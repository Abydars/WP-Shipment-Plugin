<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

?>

<div id="tab_container">
    <form method="post" action="">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="update_setting" value="custom_css">
          <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>
        
        <table class="form-table">
					
						<tr>
								<th scope="row"><h3><?php _e('General', 'wp-support-plus-responsive-ticket-system'); ?></h3></th>
								<td></td>
						</tr>
            <tr>
                <th scope="row"><?php _e('Custom CSS', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
										<textarea name="custom_css[custom_css]" rows="7" cols="100"><?php echo stripslashes($wpsupportplus->functions->get_custom_css());?> </textarea>
                </td>
            </tr><br><br>
						<tr>
                <th scope="row"><?php _e('Theme Integration', 'wp-support-plus-responsive-ticket-system'); ?>
								<br /><small><i><?php _e('Applicable only in theme integration view','wp-support-plus-responsive-ticket-system');?></i></small></th>
                <td>
										<textarea name="custom_css[theme_intrgration]" rows="7" cols="100"><?php echo stripslashes($wpsupportplus->functions->get_theme_intrgration());?> </textarea>
                </td>
            </tr>
            
        </table>
         
        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">
        </p>
    </form>
    
</div>
