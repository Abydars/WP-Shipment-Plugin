<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$ticket_id  = isset($_POST['ticket_id']) ? intval(sanitize_text_field($_POST['ticket_id'])) : 0 ;
$nonce      = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '' ;

/**
 * Check nonce
 */
$agent_settings = $wpsupportplus->functions->get_agent_settings();
if( !wp_verify_nonce( $nonce, $ticket_id ) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

$modal_title = __('Change Ticket Fields','wp-support-plus-responsive-ticket-system');

ob_start();

?>

<form id="frm_ticket_fields">
    
    <?php

        $sql = "SELECT c.id as id, c.field_type as field_type, c.field_options as field_options FROM {$wpdb->prefix}wpsp_custom_fields c "
                . "INNER JOIN {$wpdb->prefix}wpsp_ticket_form_order f "
                . "ON c.id=f.field_key "
                . "WHERE 1=1 "
                . "AND c.isVarFeild=0 "
                . "AND ( "
                        . "c.field_categories = 0 OR c.field_categories RLIKE '(^|,)".$ticket->cat_id."(,|$)'"
                    . " ) "
                . "ORDER BY f.load_order ASC ";
                
        $ticket_fields = $wpdb->get_results( $sql );
        
        include WPSP_ABSPATH . 'includes/ajax/ticket-individual/class-ticket-fields-format.php';
        $field_format = new WPSP_Ticket_Fields_Format($ticket);

        if( $ticket_fields ) {

            foreach ( $ticket_fields as $custom_field ){
                
                $field_format->print_field($custom_field);
                
            }
            
        } else {

            _e('No Ticket fields','wp-support-plus-responsive-ticket-system');
        }

        ?>
    
    <input type="hidden" name="action" value="wpsp_set_ticket_fields" />
    <input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id)?>" />
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
    
    wpspjq( ".wpsp_date" ).datepicker({
        dateFormat : wpsp_data.date_format,
        showAnim : 'slideDown',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
    });
    
});
</script>

<?php

$modal_body = ob_get_clean();

ob_start();

?>

<div class="row">
    <div class="col-md-12" style="text-align: right;">
        <button type="button" class="btn btn-default" onclick="wpsp_ajax_modal_cancel();"><?php _e('Cancel','wp-support-plus-responsive-ticket-system');?></button>
        <button type="button" class="btn btn-primary" onclick="wpsp_set_ticket_fields();"><?php _e('Save Changes','wp-support-plus-responsive-ticket-system');?></button>
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
