<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Cron' ) ) :
    
    /**
     * Cron class for WPSP.
     * @class WPSP_Cron
     */
    class WPSP_Cron {
        
        /**
         * Constructor
         */
        public function __construct(){
					
        }
				
				public function daily_check_ticket_status(){
						global $wpsupportplus,$wpdb,$current_user;
						$general_advanced_settings 	= get_option('wpsp_general_settings_advanced');
						$status                    	= isset($general_advanced_settings['status'])? $general_advanced_settings['status']:array();
						$time           						= current_time('mysql', 1);
						if(isset($general_advanced_settings['selected_status_ticket_close']) && !empty($status)){
								
								$close_btn_status = $wpsupportplus->functions->get_close_btn_status();
								$diffDay     = $general_advanced_settings['selected_status_ticket_close'];
								
								$where       = " WHERE status_id IN (". implode(',', $status).") ";
								$where = $where."AND ( TIMESTAMPDIFF(DAY,update_time,UTC_TIMESTAMP()) >= ".$diffDay.")" ;
								$tickets = $wpdb->get_results( "select * from {$wpdb->prefix}wpsp_ticket".$where );
								
								foreach($tickets as $ticket){
									$sql         = "UPDATE ".$wpdb->prefix.'wpsp_ticket'." SET status_id=". $close_btn_status.",update_time='".current_time('mysql', 1)."' ";
									$sql = $sql . " WHERE id =".$ticket->id;
									
									$wpdb->query($sql );
									
									$values = array(
									    'ticket_id'         => $ticket->id,
									    'body'              => $close_btn_status,
									    'is_note'           => '7',
									    'create_time'       => $time
									);
									$wpdb->insert($wpdb->prefix . 'wpsp_ticket_thread', $values);
							}
						}
					}
        
    }
    
endif;