
wpspjq = jQuery;
function wpsp_pipe_gmail_app_config(nonce){

    wpspjq('#gmail_client_secret').text('Please wait...');

    var data = new FormData(wpspjq('#wpsp_frm_email_piping_settings')[0]);
    data.append('action','wpsp_ep_upload_google_app_config');
    data.append('nonce',nonce);

    wpspjq.ajax({
        type: 'post',
        url: wpsp_admin.ajax_url,
        data: data,
        processData: false,
        contentType: false,
        success: function(response) {
            window.location.reload();
        }
    });
}

function wpsp_ep_delete_gmail_connection( btn_obj, email_address, nonce ){

    var flag = confirm("Are you sure to remove pipe connection for "+email_address+" ?");
    if(flag){

        wpspjq(btn_obj).removeClass('dashicons-no-alt');
        wpspjq(btn_obj).addClass('dashicons-clock');

        var data = {
            action: 'wpsp_ep_delete_gmail_pipe_connection',
            email_address: email_address,
            nonce : nonce
        };

        wpspjq.post(wpsp_admin.ajax_url, data, function(response) {
            window.location.href = 'admin.php?page=wp-support-plus&setting=addons&section=email_piping';
        });

    }
}

function wpsp_ep_add_imap(){

  wpspjq('.wpsp_ep_save_pipe_connection_loder,.wpsp_ep_save_pipe_connection_msg').hide();

  var flag = true;
  wpspjq('input.required').each(function(index, el) {
    if (wpspjq(this).val().trim()=='') {
      wpspjq(this).css('border-color', 'red');
      flag = false;
    }
  });

  if (!flag) {
    return;
  }

  wpspjq('.wpsp_ep_save_pipe_connection_loder').show();

  var dataform = new FormData(wpspjq('#wpsp_frm_ep_edit_frm')[0]);
  wpspjq.ajax({
      url: wpsp_admin.ajax_url,
      type: 'POST',
      data: dataform,
      processData: false,
      contentType: false
  })
  .done(function (response) {

      var res = wpspjq.parseJSON(response);
      wpspjq('.wpsp_ep_save_pipe_connection_loder').hide();
      wpspjq('.wpsp_ep_save_pipe_connection_msg').html(res.msg).show();
      if(res.is_error==0){
        //redirect to overview
        window.location.href = 'admin.php?page=wp-support-plus&setting=addons&section=email_piping';
      }
  });

}

function wpsp_ep_delete_imap_connection( id, email_address, nonce ){

    var flag = confirm("Are you sure to remove pipe connection for "+email_address+" ?");
    if(flag){

        var data = {
            action: 'wpsp_ep_delete_imap_pipe_connection',
            id: id,
            nonce : nonce
        };

        wpspjq.post(wpsp_admin.ajax_url, data, function(response) {
          window.location.href = 'admin.php?page=wp-support-plus&setting=addons&section=email_piping';
        });

    }
}
