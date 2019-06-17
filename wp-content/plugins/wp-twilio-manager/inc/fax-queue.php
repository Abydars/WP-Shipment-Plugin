<?php

if ( ! class_exists( 'WPTM_FaxQueue' ) ) {

	class WPTM_FaxQueue
	{

		const STATUS_QUEUED = 'queued';

		private static $instance;
		private $twilio;

		public static function get_instance()
		{
			if ( ! self::$instance ) {
				self::$instance = new WPTM_FaxQueue();
			}

			return self::$instance;
		}

		public function __construct()
		{
			$this->twilio = new WPTM_FaxManager();
		}

		public function add( $to, $doc )
		{
			global $wpdb;

			$table_name = $wpdb->prefix . 'fax_queue';
			$wpdb->insert( $table_name, array(
				'to'  => $to,
				'doc' => $doc
			) );

			return $wpdb->insert_id;
		}

		public function get( $to )
		{
			global $wpdb;

			$table_name = $wpdb->prefix . 'fax_queue';

			return $wpdb->get_results( "SELECT * FROM {$table_name} WHERE `to` = '{$to}' AND status = 'queue' ORDER BY id ASC" );
		}

		public function getFaxById( $id )
		{
			global $wpdb;

			$table_name = $wpdb->prefix . 'fax_queue';

			return $wpdb->get_row( "SELECT * FROM {$table_name} WHERE id = '{$id}'" );
		}

		public function pop( $to )
		{
			global $wpdb;

			$table_name = $wpdb->prefix . 'fax_queue';
			$queue      = $this->get( $to );

			if ( ! empty( $queue ) ) {
				$fax = $queue[0];

				try {
					$twilio_fax = $this->twilio->sendRealFax( $fax->to, $fax->doc );

					$updated = $wpdb->update( $table_name, array(
						'last_tried_timestamp' => null,
						'tries'                => $fax->tries + 1,
						'status'               => 'queued',
						'sid'                  => $twilio_fax->sid
					), array(
						                          'id' => $fax->id
					                          ) );

				} catch ( Exception $e ) {
					$wpdb->update( $table_name, array(
						'status' => 'failed'
					), array(
						               'id' => $fax->id
					               ) );
					do_action( 'wptm_fax_failed', $fax->id );
				}
			}
		}

		public function putFaxInQueue( $faxid )
		{
			global $wpdb;

			$table_name = $wpdb->prefix . 'fax_queue';
			$fax        = $this->getFaxById( $faxid );
			$status     = 'queue';

			if ( $fax->tries >= WPTM_TWILIO_FAX_MAX_TRIES ) {
				$status = 'failed';
			}

			$wpdb->update( $table_name, array(
				'status' => $status
			), array(
				               'id' => $faxid
			               ) );

			if ( $status == 'failed' ) {
				do_action( 'wptm_fax_failed', $this->getFaxById( $faxid ) );
			}
		}

		public function doneFax( $faxid )
		{
			global $wpdb;

			do_action( 'wptm_fax_success', $this->getFaxById( $faxid ) );

			$table_name = $wpdb->prefix . 'fax_queue';

			$wpdb->update( $table_name, array(
				'status' => 'delivered'
			), array(
				               'id' => $faxid
			               ) );
		}

		public function getQueues()
		{
			global $wpdb;

			$table_name = $wpdb->prefix . 'fax_queue';

			return $wpdb->get_results( "SELECT DISTINCT `to` FROM {$table_name} WHERE status = 'queue'" );
		}

		public function getQueuedFaxes( $to = false )
		{
			global $wpdb;

			$table_name = $wpdb->prefix . 'fax_queue';

			if ( $to ) {
				return $wpdb->get_results( "SELECT * FROM {$table_name} WHERE `to` = '{$to}' AND status = 'queued'" );
			}

			return $wpdb->get_results( "SELECT * FROM {$table_name} WHERE status = 'queued'" );
		}
	}

}