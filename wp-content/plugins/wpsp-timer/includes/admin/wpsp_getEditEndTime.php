<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb;
$query = "select * FROM {$wpdb->prefix}wpsp_timer  where ticket_id=".$_POST['ticket_id']." ORDER BY id DESC LIMIT 1 ";
$log_time=$wpdb->get_row( $query );
$sql = "select * FROM {$wpdb->prefix}wpsp_ticket WHERE id=".$_POST['ticket_id'];
$ticket=$wpdb->get_row( $sql );
$advancedSettings=get_option( 'wpsp_advanced_settings' );
?>
<h3><?php echo '['.__($advancedSettings['ticket_label_alice'][1],'wpsp-timer')?> <?php echo $advancedSettings['wpsp_ticket_id_prefix'].$_POST['ticket_id'].'] '.stripcslashes(htmlspecialchars_decode($ticket->subject,ENT_QUOTES));?></h3><br>
<strong><h3><?php echo __('Edit End Time','wpsp-timer')?></h3></strong><br>
<b><?php echo __('Start Time :','wpsp-timer')?></b> <?php echo $log_time->start_time;?><br>
<b><?php echo __('End Time :','wpsp-timer')?></b> <input type="text" value="<?php echo $log_time->end_time;?>" id="wpsp_end_time"><br><br>
<button class="btn btn-success" onclick="wpsp_setEditEndTime(<?php echo $log_time->id?>,<?php echo $_POST['ticket_id']?>);"><?php echo __('Save Changes','wpsp-timer')?></button>