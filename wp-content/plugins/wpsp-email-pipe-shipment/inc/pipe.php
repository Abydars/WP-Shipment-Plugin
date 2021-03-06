#!/usr/local/bin/php71 -q
<?php

$fd    = fopen( "php://stdin", "r" );
$email = ""; // This will be the variable holding the data.

while ( ! feof( $fd ) ) {
	$email .= fread( $fd, 1024 );
}

fclose( $fd );

require_once dirname( __FILE__ ) . '/../../../../wp-load.php';

$params = [
	'action'  => 'wpsp_parse_email_pipe',
	'content' => $email
];

$ch = curl_init();

curl_setopt( $ch, CURLOPT_URL, admin_url( 'admin-ajax.php' ) );
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
curl_setopt( $ch, CURLOPT_HEADER, false );
curl_setopt( $ch, CURLOPT_POST, 1 );
curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );

$output = curl_exec( $ch );

curl_close( $ch );