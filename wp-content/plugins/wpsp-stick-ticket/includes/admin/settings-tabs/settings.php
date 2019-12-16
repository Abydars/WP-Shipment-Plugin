<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $current_user,$wpdb;
$current_user=wp_get_current_user();
$stickTicketSettings=get_option( 'wpsp_stick_ticket_settings' );
?>
<br><br>
<span class="label label-info wpsp_title_label"><?php _e('Allow User To Stick Ticket','wpsp-stick-ticket');?></span><br><br>
<input type="radio" name="wpsp_stick_ticket_setting" value="1"<?php echo ($stickTicketSettings['wpsp_allow_user_to_stick_ticket']==1)?'checked="checked"':''; ?>> <?php _e('Admin only','wpsp-stick-ticket');?>
<br>
<input type="radio" name="wpsp_stick_ticket_setting" value="0"<?php echo ($stickTicketSettings['wpsp_allow_user_to_stick_ticket']==0)?'checked="checked"':''; ?>> <?php _e('Indivisual user can stick ticket','wpsp-stick-ticket');?>
<hr>
<span class="label label-info wpsp_title_label"><?php _e('Stick Ticket Color : ','wpsp-stick-ticket');?></span><br><br>
<table>
<tr>         
    <td></td>         
    <td><input type="text" id="wpspstickTicket_bc" value="<?php _e($stickTicketSettings['stick_ticket_color'],'wpsp-stick-ticket');?>" class="wp-support-plus-color-picker" ></td>     
</tr> 
</table> 
<br>
<button type="submit" class="btn btn-success" onclick="wpsp_stick_setting()"><?php echo __('Submit','wpsp-stick-ticket')?></button>
<hr>
<script type="text/javascript">
jQuery(document).ready(function(jQuery){
    jQuery('.wp-support-plus-color-picker').wpColorPicker();
});
</script>