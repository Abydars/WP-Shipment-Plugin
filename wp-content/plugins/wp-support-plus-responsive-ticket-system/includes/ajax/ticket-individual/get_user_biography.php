<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

$ticket_id  = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : 0 ;
$nonce      = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '' ;

/**
 * Check nonce
 */
if( !wp_verify_nonce( $nonce, $ticket_id ) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$ticket_user = $wpdb->get_var( "select created_by from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

$description = get_user_meta( $ticket_user,'description' );

$modal_title = __('Biographical Info','wp-support-plus-responsive-ticket-system');

ob_start();
?>

<form id="frm_get_user_biography" >
  <?php echo $description[0]; ?>
</form>

<?php

$modal_body = ob_get_clean();

$response = array(
    'title'     => $modal_title,
    'body'      => $modal_body
);

echo json_encode($response);
