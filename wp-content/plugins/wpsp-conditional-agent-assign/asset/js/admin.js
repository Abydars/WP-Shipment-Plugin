function wpsp_add_condition(){         
    jQuery('#wpsp_condition_container .wait').show();         
    jQuery('#wpsp_condition_container .add_condition').hide();         
    jQuery('.show_conditions').hide();         
    var data = {             
        'action': 'wpsp_add_condition'        
    };         
    jQuery.post(wpsp_caa_data.wpsp_ajax_url, data, function(response) {             
        jQuery('#wpsp_condition_container .wait').hide();            
        jQuery('#wpsp_condition_container .add_condition').html(response);            
        jQuery('#wpsp_condition_container .add_condition').show();            
    }); 
} 

function setCondition(e,frm_obj){   
    e.preventDefault();
    flag=true;
    if(jQuery('input[name="wpsp_caa[label]"]').val().trim()===''){
        alert('Please enter Condition Name');
        flag=false;
    }
    if( flag && jQuery('input[name="wpsp_caa[agents][]"]:checked').length === 0 ){
        alert('Please select at least one agent!');
        flag=false;
    }
    if( flag && !wpsp_caa_validate_rules()){
        alert('Either no rule is applied or no option selected for active rule!');
        flag=false;
    }
    
    if(flag){
        var dataform = new FormData(frm_obj);
        dataform.append("caa_desc",jQuery('#wpsp_caa_description').html());
        jQuery.ajax({
            url: wpsp_caa_data.wpsp_ajax_url,
            type: 'POST',
            data: dataform,
            processData: false,
            contentType: false
        })
        .done(function (msg) {
            window.location.reload();
        });
    }
    e.preventDefault();
} 

function wpsp_caa_delete_condition(id) {
    var rule_label = jQuery('#wpsp_caa_cond_name_'+id).text();
    if (confirm('Are you sure to delete '+rule_label+'?')) {
        jQuery('#wpsp_condition_container .wait').show();     
        jQuery('#wpsp_condition_container .edit_condition').hide();     
        jQuery('.show_conditions').hide();
        var data = {
            'action': 'setDeleteConditional',
            'cond_id': id
        };
        jQuery.post(wpsp_caa_data.wpsp_ajax_url, data, function (response) {
            window.location.reload();
        });
    }
}

function getEditCondtional(rule_id) {
    jQuery('#wpsp_condition_container .wait').show();     
    jQuery('#wpsp_condition_container .edit_condition').hide();     
    jQuery('.show_conditions').hide();
    var data = {
        'action': 'getEditCondtional',
        'rule_id': rule_id
    };
    jQuery.post(wpsp_caa_data.wpsp_ajax_url, data, function (response) {
        jQuery('#wpsp_condition_container .wait').hide();  
        jQuery('#edit_label').prop('readonly', true);
        jQuery('#wpsp_condition_container .edit_condition').html(response); 
        jQuery('#wpsp_condition_container .edit_condition').show();  
    });
} 

function showConditionList(){
    jQuery('.show_conditions').show();      
    jQuery('#wpsp_condition_container .edit_condition').hide();
    jQuery('#wpsp_condition_container .add_condition').hide();
    location.reload(); 
}

function setEditConditional(){
        jQuery('#wpsp_create_ticket_category').val();
        jQuery('#wpsp_edit_default_assignee').val();
        
        var CaaFromObject = document.getElementById('wpsp_caa_edit_form');
        var dataform = new FormData(CaaFromObject);
        
        jQuery.ajax({
            url: wpsp_caa_data.wpsp_ajax_url,
            type: 'POST',
            data: dataform,
            processData: false,
            contentType: false
        })
        .done(function (msg) {
            alert("Settings Saved!!");
            jQuery('#wpsp_condition_container .edit_condition').hide();
            jQuery('.show_conditions').show();
        });
}

function wpsp_ca_apply_toggle(field,status){
    if( status === 1){
        jQuery('#'+field+'_options_container').slideDown();
    } else {
        jQuery('#'+field+'_options_container').slideUp();
    }
    wpsp_reset_ca_rule_text();
}

function wpsp_reset_ca_rule_text(){
    var caa_rule_arr = [];
    jQuery('#wpsp_caa_tbl').find('.caa_rule').each(function(){
        var rule = jQuery(this).val();
        if(jQuery('input[name="wpsp_caa[rules]['+rule+'][status]"]:checked').val() === '1'){
            caa_rule_arr.push(rule);
        }
    });
    if( caa_rule_arr.length > 0 ){
        var html_str = 'Assign selected Agents<br><strong>IF</strong><br>';
        var and_rules = [];
        jQuery(caa_rule_arr).each(function(index, value){
            var and_rule_str = '';
            and_rule_str += jQuery('#'+value+'_name').text()+' is ';
            var or_rules = [];
            jQuery('input[name="wpsp_caa[rules]['+value+'][options][]"]').each(function(key,val){
                if(jQuery(this).is(':checked')){
                    or_rules.push(jQuery('#'+value+'_'+key).text());
                }
            });
            and_rule_str += or_rules.join(' <strong>OR</strong> ');
            and_rules.push(and_rule_str);
        });
        html_str += and_rules.join('<br><strong>AND</strong><br>');
        jQuery('#wpsp_caa_description').html(html_str);
    } else {
        jQuery('#wpsp_caa_description').html("No Rule Applied!");
    }
}

function wpsp_caa_validate_rules(){
    var flag = true;
    var caa_rule_arr = [];
    jQuery('#wpsp_caa_tbl').find('.caa_rule').each(function(){
        var rule = jQuery(this).val();
        if(jQuery('input[name="wpsp_caa[rules]['+rule+'][status]"]:checked').val() === '1'){
            caa_rule_arr.push(rule);
        }
    });
    if( caa_rule_arr.length > 0 ){
        jQuery(caa_rule_arr).each(function(index, value){
            if( jQuery('input[name="wpsp_caa[rules]['+value+'][options][]"]:checked').length === 0 ){
                flag = false;
                return false;
            }
        });
    } else {
        flag = false;
    }
    return flag;
}