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
$isError       = false;
$errorMessege  = '';
$attachment_id = 0;

if(!$_FILES){
    $isError=true;
}

if( !$isError ){
    $tempExtension = explode('.', $_FILES['file']['name']);
    $extension     = $tempExtension[count($tempExtension)-1];
    switch ($extension){
        case 'exe':
        case 'php':
        case 'js':
            $isError      = true;
            $errorMessege = __('Error: file format not supported!','wp-support-plus-responsive-ticket-system');
            break;
    }
    if ( preg_match('/php/i', $extension) || preg_match('/phtml/i', $extension) ){
      $isError=true;
      $errorMessege=__('Error: file format not supported!','wp-support-plus-responsive-ticket-system');
    }
}

if( !$isError && $_FILES['file']['tmp_name']==''){
    $isError      = true;
    $errorMessege = __('Error: file size exceeded allowed limit!','wp-support-plus-responsive-ticket-system');
}

if( !$isError ){

    $upload_dir = wp_upload_dir();
    if (!file_exists($upload_dir['basedir'] . '/wpsp/')) {
        mkdir($upload_dir['basedir'] . '/wpsp/', 0755, true);
    }

    $save_file_name = str_replace(' ','_',$_FILES['file']['name']);
    $save_file_name = str_replace(',','_',$_FILES['file']['name']);
    $save_file_name = explode('.', $save_file_name);

    $extension      = $save_file_name[count($save_file_name)-1];
    unset( $save_file_name[count($save_file_name)-1] );

    $save_file_name = implode('-', $save_file_name);

    $save_file_name = $save_file_name.'.'.$extension;

    $save_directory = $upload_dir['basedir'] . '/wpsp/'.time().'_'.$save_file_name;

    move_uploaded_file( $_FILES['file']['tmp_name'], $save_directory );

    $values=array(
        'filename' => $save_file_name,
        'filepath' => $save_directory,
        'filetype' => $_FILES['file']['type'],
        'active'   => 0
    );
    $wpdb->insert($wpdb->prefix.'wpsp_attachments',$values);
    $attachment_id = $wpdb->insert_id;
    $errorMessege  = __('done','wp-support-plus-responsive-ticket-system');

}

$isError=($isError)?'yes':'no';

$response = array(
    'error'        => $isError,
    'errorMessege' => $errorMessege,
    'id'           => $attachment_id
);

echo json_encode($response);
