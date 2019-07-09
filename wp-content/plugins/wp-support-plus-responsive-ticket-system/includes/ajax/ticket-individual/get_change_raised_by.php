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
if( !wp_verify_nonce( $nonce, $ticket_id ) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );

$modal_title = __('Change Raised By','wp-support-plus-responsive-ticket-system');

ob_start();

?>

<form id="frm_change_raised_by">
    <div class="row">
        <div class="form-group col-md-4">
            <label class="label label-default"><?php _e('User Type', 'wp-support-plus-responsive-ticket-system')?></label><br>
            <select class="form-control" id="create_ticket_as" name="create_ticket_as" onchange="change_create_ticket_as_type(this,<?php echo htmlentities($ticket->created_by)?>,'<?php echo $ticket->guest_name?>','<?php echo $ticket->guest_email?>')">
                <option <?php echo $ticket->created_by ? 'selected="selected"' : ''?> value="1"><?php _e('Registered User', 'wp-support-plus-responsive-ticket-system')?></option>
                <option <?php echo !$ticket->created_by ? 'selected="selected"' : ''?> value="0"><?php _e('Guest', 'wp-support-plus-responsive-ticket-system')?></option>
            </select>
        </div>
        <div class="form-group regi-field col-md-8" style="<?php echo !$ticket->created_by ? 'display:none;':''?>">
            <label class="label label-default"><?php _e('Choose User', 'wp-support-plus-responsive-ticket-system')?></label><br>
            <input id="regi_user_autocomplete" type="text" class="form-control" value="<?php echo htmlentities($ticket->guest_name)?>" autocomplete="off" placeholder="<?php _e('Search user ...', 'wp-support-plus-responsive-ticket-system')?>" />
        </div>
        <div data-field ="text" id="guest_name" class="form-group guest-field col-md-4" style="<?php echo $ticket->created_by ? 'display:none;':''?>">
            <label class="label label-default"><?php _e('Guest Name', 'wp-support-plus-responsive-ticket-system')?></label>  <span class="fa fa-snowflake-o"></span><br>
            <input type="text" class="form-control" name="guest_name" value="<?php echo htmlentities($ticket->guest_name)?>"/>
        </div>
        <div data-field ="email" id="guest_email" class="form-group guest-field col-md-4" style="<?php echo $ticket->created_by ? 'display:none;':''?>">
            <label class="label label-default"><?php _e('Guest Email', 'wp-support-plus-responsive-ticket-system')?></label>  <span class="fa fa-snowflake-o"></span><br>
            <input type="text" class="form-control" name="guest_email" value="<?php echo $ticket->guest_email?>"/>
        </div>
    </div>
    
    <input type="hidden" name="action" value="wpsp_set_change_raised_by" />
    <input type="hidden" id="user_id" name="user_id" value="<?php echo htmlentities($ticket->created_by)?>" />
    <input type="hidden" name="ticket_id" value="<?php echo htmlentities($ticket_id)?>" />
    <input type="hidden" name="nonce" value="<?php echo wp_create_nonce($ticket_id)?>" />
    
    <input type="hidden" id="ticket_user_id" value="<?php echo htmlentities($ticket->created_by)?>" />
    <input type="hidden" id="ticket_guest_name" value="<?php echo htmlentities($ticket->guest_name)?>" />
    <input type="hidden" id="ticket_guest_email" value="<?php echo htmlentities($ticket->guest_email)?>" />
    
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
    
    wpspjq( "#regi_user_autocomplete" ).on('focus',function(){
        wpspjq(this).val('');
        wpspjq('#user_id').val('0');
    });
    
    wpspjq( "#regi_user_autocomplete" ).focusout(function(){
        var type = parseInt(wpspjq('#create_ticket_as').val().trim());
        if( type == 1 && parseInt(wpspjq('#user_id').val().trim()) == 0 ){
            wpspjq('#user_id').val(wpspjq('#ticket_user_id').val());
            wpspjq('.regi-field').find('input').val(wpspjq('#ticket_guest_name').val());
        }
    });
    
    wpspjq( "#regi_user_autocomplete" ).autocomplete({
      
        source: function (request, response) {
            wpspjq.ajax({
                url: wpsp_data.ajax_url,
                dataType: "json",
                method: "post",
                data: {
                    term: request.term,
                    action: 'wpsp_autocomplete',
                    input_id: 'wp_user',
                    nonce: wpspjq('#wpsp_nonce').val().trim()
                },
                success: function (data) {
                    response(wpspjq.map(data, function (item) {
                        return {
                            label: item.label,
                            uid: item.uid
                        }
                    }));
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            wpspjq('#user_id').val(ui.item.uid);
        }
      
    });
    
});

function change_create_ticket_as_type(obj,user_id,user_name,user_email){    
    
    var type = parseInt(wpspjq(obj).val().trim());
    if( type === 1 ){
        wpspjq('#user_id').val(user_id);
        wpspjq('.regi-field').find('input').val(user_name);
        wpspjq('.guest-field').hide();
        wpspjq('.regi-field').show();
    } else {
        wpspjq('#user_id').val('0');
        wpspjq('.guest-field').find('input[name=guest_name]').val(user_name);
        wpspjq('.guest-field').find('input[name=guest_email]').val(user_email);
        wpspjq('.regi-field').hide();
        wpspjq('.guest-field').show();
    }
}
</script>

<?php

$modal_body = ob_get_clean();

ob_start();

?>

<div class="row">
    <div class="col-md-12" style="text-align: right;">
        <button type="button" class="btn btn-default" onclick="wpsp_ajax_modal_cancel();"><?php _e('Cancel','wp-support-plus-responsive-ticket-system');?></button>
        <button type="button" class="btn btn-primary" onclick="wpsp_set_change_raised_by();"><?php _e('Save Changes','wp-support-plus-responsive-ticket-system');?></button>
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
