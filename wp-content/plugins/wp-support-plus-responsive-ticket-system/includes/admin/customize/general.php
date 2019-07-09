<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

$customize_general = $wpsupportplus->functions->get_customize_general();

?>
<div id="tab_container">
    
    <form method="post" action="">
        
        <input type="hidden" name="action" value="update"/>
        <input type="hidden" name="update_setting" value="customize_general_settings"/>
        <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>
        
				<div style="clear:both;padding-top:10px;">
					In version series 9x you can choose to theme Integrate or have Stand-Alone interface for your support page.
					You can customize Stand-Alone interface using this custom css.
				</div>
        <table class="form-table">
            
            <tr>
                <th scope="row"><h3><?php _e('General CSS', 'wp-support-plus-responsive-ticket-system'); ?></h3></th>
                <td></td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Header', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <textarea name="customize_general[header]" rows="8" cols="80"><?php echo $customize_general['header'];?></textarea>
                </td>
            </tr>
						
						<tr>
                <th scope="row"><?php _e('Secondary Menu', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <textarea name="customize_general[secondery_menu]" rows="8" cols="80"><?php echo $customize_general['secondery_menu'];?></textarea>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><?php _e('Body', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <textarea name="customize_general[body]" rows="8" cols="80"><?php echo $customize_general['body'];?></textarea>
                </td>
            </tr>
						
						<tr>
                <th scope="row"><?php _e('Footer', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <textarea name="customize_general[footer]" rows="8" cols="80"><?php echo $customize_general['footer'];?></textarea>
                </td>
            </tr>
						
						<tr>
                <th scope="row"><?php _e('Buttons', 'wp-support-plus-responsive-ticket-system'); ?></th>
                <td>
                    <textarea name="customize_general[buttons]" rows="8" cols="80"><?php echo $customize_general['buttons'];?></textarea>
                </td>
            </tr>
            
            
        </table>
        
        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">
						<button type="button" class="button" onclick="wpsp_customize_reset_default('general');"><?php _e('Reset default', 'wp-support-plus-responsive-ticket-system'); ?></button>
        </p>
        
    </form>
    
</div>