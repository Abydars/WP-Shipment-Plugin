<?php
$cu = wp_get_current_user();
$timerSettings=get_option('wpsp_timer_settings' );
if ($cu->has_cap('manage_options')) {
    if(!empty($_POST)){
        $timerSettings=array(
                'timer_visibility'=>($_POST['wpsp_timer_setting'])
            );
        update_option('wpsp_timer_settings',$timerSettings);
    }
}
?>
<br>
<form id="job-fields" method="post" action="<?php echo admin_url( 'admin.php?page=wpsp-timer&tab=timer-settings' );?>" onsubmit="">
    <span class="label label-info wpsp_title_label"><?php _e('Timer settings','wpsp-timer');?></span><br><br>
        <input type="radio" name="wpsp_timer_setting" value="1"<?php echo $timerSettings['timer_visibility']==1?'checked="checked"':''; ?>> <?php _e('Timer enable for customer','wpsp-timer');?>
        <br>
        <input type="radio" name="wpsp_timer_setting" value="0"<?php echo $timerSettings['timer_visibility']==0?'checked="checked"':''; ?>> <?php _e('Timer disable for customer','wpsp-timer');?>
        <hr>
    <button type="submit"><?php echo __('Submit','wpsp-timer')?></button>
</form>