<?php
final class WPSPTimerBackend {
    function loadScripts(){
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script('wpsp_timer_admin', WPSP_TIMER_URL . 'asset/js/admin.js?version='.WPSP_TIMER_VERSION);
        wp_enqueue_style('wpsp_timer_admin', WPSP_TIMER_URL . 'asset/css/admin.css?version='.WPSP_TIMER_VERSION);
        
        $localize_script_data=array(
            'wpsp_ajax_url'=>admin_url( 'admin-ajax.php' ),
            'wpsp_timer_site_url'=>site_url(),
            'plugin_url'=>WPSP_TIMER_URL,
            'plugin_dir'=>WPSP_TIMER_DIR,
            'insert_job_label'=>__('Please insert Job Label','wpsp-timer'),
            'insert_field_label'=>__('Please insert Field Label','wpsp-timer'),
            'sure_to_enable_reply'=>__('Are you sure to enable reply?','wpsp-timer'),
            'insert_disable_reply_message'=>__('Please insert disable reply message','wpsp-timer')
        );
        wp_localize_script( 'wpsp_timer_admin', 'wpsp_timer_data', $localize_script_data );
    }
    
    function custom_menu_page(){
        add_submenu_page( 'wp-support-plus', 'WP Support Plus Timer', __('Timer','wpsp-timer'), 'manage_options', 'wpsp-timer', array($this,'customization_submenu') );
    }
    
    function customization_submenu(){
        wp_enqueue_script('wpce_bootstrap', WCE_PLUGIN_URL . 'asset/js/bootstrap/js/bootstrap.min.js?version='.WPSP_VERSION);
        wp_enqueue_style('wpce_bootstrap', WCE_PLUGIN_URL . 'asset/js/bootstrap/css/bootstrap.min.css?version='.WPSP_VERSION);
        include WPSP_TIMER_DIR.'includes/admin/customize-settings.php';
    }
    
    function setStartTimer(){
        include WPSP_TIMER_DIR.'includes/admin/wpsp_start_timer.php';
        die();
    }
    
    function wpsp_stop_time(){
        include WPSP_TIMER_DIR.'includes/admin/wpsp_stop_timer.php';
        die();
    }
    
    function getTimerLog(){
        include WPSP_TIMER_DIR.'includes/admin/wpsp_getTimerLog.php';
        die();
    }
    
    function wpsp_getEditEndTime(){
        include WPSP_TIMER_DIR.'includes/admin/wpsp_getEditEndTime.php';
        die();
    }
    
    function wpsp_setEditEndTime(){
        include WPSP_TIMER_DIR.'includes/admin/wpsp_setEditEndTime.php';
        die();
    }
    
    function wpsp_calculate_total_time_required(){
        include WPSP_TIMER_DIR.'includes/admin/wpsp_total_time.php';
        die();
    }
    
    function wpsp_display_total_time_required_to_ticket($ticket){
        global $wpdb;
        global $current_user;
        $timerSettings=get_option('wpsp_timer_settings' );
        $current_user=wp_get_current_user();
        if($current_user->has_cap('manage_support_plus_ticket')||$timerSettings['timer_visibility']==1){
            ?>
            <div class="wpsp_timer_container">
            <?php
        }
        if($current_user->has_cap('manage_support_plus_ticket')||$timerSettings['timer_visibility']==1){?>
            <div class="wpsp_ticket_time">
                <?php
                $_POST['ticket_id']=$ticket->id;
                include WPSP_TIMER_DIR.'includes/admin/wpsp_total_time.php';
                ?>
            </div>
            <?php
        }?>
        <script type="text/javascript">
            jQuery(document).ready(function(){
                setInterval(function(){wpsp_calculate_total_time_required(<?php echo $ticket->id?>)},10000);
            });
        </script>
        <?php
        $timerSettings=get_option('wpsp_timer_settings' );
        $btn_value='';
        $timer_sql="select * from {$wpdb->prefix}wpsp_timer where ticket_id=".$ticket->id." && timer_status=1";
        $timer = $wpdb->get_row( $timer_sql );
        $log_id=isset($timer->id)?$timer->id:0;
        if($timer){
            $btn_value=__('Stop Timer','wpsp-timer');
            $cnt=1;
        }else{
            $btn_value=__('Start Timer','wpsp-timer');
            $cnt=0;
        }?> <?php
        if($current_user->has_cap('manage_support_plus_ticket')){?>
            <div class='wpsp_timer'>
                <input type="hidden" id="wpsp_start_time" value="<?php echo $cnt?>">
                <button  id='wpsp_timer_btn' class="wpsp_timer_btn" onclick="wpsp_set_timer(<?php echo $ticket->id ?>,<?php echo $log_id ?>);"><?php echo $btn_value;?></button><?php   
                if($cnt==0){?>
                    <button  id='wpsp_view_log_btn' class="wpsp_timer_log" onclick="getTimerLog(<?php echo $ticket->id ?>);"><?php echo __('View Log','wpsp-timer')?></button><br><br><?php   
                }?>
            </div><?php 
        }
        
        if($current_user->has_cap('manage_support_plus_ticket') || $timerSettings['timer_visibility']==1){
            ?>
            </div>
            <?php
        }
        
    }
}
?>