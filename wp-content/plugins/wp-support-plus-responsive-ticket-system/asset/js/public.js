
jQuery(document).ready(function(){

    wpspjq( "#regi_user_autocomplete" ).on('focus',function(){
        wpspjq(this).val('');
        wpspjq('#user_id').val('0');
    });

    wpspjq( "#regi_user_autocomplete" ).focusout(function(){
        var type = parseInt(wpspjq('#create_ticket_as').val().trim());
        if( type == 1 && parseInt(wpspjq('#user_id').val().trim()) == 0 ){
            wpspjq('#user_id').val(wpsp_data.current_user_id);
            wpspjq('.regi-field').find('input').val(wpsp_data.current_user_name);
        }
    });

    wpspjq('#frm_create_ticket input[type=text]').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
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

    wpspjq('.button-checkbox').each(function () {

        // Settings
        var $widget = wpspjq(this),
            $button = $widget.find('button'),
            $checkbox = $widget.find('input:checkbox'),
            color = $button.data('color'),
            settings = {
                on: {
                    icon: 'glyphicon glyphicon-check'
                },
                off: {
                    icon: 'glyphicon glyphicon-unchecked'
                }
            };

        // Event Handlers
        $button.on('click', function () {
            $checkbox.prop('checked', !$checkbox.is(':checked'));
            $checkbox.triggerHandler('change');
            updateDisplay();
        });
        $checkbox.on('change', function () {
            updateDisplay();
        });

        // Actions
        function updateDisplay() {
            var isChecked = $checkbox.is(':checked');

            // Set the button's state
            $button.data('state', (isChecked) ? "on" : "off");

            // Set the button's icon
            $button.find('.state-icon')
                .removeClass()
                .addClass('state-icon ' + settings[$button.data('state')].icon);

            // Update the button's color
            if (isChecked) {
                $button
                    .removeClass('btn-default')
                    .addClass('btn-' + color + ' active');
            }
            else {
                $button
                    .removeClass('btn-' + color + ' active')
                    .addClass('btn-default');
            }
        }

        // Initialization
        function init() {

            updateDisplay();

            // Inject the icon if applicable
            if ($button.find('.state-icon').length == 0) {
                $button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
            }
        }
        init();
    });

    wpspjq('.button-radio').each(function () {

        // Settings
        var $widget = wpspjq(this),
            $button = $widget.find('button'),
            $radio = $widget.find('input:radio'),
            color = $button.data('color'),
            settings = {
                on: {
                    icon: 'glyphicon glyphicon-record'
                },
                off: {
                    icon: 'fa fa-circle-o'
                }
            };

        // Event Handlers
        $button.on('click', function () {
            $radio.prop('checked', !$radio.is(':checked'));
            $radio.triggerHandler('change');
            updateDisplay();
        });
        $radio.on('change', function () {
            var radio_group = $radio.parent().parent().parent().find('input:radio');
            wpspjq(radio_group).each(function(){

                var isChecked = wpspjq(this).is(':checked');

                // Set the button's state
                wpspjq(this).parent().find('button').data('state', (isChecked) ? "on" : "off");

                // Set the button's icon
                wpspjq(this).parent().find('button').find('.state-icon')
                    .removeClass()
                    .addClass('state-icon ' + settings[wpspjq(this).parent().find('button').data('state')].icon);

                // Update the button's color
                if (isChecked) {
                    wpspjq(this).parent().find('button')
                        .removeClass('btn-default')
                        .addClass('btn-' + color + ' active');
                }
                else {
                    wpspjq(this).parent().find('button')
                        .removeClass('btn-' + color + ' active')
                        .addClass('btn-default');
                }

            });
        });

        // Actions
        function updateDisplay() {
            var isChecked = $radio.is(':checked');

            // Set the button's state
            $button.data('state', (isChecked) ? "on" : "off");

            // Set the button's icon
            $button.find('.state-icon')
                .removeClass()
                .addClass('state-icon ' + settings[$button.data('state')].icon);

            // Update the button's color
            if (isChecked) {
                $button
                    .removeClass('btn-default')
                    .addClass('btn-' + color + ' active');
            }
            else {
                $button
                    .removeClass('btn-' + color + ' active')
                    .addClass('btn-default');
            }
        }

        // Initialization
        function init() {

            updateDisplay();

            // Inject the icon if applicable
            if ($button.find('.state-icon').length == 0) {
                $button.prepend('<i class="state-icon ' + settings[$button.data('state')].icon + '"></i> ');
            }
        }
        init();
    });

    wpspjq( ".wpsp_date" ).datepicker({
        dateFormat : wpsp_data.date_format,
        showAnim : 'slideDown',
        changeMonth: true,
        changeYear: true,
        yearRange: "-50:+50",
    });

    wpspjq( ".filter_autocomplete" ).autocomplete({

        source: function (request, response) {
          
            var img='filter_wait_'+wpspjq(this.element).data('field_key');
            wpspjq('[data-field_key="'+img+'"]').show();  
            
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
                    
                    wpspjq('[data-field_key="'+img+'"]').hide();
                }
            });
        },
        minLength: 0,
        select: function (event, ui) {

            var exists = false;
            wpspjq('#filter_'+ui.item.field_key).find('input[type=hidden]').each(function(){
                if( wpspjq(this).val() == ui.item.value ){
                    exists = true;
                }
            });

            if( ui.item.value != 0 && !exists ){
                var html_to_append = '<div class="wpsp_autocomplete_choice_item">'
                                +ui.item.label+' <span onclick="wpsp_autocomplete_choice_item_delete(this)" class="fa fa-times wpsp_autocomplete_choice_item_delete"></span>'
                                +'<input type="hidden" name="filter[elements]['+ui.item.field_key+'][label][]" value="'+ui.item.label+'" />'
                                +'<input type="hidden" name="filter[elements]['+ui.item.field_key+'][val][]" value="'+ui.item.field_val+'" />'
                            +'</div>';

                wpspjq('#filter_'+ui.item.field_key).append(html_to_append);
            }

            wpspjq(this).val('');

            wpspjq('#page_no').val('1');

            get_tickets();

            return false;
        }

    })
    .focus(function(){
        wpspjq(this).data("uiAutocomplete").search(wpspjq(this).val());
    });

    /**
     * Date filter
     */
    wpspjq('.date_filter').change(function(){

        var key = wpspjq(this).data('key');
        var flag_not_empty = false;
        wpspjq('.filter_'+key).each(function(){
            if( wpspjq(this).val().trim() === '' ){
                flag_not_empty = true;
            }
        });
        var flag_empty = false;
        wpspjq('.filter_'+key).each(function(){
            if( wpspjq(this).val().trim() !== '' ){
                flag_empty = true;
            }
        });

        if( !flag_not_empty || !flag_empty ){

            wpspjq('#page_no').val('1');
            get_tickets();
        }

    });

});

