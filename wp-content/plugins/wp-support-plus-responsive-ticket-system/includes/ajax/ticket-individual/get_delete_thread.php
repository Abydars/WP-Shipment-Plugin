<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$ticket_id  = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : 0 ;
$thread_id  = isset($_POST['thread_id']) ? intval(sanitize_text_field($_POST['thread_id'])) : 0 ;
$nonce      = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '' ;

$locale = get_locale();

/**
 * Check nonce
 */
$agent_settings = $wpsupportplus->functions->get_agent_settings();
if( !wp_verify_nonce( $nonce, $ticket_id ) || !$thread_id ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$modal_title = __('Delete Thread','wp-support-plus-responsive-ticket-system');

ob_start();

?>

<form id="frm_delete_thread">
    
    <p><?php _e('Are you sure to delete this thread?','wp-support-plus-responsive-ticket-system');?></p>
    
    <input type="hidden" name="action" value="wpsp_set_delete_thread" />
    <input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id)?>" />
    <input type="hidden" name="thread_id" value="<?php echo htmlentities($thread_id)?>" />
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce($ticket_id)?>" />
    
</form>
<style>
    .ui-autocomplete{
        position:absolute;
        cursor:default;
        z-index:9999999999 !important
    }
</style>
<script>
jQuery(function () {
    
});
</script>

<?php

$modal_body = ob_get_clean();

ob_start();

?>

<div class="row">
    <div class="col-md-12" style="text-align: right;">
        <button type="button" class="btn btn-default" onclick="wpsp_ajax_modal_cancel();"><?php _e('Cancel','wp-support-plus-responsive-ticket-system');?></button>
        <button type="button" class="btn btn-primary" onclick="wpsp_set_delete_thread();"><?php _e('Confirm','wp-support-plus-responsive-ticket-system');?></button>
    </div>
</div>

<?php

$modal_footer = ob_get_clean();

$response = array(
    'title'     => $modal_title,
    'body'      => $modal_body,
    'footer'    => $modal_footer
);

echo json_encode($response);
