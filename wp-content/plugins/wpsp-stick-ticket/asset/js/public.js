function wpsp_stickticket(ticket_id){
        var data = {
		'action': 'wpsp_getstickticket',
		'ticket_id':ticket_id
	};
        jQuery.post(wpsp_stick_data.wpsp_ajax_url, data, function(response) {
            getTickets();
	});
}