/**
 * Sign-In user
 */
function wpsp_sign_in(obj){

    wpspjq('#wpsp_sign_in_notice').html('<p class="bg-info wpsp_notice">'+wpsp_data.lbl_please_wait+'</p>');

    var dataform = new FormData(obj);
    wpspjq.ajax({
        url: wpsp_data.ajax_url,
        type: 'POST',
        data: dataform,
        processData: false,
        contentType: false
    })
    .done(function (response_str) {
        var response = JSON.parse(response_str);
        wpspjq('#wpsp_sign_in_notice').html(response.messege);
        wpspjq('#inputPassword').val('');

        if(response.success){
            window.location.href = wpsp_data.support_url+'?login=success';
        }
    });

    return false;
}

/**
 * Guest Sign-In user
 */
function wpsp_guest_sign_in(obj){

    if(confirm(wpsp_data.lbl_guest_confirm)){

        wpspjq('#wpsp_guest_sign_in_notice').html('<p class="bg-info wpsp_notice">'+wpsp_data.lbl_please_wait+'</p>');

        var dataform = new FormData(obj);
        wpspjq.ajax({
            url: wpsp_data.ajax_url,
            type: 'POST',
            data: dataform,
            processData: false,
            contentType: false
        })
        .done(function (response_str) {
            var response = JSON.parse(response_str);
            wpspjq('#wpsp_guest_sign_in_notice').html(response.messege);

            if(response.success){
                window.location.href = wpsp_data.support_url;
            }
        });
    }

    return false;
}

/**
 * Change create ticket as type
 */
function change_create_ticket_as_type(obj,user_id,user_name){

    var type = parseInt(wpspjq(obj).val().trim());
    if( type === 1 ){
        wpspjq('#user_id').val(user_id);
        wpspjq('.regi-field').find('input').val(user_name);
        wpspjq('.guest-field').find('input').val('');
        wpspjq('.guest-field').removeClass('wpsp_require')
        wpspjq('.guest-field').hide();
        wpspjq('.regi-field').show();
    } else {
        wpspjq('#user_id').val('0');
        wpspjq('.regi-field').hide();
        wpspjq('.guest-field').show();
        wpspjq('.guest-field').addClass('wpsp_require')
    }
}

/**
 * Create ticket description attachment
 */
function create_ticket_desc_attach(){
    wpspjq('#attachment_upload').unbind('change');
    wpspjq('#attachment_upload').on('change', function() {
        var flag = false;
        var file = this.files[0];
        wpspjq('#attachment_upload').val('');
        var allowedExtension = ['exe', 'php'];
        var file_name_split = file.name.split('.');
        var file_extension = file_name_split[file_name_split.length-1];

        if (!flag && wpspjq.inArray(file_extension, allowedExtension) > -1){
            flag = true;
            alert(wpsp_data.lbl_attachment_file_type_not_allowed);
        }

        var current_filesize=file.size/1000000;
        if(current_filesize>wpsp_data.wpspAttachMaxFileSize){
            flag = true;
            alert(wpsp_data.wpspAttachFileSizeExeeded);
        }

        if (!flag){

            wpspjq('#description_container').find('div[aria-label="'+wpsp_data.attachment_tooltip+'"]').hide();
            wpspjq('#desc_attach_plus').hide();

            wpspjq('#description_attachment').show();

            var html_str = '<div class="col-md-4 wpsp_attachment inner_control">'+
                        '<div class="progress">'+
                            '<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">'+
                              file.name+
                            '</div>'+
                        '</div>'+
                        '<img onclick="attachment_cancel(this, false);" class="attachment_cancel" src="'+wpsp_data.attachment_cancel_icon+'" style="display:none;" />'+
                    '</div>';

            wpspjq('#description_attachment').append(html_str);

            var attachment = wpspjq('#description_attachment').find('.wpsp_attachment').last();

            var data = new FormData();
            data.append('file', file);
            data.append('action', 'wpsp_upload_file');
            data.append('nonce', wpspjq('#wpsp_nonce').val().trim());

            wpspjq.ajax({
                type: 'post',
                url: wpsp_data.ajax_url,
                data: data,
                xhr: function(){
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
                            wpspjq(attachment).find('.progress-bar').css('width',percentComplete+'%');
                        }
                    }, false);
                    return xhr;
                },
                processData: false,
                contentType: false,
                success: function(response) {

                    var return_obj=JSON.parse(response);

                    wpspjq('#description_container').find('div[aria-label="'+wpsp_data.attachment_tooltip+'"]').show();
                    wpspjq('#desc_attach_plus').show();
                    wpspjq(attachment).find('.attachment_cancel').show();

                    if( parseInt(return_obj.id) != 0 ){
                        wpspjq(attachment).append('<input type="hidden" name="desc_attachment[]" value="'+return_obj.id+'">');
                        wpspjq(attachment).find('.progress-bar').addClass('progress-bar-success');
                    } else {
                        wpspjq(attachment).find('.progress-bar').addClass('progress-bar-danger');
                    }

                }
            });

        }

    });
    wpspjq('#attachment_upload').trigger('click');
}

