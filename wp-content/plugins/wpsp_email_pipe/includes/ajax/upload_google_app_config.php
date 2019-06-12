<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check nonce
 */
if( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce']) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpdb;
$isError=false;
$errorMessege=__('','wp-support-plus-responsive-ticket-system');
$attachment_id = 0;

if(!$_FILES){
    $isError=true;
}

if( !$isError ){
    $tempExtension=  explode('.', $_FILES['gmail_client_secret']['name']);
    $extension=$tempExtension[count($tempExtension)-1];
    switch ($extension){
        case 'exe':
        case 'php':
        case 'js':
            $isError=true;
            $errorMessege=__('Error: file format not supported!','wp-support-plus-responsive-ticket-system');
            break;
    }
}

if( !$isError && $_FILES['gmail_client_secret']['tmp_name']==''){
    $isError=true;
    $errorMessege=__('Error: file size exceeded allowed limit!','wp-support-plus-responsive-ticket-system');
}

if( !$isError ){
    
    $upload_dir = wp_upload_dir();
    if (!file_exists($upload_dir['basedir'] . '/wpsp/')) {
        mkdir($upload_dir['basedir'] . '/wpsp/', 0755, true);
    }
    
    $save_file_name = str_replace(' ','_',$_FILES['gmail_client_secret']['name']);
    $save_file_name = explode('.', $save_file_name);
    
    $extension      = $save_file_name[count($save_file_name)-1];
    unset( $save_file_name[count($save_file_name)-1] );
    
    $save_file_name = implode('-', $save_file_name);
    
    $save_file_name = preg_replace('/[^A-Za-z0-9\-]/', '', $save_file_name).'.'.$extension;
    
    $save_directory = $upload_dir['basedir'] . '/wpsp/'.time().'_'.$save_file_name;
    $save_url = $upload_dir['baseurl'] . '/wpsp/'.time().'_'.$save_file_name;
    
    move_uploaded_file( $_FILES['gmail_client_secret']['tmp_name'], $save_directory );
    
}

if($save_directory){
    
    $email_piping = $_POST['email_piping'];
    $email_piping['gmail_client_secret'] = $save_directory;
    update_option('wpsp_email_pipe_settings',$email_piping);
}
