<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$tab=(empty($_REQUEST['tab']))?'timer-settings':$_REQUEST['tab'];

?>
<br>
<ul class="nav nav-tabs">
    <li class="<?php echo ($tab=='timer-settings')?'active':'';?>">
        <a href="<?php echo admin_url( 'admin.php?page=wpsp-timer&tab=timer-settings' );?>"><?php _e('Timer Settings','wpsp-timer');?></a>
    </li>
</ul>

<?php
switch ($tab){
    case 'timer-settings': include WPSP_TIMER_DIR.'includes/admin/settings-tabs/timer-settings.php';
        break;
}
?>