/**
 * Reply description attachment
 */
function reply_ticket_desc_attach(){
    wpspjq('#attachment_upload').unbind('change');
    wpspjq('#attachment_upload').on('change', function() {
        var flag = false;
        var file = this.files[0];
        wpspjq('#attachment_upload').val('');
        var allowedExtension = ['exe', 'php'];
        var file_name_split = file.name.split('.');
        var file_extension = file_name_split[file_name_split.length-1];

        if (!flag && wpspjq.inArray(file_extension, allowedExtension) > -1){
            flag = true;
            alert(wpsp_data.lbl_attachment_file_type_not_allowed);
        }

        var current_filesize=file.size/1000000;
        if(current_filesize>wpsp_data.wpspAttachMaxFileSize){
            flag = true;
            alert(wpsp_data.wpspAttachFileSizeExeeded);
        }

        if (!flag){

            wpspjq('#reply_ticket_form_container').find('div[aria-label="'+wpsp_data.attachment_tooltip+'"]').hide();
            wpspjq('#reply_ticket_form_container .attach_plus').hide();

            wpspjq('#ticket_reply_editor_attachment').show();

            var html_str = '<div class="col-md-4 wpsp_attachment inner_control">'+
                        '<div class="progress">'+
                            '<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">'+
                              file.name+
                            '</div>'+
                        '</div>'+
                        '<img onclick="attachment_cancel(this,false);" class="attachment_cancel" src="'+wpsp_data.attachment_cancel_icon+'" style="display:none;" />'+
                    '</div>';

            wpspjq('#ticket_reply_editor_attachment').append(html_str);

            var attachment = wpspjq('#ticket_reply_editor_attachment').find('.wpsp_attachment').last();

            var data = new FormData();
            data.append('file', file);
            data.append('action', 'wpsp_upload_file');
            data.append('nonce', wpspjq('#wpsp_nonce').val().trim());

            wpspjq.ajax({
                type: 'post',
                url: wpsp_data.ajax_url,
                data: data,
                xhr: function(){
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
                            wpspjq(attachment).find('.progress-bar').css('width',percentComplete+'%');
                        }
                    }, false);
                    return xhr;
                },
                processData: false,
                contentType: false,
                success: function(response) {

                    var return_obj=JSON.parse(response);

                    wpspjq('#reply_ticket_form_container').find('div[aria-label="'+wpsp_data.attachment_tooltip+'"]').show();
                    wpspjq('#reply_ticket_form_container .attach_plus').show();
                    wpspjq(attachment).find('.attachment_cancel').show();

                    if( parseInt(return_obj.id) != 0 ){
                        wpspjq(attachment).append('<input type="hidden" class="reply_attachment" name="desc_attachment[]" value="'+return_obj.id+'">');
                        wpspjq(attachment).find('.progress-bar').addClass('progress-bar-success');
                    } else {
                        wpspjq(attachment).find('.progress-bar').addClass('progress-bar-danger');
                    }

                }
            });

        }

    });
    wpspjq('#attachment_upload').trigger('click');
}

/**
 * Note description attachment
 */
