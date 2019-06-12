<h3><?php _e('Edit Company','wpsp-company');?></h3><br>
<?php 
global $wpdb;
$comp=$wpdb->get_row("select * from {$wpdb->prefix}wpsp_companies where id=".$_REQUEST['id']);
$company=explode(',', $comp->users);
?>
<form id="wpsp_edit_company" method="post">
    <b><?php _e('Company/Usergroup Name','wpsp-company');?>:</b><br>
    <input type="text" id="wpsp_title" name="wpsp_title" value="<?php echo stripcslashes($comp->name);?>" style="width: 50%;"><br><br>
    <strong><?php _e('Users','wpsp-company');?>: </strong>
    <button type="button" id="searchUserModal" onclick="getSearchUserForm();"><?php _e('Add User','wpsp-company');?></button>
    <div id="selectcompanyusers" class="selectcompanyusers" style="border: 5px;">
        <?php foreach ($company as $cm){
                $user=get_userdata( $cm );
                if($user){
                    echo '<div class="companyuser" id='.$cm.'>'.$user->display_name;
                    echo '<img alt="edit" class="wpsp_remove_user_from_company_icon" onclick=removeuser('.$cm.') src='.WPSP_COMP_URL.'asset/images/delete.png>'; 
                    echo "<input type='hidden' name='wpsp_company_employee[]' value='".$user->ID."'></div>";
                }
            } 
        ?>
    </div>
    <br>
    <input type='hidden' name="action" value="editCompanyUser">
    <input type='hidden' id="comp_id" name="comp_id" value="<?php echo $comp->id ?>">
    <button type="submit" class="btn btn-success"><?php _e('Submit','wpsp-company');?></button>
</form>
<div  id="wsp_change_user_modal" style="display:none">
  <div id="modal-dialog">
    <div id="modal-content">
      <div id="modal-header">
          <button type="button" class="close" onclick="wpsp_close_user_popup();">&times;</button>
        <h4 class="title" id="myModalLabel"><?php _e('Select User','wpsp-company');?></h4>
      </div>
      <div id="body">
        <?php include( WPSP_COMP_DIR.'includes/admin/searchRegisteredUsaers.php' );?>
      </div>
      <div id="footer">
          <button type="button" class="btn btn-default" onclick="wpsp_close_user_popup();"><?php _e('Close','wpsp-company');?></button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery("#searchUserModal").click(function(){
            jQuery("#wsp_change_user_modal").show();
            jQuery("selectcompanyusers").show();
        });
    });
    
    jQuery('#wpsp_edit_company').submit(function(e){
	e.preventDefault();
        var flag=true;
        if(jQuery('#wpsp_title').val().trim()==''){
            alert(display_company_data.enter_company_name);
            flag=false;
        }
        var comapny_users=jQuery('#selectcompanyusers').find('.companyuser');
        
        if(comapny_users.length==0){
            alert(display_company_data.select_at_least_one_user);
            flag=false;
        }
        
        if(flag){
            var dataform=new FormData( this );
            jQuery.ajax( {
                url: display_company_data.wpsp_ajax_url,
                type: 'POST',
                data: dataform,
                processData: false,
                contentType: false
            }) 
            .done(function( msg ) {
                window.location.href="<?php echo admin_url('admin.php?page=wp-support-plus-company');?>";
            });
        }
     });
     
    function wpsp_close_user_popup(){
        jQuery('#wsp_change_user_modal').hide();
    }
    
    function removeuser(userid){
        jQuery("#"+userid).remove();
    }

</script>
