<?php
/*
Plugin Name: WP Twilio Manager
Plugin URI: hztech.biz
description: Manage/Use Twilio action
Version: 1.0
Author: Hztech
Author URI: hztech.biz
*/

define( 'WPTM_TWILIO_SID', 'AC1d1c3e14dc48997f09ef0d7c3917984c' );//AC1d1c3e14dc48997f09ef0d7c3917984c
define( 'WPTM_TWILIO_TOKEN', '22703760acf214c9d5a2d3252f4f3379' );//22703760acf214c9d5a2d3252f4f3379
define( 'WPTM_DB_VERSION', '0.0.1' );
define( 'WPTM_TWILIO_NUMBER', '+17177757281' );//17173245684
define( 'WPTM_TWILIO_STANDARD_NUMBER', WPTM_TWILIO_NUMBER );//17173245684
define( 'WPTM_TWILIO_SPECIAL_NUMBER', '+17179005616' );//17173245684
define( 'WPTM_TWILIO_FAX_MAX_TRIES', 5 );
define( 'WPTM_TWILIO_FAX_TRY_DURATION', ( ( 1000 * 60 ) * 3 ) ); //3 mins in ms
define( 'WPTM_TWILIO_LOG_DIR', WP_CONTENT_DIR . '/uploads/logs/wptm/' ); //3 mins in ms

require 'inc/fax-manager.php';
require 'inc/fax-queue.php';

