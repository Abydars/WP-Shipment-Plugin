<?php
final class WPSPConditionalAgentAssignBackend {
    
    function loadScripts(){
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script('wpsp_caa_admin', WPSP_CAA_URL . 'asset/js/admin.js?version='.WPSP_VERSION);
        wp_enqueue_style('wpsp_caa_admin', WPSP_CAA_URL . 'asset/css/admin.css?version='.WPSP_VERSION);
        
        $localize_script_data=array(
            'wpsp_ajax_url'=>admin_url( 'admin-ajax.php' ),
            'insert_all_required'=>__('Please Enter all required fields','wpsp-caa'),
            'insert_field_label'=>__('Please insert Field Label','wpsp-caa'),
            'sure_to_delete'=>__('Are you sure to delete this field?','wpsp-caa')
        );
        wp_localize_script( 'wpsp_caa_admin', 'wpsp_caa_data', $localize_script_data );
        
    }
    
    function custom_menu_page(){
        add_submenu_page( 'wp-support-plus', 'WP Support Plus Conditional Agent Assign', __('Conditional Agent Assign','wpsp-caa'), 'manage_options', 'wpsp-caa', array($this,'caa_submenu') );
    }
    
    function caa_submenu(){
        wp_enqueue_script('wpce_bootstrap', WCE_PLUGIN_URL . 'asset/js/bootstrap/js/bootstrap.min.js?version='.WPSP_VERSION);
        wp_enqueue_style('wpce_bootstrap', WCE_PLUGIN_URL . 'asset/js/bootstrap/css/bootstrap.min.css?version='.WPSP_VERSION);
        include WPSP_CAA_DIR.'includes/admin/wpsp-caa-settings.php';
    }
    
    function wpsp_conditional_agent_assign(){
        $wpsp_caa_conditions = get_option( 'wpsp_caa_conditions' );
        if( !$wpsp_caa_conditions ){
            $wpsp_caa_conditions = array();
        }
        if(isset($_POST['wpsp_caa'])){
            $wpsp_caa = $_POST['wpsp_caa'];
            $caa_desc = (isset($_POST['caa_desc']))?$_POST['caa_desc']:'';
            $wpsp_caa['caa_desc'] = $caa_desc;
            $wpsp_caa_conditions[] = $wpsp_caa;
        }
        update_option('wpsp_caa_conditions',$wpsp_caa_conditions);
    }
    
    function wpsp_create_new_ticket_values($values){
        global $current_user, $WPSP_CAA;
        $ass_agent=array();
        $wpsp_caa_conditions = get_option( 'wpsp_caa_conditions' );
        foreach($wpsp_caa_conditions as $key => $condition ){
            if( $WPSP_CAA->isRuleMatch($condition['rules']) ){
                $ass_agent = array_merge( $ass_agent, $condition['agents'] );
            }
        }
        $ass_agent = array_unique($ass_agent);
        if($ass_agent){
            $values['assigned_to'] = implode(',',$ass_agent);
        }
        return $values;
    }
    
    function wpsp_setEditConditional(){
        include( WPSP_CAA_DIR . 'includes/admin/setEditCondtionalAA.php' );
        die();
    }
    
    function setDeleteConditional(){
        include( WPSP_CAA_DIR . 'includes/admin/deleteCondtionalAA.php' );
        die();
    }
    
    function getEditCondtional(){
        include( WPSP_CAA_DIR . 'includes/admin/getEditCondtional.php' );
        die();
    }
    
    function wpsp_add_condition(){
        include( WPSP_CAA_DIR . 'includes/admin/wpsp_add_condition.php' );
        die();
    }
}
?>