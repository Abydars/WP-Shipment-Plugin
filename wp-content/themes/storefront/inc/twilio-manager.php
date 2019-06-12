<?php

require(dirname(__FILE__) . '/../Twilio/autoload.php');
use Twilio\Rest\Client;

class TwilioManager {
	
	private $client;
	private $number;
	
	public function __construct() {
		
		$sid = 'AC1d1c3e14dc48997f09ef0d7c3917984c';
		$token = '22703760acf214c9d5a2d3252f4f3379';
		
		$this->number = '+17173245684';
		$this->client = new Client($sid, $token);
	}
	
	public function sendFax($to, $doc) {
		$options = array(
		  "from" => $this->number
		);
		
		$fax = $this->client->fax->v1->faxes->create($to, $doc, $options);
		
		return $fax;
	}
	
	public function getFax($faxsid) {
		
		$attach_id = false;
		$fax = $this->client->fax->v1->faxes( $faxsid )->fetch();
		
		if($fax->status == 'received') {
			$url = $fax->mediaUrl;

			$ch = curl_init();
			
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

			$d = curl_exec($ch);
			
			curl_close($ch);
			
			if(preg_match('#Location: (.*)#', $d, $r)) {
				$l = trim($r[1]);
				
				$attach_id = $this->put_contents($l);
			} else {
				$attach_id = $this->put_contents_by_data($d);
			}
		}
		return $attach_id;
	}
	
	private function put_contents($l) {
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $l);

		$data = curl_exec($ch);
		
		curl_close($ch);
		
		return $this->put_contents_by_data($data);
	}

	private function put_contents_by_data($data) {
		global $wpdb;
		
		$upload_dir = wp_upload_dir();
		$path =  $upload_dir["path"] . "/fax-".date('Y-m-d h-i-s').".pdf";
		
		file_put_contents($path, $data);
		
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
		
		$this->addTicketAttachment($path, $attach_id, $filetype['type']);
		
		return $attach_id;
	}
	
	private function addTicketAttachment($path, $attach_id, $type) {
		global $wpdb;
		
		$wpdb->insert($wpdb->prefix . "wpsp_attachments", array(
			"filepath" => $path,
			"id" => $attach_id,
			"filename" => basename($path),
			"filetype" => $type,
			"fileurl" => wp_get_attachment_url($attach_id),
			"download_key" => md5($attach_id)
		));
	}
	
}