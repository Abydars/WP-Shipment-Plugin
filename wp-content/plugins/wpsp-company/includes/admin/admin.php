<?php
final class WPSPCompanyBackend {
    public function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'loadScripts') );
        add_action( 'admin_menu', array($this,'custom_menu_page') );
    }
	
    function loadScripts(){
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script('wpce_display_company', WPSP_COMP_URL . 'asset/js/admin.js?version='.WPSP_COMP_VERSION);
        wp_enqueue_style('wpce_display_company', WPSP_COMP_URL . 'asset/css/admin.css?version='.WPSP_COMP_VERSION);
        
        $localize_script_data=array(
            'wpsp_ajax_url'=>admin_url( 'admin-ajax.php' ),
            'insert_job_label'=>__('Please insert Job Label','wpsp-customize'),
            'insert_field_label'=>__('Please insert Field Label','wpsp-customize'),
            'sure_to_enable_reply'=>__('Are you sure to enable reply?','wpsp-customize'),
            'insert_disable_reply_message'=>__('Please insert disable reply message','wpsp-customize'),
            'enter_company_name'=>__('Please enter company title','wpsp-company'),
            'select_at_least_one_user'=>__('Please select at least one user','wpsp-company')
        );
        wp_localize_script( 'wpce_display_company', 'display_company_data', $localize_script_data );
    }
        
    function custom_menu_page(){
        add_submenu_page( 'wp-support-plus', 'WP Support Plus Company', __('Company/Usergroup','wpsp-company'), 'manage_options', 'wp-support-plus-company', array($this,'company') );
    }
    
    function company(){
        global $current_user;
        $current_user=wp_get_current_user();
        wp_enqueue_script('wpce_bootstrap', WPSP_COMP_URL . 'asset/js/bootstrap/js/bootstrap.min.js?version='.WPSP_COMP_VERSION);
        wp_enqueue_style('wpce_bootstrap', WPSP_COMP_URL . 'asset/js/bootstrap/css/bootstrap.min.css?version='.WPSP_COMP_VERSION);
        wp_enqueue_script('wpce_display_company', WPSP_COMP_URL . 'asset/js/admin.js?version='.WPSP_COMP_VERSION);
        wp_enqueue_style('wpce_display_company', WPSP_COMP_URL . 'asset/css/display_company.css?version='.WPSP_COMP_VERSION);
        $localize_script_data=array(
                'wpsp_ajax_url'=>admin_url( 'admin.php' ),
                'wpsp_comp_site_url'=>site_url(),
                'plugin_url'=>WPSP_COMP_URL,
                'plugin_dir'=>WPSP_COMP_DIR,
                'enter_company_name'=>__('Please enter company title','wpsp-company')
                
            );
        wp_localize_script( 'wpce_display_company', 'display_company_data', $localize_script_data );
        ?>
         
        <div class="panel panel-primary wpsp_admin_panel">
            <div class="panel-heading">
              <h3 class="panel-title"><?php _e('WP Support Plus Companies','wp-support-plus-responsive');?></h3>
              <span class="wpsp_support_company_admin_welcome"><?php echo __('Welcome','wp-support-plus-responsive').", ".$current_user->display_name;?></span>
            </div>
            <div class="panel-body">
                <?php include( WPSP_COMP_DIR.'includes/admin/company.php' );?>
            </div>
        </div>
        <?php 
    }
 
    function wpsp_create_company(){
        include( WPSP_COMP_DIR.'includes/admin/getCreateCompanyForm.php' );
        die();
    }
    
    function searchRegisteredUsaers(){
        include( WPSP_COMP_DIR.'includes/admin/searchRegisteredUsaers.php' );
        die();
    }
    
    function wpspSelectRegisteredUser(){
        include( WPSP_COMP_DIR.'includes/admin/selectRegisteredUsaers.php' );
        die();
    } 
    
    function setCompanyUser(){
        include( WPSP_COMP_DIR.'includes/admin/setCompanyUser.php' );
        die();
    }
    
    function editCompanyUser(){
        include( WPSP_COMP_DIR.'includes/admin/editCompanyUser.php' );
        die();
    }
 
    function wpsp_insert_company_id_in_ticket($values){
        global $wpdb;
        global $current_user;
        $current_user=wp_get_current_user();
        $user_comp=get_user_meta($current_user->ID,'wpspCompanyUser',true);
        if($user_comp){
            $values['cid']=$user_comp;
        }
        return $values ;
    }
    
    function wpsp_get_ticket_list_where_frontend($where,$customFieldsDropDown,$current_user){
        global $wpdb;
        $advancedSettings=get_option( 'wpsp_advanced_settings' );        
        $where="WHERE ";
        $flagUseWhere=true;    
        if($current_user->has_cap('manage_support_plus_agent') && $current_user->has_cap('manage_support_plus_ticket'))
        {
            $where.="(t.ticket_type=1 OR t.ticket_type=0) ";
        }
        else if(!$current_user->has_cap('manage_support_plus_agent') && $current_user->has_cap('manage_support_plus_ticket'))
        {
            $where.="(t.assigned_to LIKE '%".$current_user->ID."%' OR t.assigned_to='0' OR t.created_by='".$current_user->ID."' OR t.ticket_type=1) ";
        }
        else
        {
            $coleags=array();
            $coleags[]=$current_user->ID;
            $user_companies=get_user_meta($current_user->ID,'wpspUserComapnies',true);
            foreach($user_companies as $user_company){
                $company_users= $wpdb->get_var("select users from {$wpdb->prefix}wpsp_companies where id=".$user_company);
                $coleags=array_merge($coleags,explode(',',$company_users));
            }
            $coleags=array_unique($coleags);
            $coleags=implode(',', $coleags);
            
            $where.="(t.created_by in(".$coleags.") OR t.ticket_type=1 OR t.guest_email='".$current_user->user_email."') ";
        }
        $hideStatus = array();
        $custom_statusses=$wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_status");
        if(isset($_POST['filter_by_status_front']) && $_POST['filter_by_status_front'] != 'all' ) {
                $where .= "AND t.status='" . $_POST['filter_by_status_front'] . "' ";               
        }else if(!empty($advancedSettings['hide_selected_status_ticket_frontend'])){
               foreach($custom_statusses as $custom_status){
                if(is_numeric(array_search($custom_status->id,$advancedSettings['hide_selected_status_ticket_frontend']))){
                        $hideStatus = array_merge($hideStatus,array("'".$custom_status->name."'"));
                }
            }
            $where .= "AND t.status NOT IN (" .implode(',', $hideStatus). ") ";
        }

        if(isset($_POST['filter_by_category_front']) && $_POST['filter_by_category_front']!='all'){
                $where.="AND c.id='".$_POST['filter_by_category_front']."' ";
        }

        $customFieldsDropDown = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsp_custom_fields where field_type=2 OR field_type=4" );
        foreach ($customFieldsDropDown as $field){
                if(isset($_POST['cust'.$field->id]) && $_POST['cust'.$field->id]!='all'){
                    $where.="AND t.cust".$field->id."='".$_POST['cust'.$field->id]."' ";
                }
        }

        if(isset($_POST['filter_by_selection_front'])){
                switch($_POST['filter_by_selection_front'])
                {
                        case 'id':
                                if($_POST['filter_by_search_front']!=''){

                                        //custome fields
                                        $custCondition='';
                                        $customFields = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsp_custom_fields" );
                                        $total_cust_field=$wpdb->num_rows;
                                        if($total_cust_field){
                                                foreach ($customFields as $field){
                                                        $custCondition.="OR t.cust".$field->id." LIKE '%".$_POST['filter_by_search_front']."%' ";
                                                }
                                        }

                                        $where.="AND t.id IN (SELECT DISTINCT t.id from {$wpdb->prefix}wpsp_ticket t INNER JOIN {$wpdb->prefix}wpsp_ticket_thread th ON t.id=th.ticket_id WHERE t.id=".$_POST['filter_by_search_front']." OR t.id LIKE '%".$_POST['filter_by_search_front']."%') ";
                                }
                                break;
                        case 'text':
                                if($_POST['filter_by_search_front']!=''){
                                        //custome fields
                                        $custCondition='';
                                        $customFields = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsp_custom_fields" );
                                        $total_cust_field=$wpdb->num_rows;
                                        if($total_cust_field){
                                                foreach ($customFields as $field){
                                                        $custCondition.="OR t.cust".$field->id." LIKE '%".$_POST['filter_by_search_front']."%' ";
                                                }
                                        }

                                        $where.="AND t.id IN (SELECT DISTINCT t.id from {$wpdb->prefix}wpsp_ticket t INNER JOIN {$wpdb->prefix}wpsp_ticket_thread th ON t.id=th.ticket_id WHERE t.subject LIKE '%".$_POST['filter_by_search_front']."%' OR th.body LIKE '%".$_POST['filter_by_search_front']."%' ".$custCondition.") ";
                                }
                                break;
                        case 'created_by':
                            if($_POST['filter_by_search_front']!=''){
                                $term=esc_attr( $_POST['filter_by_search_front'] );
                                $sql1 = "SELECT * FROM " . $wpdb->base_prefix . "users WHERE 1=1 AND (user_login LIKE '%" . $term . "%' OR user_email LIKE '%" . $term . "%' OR display_name LIKE '%" . $term . "%')";
                                $users = $wpdb->get_results( $sql1 );
                                if(is_array($users) && count($users)>0)
                                {
                                        $user_ids=array();
                                        foreach($users as $user)
                                        {
                                                $user_ids=array_merge($user_ids,array($user->ID));
                                        }
                                        $where.="AND ( t.id IN (SELECT DISTINCT t.id from {$wpdb->prefix}wpsp_ticket t INNER JOIN {$wpdb->prefix}wpsp_ticket_thread th ON t.id=th.ticket_id WHERE t.created_by IN (".implode(",",$user_ids).")) OR ";
                                        $where.="t.id IN (SELECT DISTINCT t.id from {$wpdb->prefix}wpsp_ticket t INNER JOIN {$wpdb->prefix}wpsp_ticket_thread th ON t.id=th.ticket_id WHERE t.guest_name LIKE '%".$_POST['filter_by_search_front']."%' OR t.guest_email LIKE'%".$_POST['filter_by_search_front']."%')) ";
                                }
                                else
                                {
                                        $where.="AND t.id IN (SELECT DISTINCT t.id from {$wpdb->prefix}wpsp_ticket t INNER JOIN {$wpdb->prefix}wpsp_ticket_thread th ON t.id=th.ticket_id WHERE t.guest_name LIKE '%".$_POST['filter_by_search_front']."%' OR t.guest_email LIKE'%".$_POST['filter_by_search_front']."%') ";
                                }
                            }
                            break;
                }
        }         
        $where.="AND t.active='1' "; 
        
        return $where;    
    }
    
    function wpsp_hack_flag_front_for_otherthan_staff_user($flag,$ticket,$current_user){
        global $wpdb;
        $coleags=array();
        $coleags[]=$current_user->ID;
        $user_companies=get_user_meta($current_user->ID,'wpspUserComapnies',true);
        foreach($user_companies as $user_company){
            $company_users= $wpdb->get_var("select users from {$wpdb->prefix}wpsp_companies where id=".$user_company);
            $coleags=array_merge($coleags,explode(',',$company_users));
        }
        $coleags=array_unique($coleags);
        
        if(array_search($ticket->created_by, $coleags)>-1) $flag=true;
        
        return $flag;
    }
    
}
?>