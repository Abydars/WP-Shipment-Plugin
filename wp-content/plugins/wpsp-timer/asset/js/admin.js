function getTimerLog(ticket_id){
    wpsp_show_front_popup();
    var data = {
        'action': 'getTimerLog',
        'ticket_id':ticket_id

    };
    jQuery.post(wpsp_timer_data.wpsp_ajax_url, data, function(response) {
        jQuery('#wpsp_front_popup_body').html(response);
        jQuery('#wpsp_front_popup_blank,#wpsp_front_popup_loading_img').hide();
        jQuery('#wpsp_front_popup_body').show();
    });
}
function wpsp_set_timer(ticket_id,timer_log_id){
    if(jQuery('#wpsp_start_time').val()==0){
        var data = {
            'action': 'setStartTimer',
            'ticket_id':ticket_id
        };
        jQuery.post(wpsp_timer_data.wpsp_ajax_url, data, function(response) {
        });
    }else{
        var data={
            'action': 'wpsp_stop_time',
            'timer_log_id':timer_log_id
        };
        jQuery.post(wpsp_timer_data.wpsp_ajax_url, data, function(response) {
        });
    }
    openTicket(ticket_id);
}

function wpsp_getEditEndTime(ticket_id){
    wpsp_show_front_popup();
    var data = {
            'action': 'wpsp_getEditEndTime',
            'ticket_id':ticket_id
            
        };
        jQuery.post(wpsp_timer_data.wpsp_ajax_url, data, function(response) {
            jQuery('#wpsp_front_popup_body').html(response);
            jQuery('#wpsp_front_popup_blank,#wpsp_front_popup_loading_img').hide();
            jQuery('#wpsp_front_popup_body').show(); 
        });
}

function wpsp_setEditEndTime(id,ticket_id){
    wpsp_show_front_popup();
    var data = {
            'action': 'wpsp_setEditEndTime',
            'end_time':jQuery('#wpsp_end_time').val(),
            'id':id
        };
        jQuery.post(wpsp_timer_data.wpsp_ajax_url, data, function(response) {
            getTimerLog(ticket_id);
            openTicket(ticket_id);
        });
}

function wpsp_calculate_total_time_required(ticket_id){
    if(currentScreen=='open_ticket' && currentTicketID==ticket_id){
        var data = {
            'action': 'wpsp_calculate_total_time_required',
            'ticket_id':ticket_id
        };
        jQuery.post(wpsp_timer_data.wpsp_ajax_url, data, function(response) {
            jQuery(".wpsp_ticket_time").html(response);
        });
    }
}