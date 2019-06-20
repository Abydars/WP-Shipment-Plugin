<?php
/*
Plugin Name: WPSP Email Pipe Addon
Plugin URI: hztech.biz
description: This plugin works only with WPSP Shipment Plugin.
Version: 1.0
Author: Hztech
Author URI: hztech.biz
*/

include 'inc/email-parser.php';

if ( ! class_exists( 'WPSP_EmailPipe' ) ) {
	class WPSP_EmailPipe
	{
		public function __construct()
		{
			add_action( 'wp_ajax_wpsp_parse_email_pipe', [ $this, 'wpsp_parse_email_pipe' ] );
			add_action( 'wp_ajax_nopriv_wpsp_parse_email_pipe', [ $this, 'wpsp_parse_email_pipe' ] );
		}

		function wpsp_parse_email_pipe()
		{
			$content = $_REQUEST['content'];
			$error   = false;

			if ( isset( $_REQUEST['debug'] ) ) {
				$content = file_get_contents( dirname( __FILE__ ) . '/sample.txt' );
			}

			$parser = new WPSP_EmailParser( $content );
			$from   = $this->get_string_between( trim( $parser->getHeader( 'From' ) ), '<', '>' );
			$lines  = trim( $this->get_string_between( $parser->getPlainBody(), '--wpsp--', '--wpsp--' ) );

			if ( ! empty( $lines ) ) {

				$customer = WPSP_Customer::get_customer_by_email( $from );

				if ( ! empty( $customer ) ) {

					$lines = array_map( function ( $line ) {
						return array_map( function ( $kv ) {
							return trim( $kv );
						}, explode( ':', $line ) );
					}, explode( PHP_EOL, $lines ) );

					$data = [
						'shipping_date' => date( 'Y-m-d' ),
						'customer'      => $customer->ID
					];

					$keys         = [
						'from'    => 'from',
						'to'      => 'to',
						'carrier' => 'carrier',
						'level'   => 'shipping_method',
						'package' => 'package_type',
						'weight'  => 'packages[0][weight]',
						'length'  => 'packages[0][length]',
						'width'   => 'packages[0][width]',
						'height'  => 'packages[0][height]',
						'pickup'  => 'pickup_date',
						'ticket'  => 'ticket_id',
						'date'    => 'shipping_date'
					];
					$non_required = [ 'pickup_date', 'ticket_id' ];
					$carrier      = false;

					foreach ( $lines as $line ) {
						if ( $line[0] == 'carrier' ) {
							$carrier = $line[1];
						}
					}

					foreach ( $lines as $line ) {
						$key = $line[0];

						if ( isset( $keys[ $line[0] ] ) ) {
							$key = $keys[ $key ];
						}

						$data[ $key ] = apply_filters( "wpsp_email_piping_field_value_{$carrier}", $line[1], $key );
					}

					$data['creator_id'] = $customer->ID;
					$data['_wpnonce']   = wp_create_nonce( 'wpsp_save_label' );
					$data['action']     = 'save_label';

					if ( ! empty( $data['from'] ) ) {
						$from_address = WPSP_Address::get_address_by_code( $data['from'] );

						if ( $from_address ) {
							$data['from'] = $from_address['id'];
						} else {
							$error = __( "From address code not found", WPSP_LANG );
						}
					} else {
						$data['from'] = WPSP_Address::get_default_customer_address( $customer->ID );
					}

					if ( ! empty( $data['to'] ) ) {
						$to_address = WPSP_Address::get_address_by_code( $data['to'] );

						if ( $to_address ) {
							$data['to'] = $to_address['id'];
						} else {
							$error = __( "To address code not found", WPSP_LANG );
						}
					}

					foreach ( $keys as $key ) {
						if ( empty( $data[ $key ] ) && ! in_array( $key, $non_required ) ) {
							$error = __( "{$key} is required", WPSP_LANG );
							break;
						}
					}

					if ( $error === false ) {
						$response = $this->request( admin_url( 'admin-ajax.php' ), $data );
						$response = json_decode( $response, true );
					} else {
						$response = [
							'status'  => false,
							'message' => $error
						];
					}

					if ( $response['status'] == false ) {
						$blogname = get_bloginfo( 'name' );
						$text     = "Error: {$response['message']}";

						wp_mail( $from, "{$blogname} - Shipment Failed", $text );
					}

					header( 'Content-Type: application/json' );
					echo json_encode( $response );
				}
			}
			die;
		}

		private function get_string_between( $string, $start, $end )
		{
			$string = ' ' . $string;
			$ini    = strpos( $string, $start );
			if ( $ini == 0 ) {
				return '';
			}
			$ini += strlen( $start );
			$len = strpos( $string, $end, $ini ) - $ini;

			return substr( $string, $ini, $len );
		}

		private function request( $url, $data )
		{
			$ch = curl_init();

			curl_setopt( $ch, CURLOPT_URL, $url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HEADER, false );
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $data );

			$output = curl_exec( $ch );

			curl_close( $ch );

			return $output;
		}
	}
}

new WPSP_EmailPipe();