if ( ! class_exists( 'WPTM_Twilio' ) ) {

	class WPTM_Twilio
	{
		public function __construct()
		{
			add_action( 'init', array( $this, 'handle_fax' ) );
			add_action( 'init', array( $this, 'cron_jobs' ) );
			add_action( 'init', array( $this, 'fxr' ) ); // for testing fax
		}

		function update_cron_timestamp( $type )
		{
			file_put_contents( WPTM_TWILIO_LOG_DIR . "{$type}.txt", "{$type}: " . date( "Y-m-d h:i:s" ) . PHP_EOL, FILE_APPEND );
		}

		public function cron_jobs()
		{
			if ( isset( $_GET['twilio_job'] ) ) {
				$job = $_GET['twilio_job'];

				switch ( $job ) {
					case 'send_queue_faxes':
						$this->send_queue_faxes();
						break;
					case 'track_queued_faxes':
						$this->track_queued_faxes();
						break;
				}
				exit;
			}
		}

		private function track_queued_faxes()
		{
			$fax_queue = WPTM_FaxQueue::get_instance();
			$twilio    = new WPTM_FaxManager;

			$faxes = $fax_queue->getQueuedFaxes();

			foreach ( $faxes as $fax ) {
				$twilio_fax = $twilio->getFaxDetails( $fax->sid );

				echo "<br/>Tracking {$fax->id} ...";

				if ( $twilio_fax->status == 'queued' || $twilio_fax->status == 'sending' ) {
					echo 'Status still ' . $twilio_fax->status . '...';
					continue;
				} else if ( $twilio_fax->status != 'delivered' ) {
					echo 'Status: ' . $twilio_fax->status . '...Put in queue again...';
					$fax_queue->putFaxInQueue( $fax->id );
				} else if ( $twilio_fax->status == 'delivered' ) {
					echo 'Status delivered';
					$fax_queue->doneFax( $fax->id );
				}
			}
		}

		private function send_queue_faxes()
		{
			$fax_queue = WPTM_FaxQueue::get_instance();
			$queues    = $fax_queue->getQueues();

			foreach ( $queues as $queue ) {
				$queued = $fax_queue->getQueuedFaxes( $queue->to );

				if ( empty( $queued ) ) {
					echo "Sending fax to {$queue->to}...<br/>";
					$fax_queue->pop( $queue->to );
				}
			}
		}

		public function fxr()
		{
			//var_dump($this->get_last_ticket(11, date("Y-m-d")));
			if ( isset( $_GET['faxsid'] ) ) {
				$twilio = new WPTM_FaxManager;
				//echo '<pre>';var_dump($twilio->getFaxDetails($_GET['faxsid']));exit;
			}
			if ( isset( $_GET['fxr'] ) ) {

				$twilio     = new WPTM_FaxManager;
				$attachment = 'http://ship4lesslabels.com/wp-content/uploads/wpsp/1503455930_fax-2017-08-17_07-25-33.pdf';
				//$attachment = 'https://ship4lesslabels.com/wp-content/plugins/shipment-form/files/label-31.PNG';
				$pathinfo = pathinfo( $attachment );

				if ( strtolower( $pathinfo["extension"] ) == "png" ) {
					$pdf        = ShipmentPdf::generate( $attachment, "", "Label", "image" );
					$attachment = $pdf["url"];
				}
				$pdf        = ShipmentPdf::generate( "Test Fax" );
				$attachment = $pdf["url"];

				$f = $twilio->sendFax( WPTM_TWILIO_NUMBER, $attachment );//+17176869696
				//$f = $twilio->sendFax('+17177861420', $attachment);//+17176869696
				//$f = $twilio->sendFax('+17177861420', $attachment);//+17176869696

				echo '<pre>';
				var_dump( $f );
				exit;
			}
		}

		public function handle_fax()
		{
			if ( isset( $_GET['fxrequest'] ) ) {

				if ( ! isset( $_GET['debug'] ) ) {
					header( "content-type: text/xml" );
					echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
				}
				?>

				<?php

				if ( isset( $_REQUEST['FaxSid'] ) ) {
					$faxsid      = $_REQUEST['FaxSid'];
					$from        = $_REQUEST['From'];
					$to          = $_REQUEST['To'];
					$is_standard = $to == WPTM_TWILIO_STANDARD_NUMBER;

					file_put_contents( dirname( __FILE__ ) . '/logs/received/' . $from . '.txt', json_encode( $_REQUEST ) );

					$users     = get_users();
					$from_user = false;

					foreach ( $users as $user ) {
						$fax = get_field( "fax_number", "user_" . $user->id );
						$fax = '+' . $fax;
						if ( ! empty( $fax ) && $fax == $from ) {
							$from_user = $user;
							break;
						}
					}

					$twilio    = new WPTM_FaxManager;
					$attach_id = $twilio->getFax( $faxsid );

					if ( $attach_id ) {

						$attachment = get_attached_file( $attach_id );

						if ( $attachment ) {
							$headers = array(
								'Content-Type: text/html; charset=UTF-8',
								'From: ' . get_bloginfo( 'name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>'
							);
							//wp_mail('support@pinnaclemetalcraft.com', "New Fax from {$from}", "FaxSid: {$faxsid}<br/>From {$from}<br/>To: {$to}", $headers, $attachment);
						}

						$create_new_ticket = true;
						$last_ticket       = false;

						if ( $from_user && isset( $from_user->id ) ) {
							$last_ticket = $this->get_last_ticket( $from_user->id, date( "Y-m-d" ) );

							if ( $last_ticket ) {
								$create_new_ticket = false;
							}
						}

						$from_id = ( ( $from_user && isset( $from_user->id ) ) ? $from_user->id : '' );

						if ( $create_new_ticket ) {
							$params = array(
								"subject"            => $faxsid,
								"description"        => "Ticket via Fax, From: {$from}; To: {$to}",
								"ckeditor_enabled"   => "1",
								"category"           => $is_standard ? "1" : "2", // General or Special
								"priority"           => $is_standard ? "Normal" : "High",
								"desc_attachment[0]" => $attach_id,
								"create_ticket_type" => "0",
								"user_id"            => $from_id,
								"type"               => "user",
								"guest_name"         => "",
								"guest_email"        => "",
								"action"             => "createNewTicket",
								"pipe"               => 1,
								"nonce"              => wp_create_nonce()
							);

							$ch = curl_init();

							$url = admin_url( "admin-ajax.php" );
							//$url = 'https://ship4lesslabels.com/support-tickets/?page=tickets&section=create-ticket';

							curl_setopt( $ch, CURLOPT_URL, $url );
							curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
							curl_setopt( $ch, CURLOPT_HEADER, false );
							curl_setopt( $ch, CURLOPT_POST, 1 );
							curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );

							$output = curl_exec( $ch );

							curl_close( $ch );

						} else {
							$params = array(
								"reply_body"         => "Replied via Fax",
								//"reply_ticket_status" => "open",
								//"reply_ticket_category" => $is_standard ? "1" : "2", // General or Special
								//"reply_ticket_priority" => $is_standard ? "normal" : "high",
								"desc_attachment[0]" => $attach_id,
								"user_id"            => $from_id,
								"type"               => "user",
								"guest_name"         => "",
								"guest_email"        => "",
								"action"             => "replyTicket",
								"ticket_id"          => $last_ticket->id,
								"notify"             => true,
								"pipe"               => 1,
								"nonce"              => wp_create_nonce( $last_ticket->id )
							);

							$ch = curl_init();

							curl_setopt( $ch, CURLOPT_URL, admin_url( "admin-ajax.php" ) );
							curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
							curl_setopt( $ch, CURLOPT_HEADER, false );
							curl_setopt( $ch, CURLOPT_POST, 1 );
							curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );

							$output = curl_exec( $ch );

							curl_close( $ch );
						}
					}
				}
				?>
                <Response>
                    <Receive/>
                </Response>
				<?php
				die();
			}
		}

		public function activation()
		{
			$installed_ver = get_option( "wptm-db-version" );

			if ( $installed_ver != WPTM_DB_VERSION ) {

				$this->createTable( 'fax_queue', "id mediumint(9) NOT NULL AUTO_INCREMENT,
				`to` varchar(100) DEFAULT '' NOT NULL,
				doc varchar(100) DEFAULT '' NOT NULL,
				status varchar(100) DEFAULT 'queue',
				tries int(9) DEFAULT 0,
				last_tried_timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				sid varchar(100) DEFAULT '',
				PRIMARY KEY  (id)" );

				update_option( "wptm-db-version", WPTM_DB_VERSION );
			}
		}

		private function createTable( $name, $columns )
		{
			global $wpdb;
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$table_name = $wpdb->prefix . $name;
			dbDelta( 'CREATE TABLE ' . $table_name . ' (' . $columns . ');' );
		}

	}
}

if ( class_exists( 'WPSP' ) ) {
	$loader = new WPTM_Twilio();

	register_activation_hook( __FILE__, array( $loader, 'activation' ) );
}