function note_ticket_desc_attach(){

    wpspjq('#attachment_upload').unbind('change');
    wpspjq('#attachment_upload').on('change', function() {
        var flag = false;
        var file = this.files[0];
        wpspjq('#attachment_upload').val('');
        var allowedExtension = ['exe', 'php'];
        var file_name_split = file.name.split('.');
        var file_extension = file_name_split[file_name_split.length-1];

        if (!flag && wpspjq.inArray(file_extension, allowedExtension) > -1){
            flag = true;
            alert(wpsp_data.lbl_attachment_file_type_not_allowed);
        }

        var current_filesize=file.size/1000000;
        if(current_filesize>wpsp_data.wpspAttachMaxFileSize){
            flag = true;
            alert(wpsp_data.wpspAttachFileSizeExeeded);
        }

        if (!flag){

            wpspjq('#add_note_form_container').find('div[aria-label="'+wpsp_data.attachment_tooltip+'"]').hide();
            wpspjq('#add_note_form_container .attach_plus').hide();

            wpspjq('#ticket_note_editor_attachment').show();

            var html_str = '<div class="col-md-4 wpsp_attachment inner_control">'+
                        '<div class="progress">'+
                            '<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">'+
                              file.name+
                            '</div>'+
                        '</div>'+
                        '<img onclick="attachment_cancel(this,false);" class="attachment_cancel" src="'+wpsp_data.attachment_cancel_icon+'" style="display:none;" />'+
                    '</div>';

            wpspjq('#ticket_note_editor_attachment').append(html_str);

            var attachment = wpspjq('#ticket_note_editor_attachment').find('.wpsp_attachment').last();

            var data = new FormData();
            data.append('file', file);
            data.append('action', 'wpsp_upload_file');
            data.append('nonce', wpspjq('#wpsp_nonce').val().trim());

            wpspjq.ajax({
                type: 'post',
                url: wpsp_data.ajax_url,
                data: data,
                xhr: function(){
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
                            wpspjq(attachment).find('.progress-bar').css('width',percentComplete+'%');
                        }
                    }, false);
                    return xhr;
                },
                processData: false,
                contentType: false,
                success: function(response) {

                    var return_obj=JSON.parse(response);

                    wpspjq('#add_note_form_container').find('div[aria-label="'+wpsp_data.attachment_tooltip+'"]').show();
                    wpspjq('#add_note_form_container .attach_plus').show();
                    wpspjq(attachment).find('.attachment_cancel').show();

                    if( parseInt(return_obj.id) != 0 ){
                        wpspjq(attachment).append('<input type="hidden" class="note_attachment" name="desc_attachment[]" value="'+return_obj.id+'">');
                        wpspjq(attachment).find('.progress-bar').addClass('progress-bar-success');
                    } else {
                        wpspjq(attachment).find('.progress-bar').addClass('progress-bar-danger');
                    }

                }
            });

        }

    });
    wpspjq('#attachment_upload').trigger('click');
}

/**
 * Create ticket description attachment
 */
function cust_attach( plus_icon, id){

    wpspjq('#attachment_upload').unbind('change');
    wpspjq('#attachment_upload').on('change', function() {
        var flag = false;
        var file = this.files[0];
        wpspjq('#attachment_upload').val('');
        var allowedExtension = ['exe', 'php'];
        var file_name_split = file.name.split('.');
        var file_extension = file_name_split[file_name_split.length-1];

        if (!flag && wpspjq.inArray(file_extension, allowedExtension) > -1){
            flag = true;
            alert(wpsp_data.lbl_attachment_file_type_not_allowed);
        }

        var current_filesize=file.size/1000000;
        if(current_filesize>wpsp_data.wpspAttachMaxFileSize){
            flag = true;
            alert(wpsp_data.wpspAttachFileSizeExeeded);
        }

        if (!flag){

            wpspjq(plus_icon).hide();

            var html_str = '<div class="col-md-4 wpsp_attachment inner_control">'+
                        '<div class="progress">'+
                            '<div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width:0%">'+
                              file.name+
                            '</div>'+
                        '</div>'+
                        '<img onclick="attachment_cancel(this,true);" class="attachment_cancel" src="'+wpsp_data.attachment_cancel_icon+'" style="display:none;" />'+
                    '</div>';

            wpspjq('#cust_attachment_'+id).append(html_str);

            var attachment = wpspjq('#cust_attachment_'+id).find('.wpsp_attachment').last();

            var data = new FormData();
            data.append('file', file);
            data.append('arr_name', 'cust_'+id);
            data.append('action', 'wpsp_upload_file');
            data.append('nonce', wpspjq('#wpsp_nonce').val().trim());

            wpspjq.ajax({
                type: 'post',
                url: wpsp_data.ajax_url,
                data: data,
                xhr: function(){
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function(evt){
                        if (evt.lengthComputable) {
                            var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
                            wpspjq(attachment).find('.progress-bar').css('width',percentComplete+'%');
                        }
                    }, false);
                    return xhr;
                },
                processData: false,
                contentType: false,
                success: function(response) {

                    var return_obj=JSON.parse(response);

                    wpspjq(plus_icon).show();
                    wpspjq(attachment).find('.attachment_cancel').show();

                    if( parseInt(return_obj.id) != 0 ){
                        wpspjq(attachment).append('<input type="hidden" name="cust_'+id+'[]" value="'+return_obj.id+'">');
                        wpspjq(attachment).find('.progress-bar').addClass('progress-bar-success');
                    } else {
                        wpspjq(attachment).find('.progress-bar').addClass('progress-bar-danger');
                    }

                }
            });

        }

    });
    wpspjq('#attachment_upload').trigger('click');
}

/**
 * Attachment cancel
 */
function attachment_cancel( obj, is_cust_field ){
    var attachment_fieldset = wpspjq(obj).parent().parent();
    wpspjq(obj).parent().remove();
    if( !is_cust_field && wpspjq(attachment_fieldset).find('.wpsp_attachment').length == 0 ){
        wpspjq(attachment_fieldset).hide();
    }
}

/**
 * Create ticket category change
 * used to get category dependant fields
 */
function create_ticket_cng_cat(obj){

    var data = {
        'action': 'wpsp_create_tkt_cng_cat',
        'cat_id': wpspjq(obj).val(),
        'nonce' : wpspjq('#wpsp_nonce').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {

        var return_obj=JSON.parse(response);
        console.log(return_obj);

        wpspjq('.cat_depend').addClass('hidden');
        wpspjq('.cat_depend').removeClass('wpsp_require');

        if(return_obj.keys.length){

            wpspjq(return_obj.keys).each(function(k,v){
                var cust_field = wpspjq('#cust_'+v);
                var is_required = cust_field.attr('data-required');
                cust_field.removeClass('hidden');
                if( parseInt(is_required) === 1 ){
                    cust_field.addClass('wpsp_require');
                }
            });
        }

    });
}

