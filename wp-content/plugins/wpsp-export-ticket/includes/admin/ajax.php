<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class SupportPlusExpAjax{
    function getExportTicketToExcel(){
        include( WPSP_EXP_PLUGIN_DIR.'includes/admin/getExportTicketToExcel.php' );
        die();
    }

    function setExportTicketToExcel(){
        include( WPSP_EXP_PLUGIN_DIR.'includes/admin/setExportTicketToExcel.php' );
        die();
    }
}
?>