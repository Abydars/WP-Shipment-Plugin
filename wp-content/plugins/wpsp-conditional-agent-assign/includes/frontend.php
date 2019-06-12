<?php
final class WPSPConditionalAgentAssignFrontend {
    function loadScripts(){
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script('wpsp_caa_public', WPSP_CAA_URL . 'asset/js/public.js?version='.WPSP_VERSION);
        wp_enqueue_style('wpsp_caa_public', WPSP_CAA_URL . 'asset/css/public.css?version='.WPSP_VERSION);
        
        $localize_script_data=array(
            'wpsp_ajax_url'=>admin_url( 'admin-ajax.php' )
        );
        wp_localize_script( 'wpsp_caa_public', 'wpsp_caa_data', $localize_script_data );
    }
}
?>