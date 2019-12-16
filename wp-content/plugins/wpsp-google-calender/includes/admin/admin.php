<?php
final class WPSPGoogleCalenderEventBackend {
    
    function wpsp_getindi_after_buttons_icon_planner($ticket){
        $advancedSettings = get_option('wpsp_advanced_settings');
        $base="https://calendar.google.com/calendar/render?action=TEMPLATE&text=";
        $ticket_id_subject= '[' . __($advancedSettings['ticket_label_alice'][1], 'wp-support-plus-responsive-ticket-system').' '.$advancedSettings['wpsp_ticket_id_prefix'] . $ticket->id . '] ' . stripcslashes(htmlspecialchars_decode($ticket->subject, ENT_QUOTES));
        $subject=rawurlencode($ticket_id_subject);
        $url=$base.$subject;
        ?>
        <a class="btn btn-primary wpsp_ticket_nav_btn" id="wpsp_google_cal_btn" href="<?php echo $url;?>" target="_blank" title="<?php _e('Create Google Calender Event for this ticket', 'wpsp-google-sync'); ?>" ><?php _e('Calender Event', 'wpsp-google-sync'); ?></a>
        
        <?php
    }
}
?>