<?php
if(!empty($_POST)){
    update_option('wpsp_field2',$_POST['field2']);
}
?>
<br>
<form id="cust-fields" method="post" action="<?php echo admin_url( 'admin.php?page=wpsp-timer&tab=cust-fields' );?>" onsubmit="">
    Label:<br>
    <input type="text" name="field2" id="field2" value="<?php echo get_option('wpsp_field2');?>"><br>
    <button type="submit">Submit</button>
</form>