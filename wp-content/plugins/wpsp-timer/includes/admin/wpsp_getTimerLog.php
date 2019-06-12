<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb;
global $current_user;
$current_user=wp_get_current_user();
?>
<div id="wpsp_view_log">
    <table class="table table-striped table-hover" id="wpspTimerLogTBL">
        <h4><?php _e('Activity Log','wpsp-timer');?></h4>
        <tr>
            <th style="width: 50px;">#</th>
            <th><?php _e('Start Time','wpsp-timer');?></th>
            <th><?php _e('Stop Time','wpsp-timer');?></th>
            <th><?php _e('Time Interval','wpsp-timer');?></th>
        </tr>
        <?php 
        $wpsp_timer_id=0;
        $wpsp_time=array();
        $wpsp_sum_time=strtotime('00:00:00');
        $wpsp_sum_time2=0;
        $sql="select * from {$wpdb->prefix}wpsp_timer where ticket_id=".$_POST['ticket_id'];
        $timer_log = $wpdb->get_results( $sql );
        $query = "select end_time FROM {$wpdb->prefix}wpsp_timer where ticket_id=".$_POST['ticket_id']." ORDER BY id DESC LIMIT 1 ";
        $end_time=$wpdb->get_var( $query );
        foreach($timer_log as $log){?>
            <tr id="mytr">
                <td style="width: 50px;"><?php echo ++$wpsp_timer_id;?></td>
                <td><?php echo $log->start_time;?></td>
                <td><?php echo $log->end_time;?>
                    <?php if($log->end_time==$end_time && ($current_user->has_cap('manage_options')||($current_user->has_cap('manage_support_plus_agent')))){ ?><img alt="Edit" id="wpsp_edit_end_time" title="Edit" onclick="wpsp_getEditEndTime(<?php echo $_POST['ticket_id']?>);" src="<?php echo WPSP_TIMER_URL.'asset/images/edit.png';?>" /><?php }?> 
                </td>
                <td><?php echo date('H:i:s',abs(strtotime($log->end_time)-strtotime($log->start_time)));?></td>
            </tr><?php 
        }?>
    </table>
    <?php 
    if(!$timer_log){?>
            <div style="text-align: center;"><?php _e("No log Found",'wpsp-timer');?></div><?php 
    }?>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" onclick="wpsp_close_front_popup();"><?php _e('Close','wpsp-timer');?></button>
    </div>
</div>