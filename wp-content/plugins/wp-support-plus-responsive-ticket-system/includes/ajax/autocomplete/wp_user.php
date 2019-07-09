<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus;

$s  = isset($_REQUEST['term']) ? sanitize_text_field($_REQUEST['term']) : '';
$s = $s ? '*'.$s.'*' : '';

$args=array(
	 'blog_id'      => $GLOBALS['blog_id'],
	 'orderby'      => 'display_name',
	 'order'        => 'ASC',
	 'search'       => $s,
	 'number'       => '5',
);
$all_users=get_users($args);

$response = array();

foreach ($all_users as $user){
    $response[] = array(
        'uid'   => $user->data->ID,
        'label' => $user->data->display_name
    );
}

if(!$response){
    $response[] = array(
        'uid'   => '0',
        'label' => __('No user found!', 'wp-support-plus-responsive-ticket-system')
    );
}

$response = apply_filters( 'wpsp_autocomplete_wp_user', $response );

echo json_encode($response);
