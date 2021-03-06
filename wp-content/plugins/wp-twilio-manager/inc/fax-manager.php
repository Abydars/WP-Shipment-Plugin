<?php
/*
 * TwilioManager
 */
require( dirname( __FILE__ ) . '/Twilio/autoload.php' );

use Twilio\Rest\Client;

if ( ! class_exists( 'WPTM_FaxManager' ) ) {
	class WPTM_FaxManager
	{

		private $client;
		private $number;

		public function __construct()
		{

			$sid   = WPTM_Twilio::get_option( 'twilio_sid' );
			$token = WPTM_Twilio::get_option( 'twilio_token' );

			$this->number = WPTM_TWILIO_NUMBER;
			$this->client = new Client( $sid, $token );
		}

		public function sendFax( $to, $doc )
		{
			$fax_queue = WPTM_FaxQueue::get_instance();

			return $fax_queue->add( $to, $doc );
		}

		public function sendRealFax( $to, $doc )
		{
			$options = array(
				"from" => $this->number
			);

			$fax = $this->client->fax->v1->faxes->create( $to, $doc, $options );

			return $fax;
		}

		public function getFax( $faxsid )
		{

			$attach_id = false;
			$fax       = $this->client->fax->v1->faxes( $faxsid )->fetch();

			if ( $fax->status == 'received' || $fax->status == 'delivered' ) {
				$url = $fax->mediaUrl;

				$ch = curl_init();

				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_URL, $url );
				curl_setopt( $ch, CURLOPT_HEADER, true );
				curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, false );

				$d = curl_exec( $ch );

				curl_close( $ch );

				if ( preg_match( '#Location: (.*)#', $d, $r ) ) {
					$l = trim( $r[1] );

					$attach_id = $this->put_contents( $l );
				} else {
					$attach_id = $this->put_contents_by_data( $d );
				}
			}

			return $attach_id;
		}

		public function getFaxDetails( $faxsid )
		{

			$attach_id = false;
			$fax       = $this->client->fax->v1->faxes( $faxsid )->fetch();

			return $fax;
		}

		private function put_contents( $l )
		{

			$ch = curl_init();

			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_URL, $l );

			$data = curl_exec( $ch );

			curl_close( $ch );

			return $this->put_contents_by_data( $data );
		}

		private function put_contents_by_data( $data )
		{
			global $wpdb;

			$upload_dir = wp_upload_dir();
			$path       = $upload_dir["path"] . "/fax-" . date( 'Y-m-d h-i-s' ) . ".pdf";

			file_put_contents( $path, $data );

			$filetype = wp_check_filetype( basename( $path ), null );

			$attachment = array(
				'guid'           => $upload_dir['url'] . '/' . basename( $path ),
				'post_mime_type' => $filetype['type'],
				'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $path ) ),
				'post_content'   => '',
				'post_status'    => 'inherit'
			);

			$attach_id = wp_insert_attachment( $attachment, $path );

			require_once( ABSPATH . 'wp-admin/includes/image.php' );

			$attach_data = wp_generate_attachment_metadata( $attach_id, $path );
			wp_update_attachment_metadata( $attach_id, $attach_data );

			$this->addTicketAttachment( $path, $attach_id, $filetype['type'] );

			return $attach_id;
		}

		private function addTicketAttachment( $path, $attach_id, $type )
		{
			global $wpdb;

			$wpdb->insert( $wpdb->prefix . "wpsp_attachments", array(
				"filepath" => $path,
				"id"       => $attach_id,
				"filename" => basename( $path ),
				"filetype" => $type
			) );
		}
	}
}