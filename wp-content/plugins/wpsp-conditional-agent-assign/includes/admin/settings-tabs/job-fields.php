<?php
if(!empty($_POST)){
    update_option('wpsp_field1',$_POST['field1']);
}
?>
<br>
<form id="job-fields" method="post" action="<?php echo admin_url( 'admin.php?page=wpsp-customization&tab=job-fields' );?>" onsubmit="">
    Label:<br>
    <input type="text" name="field1" id="field1" value="<?php echo get_option('wpsp_field1');?>"><br>
    <button type="submit">Submit</button>
</form>