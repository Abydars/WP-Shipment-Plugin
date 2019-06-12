jQuery(document).ready(function (){
    
    jQuery( '#job-fields' ).submit( function( e ) {
        if(jQuery( '#field1' ).val().trim()==''){
            alert(wpsp_cust_data.insert_job_label);
            jQuery( '#field1' ).val('');
            jQuery( '#field1' ).focus();
            e.preventDefault();
        }
    });
    
    jQuery( '#cust-fields' ).submit( function( e ) {
        if(jQuery( '#field2' ).val().trim()==''){
            alert(wpsp_cust_data.insert_field_label);
            jQuery( '#field2' ).val('');
            jQuery( '#field2' ).focus();
            e.preventDefault();
        }
    });
    
});

function wpsp_stickticket(ticket_id){
        var data = {
		'action': 'wpsp_getstickticket',
		'ticket_id':ticket_id
	};
        jQuery.post(wpsp_stick_data.wpsp_ajax_url, data, function(response) {
            getTickets("");
	});
}

function wpsp_stick_setting(){
    var wpsp_allow_user_to_stick_ticket =jQuery('input[name=wpsp_stick_ticket_setting]:checked').val();
    var data = {
            'action': 'wpsp_setsticksetting',
            'wpsp_allow_user_to_stick_ticket':wpsp_allow_user_to_stick_ticket,
            'stick_ticket_color': jQuery('#wpspstickTicket_bc').val()
    };
    jQuery.post(wpsp_stick_data.wpsp_ajax_url, data, function(response) {
        alert('Setting Saved!!');
    });
}