<?php
final class WPSPCompanyFrontend {
    function loadScripts(){
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_style('wpsp_comp_public', WPSP_COMP_URL . 'asset/css/public.css?version='.WPSP_COMP_VERSION);
        
        $localize_script_data=array(
            'wpsp_ajax_url'=>admin_url( 'admin-ajax.php' )
        );
        wp_localize_script( 'wpsp_comp_public', 'wpsp_comp_data', $localize_script_data );
    }
}
?>