/**
 * Submit ticket reply to DB
 */
function submit_ticket_reply(){

    var reply_body = tinyMCE.get('ticket_reply_editor').getContent().trim();

    if(reply_body === ''){
        alert(wpsp_data.lbl_reply_body_empty);
        return;
    }

    if( !ticket_reply_extended_validations() ){
        return;
    }

    wpspjq('#reply_confirm_modal').modal('show');

}

function post_ticket_reply(){

    wpspjq('#reply_confirm_modal').modal('hide');

    var reply_body = tinyMCE.get('ticket_reply_editor').getContent().trim();

    var data = new FormData(wpspjq('#frm_ticket_reply')[0]);
    data.append('reply_body',reply_body);

    wpspjq('#reply_ticket_form_container').html(wpsp_data.loading_html);

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });
}

/**
 * Submit ticket note to DB
 */
function submit_ticket_note(){

    var reply_body = tinyMCE.get('ticket_note_editor').getContent().trim();

    if(reply_body === ''){
        alert(wpsp_data.lbl_note_body_empty);
        return;
    }

    if( !ticket_note_extended_validations() ){
        return;
    }

    wpspjq('#note_confirm_modal').modal('show');

}

function wpsp_display_saved_filters(){
    wpspjq('.modal-saved-filter').modal('show');
}

function post_ticket_note(){

    wpspjq('#note_confirm_modal').modal('hide');

    var reply_body = tinyMCE.get('ticket_note_editor').getContent().trim();

    var data = new FormData(wpspjq('#frm_ticket_note')[0]);
    data.append('reply_body',reply_body);

    wpspjq('#add_note_form_container').html(wpsp_data.loading_html);

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}

function validateURL(url){
    var re = /^(http[s]?:\/\/){0,1}(www\.){0,1}[a-zA-Z0-9\.\-]+\.[a-zA-Z]{2,5}[\.]{0,1}/;
    return re.test(url);
}

function reset_create_ticket(){
    if(confirm(wpsp_data.lbl_are_you_sure)){
        window.location.reload();
    }
}

function open_filter_modal(obj){
    alert('Test');
}

function toggle_list_checkboxes(obj){

    if(wpspjq(obj).is(':checked')){
        wpspjq('.chk_ticket_list_item:enabled').prop('checked',true);
    } else {
        wpspjq('.chk_ticket_list_item:enabled').prop('checked',false);
    }
    toggle_ticket_list_actions();
}

function toggle_ticket_list_actions(){
    var checked = wpspjq('#tbl_wpsp_ticket_list').find('.chk_ticket_list_item:checked');
    if(checked.length==0){
        wpspjq('.checkbox_depend').addClass('disabled');
    } else {
        wpspjq('.checkbox_depend').removeClass('disabled');
    }
}

function wpsp_redirect(obj){
    wpspjq('#wpsp_ticket_list_container').html(wpsp_data.loading_html);
    window.location = wpspjq(obj).data("href");
}

function open_ticket_redirect(obj){
    window.open(wpspjq(obj).data("href"), '_blank');
}

function wpsp_autocomplete_choice_item_delete(obj){
    wpspjq(obj).parent().remove();
    wpspjq('#page_no').val('1');
    get_tickets();
}

function save_filter(){
    var filter_name = wpspjq('#frm_save_filter_widget').find('input[type=text]').val().trim();
    if( filter_name === '' ){
        alert(wpsp_data.lbl_enter_filter_name);
    } else {

        wpspjq('#frm_save_filter_widget').find('button').text(wpsp_data.lbl_please_wait);

        var data = new FormData(wpspjq('#ticket_filter')[0]);
        data.append('action', 'wpsp_save_ticket_filter');

        var filter_save = new FormData(wpspjq('#frm_save_filter_widget')[0]);
        data.append('filter_name', filter_name);

        if(wpspjq('#frm_save_filter_widget').find('select[name="filter_type"]').length){
            var filter_type= wpspjq('#frm_save_filter_widget').find('select[name="filter_type"]').val().trim();
            data.append('filter_type', filter_type);  
        }

        wpspjq.ajax({
            type: 'post',
            url: wpsp_data.ajax_url,
            data: data,
            processData: false,
            contentType: false,
            success: function(response) {
                window.location.reload(true);
            }
        });

    }
}

function delete_ticket_filter(){

    var id          = wpspjq('#cmb_ticket_filters').val();
    var visibility  = parseInt(wpspjq('#cmb_ticket_filters').find(':selected').data('agent_visibility'));

    if( visibility == 0 ){
        alert(wpsp_data.lbl_default_filter_can_not_deleted);
        return;
    }

    if( visibility == 1 && !wpsp_data.is_administrator ){
        alert(wpsp_data.lbl_public_filter_can_not_deleted);
        return;
    }

    if(confirm(wpsp_data.lbl_are_you_sure)){
        wpspjq('.modal-saved-filter').find('.row').html(wpsp_data.loading_html);

        var data = {
            action: 'wpsp_delete_ticket_filter',
            id: id,
            visibility:visibility,
            nonce : wpspjq('#wpsp_nonce').val().trim()
        };

        wpspjq.post(wpsp_data.ajax_url, data, function(response) {
            window.location.reload(true);
        });
    }

}

