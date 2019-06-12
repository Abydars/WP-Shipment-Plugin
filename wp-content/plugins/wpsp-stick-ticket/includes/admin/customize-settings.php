<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$tab=(empty($_REQUEST['tab']))?'settings':$_REQUEST['tab'];

?>
<br>
<ul class="nav nav-tabs">
    <li class="<?php echo ($tab=='setting')?'active':'';?>">
        <a href="<?php echo admin_url( 'admin.php?page=wpsp-stick-ticket&tab=settings' );?>"><?php _e('Settings','wpsp-stick-ticket');?></a>
    </li>

</ul>

<?php
switch ($tab){
    case 'settings': include WPSP_STICK_DIR.'includes/admin/settings-tabs/settings.php';
        break;
}
?>
