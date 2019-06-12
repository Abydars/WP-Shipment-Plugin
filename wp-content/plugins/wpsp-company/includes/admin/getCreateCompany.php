<div class="wpspCompanyContainer">
    <a href="<?php echo admin_url('admin.php?page=wp-support-plus-company&type=add');?>" class="btn btn-primary"><?php _e("+ Add New",'wpsp-company');?></a><br><br>
<?php 
global $wpdb;
global $current_user;
$current_user=wp_get_current_user();
$sql="select * from {$wpdb->prefix}wpsp_companies ";
$company = $wpdb->get_results( $sql );
$comp_id=0;
?>
<table class="table table-striped table-hover companyContainer" id="companyContainer">
    <tr>
        <th style="width: 50px;">#</th>
        <th><?php _e('Name Of Company','wpsp-company');?></th>
        <th><?php _e('Users','wpsp-company');?></th>
        <th><?php _e('Action','wpsp-company');?></th>
    </tr>
    <?php foreach ($company as $comp){ ?>
    <tr>
        <td style="width: 50px;"><?php echo ++$comp_id;?></td>
        <td><?php echo stripcslashes($comp->name);?></td>
        <td><?php if($comp->users!='0'){
                    $comp_users=explode(',', $comp->users);
                    $u_display_names=array();
                    foreach ($comp_users as $user){
                        $userdata=get_userdata($user);
                        if($userdata){
                            $u_display_names[]=$userdata->display_name;
                        }
                    }
                }
                ?>
            <?php echo implode(',',$u_display_names);?></td>
        <td>
            <button class="btn btn-info" onclick="editcompany(<?php echo $comp->id?>);"><?php _e('Edit','wpsp-company');?></button>
            <button class="btn btn-danger" onclick="deletcompany(<?php echo $comp->id;?>);"><?php _e('Delete','wpsp-company');?></button>
        </td>
    </tr><?php
        } ?>
</table>
</div>
<script>
function deletcompany(id){
    if(confirm("Are you sure?")){
        location.href="<?php echo admin_url('admin.php?page=wp-support-plus-company&type=delete&noheader=true');?>&id="+id;
    }
}

function editcompany(id){
        location.href="<?php echo admin_url('admin.php?page=wp-support-plus-company&type=edit');?>&id="+id;
}
</script>