function btn_apply_ticket_filter(){
    var id = wpspjq('#cmb_ticket_filters').val();
    var visibility  = parseInt(wpspjq('#cmb_ticket_filters').find(':selected').data('agent_visibility'));
    apply_ticket_filter(id,visibility);
}

function btn_reset_ticket_filter(obj){
    wpspjq(obj).text(wpsp_data.lbl_please_wait);
    var id = '';
    var visibility  = 0;
    apply_ticket_filter(id,visibility);
}

function apply_ticket_filter(id,visibility){

    wpspjq('.modal-saved-filter').find('.row').html(wpsp_data.loading_html);

    var data = {
        action: 'wpsp_apply_ticket_filter',
        id: id,
        visibility:visibility,
        nonce : wpspjq('#wpsp_nonce').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {
         window.location.href = wpsp_data.support_url+'?page=tickets&section=ticket-list'
    });
}

function get_tickets(){

    wpspjq('#wpsp_ticket_list_container').html(wpsp_data.loading_html);

    var data = new FormData(wpspjq('#ticket_filter')[0]);

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            wpspjq('#wpsp_ticket_list_container').html(response);
        }
    });

}

function wpsp_ticket_next_page(){

    var page_no = parseInt(wpspjq('#page_no').val().trim());
    if( page_no < total_pages ){
        page_no++;
        wpspjq('#page_no').val(page_no);
        get_tickets();
    }
}

function wpsp_ticket_prev_page(){
    var page_no = parseInt(wpspjq('#page_no').val().trim());
    if( page_no > 1 ){
        page_no--;
        wpspjq('#page_no').val(page_no);
        get_tickets();
    }
}

function ticket_list_search(obj){

    $str = wpspjq(obj).val().trim();
    wpspjq('#search').val($str);
    wpspjq('#page_no').val('1');
    get_tickets();
}

function show_ticket_reply_form(){
    wpspjq('.rich_form_container').hide();
    wpspjq('#reply_ticket_form_container').slideDown();
}

function show_ticket_add_note_form(){
    wpspjq('.rich_form_container').hide();
    wpspjq('#add_note_form_container').slideDown();
}

function wpsp_ticket_thread_expander_toggle(obj){

    var height = parseInt(wpspjq(obj).parent().find('.wpsp_ticket_thread_body').height());
    if( height === wpsp_data.ticket_thread_body_height ){
        wpspjq(obj).parent().find('.wpsp_ticket_thread_body').height('auto');
        wpspjq(obj).text(wpsp_data.lbl_view_less);
    } else {
        wpspjq(obj).parent().find('.wpsp_ticket_thread_body').height(wpsp_data.ticket_thread_body_height);
        wpspjq(obj).text(wpsp_data.lbl_view_more);
    }

}

function wpsp_ajax_modal_wait_mode(){

    wpspjq('#ajax_modal .modal-title').text(wpsp_data.lbl_please_wait);
    wpspjq('#ajax_modal .modal-body').html(wpsp_data.loading_html);
    wpspjq('#ajax_modal .modal-footer').hide();
}

function wpsp_ajax_modal_cancel(){
    wpspjq('#ajax_modal').modal('hide');
}

function change_ticket_status( ticket_id ){

    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
        action: 'wpsp_get_change_ticket_status',
        ticket_id : ticket_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {

        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();

    });
}

function wpsp_set_change_ticket_status(){

    var data = new FormData(wpspjq('#frm_change_ticket_status')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });
}

function get_change_raised_by( ticket_id ){

    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
        action: 'wpsp_get_change_raised_by',
        ticket_id : ticket_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {

        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();

    });

}

function wpsp_set_change_raised_by(){

    var user_id = parseInt(wpspjq('#user_id').val().trim());
    var user_name = wpspjq('.guest-field').find('input[name=guest_name]').val().trim();
    var user_email = wpspjq('.guest-field').find('input[name=guest_email]').val().trim();

    var type = parseInt(wpspjq('#create_ticket_as').val());

    if( type === 1 ){

        if( user_id === 0 ){

            alert(wpsp_data.lbl_choose_reg_user);
            return;
        }

    } else {

        if( user_name === '' || user_email === '' ) {

            alert(wpsp_data.lbl_name_or_email_empty);
            return;
        }

        if(!validateEmail(user_email)){
            alert(wpsp_data.lbl_wrong_email);
            return;
        }

    }

    wpspjq('#user_id').val(user_id);
    wpspjq('.guest-field').find('input[name=guest_name]').val(user_name);
    wpspjq('.guest-field').find('input[name=guest_email]').val(user_email);

    var data = new FormData(wpspjq('#frm_change_raised_by')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });

}

function get_assign_agent( ticket_id ){

    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
        action: 'wpsp_get_assign_agent',
        ticket_id : ticket_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {

        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();

    });

}

function wpsp_set_change_assign_agent(){

    var data = new FormData(wpspjq('#frm_assigned_agents')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });
}

function get_agent_fields( ticket_id ){

    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
        action: 'wpsp_get_agent_fields',
        ticket_id : ticket_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {

        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();

    });

}

function wpsp_set_agent_fields(){

    var data = new FormData(wpspjq('#frm_agent_fields')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });

}


