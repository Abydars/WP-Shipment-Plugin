<?php
/*
Plugin Name: WP Twilio Manager
Plugin URI: hztech.biz
description: Manage/Use Twilio action
Version: 1.0
Author: Hztech
Author URI: hztech.biz
*/

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
			add_action( 'init', array( $this, 'cron_jobs' ) );
			add_action( 'init', array( $this, 'fxr' ) );
			add_action( 'admin_menu', array( $this, 'add_menus' ) );
			add_action( 'admin_init', array( $this, 'actions' ) );
		}

		function actions()
		{
			if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'wptm_save_settings' ) ) {
				$not = [ '_wpnonce' ];

				foreach ( $_POST as $k => $v ) {
					if ( ! in_array( $k, $not ) ) {
						update_option( $k, $v );
					}
				}
			}
		}

		function add_menus()
		{
			add_options_page( __( 'Twilio Settings', WPSP_LANG ), __( 'Twilio Settings', WPSP_LANG ), 'manage_options', 'wptm_settings', array(
				$this,
				'settings'
			) );
		}

		function settings()
		{
			include dirname( __FILE__ ) . '/inc/templates/settings.php';
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
			if ( isset( $_GET['faxsid'] ) ) {
				$twilio = new WPTM_FaxManager();

				echo '<pre>';
				var_dump( $twilio->getFaxDetails( $_GET['faxsid'] ) );
				exit;
			}

			if ( isset( $_GET['fxr'] ) ) {

				$twilio = new WPTM_FaxManager();

				$file_path  = apply_filters( 'wpsp_file_dir', 'test-fax.pdf' );
				$attachment = apply_filters( 'wpsp_file_url', 'test-fax.pdf' );

				WPSP_PdfHelper::generate( "Test Fax!", $file_path );

				$f = $twilio->sendRealFax( WPTM_TWILIO_NUMBER, $attachment );

				echo '<pre>';
				var_dump( $f );
				exit;
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

		public static function get_option( $key, $default = false )
		{
			$defaults = [];
			$value    = get_option( $key );

			if ( empty( $value ) && ! empty( $defaults[ $key ] ) ) {
				$value = $defaults[ $key ];
			}

			return empty( $value ) ? $default : $value;
		}
	}
}

if ( class_exists( 'WPSP' ) ) {
	$loader = new WPTM_Twilio();

	register_activation_hook( __FILE__, array( $loader, 'activation' ) );
}