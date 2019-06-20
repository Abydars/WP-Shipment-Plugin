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

			if ( isset( $_REQUEST['debug'] ) ) {
				$content = file_get_contents( dirname( __FILE__ ) . '/sample.txt' );
			}

			$parser = new WPSP_EmailParser( $content );
			$from   = $this->get_string_between( trim( $parser->getHeader( 'From' ) ), '<', '>' );
			//$to      = $parser->getTo()[0];
			$lines = trim( $this->get_string_between( $parser->getPlainBody(), '--wpsp--', '--wpsp--' ) );

			if ( ! empty( $lines ) ) {

				$lines = array_map( function ( $line ) {
					return array_map( function ( $kv ) {
						return trim( $kv );
					}, explode( ':', $line ) );
				}, explode( PHP_EOL, $lines ) );
				$data  = [];

				foreach ( $lines as $line ) {
					$data[ $line[0] ] = $line[1];
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
	}
}

new WPSP_EmailPipe();