function get_ticket_fields( ticket_id ){

    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
        action: 'wpsp_get_ticket_fields',
        ticket_id : ticket_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {

        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();

    });

}

function wpsp_set_ticket_fields(){

    var data = new FormData(wpspjq('#frm_ticket_fields')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });
}

function get_edit_thread( ticket_id, thread_id ){

    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
        action: 'wpsp_get_edit_thread',
        ticket_id : ticket_id,
        thread_id : thread_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {

        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();

    });

}

function wpsp_set_edit_thread(){

    var data = new FormData(wpspjq('#frm_edit_thread')[0]);

    data.append( 'body', tinyMCE.get("wpsp_thead_edit").getContent().trim() );

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });
}

function get_new_thread(ticket_id,thread_id){
   wpsp_ajax_modal_wait_mode();
   wpspjq('#ajax_modal').modal('show');

   var data = {
       action: 'wpsp_get_new_thread',
       ticket_id : ticket_id,
       thread_id :thread_id,
       nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
   };

   wpspjq.post(wpsp_data.ajax_url, data, function(response) {
       
       var obj = wpspjq.parseJSON(response);

       wpspjq('#ajax_modal .modal-title').text(obj.title);
       wpspjq('#ajax_modal .modal-body').html(obj.body);
       wpspjq('#ajax_modal .modal-footer').html(obj.footer);
       wpspjq('#ajax_modal .modal-footer').show();
   });
 }
 
function wpsp_set_new_thread(){
  var data = new FormData(wpspjq('#wpsp_create_ticket_thread')[0]);
    wpsp_ajax_modal_wait_mode();
    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.href=wpsp_data.support_url+'?page=tickets&section=ticket-list';
        }
    });
}
function get_delete_thread( ticket_id, thread_id ){

    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
        action: 'wpsp_get_delete_thread',
        ticket_id : ticket_id,
        thread_id : thread_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {

        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();

    });

}

function wpsp_set_delete_thread(){

    var data = new FormData(wpspjq('#frm_delete_thread')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });
}

function get_delete_ticket(ticket_id){
    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
        action: 'wpsp_get_delete_ticket',
        ticket_id : ticket_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {

        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();

    });
 }

function wpsp_set_delete_ticket(){

    var data = new FormData(wpspjq('#frm_delete_ticket')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.href=wpsp_data.support_url+'?page=tickets&section=ticket-list';
        }
    });
}

function get_edit_subject( ticket_id ){

    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
        action: 'wpsp_get_edit_subject',
        ticket_id : ticket_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {

        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();

    });

}

function wpsp_set_edit_subject(){

    var data = new FormData(wpspjq('#frm_edit_subject')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });
}

function get_clone_ticket(ticket_id){
    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
        action: 'wpsp_get_clone_ticket',
        ticket_id : ticket_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {
        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();
    });
}

function get_close_ticket(ticket_id){
    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');
    var data = {
        action: 'wpsp_get_close_ticket',
        ticket_id : ticket_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };
    wpspjq.post(wpsp_data.ajax_url, data, function(response) {
        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();
    });
}

function set_clone_ticket(){
    var data = new FormData(wpspjq('#frm_edit_clone_subject')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response_str) {
            var response = JSON.parse(response_str);
            window.location.href = wpsp_data.support_url+'?page=tickets&section=ticket-list&action=open-ticket&id='+response.clone_id;
        }
    });
}

function wpsp_set_close_ticket(){
    var data = new FormData(wpspjq('#frm_close_ticket')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });
}

function get_delete_bulk_ticket(nonce){
    var checked = wpspjq('#tbl_wpsp_ticket_list').find('.chk_ticket_list_item:checked');
    if(checked.length!=0){
        wpsp_ajax_modal_wait_mode();
        wpspjq('#ajax_modal').modal('show');

        var values = wpspjq('.chk_ticket_list_item:checked').map(function () {
                return this.value;
        }).get();
        var ticket_id=String(values);

        var data = {
            action: 'wpsp_get_delete_bulk_ticket',
            ticket_id : ticket_id,
            nonce : nonce
        };
        wpspjq.post(wpsp_data.ajax_url, data, function(response) {
            var obj = wpspjq.parseJSON(response);
            wpspjq('#ajax_modal .modal-title').text(obj.title);
            wpspjq('#ajax_modal .modal-body').html(obj.body);
            wpspjq('#ajax_modal .modal-footer').html(obj.footer);
            wpspjq('#ajax_modal .modal-footer').show();
        });
    }
}

function get_bulk_assign_agent(nonce){
    var checked = wpspjq('#tbl_wpsp_ticket_list').find('.chk_ticket_list_item:checked');
    if(checked.length!=0){
        wpsp_ajax_modal_wait_mode();
        wpspjq('#ajax_modal').modal('show');

        var values = wpspjq('.chk_ticket_list_item:checked').map(function () {
                return this.value;
        }).get();
        var ticket_id=String(values);

        var data = {
            action: 'wpsp_get_bulk_assign_agent',
            ticket_id: ticket_id,
            nonce: nonce
        };

        wpspjq.post(wpsp_data.ajax_url, data, function(response) {

            var obj = wpspjq.parseJSON(response);

            wpspjq('#ajax_modal .modal-title').text(obj.title);
            wpspjq('#ajax_modal .modal-body').html(obj.body);
            wpspjq('#ajax_modal .modal-footer').html(obj.footer);
            wpspjq('#ajax_modal .modal-footer').show();
        });
    }
}

