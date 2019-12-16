<?php if (!defined('ABSPATH')) exit;?>

<h3><?php _e('Add New Company/Usergroup','wpsp-company');?></h3><br>
<?php 
global $wpdb;
global $current_user;
$current_user=wp_get_current_user();
$roleManage=get_option( 'wpsp_role_management' );
?>
<form id="wpsp_add_company" method="post">
    <b><?php _e('Company/Usergroup Name','wpsp-company');?>:</b><br>
    <input type="text" id="wpsp_title" name="wpsp_title"><br><br>
    <strong><?php _e('Users','wpsp-company');?>: </strong>
    <button type="button" class="" id="searchUserModal" onclick="getSearchUserForm();"><?php _e('Add User','wpsp-company');?></button>
    <div id="selectcompanyusers" class="selectcompanyusers"></div>
    <br>
    <input type='hidden' name="action" value='setCompanyUser'>
    <button type="submit" class="btn btn-success"><?php _e('Submit','wpsp-company');?></button>
</form>
<div id="wsp_change_user_modal" style="display:none">
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
        });
    });

    jQuery('#wpsp_add_company').submit(function(e){
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
</script>
