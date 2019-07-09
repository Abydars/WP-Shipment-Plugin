<?php
if (!defined('ABSPATH')) exit;

/**
 * Check nonce
 */
if( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce']) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$uploadedfile = $_FILES['img'];
$upload_overrides = array( 'test_form' => false );
$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

$url = $movefile['url'];
$movefile['url'] = $url;

echo json_encode($movefile);
