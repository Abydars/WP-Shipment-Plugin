<?php
global $wpdb;
$values=array(
            'ticket_id'=>$_POST['ticket_id'],
            'start_time'=>date('Y-m-d H:i:s'),
            'timer_status'=>1
        );
$wpdb->insert($wpdb->prefix.'wpsp_timer',$values);
?>