<?php
if (!defined('ABSPATH')) exit;

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WPSP_Agent_List_Table extends WP_List_Table {
    
            
    function get_columns() {
        
        $columns = array(
            'username' => __('Username','wp-support-plus-responsive-ticket-system'),
            'name' => __('Name','wp-support-plus-responsive-ticket-system'),
            'email' => __('Email','wp-support-plus-responsive-ticket-system'),
            'role' => __('Role','wp-support-plus-responsive-ticket-system'),
        );
        return $columns;
    }

    function prepare_items() {
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $this->get_agents();
    }
    
    function column_default($item, $column_name) {
        
        switch ($column_name) {
            
            case 'name':
            case 'email':
                return $item->$column_name;
            case 'role':
                $role = '';
                switch ($item->$column_name){
                    case 1: $role = __('Agent','wp-support-plus-responsive-ticket-system');
                        break;
                    case 2: $role = __('Supervisor','wp-support-plus-responsive-ticket-system');
                        break;
                    case 3: $role = __('Administrator','wp-support-plus-responsive-ticket-system');
                        break;
                }
                return $role;
            default:
                return 'NA';
        }
    }
    
    function get_sortable_columns(){
        $sortable_columns = array(
            'username' => array('username', true),
            'name' => array('name', false),
            'email' => array('email', false)
        );
        return $sortable_columns;
    }
    
    function column_username($item) {
        
        $edit_btn = '<span class="dashicons dashicons-edit wpsp_pointer" onclick="wpsp_admin_load_popup(\'get_edit_agent\', '.$item->id.', \''.wp_create_nonce($item->id).'\', \''.$item->name.'\', 300, 200, 63)"></span>';
        $delete_btn = '<span class="dashicons dashicons-trash wpsp_pointer" onclick="wpsp_admin_load_popup(\'get_delete_agent\', '.$item->id.', \''.wp_create_nonce($item->id).'\', \''.__('Delete Agent','wp-support-plus-responsive-ticket-system').'\', 300, 200, 63)"></span>';
        $actions = array(
            1 => $edit_btn,
            'delete' => $delete_btn
        );
        return sprintf('%1$s %2$s', get_avatar( $item->email, 60 ).'<strong>'.$item->username.'</strong>', $this->row_actions($actions));
    }
    
    function get_agents(){
        
        global $wpdb;
        $per_page = 20;
        $current_page = $this->get_pagenum();
        
        $sql = "select SQL_CALC_FOUND_ROWS "
                . "u.ID as id, "
                . "u.user_login as username, "
                . "u.display_name as name, "
                . "u.user_email as email, "
                . "w.role as role "
                . "FROM {$wpdb->prefix}wpsp_users w "
                . "INNER JOIN  {$wpdb->base_prefix}users u ON w.user_id = u.ID ";
        
        /**
         * Where Part
         */
        $where = "WHERE 1=1 ";
        
        if( isset($_REQUEST['role']) && intval($_REQUEST['role']) ){
            $where .= 'AND w.role = '.intval( sanitize_text_field($_REQUEST['role']) ).' ';
        }
        
        if( isset($_REQUEST['s']) && $_REQUEST['s'] != '' ){
            
            $where .= 'AND ( ';
            $keywords = explode( ' ', sanitize_text_field($_REQUEST['s']) );
            $search = array();
            foreach ($keywords as $keyword){
                $search[] = "u.user_login LIKE '%".$keyword."%'";
                $search[] = "u.display_name LIKE '%".$keyword."%'";
                $search[] = "u.user_email LIKE '%".$keyword."%'";
            }
            $where .= implode(' OR ', $search);
            $where .= ' ) ';
        }
        
        /**
         * Order By Part
         */
        $orderby  = 'ORDER BY ';
        
        switch ( $column = isset($_REQUEST['orderby']) ? sanitize_text_field($_REQUEST['orderby']) : 'username'  ){
            case 'username': $orderby .='u.user_login ';
                break;
            case 'name': $orderby .='u.display_name ';
                break;
            case 'email': $orderby .='u.user_email ';
                break;
        }
        $order = isset($_REQUEST['order']) ? sanitize_text_field($_REQUEST['order']).' ' : 'asc ';
        
        /**
         * Limit part
         */
        $limit = 'LIMIT '.$per_page.' OFFSET '.($current_page*$per_page-$per_page) ;
        
        /**
         * Combining Query
         */
        $sql = $sql.$where.$orderby.$order.$limit;
        
        /**
         * Getting results.
         */
        $agents = $wpdb->get_results($sql);
        
        /**
         * Total items that needed for pagination
         */
        $total_items = $wpdb->get_var("SELECT FOUND_ROWS()");
        
        /**
         * Set pagination UI
         */
        $this->set_pagination_args(array(
            'total_items'   => $total_items,
            'per_page'      => $per_page
        ));
        
        return $agents;
    }

}