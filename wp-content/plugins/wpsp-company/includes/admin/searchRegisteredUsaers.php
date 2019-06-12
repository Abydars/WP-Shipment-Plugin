<form id="frmSearchRegisteredUser" onsubmit="searchRegisteredUserByKeyword();return false;">
        <input id="txtSearchRegUser" type="text" placeholder="<?php _e('Username or Email','wpsp-company');?>"/>
        <button type="submit" class="btn btn-primary"><?php _e('Search','wpsp-company');?></button>
</form>
<div id="wpsp_registered_user_container">
	<input type="text" id="user">
</div>
<div id="wpsp_search_reg_user_wait" style="text-align: center;height: 250px;">
	<img alt="Please Wait" style="margin-top: 120px;" src="<?php echo WPSP_COMP_URL.'asset/images/ajax-loader@2x.gif?ver='.WPSP_COMP_VERSION;?>">
</div>
<script type="text/javascript">
    function getSearchUserForm(){
        jQuery('#txtSearchRegUser').val('');
        searchRegisteredUserByKeyword();
    }

    function searchRegisteredUserByKeyword(){
        jQuery('#wpsp_registered_user_container').hide();
        jQuery('#wpsp_search_reg_user_wait').show();

        var data = {
                'action': 'wpspSearchRegisteredUser',
                'search_keywords':jQuery('#txtSearchRegUser').val().trim()
        };

        jQuery.post('<?php echo admin_url( 'admin-ajax.php' );?>', data, function(response) {
                jQuery('#wpsp_search_reg_user_wait').hide();
                jQuery('#wpsp_registered_user_container').html(response);
                jQuery('#wpsp_registered_user_container').show();
        });
    }

    function wpspChangeUserFromSearchTable(user_id,user_name){
        jQuery( '#selectcompanyusers' ).add( "#create_ticket_as_user" );
        jQuery('#wsp_change_user_modal').hide();
        jQuery( '#selectcompanyusers' ).show();
        var flag=true;
        jQuery('.companyuser').each(function(){ 
           if(jQuery(this).attr('id')==user_id){
              flag=false; 
           }
        });
        if(flag){
            jQuery('#selectcompanyusers').append("<div class='companyuser' id="+user_id+">" +user_name+ " <img alt='Edit' title='Edit' onclick='wpsp_add_company_removeuser("+user_id+");' src='<?php echo WPSP_COMP_URL.'asset/images/delete.png';?>' /><input type='hidden' name='wpsp_company_employee[]' value='"+user_id+"'></div>");
        }

    }
    function wpsp_add_company_removeuser(userid){
        jQuery("#"+userid).remove();
    }
</script>