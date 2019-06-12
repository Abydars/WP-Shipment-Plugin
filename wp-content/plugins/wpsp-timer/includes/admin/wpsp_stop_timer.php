<?php
global $wpdb;
$values=array(
            'end_time'=>date('Y-m-d H:i:s'),
            'timer_status'=>0
        );
$wpdb->update($wpdb->prefix.'wpsp_timer',$values,array('id'=>$_POST['timer_log_id']));
?>