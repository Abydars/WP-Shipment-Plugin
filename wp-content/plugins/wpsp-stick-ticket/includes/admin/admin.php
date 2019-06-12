<?php
final class WPSPStickTicketBackend {
    
    function loadScripts(){
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script('wpsp_stick_admin', WPSP_STICK_URL . 'asset/js/admin.js?version='.WPSP_VERSION);
        wp_enqueue_style('wpsp_stick_admin', WPSP_STICK_URL . 'asset/css/admin.css?version='.WPSP_VERSION);
        
        $localize_script_data=array(
            'wpsp_ajax_url'=>admin_url( 'admin-ajax.php' ),
            'insert_job_label'=>__('Please insert Job Label','wpsp-stick-ticket'),
            'insert_field_label'=>__('Please insert Field Label','wpsp-stick-ticket')
        );
        wp_localize_script( 'wpsp_stick_admin', 'wpsp_stick_data', $localize_script_data );
        
    }
    
    function custom_menu_page(){
        add_submenu_page( 'wp-support-plus', 'WP Support Plus Stick Ticket', __('Stick Ticket','wpsp-stick-ticket'), 'manage_options', 'wpsp-stick-ticket', array($this,'stick_ticket_submenu') );
    }
    
    function stick_ticket_submenu(){
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script('wpce_bootstrap', WCE_PLUGIN_URL . 'asset/js/bootstrap/js/bootstrap.min.js?version='.WPSP_VERSION);
        wp_enqueue_style('wpce_bootstrap', WCE_PLUGIN_URL . 'asset/js/bootstrap/css/bootstrap.min.css?version='.WPSP_VERSION);
        include WPSP_STICK_DIR.'includes/admin/customize-settings.php';
    }
    
    function wpsp_getstickticket(){
        include( WPSP_STICK_DIR . 'includes/admin/wpsp_getstickticket.php' );
        die();
    }
    
    function wpsp_after_th_in_getticketsbyfilter(){
        $cu=wp_get_current_user();
        $stickTicketSettings=get_option( 'wpsp_stick_ticket_settings' );
        if (($cu->has_cap('manage_support_plus_ticket') && $stickTicketSettings['wpsp_allow_user_to_stick_ticket']==0) || ($cu->has_cap('manage_options') && ( $stickTicketSettings['wpsp_allow_user_to_stick_ticket']==1)) ){
            ?><th><?php echo __(' ','wpsp-stick-ticket')?></th><?php
        }
    }
    
    function wpsp_add_td( $ticket, $tr = 'default' ){
        $stick_ticket=get_option('wpsp_stick_ticket_id');
        $setting=get_option( 'wpsp_stick_ticket_settings' );
        $cu=wp_get_current_user();
        $usermeta = get_user_meta($cu->ID,'wpsp_stick_ticket',TRUE);
        
        if(!$usermeta){
            $usermeta = array();
        }
        
        $td_class = '';
        if($tr!='stick'){
            $td_class='wpsp_sticktd';
        }
        
        if($ticket->active==1){
            if($cu->has_cap('manage_options')){
                if($stick_ticket){
                    if(array_search($ticket->id, $stick_ticket)> -1){
                        ?><td onmouseover='link=false;' onmouseout='link=true;' class="<?php echo $td_class;?>">
                            <?php if($tr=='stick'):?>
                            <img alt="unstick" title="Unstick" style="cursor: pointer" src="<?php echo WPSP_STICK_URL.'asset/images/unstickimg.png';?>" onclick="wpsp_stickticket(<?php echo $ticket->id ?>)">
                            <?php endif;?>
                        </td><?php
                    }else{
                        ?><td onmouseover='link=false;' onmouseout='link=true;' class="<?php echo $td_class;?>"><img alt="stick" title="Stick" style="cursor: pointer" src="<?php echo WPSP_STICK_URL.'asset/images/unstickimg.png';?>" onclick="wpsp_stickticket(<?php echo $ticket->id ?>)"></td><?php
                    }
                }else{
                    ?><td onmouseover='link=false;' onmouseout='link=true;' class="<?php echo $td_class;?>"><img alt="stick" title="Stick" style="cursor: pointer" src="<?php echo WPSP_STICK_URL.'asset/images/unstickimg.png';?>" onclick="wpsp_stickticket(<?php echo $ticket->id ?>)"></td><?php
                }
            }
            if($cu->has_cap('manage_support_plus_ticket') && !$cu->has_cap('manage_options') && $setting['wpsp_allow_user_to_stick_ticket']==0){
                if(!empty($usermeta)){
                    if(array_search($ticket->id, $usermeta)> -1){
                        ?><td onmouseover='link=false;' onmouseout='link=true;' class="<?php echo $td_class;?>">
                            <?php if($tr=='stick'):?>
                            <img alt="unstick" title="Unstick" style="cursor: pointer" src="<?php echo WPSP_STICK_URL.'asset/images/unstickimg.png';?>" onclick="wpsp_stickticket(<?php echo $ticket->id ?>)">
                            <?php endif;?>
                          </td><?php
                    }else{
                        ?><td onmouseover='link=false;' onmouseout='link=true;' class="<?php echo $td_class;?>"><img alt="stick" title="Stick" style="cursor: pointer" src="<?php echo WPSP_STICK_URL.'asset/images/unstickimg.png';?>" onclick="wpsp_stickticket(<?php echo $ticket->id ?>)"></td><?php
                    }
                }else{
                    ?><td onmouseover='link=false;' onmouseout='link=true;' class="<?php echo $td_class;?>"><img alt="stick" title="Stick" style="cursor: pointer" src="<?php echo WPSP_STICK_URL.'asset/images/unstickimg.png';?>" onclick="wpsp_stickticket(<?php echo $ticket->id ?>)"></td><?php
                }
            }
        }
    }
        
    function wpsp_before_ticket_list($filter){
        global $cu;
        $cu=wp_get_current_user();
        $usermeta=get_user_meta($cu->ID,'wpsp_stick_ticket',true);
        $stick_ticket_id=get_option('wpsp_stick_ticket_id');
        if($usermeta || $stick_ticket_id){
            include( WPSP_STICK_DIR . 'includes/admin/wpsp_stickticketlist.php' );
        }
    }
    
    function wpsp_before_ticket_list_frontend(){
        global $cu;
        $cu=wp_get_current_user();
        $usermeta=get_user_meta($cu->ID,'wpsp_stick_ticket',true);
        $stick_ticket_id=get_option('wpsp_stick_ticket_id');
        if($usermeta || $stick_ticket_id){
            include( WPSP_STICK_DIR . 'includes/admin/wpsp_frontstickticketlist.php' );
        }
    }
    
    function wpsp_getstickticketfront(){
        include( WPSP_STICK_DIR . 'includes/admin/wpsp_getstickticket.php' );
        die();
    }
    
    function wpsp_setsticksetting(){
        include( WPSP_STICK_DIR . 'includes/admin/wpsp_setsticksetting.php' );
        die();
    }
}
?>