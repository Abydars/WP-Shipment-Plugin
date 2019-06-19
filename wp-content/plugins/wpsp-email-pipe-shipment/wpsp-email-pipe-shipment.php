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
			add_action( 'wp_ajax_wpsp_parse_email_pipe', array( $this, 'wpsp_parse_email_pipe' ) );
			add_action( 'wp_ajax_nopriv_wpsp_parse_email_pipe', array( $this, 'wpsp_parse_email_pipe' ) );
		}

		function wpsp_parse_email_pipe()
		{
			file_get_contents( 'email-test.txt', $_REQUEST['content'] );
			//$parser = new WPSP_EmailParser( $_REQUEST['content'] );
			//echo $parser->getPlainBody();
			die;
		}
	}
}

new WPSP_EmailPipe();