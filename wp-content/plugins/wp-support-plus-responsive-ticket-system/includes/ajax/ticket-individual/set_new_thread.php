<?php
 if ( ! defined( 'ABSPATH' ) ) {
 	exit; // Exit if accessed directly
 }
 
global $wpdb, $wpsupportplus,$current_user;

$ticket_id  = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : 0 ;
$subject    = isset($_POST['subject']) ? sanitize_text_field($_POST['subject']) : '';
$nonce      = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '' ;
$thread_id  = isset($_POST['thread_id']) ? intval(sanitize_text_field($_POST['thread_id'])):0 ;
$ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

if( !wp_verify_nonce( $nonce, $ticket_id ) || !$thread_id || !$wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'new_thread' ) ){
  die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

if( !$wpsupportplus->functions->get_ticket_id_sequence() ){
    $id = 0;
    do {
          $id = rand(11111111, 99999999);
          $sql = "select id from {$wpdb->prefix}wpsp_ticket where id=" . $id;
          $result = $wpdb->get_var($sql);
    } while ($result);
          $values['id'] = $id;
}

$now=time();
$wpsp_settings_general = get_option('wpsp_settings_general');

$sql = "CREATE TEMPORARY TABLE {$wpdb->prefix}wpsp_temp_table AS SELECT * FROM {$wpdb->prefix}wpsp_ticket WHERE id='" . $ticket_id . "'";
$wpdb->query($sql);  

$sql = "UPDATE {$wpdb->prefix}wpsp_temp_table SET id='".$id."',subject='".$subject."',updated_by='0',status_id='".$wpsupportplus->functions->get_default_status()."',
        cat_id='" . $wpsupportplus->functions->get_default_category() . "',create_time='" . current_time('mysql', 1) . "',update_time='" . current_time('mysql', 1) . "',
        priority_id='".$wpsupportplus->functions->get_default_priority()."',type  ='user'
        WHERE id='" . $ticket_id . "'";

$wpdb->query($sql);

$sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket SELECT * FROM {$wpdb->prefix}wpsp_temp_table"; 

$wpdb->query($sql);

$new_ticket = $wpdb->insert_id;

$sql = "DROP TEMPORARY TABLE  {$wpdb->prefix}wpsp_temp_table";

$wpdb->query($sql);

$sql = "SELECT * FROM {$wpdb->prefix}wpsp_ticket WHERE id='" . $new_ticket . "'";
$result = $wpdb->get_row($sql);
$created_by = $result->created_by;
$guest_name = $result->guest_name;
$guest_email = $result->guest_email;

$sql = "CREATE TEMPORARY TABLE {$wpdb->prefix}wpsp_temp_table AS SELECT * FROM {$wpdb->prefix}wpsp_ticket_thread WHERE id='" . $thread_id . "'";
$wpdb->query($sql);

$sql = "UPDATE {$wpdb->prefix}wpsp_temp_table SET id='0',ticket_id='" . $new_ticket . "', create_time='" . current_time('mysql', 1) . "',
        created_by='" . $created_by . "',guest_name='" . $guest_name . "',guest_email='" . $guest_email . "' ,is_note    ='0'
        WHERE id='" . $thread_id . "'";

$wpdb->query($sql);

$sql = "INSERT INTO {$wpdb->prefix}wpsp_ticket_thread SELECT * FROM {$wpdb->prefix}wpsp_temp_table";
$wpdb->query($sql);

$sql = "DROP TEMPORARY TABLE {$wpdb->prefix}wpsp_temp_table";
$wpdb->query($sql);
?>