
function wpsp_en_clone(obj){

  if(confirm("Are you sure to clone this notification?")){
    var clone_url = wpspjq(obj).data('href');
    window.location.href = clone_url;
  }

}

function wpsp_en_delete(obj){

  if(confirm("Are you sure to delete this notification?")){
  var delete_url = wpspjq(obj).data('href');
    window.location.href = delete_url;
  }

}

function wpsp_en_show_add_condition(){

  wpspjq('#wpsp_en_conditional_field').val('');
  wpspjq('#wpsp_en_conditional_field_option').val('');
  wpspjq('#wpsp_en_conditional_field_has_word').val('');
  wpspjq('.wpsp_en_conditional_values').hide();
  wpspjq('#wpsp_en_condition_add_container').slideDown();

}

function wpsp_en_conditional_field_set(obj){

  wpspjq('#wpsp_en_conditional_field_option').val('');
  wpspjq('#wpsp_en_conditional_field_has_word').val('');
  wpspjq('.wpsp_en_conditional_values').hide();

  var field_key  = wpspjq(obj).val();
  var field_type = wpspjq(obj).find('option:selected').data('type');

  if (field_type == 'text') {

    wpspjq('#wpsp_en_conditional_field_has_word').show().focus();

  } else if (field_type == 'drop-down') {

    var data = {
        'action'    : 'wpsp_en_get_field_options',
        'field_key' : field_key
    };
    wpspjq.post(wpsp_admin.ajax_url, data, function(response) {
        wpspjq('#wpsp_en_conditional_field_option').html(response);
        wpspjq('#wpsp_en_conditional_field_option').show().focus();
    });

  } else {
    wpspjq('.wpsp_en_conditional_values').hide();
  }

}

function wpsp_en_conditional_has_word_has_char(obj){

  var str = wpspjq(obj).val().trim();
  if (str.length>1) {
    wpspjq('#wpsp_en_btn_add_condition').show();
  } else {
    wpspjq('#wpsp_en_btn_add_condition').hide();
  }

}

function wpsp_en_conditional_option_has_option(obj){

  var option = wpspjq(obj).val().trim();
  if (option=='') {
    wpspjq('#wpsp_en_btn_add_condition').hide();
  } else {
    wpspjq('#wpsp_en_btn_add_condition').show();
  }
}

function wpsp_en_set_add_condition(){

  var field_key       = wpspjq('#wpsp_en_conditional_field').val();
  var field_key_label = wpspjq('#wpsp_en_conditional_field').find('option:selected').text();
  var field_val       = '';
  var field_val_label = '';
  var html_str        = '';

  if ( wpspjq('#wpsp_en_conditional_field_option').val().trim() != '' ) {
    field_val       = wpspjq('#wpsp_en_conditional_field_option').val().trim();
    field_val_label = wpspjq('#wpsp_en_conditional_field_option').find('option:selected').text();
  } else {
    field_val       = wpspjq('#wpsp_en_conditional_field_has_word').val().trim();
    field_val_label = wpspjq('#wpsp_en_conditional_field_has_word').val().trim();
  }

  if ( field_key !='' && field_val != '' ) {

    var duplicate_flag = false;
    var exist_items    = wpspjq('#wpsp_en_condition_container').find("input[name='wpsp_en_notification[condition]["+field_key+"][]']");
    wpspjq(exist_items).each(function(index, el) {
      if( wpspjq(el).val().trim() == field_val ){
        duplicate_flag = true;
      }
    });
    if (duplicate_flag) {
      wpspjq('#wpsp_en_condition_add_container').hide();
      return;
    }

    html_str  += '<div class="wpsp_autocomplete_choice_item">';
    html_str  += '  '+ field_key_label +' = "'+ field_val_label +'" <span onclick="wpsp_autocomplete_choice_item_delete(this)" class="dashicons dashicons-no-alt wpsp_autocomplete_choice_item_delete"></span>';
    html_str  += '  <input name="wpsp_en_notification[condition]['+ field_key +'][]" value="'+ field_val +'" type="hidden">';
    html_str  += '</div>';
    wpspjq('#wpsp_en_condition_container').append(html_str);
    wpspjq('#wpsp_en_condition_add_container').hide();
  }
}
