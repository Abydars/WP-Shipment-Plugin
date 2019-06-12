<?php
global $wpdb;
$sql="select * from {$wpdb->prefix}wpsp_timer where ticket_id=".$_POST['ticket_id'];
$timer_log = $wpdb->get_results( $sql );
$wpsp_time='';
$wpsp_total_time=strtotime('00:00:00');
foreach($timer_log as $log){ 
    if($log->timer_status==0){
        $wpsp_diff=date('H:i:s',abs(strtotime($log->end_time)-strtotime($log->start_time)));
        $wpsp_total_time=$wpsp_total_time+strtotime($wpsp_diff);
    }
    else{
        $wpsp_current_time=time();
        $time=$wpsp_current_time-strtotime($log->start_time);
        $wpsp_total_time=$wpsp_total_time+$time;
    }
}?>
<strong><?php echo __('Total Time:','wpsp-timer').' '.date("H:i:s",$wpsp_total_time);?></strong>