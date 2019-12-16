<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

<div class="show_conditions">     
    <?php         
    include( WPSP_CAA_DIR.'includes/admin/showconditions.php' );     
    ?> 
</div>

<div id="wpsp_condition_container">     
    <div class="add_condition"></div>     
    <div class="edit_condition"></div>     
    <div class="wait"><img alt="Please Wait" src="<?php echo WCE_PLUGIN_URL.'asset/images/ajax-loader@2x.gif?ver='.WPSP_VERSION;?>"></div> 
</div>
