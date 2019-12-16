<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class WPSP_EN_Frontend {

    public function create_new_ticket($ticket_id){

			global $wpdb, $wpsupportplus, $current_user;
			$ticket = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_ticket WHERE id=".$ticket_id);
			$wpsp_en_notification = get_option('wpsp_en_notification');
			foreach ($wpsp_en_notification as $key => $notification) {
				if ( $notification['type'] == 'crt_tkt' ) {
					new WPSP_EN_Mailer( $notification, $ticket );
				}
			}

    }

		public function reply_ticket( $ticket_id, $thread_id ){

			global $wpdb, $wpsupportplus, $current_user;
			$ticket = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_ticket WHERE id=".$ticket_id);
			$wpsp_en_notification = get_option('wpsp_en_notification');
			foreach ($wpsp_en_notification as $key => $notification) {
				if ( $notification['type'] == 'rep_tkt' ) {
					new WPSP_EN_Mailer( $notification, $ticket );
				}
			}

    }

		public function change_status( $ticket_id ){

			global $wpdb, $wpsupportplus, $current_user;
			$ticket = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_ticket WHERE id=".$ticket_id);
			$wpsp_en_notification = get_option('wpsp_en_notification');
			foreach ($wpsp_en_notification as $key => $notification) {
				if ( $notification['type'] == 'cng_sts' ) {
					new WPSP_EN_Mailer( $notification, $ticket );
				}
			}

		}

		public function assign_agent( $ticket_id, $old_assigned, $new_assigned ){

			global $wpdb, $wpsupportplus, $current_user;
			$ticket = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_ticket WHERE id=".$ticket_id);
			$wpsp_en_notification = get_option('wpsp_en_notification');
			foreach ($wpsp_en_notification as $key => $notification) {
				if ( $notification['type'] == 'asgn_agnt' ) {
					new WPSP_EN_Mailer( $notification, $ticket );
				}
			}

		}

		public function delete_ticket( $ticket_id ){

			global $wpdb, $wpsupportplus, $current_user;
			$ticket = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_ticket WHERE id=".$ticket_id);
			$wpsp_en_notification = get_option('wpsp_en_notification');
			foreach ($wpsp_en_notification as $key => $notification) {
				if ( $notification['type'] == 'del_tkt' ) {
					new WPSP_EN_Mailer( $notification, $ticket );
				}
			}

		}

		public function add_note( $ticket_id, $thread_id ){
 
 			global $wpdb, $wpsupportplus, $current_user;
 			$ticket = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_ticket WHERE id=".$ticket_id);
 			$wpsp_en_notification = get_option('wpsp_en_notification');
 			foreach ($wpsp_en_notification as $key => $notification) {
 				if ( $notification['type'] == 'note_tkt' ) {
 					new WPSP_EN_Mailer( $notification, $ticket );
 				}
 			}
 
 		}

}
?>
