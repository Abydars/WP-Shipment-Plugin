<?php
final class WPSPTimerFrontend {
    function loadScripts(){
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script('wpsp_timer_public', WPSP_TIMER_URL . 'asset/js/public.js?version='.WPSP_TIMER_VERSION);
        wp_enqueue_style('wpsp_timer_public', WPSP_TIMER_URL . 'asset/css/public.css?version='.WPSP_TIMER_VERSION);
        
        $localize_script_data=array(
            'wpsp_ajax_url'=>admin_url( 'admin-ajax.php' )
        );
        wp_localize_script( 'wpsp_timer_public', 'wpsp_timer_data', $localize_script_data );
    }
}
?>