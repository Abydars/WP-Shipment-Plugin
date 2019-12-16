<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$tab=(empty($_REQUEST['tab']))?'caa-settings':$_REQUEST['tab'];

?>
<br>
<ul class="nav nav-tabs">
    <li class="<?php echo ($tab=='caa-settings')?'active':'';?>">
        <a href="<?php echo admin_url( 'admin.php?page=wpsp-caa&tab=caa-settings' );?>"><?php _e('Conditional Agent Assign','wpsp-caa');?></a>
    </li>
</ul>

<?php
switch ($tab){
    case 'caa-settings': include WPSP_CAA_DIR.'includes/admin/settings-tabs/caa-setting.php';
        break;
}
?>
