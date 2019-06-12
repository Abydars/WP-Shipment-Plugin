<?php
final class WPSPStickTicketFrontend {
    function loadScripts(){
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script('wpsp_stick_public', WPSP_STICK_URL . 'asset/js/public.js?version='.WPSP_VERSION);
        wp_enqueue_style('wpsp_stick_public', WPSP_STICK_URL . 'asset/css/public.css?version='.WPSP_VERSION);
        
        $localize_script_data=array(
            'wpsp_ajax_url'=>admin_url( 'admin-ajax.php' )
        );
        wp_localize_script( 'wpsp_stick_public', 'wpsp_stick_data', $localize_script_data );
    }
    
    function wpsp_getstickticketfront(){
        include( WPSP_STICK_DIR . 'includes/admin/wpsp_getstickticket.php' );
        die();
    }
    
}
?>