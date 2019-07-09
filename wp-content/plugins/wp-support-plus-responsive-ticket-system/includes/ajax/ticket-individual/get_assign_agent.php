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

$modal_title = __('Change Assigned Agent','wp-support-plus-responsive-ticket-system');

ob_start();

?>

<form id="frm_assigned_agents">
    <div class="row">
        <div class="form-group col-md-12">
            <input data-field_key="assigned_agent" class="form-control filter_autocomplete" type="text" autocomplete="off" placeholder="<?php _e('Search agent ...','wp-support-plus-responsive-ticket-system')?>" />
        </div>
        <div id="assigned_agents" class="form-group col-md-12">
            
            <?php
            $agents = $ticket->assigned_to ? explode(',', $ticket->assigned_to ) : array();

            foreach ($agents as $agent){

                $user = get_userdata($agent);

                ?>

                <div class="wpsp_autocomplete_choice_item">
                    <?php echo $user->display_name?> <span onclick="wpsp_autocomplete_choice_item_delete(this)" class="fa fa-times wpsp_autocomplete_choice_item_delete"></span>
                    <input type="hidden" name="assigned_agents[]" value="<?php echo htmlentities($user->ID)?>" />
                </div>

                <?php

            }

            ?>
            
        </div>
    </div>
    
    <input type="hidden" name="action" value="wpsp_set_assign_agent" />
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
		jQuery("#frm_assigned_agents").keypress(function(e) {
			//Enter key
			if (e.which == 13) {
				return false;
			}
		});
    wpspjq( ".filter_autocomplete" ).autocomplete({
      
        source: function (request, response) {
            wpspjq.ajax({
                url: wpsp_data.ajax_url,
                dataType: "json",
                method: "post",
                data: {
                    term: request.term,
                    action: 'wpsp_autocomplete',
                    input_id: 'ticket_filter',
                    field_key: wpspjq(this.element).data('field_key'),
                    nonce: wpspjq('#wpsp_nonce').val().trim()
                },
                success: function (data) {
                    response(wpspjq.map(data, function (item) {
                        return {
                            label: item.label,
                            field_key: item.field_key,
                            value: item.label,
                            field_val: item.value
                        }
                    }));
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            
            var exists = false;
            wpspjq('#assigned_agents').find('input[type=hidden]').each(function(){
                if( wpspjq(this).val() == ui.item.field_val ){
                    exists = true;
                }
            });
            
            if( ui.item.value != 0 && !exists ){
                var html_to_append = '<div class="wpsp_autocomplete_choice_item">'
                                +ui.item.label+' <span onclick="wpsp_autocomplete_choice_item_delete(this)" class="fa fa-times wpsp_autocomplete_choice_item_delete"></span>'
                                +'<input type="hidden" name="assigned_agents[]" value="'+ui.item.field_val+'" />'
                            +'</div>';

                wpspjq('#assigned_agents').append(html_to_append);
            }
            
            wpspjq(this).val('');
            
            return false;
        }
      
    })
    .focus(function(){     
        wpspjq(this).data("uiAutocomplete").search(wpspjq(this).val());
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
        <button type="button" class="btn btn-primary" onclick="wpsp_set_change_assign_agent();"><?php _e('Save Changes','wp-support-plus-responsive-ticket-system');?></button>
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