function get_bulk_change_status(nonce){
    var checked = wpspjq('#tbl_wpsp_ticket_list').find('.chk_ticket_list_item:checked');
    if(checked.length!=0){
        wpsp_ajax_modal_wait_mode();
        wpspjq('#ajax_modal').modal('show');

        var values = wpspjq('.chk_ticket_list_item:checked').map(function () {
                return this.value;
        }).get();
        var ticket_id=String(values);

        var data = {
            action: 'wpsp_get_bulk_change_status',
            ticket_id: ticket_id,
            nonce: nonce
        };

        wpspjq.post(wpsp_data.ajax_url, data, function(response) {

            var obj = wpspjq.parseJSON(response);

            wpspjq('#ajax_modal .modal-title').text(obj.title);
            wpspjq('#ajax_modal .modal-body').html(obj.body);
            wpspjq('#ajax_modal .modal-footer').html(obj.footer);
            wpspjq('#ajax_modal .modal-footer').show();
        });
    }
}
function wpsp_set_delete_bulk_ticket(){
    var data = new FormData(wpspjq('#frm_delete_bulk_ticket')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });
}

function wpsp_set_bulk_assign_agent(){

    var data = new FormData(wpspjq('#frm_bulk_assigned_agents')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });
}

function wpsp_set_bulk_change_status(){
    var data = new FormData(wpspjq('#frm_bulk_change_ticket_status')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });
}
function set_agent_setting(){
    var agent_setting = tinyMCE.get('agent_setting').getContent().trim();
    wpsp_ajax_modal_wait_mode();
    var data = {
      'action': 'wpsp_set_agent_setting',
      'signature': agent_setting
    };
    wpspjq.post(wpsp_data.ajax_url, data, function(response) {
      window.location.reload();
    });
}

function get_ticket_filter(){
	wpspjq('#ticket_filter_container').toggle('slow',function() {
		if(wpspjq('#ticket_filter_container').is(':visible')) {
			wpspjq('#btn_ticket_filter').text(wpsp_data.hide_filters);
		}
		else{
			wpspjq('#btn_ticket_filter').text(wpsp_data.show_filters);
		}
	});
	if(wpspjq('#ticket_list_container').hasClass('col-md-12')){
			wpspjq('#ticket_list_container').removeClass('col-md-12');
			wpspjq('#ticket_list_container').addClass('col-md-9');
	}else{
			wpspjq('#ticket_list_container').removeClass('col-md-9');
			wpspjq('#ticket_list_container').addClass('col-md-12');
	}
}

function get_user_biography(ticket_id){
    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
        action: 'wpsp_get_user_biography',
        ticket_id : ticket_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {
        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();
    });
}

function wpsp_deleted_ticket_filter(){
    get_tickets();
}

function get_restore_ticket(ticket_id){
  
    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
        action: 'wpsp_get_restore_ticket',
        ticket_id : ticket_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {
        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();
    });
}

function wpsp_set_restore_ticket(){

    var data = new FormData(wpspjq('#frm_restore_ticket')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.href=wpsp_data.support_url+'?page=tickets&section=ticket-list';
        }
    });
}

function get_permanent_delete_ticket(ticket_id){
  
    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
        action: 'wpsp_get_permanent_delete_ticket',
        ticket_id : ticket_id,
        nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {
        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();
    });
}

function set_permanent_delete_ticket(){

    var data = new FormData(wpspjq('#frm_permanent_delete_ticket')[0]);

    wpsp_ajax_modal_wait_mode();

    wpspjq.ajax({
        type: 'post',
        url: wpsp_data.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.href=wpsp_data.support_url+'?page=tickets&section=ticket-list';
        }
    });
}

function get_all_tickets(ticket_id, guest_email){
    wpsp_ajax_modal_wait_mode();
    wpspjq('#ajax_modal').modal('show');

    var data = {
      action: 'wpsp_get_ticket_created',
      ticket_id : ticket_id,
      guest_email : guest_email,
      nonce : wpspjq('#frm_ticket_reply').find('input[name=nonce]').val().trim()
    };

    wpspjq.post(wpsp_data.ajax_url, data, function(response) {

        var obj = wpspjq.parseJSON(response);

        wpspjq('#ajax_modal .modal-title').text(obj.title);
        wpspjq('#ajax_modal .modal-body').html(obj.body);
        wpspjq('#ajax_modal .modal-footer').html(obj.footer);
        wpspjq('#ajax_modal .modal-footer').show();

    });
}

function get_captcha_code(e){
		wpspjq(e).hide();
		wpspjq('#captcha_wait').show();
		var data = {
	    action: 'wpsp_get_captcha_code'
	  };
		wpspjq.post(wpsp_data.ajax_url, data, function(response) {
			wpspjq('#captcha_code').val(response.trim());;
			wpspjq(e).show();
			wpspjq(e).prop('disabled',true);
      wpspjq('#captcha_wait').hide();
	  });
}

function wpsp_submit_ticket(){
     var data = new FormData(wpspjq('#frm_create_ticket')[0]);
     wpspjq.ajax({
          type: 'post',
          url: wpsp_data.ajax_url,
          data: data,
          processData: false,
          contentType: false,
          success: function(response) {
            var url= JSON.parse(response);
            window.location.replace(url);
          }
     });
}