<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

?>
<div id="tab_container">
    <form method="post" action="">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="update_setting" value="footer_text">
          <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>
        
        <table class="form-table" width="100%">
            <tr>
                <th scope="row"><h3><?php _e('Footer Text','wp-support-plus-responsive-ticket-system');?></h3>
									<div style="">
					        	<small><?php _e('Applicable on Stand-Alone interface only.','wp-support-plus-responsive-ticket-system');?></small> 
					        </div> 
								</th>
								
                <td></td>
            </tr>
            
            <tr>
                <td width="100%">
                    <?php
                        
                         $editor_id = 'footer_text';
                         $settings  = array( 'textarea' =>'footer_text' ,'editor_height' =>'200');
                         $content   = stripslashes($wpsupportplus->functions->get_footer_text());
                         wp_editor( $content, $editor_id, $settings );
                    ?>
                </td>
            </tr>
            
        </table>
         
        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">
        </p>
    </form>
    
</div>


