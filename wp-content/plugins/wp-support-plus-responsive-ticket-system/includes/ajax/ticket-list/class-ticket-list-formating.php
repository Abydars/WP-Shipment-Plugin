<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Ticket_List_Formating' ) ) :
    
    class WPSP_Ticket_List_Formating {
    
        var $val        = null;
        var $type       = null;
        var $ticket     = null;
    
        public function format( $val, $type, $ticket ){
            
            $this->val          = $val;
            $this->type         = $type;
            $this->ticket       = $ticket;
            
            switch ($type){

                case 'id'   : 
                case 1      :
                case 2      :
                case 4      :
                case 10     : echo $this->val;
                    break;
                
                case 'date_created' :
                case 'date_updated' : $this->date();
                    break;
                
                case 6 : $this->custom_date();
                    break;
                
                case 'status' : $this->status();
                    break;
                
                case 'subject' : $this->subject();
                    break;
                
                case 'category' : $this->category();
                    break;
                
                case 'priority' : $this->priority();
                    break;
                
                case 'raised_by' : $this->raised_by();
                    break;
                
                case 'user_type' : $this->user_type();
                    break;
                
                case 'created_agent'    : 
                case 'assigned_agent'   : $this->agent_list();
                    break;
                
                case 3  : $this->checkbox();
                    break;
                    
                default : do_action( 'wpsp_print_ticket_list_custom_field', $this );
                    break;
            }
            
        }
        
        /**
         * Print subject
         */
        public function subject(){
            
            $allowed_chars  = apply_filters( 'wpsp_list_subject_allowed_chars', 25 );
						$wpsp_ticket_list_advanced_settings = get_option('wpsp_ticket_list_advanced_settings');
						$allowed_chars = isset($wpsp_ticket_list_advanced_settings['sub_char_length']) ? $wpsp_ticket_list_advanced_settings['sub_char_length'] : 25;
            $dots           = '';
            $str            = stripslashes($this->val);
            
            if( strlen($this->val) > $allowed_chars ){
                $dots   = '...';
                $str    = substr( stripslashes($this->val) , 0, $allowed_chars );
            }
            
            echo '<span data-toggle="tooltip" data-placement="top" title="'.htmlspecialchars( stripslashes($this->val) , ENT_QUOTES).'">'.$str.$dots.'</span>';
        }

        /**
         * Print user type
         */
        function user_type(){
            echo ucfirst($this->val);
        }
        
        /**
         * Print status
         */
        function status(){
            
            global $wpdb;
            $status = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_custom_status where id=".$this->val );
						$custom_status_localize = get_option('wpsp_custom_status_localize');
            ?>
            <span class="wpsp_admin_label" style="background-color:<?php echo $status->color?>;"><?php echo $custom_status_localize['label_'.$this->val]?></span>
            <?php
            
        }
        
        /**
         * Print priority
         */
        public function priority(){
            
            global $wpdb;
            $priority = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_custom_priority where id=".$this->val );
						$custom_priority_localize = get_option('wpsp_custom_priority_localize');
            ?>
            <span class="wpsp_admin_label" style="background-color:<?php echo $priority->color?>;"><?php echo $custom_priority_localize['label_'.$this->val];?></span>
            <?php
        }
        
        /**
         * Print priority
         */
        public function category(){
            
            global $wpdb;
            $category_name = $wpdb->get_var( "select name from {$wpdb->prefix}wpsp_catagories where id=".$this->val );
						$custom_category_localize = get_option('wpsp_custom_category_localize');
            echo $custom_category_localize['label_'.$this->val];
        }

        /**
         * Print raised by user
         */
        function raised_by(){
            
            global $wpdb;
            $wpsp_ticket_list_advanced_settings = get_option('wpsp_ticket_list_advanced_settings');
            $user_name = $wpdb->get_var("select guest_name from {$wpdb->prefix}wpsp_ticket where id=".$this->ticket->ID);
						$allowed_chars = isset($wpsp_ticket_list_advanced_settings['raised_char_length']) ? $wpsp_ticket_list_advanced_settings['raised_char_length'] : 25;
            $allowed_chars  = apply_filters( 'wpsp_list_raised_by_allowed_chars',$allowed_chars  );
					  
						$dots           = '';
            $str            = stripslashes($user_name);
            
            if( strlen($user_name) > $allowed_chars ){
                $dots   = '...';
                $str    = substr( stripslashes($user_name) , 0, $allowed_chars );
            }
            
            echo '<div data-toggle="tooltip" data-placement="top " title="'.htmlspecialchars( stripslashes($user_name) , ENT_QUOTES).'">'.get_avatar( $this->val, 20 ).' '.$str.$dots.'</div>';
          
        }
        
        /**
         * Print assigned agents and agent list related fields
         */
        function agent_list(){
            
            $agents = explode(',', $this->val);
            
            if(count($agents) == 1 && $agents[0] == 0 ){
                
                _e('None','wp-support-plus-responsive-ticket-system');
                
            } else {
                
                $html = array();
                foreach ( $agents as $agent_id ){
                    $agent  = get_userdata($agent_id);
                    $html[] = $agent->display_name;
                }
                echo implode(', ', $html);
                
            }
            
        }
        
        /**
         * Print check-box representation
         */
        function checkbox(){
            
            $checkboxes = explode('|||', $this->val);
            $checkboxes = implode(', ', $checkboxes);
            
            echo $checkboxes;
            
        }
        
        /**
         * Print date
         */
        function date(){
            
            global $wpsupportplus;
                
            $date = '';
            
            if($this->val){
                
               $date = date_i18n( $wpsupportplus->functions->get_display_date_format(), strtotime( get_date_from_gmt( $this->val, 'Y-m-d H:i:s') ) ) ;
              
            }
            
            echo $date;
            
        }
        
        /**
         * Print custom date
         */
        function custom_date(){
            
            global $wpsupportplus;
            
            $print_value = '';
            
            if($this->val){
                               
                $print_value = date_i18n( $wpsupportplus->functions->get_custom_date_format(), strtotime( get_date_from_gmt( $this->val, 'Y-m-d H:i:s') ) ) ;

            }
            
            echo $print_value;
            
        }
    
    }
    
endif;