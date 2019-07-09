
jQuery(document).ready(function(){
    
});

/**
 * Loads popup with given action and other parameters
 */
function wpsp_admin_load_popup(action, id, nonce, title, width, height, margin_top){
    
    wpsp_show_admin_popup(title, width, height, margin_top);
    
    var data = {
        'action': 'wpsp_'+action,
        'load_id': id,
        'nonce' : nonce
    };

    wpspjq.post(wpsp_admin.ajax_url, data, function(response) {
        wpspjq('#wpsp-admin-popup-wait').hide();
        wpspjq('#wpsp-admin-popup-body').html(response);
        wpspjq('#wpsp-admin-popup-body').show();
    });
    
}

/**
 * Execute poup form submit action via ajax
 */
function wpsp_admin_submit_popup(obj){
    
    wpspjq('#wpsp-admin-popup-body').hide();
    wpspjq('#wpsp-admin-popup-wait').show();
    
    var dataform = new FormData(obj);
    wpspjq.ajax({
        url: wpsp_admin.ajax_url,
        type: 'POST',
        data: dataform,
        processData: false,
        contentType: false
    })
    .done(function (response) {
        window.location.reload();
    });
}

/**
 * Save order to DB
 */
function wpsp_save_table_order(table, nonce){
    
    var order = wpspjq("#wpsp_sortable").sortable("toArray");
    
    var data = {
        'action': 'wpsp_save_table_order',
        'table': table,
        'order': order,
        'nonce' : nonce
    };
    
    wpspjq.post(wpsp_admin.ajax_url, data, function(response) {
        alert(response);
    });

}

/**
 * Show popup screen
 */
function wpsp_show_admin_popup(title, width, height, margin_top){
    wpspjq('#wpsp-admin-popup-wait img').css('margin-top',margin_top);
    tb_show( title, '#TB_inline?width='+width+'&height='+height+'&inlineId=wpsp-admin-popup-content' );
}

/**
 * Hide popup screen
 */
function wpsp_close_admin_popup(){
    tb_remove();
}

/**
 * Get autocomplete results
 */
function wpsp_get_autocomplete_results( input_id, s='' ) {
    
    var nonce = wpspjq('#wpsp_nonce').val().trim();
    var exclude = new Array();
    
    var sel_objects = wpspjq('#'+input_id+'_container .wpsp_autocomplete_choosen_choices').find('input[type=hidden]');
    if( sel_objects.length > 0){
        wpspjq(sel_objects).each(function(){
            exclude.push( wpspjq(this).val().trim() );
        });
    }
    
    var data = {
        'action'    : 'wpsp_autocomplete',
        'input_id'  : input_id,
        's'         : s,
        'exclude'   : exclude,
        'nonce'     : nonce
    };
    
    wpspjq.post(wpsp_admin.ajax_url, data, function(response) {
        wpspjq('#'+input_id+'_container').find('.wpsp_autocomplete_choosen_results').html(response);
    });
    
}

/**
 * Autocomplete result mouseover
 */
function wpsp_autocomplete_res_mouseover( input_id, res ){
    wpspjq('#'+input_id+'_container').find('.active-result').removeClass('heightligted');
    wpspjq(res).addClass('heightligted');
}

/**
 * Autocomplete choose result
 */
function wpsp_autocomplete_res_choose( input_id, arr, val ){
    var display_text = wpspjq('#'+input_id+'_container').find('.heightligted').text();
    var html_to_append = '<div class="wpsp_autocomplete_choice_item">'
                            +display_text+' <span onclick="wpsp_autocomplete_choice_item_delete(this)" class="dashicons dashicons-no-alt wpsp_autocomplete_choice_item_delete"></span>'
                            +'<input type="hidden" name="'+arr+'[]" value="'+val+'" />'
                        +'</div>';
    wpspjq('#'+input_id+'_container').find('.wpsp_autocomplete_choosen_choices').append(html_to_append);
    wpspjq('#'+input_id+'_container').find('.wpsp_autocomplete_choosen_drop').hide();
    wpspjq('.wpsp_autocomplete').val('');
}

/**
 * Autocomplete choice item delete
 */
function wpsp_autocomplete_choice_item_delete(obj){
    wpspjq(obj).parent().remove();
}

function wpsp_upgrade_start(nonce){
    
    var html_str = wpspjq('#wpsp_wait_html').html();
    wpspjq('.wpsp_installation_container').html(html_str);
    wpsp_upgrade(nonce);
    
}

function wpsp_upgrade(nonce){
    
    var data = {
        'action'    : 'wpsp_upgrade',
        'nonce'     : nonce
    };
    
    wpspjq.post(wpsp_admin.ajax_url, data, function(response_str) {
        
        var response = JSON.parse(response_str);
        
        wpspjq('#wpsp_upgrade_complete_percentage').text(response.percentage);
        
        if( response.is_next == 1 ){
            wpsp_upgrade(nonce);
        } else {
            window.location.href = 'admin.php?page=wp-support-plus&action=wpsp_installation';
        }
        
    });
    
}

function wpsp_installation_next( current_step, nonce ){
    
    var html_str = wpspjq('#wpsp_wait_html').html();
    wpspjq('.wpsp_installation_container').html(html_str);
    
    var data = {
        action          : 'wpsp_installation',
        current_step    : current_step,
        nonce           : nonce
    };
    
    wpspjq.post(wpsp_admin.ajax_url, data, function(response_str) {
        
        window.location.href = 'admin.php?page=wp-support-plus&action=wpsp_installation';
        
    });
}

function wpsp_create_support_page(nonce){
    
    var page_title = wpspjq('#wpsp_support_page_title').val().trim();
    var html_str = wpspjq('#wpsp_wait_html').html();
    wpspjq('.wpsp_installation_container').html(html_str);
    
    var data = {
        action          : 'wpsp_create_support_page',
        page_title      : page_title,
        nonce           : nonce
    };
    
    wpspjq.post(wpsp_admin.ajax_url, data, function(response_str) {
        
        window.location.href = 'admin.php?page=wp-support-plus&action=wpsp_installation';
        
    });
    
}

function wpsp_select_support_page(nonce){
    var page_id = wpspjq('#wpsp_support_page_id').val().trim();
    if(page_id===''){
        alert('Please select page');
        return;
    }
    
    var html_str = wpspjq('#wpsp_wait_html').html();
    wpspjq('.wpsp_installation_container').html(html_str);
    
    var data = {
        action          : 'wpsp_select_support_page',
        page_id         : page_id,
        nonce           : nonce
    };
    
    wpspjq.post(wpsp_admin.ajax_url, data, function(response_str) {
        
        window.location.href = 'admin.php?page=wp-support-plus&action=wpsp_installation';
        
    });
    
}

function wpsp_customize_reset_default( setting ){
  
    if (confirm(wpsp_admin.confirm)) {
        var data = {
            'action'  : 'wpsp_customize_reset_default',
            'setting' : setting
        };
        
        wpspjq.post(wpsp_admin.ajax_url, data, function(response) {
            window.location.reload(true);
        });
    }
  
}
