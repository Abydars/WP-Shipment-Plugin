<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$GLOBALS['wpsp_template'] = new WPSP_Template_Functions();

global $wpsupportplus, $current_user, $wpdb, $wpsp_template;

$is_wpsp_template = true;
$GLOBALS['is_wpsp_template'] = true;

$support_btn_settings   = $wpsupportplus->functions->get_support_btn_settings();
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $support_btn_settings['support_btn_label']?></title>

    <link href="<?php echo WPSP_PLUGIN_URL.'asset/library/jquery-ui/jquery-ui.min.css?version='.WPSP_VERSION;?>" rel="stylesheet">
    <link href="<?php echo WPSP_PLUGIN_URL.'asset/library/jquery-ui/jquery-ui.structure.min.css?version='.WPSP_VERSION;?>" rel="stylesheet">
    <link href="<?php echo WPSP_PLUGIN_URL.'asset/library/jquery-ui/jquery-ui.theme.min.css?version='.WPSP_VERSION;?>" rel="stylesheet">
    <link href="<?php echo WPSP_PLUGIN_URL.'asset/library/bootstrap/css/bootstrap.min.css?version='.WPSP_VERSION;?>" rel="stylesheet">
    <link href="<?php echo WPSP_PLUGIN_URL.'asset/library/font-awesome/css/font-awesome.min.css?version='.WPSP_VERSION;?>" rel="stylesheet">
    <link href="<?php echo WPSP_PLUGIN_URL.'asset/library/ImageViewer/imageviewer.css?version='.WPSP_VERSION;?>" rel="stylesheet">
    <link href="<?php echo WPSP_PLUGIN_URL.'asset/css/public.css?version='.WPSP_VERSION;?>" rel="stylesheet">
    
    <script src="<?php echo WPSP_PLUGIN_URL.'asset/library/jquery.min.js?version='.WPSP_VERSION;?>"></script>
    <script src="<?php echo WPSP_PLUGIN_URL.'asset/library/jquery-ui/jquery-ui.min.js?version='.WPSP_VERSION;?>"></script>
    <script src="<?php echo WPSP_PLUGIN_URL.'asset/library/bootstrap/js/bootstrap.min.js?version='.WPSP_VERSION;?>"></script>
    <script src="<?php echo WPSP_PLUGIN_URL.'asset/library/tinymce/tinymce.min.js?version='.WPSP_VERSION;?>"></script>
    <script src="<?php echo WPSP_PLUGIN_URL.'asset/library/ImageViewer/imageviewer.min.js?version='.WPSP_VERSION;?>" type="text/javascript"></script>
    <script src="<?php echo WPSP_PLUGIN_URL.'asset/js/public.js?version='.WPSP_VERSION;?>"></script>
    
    <?php do_action('wpsp_enqueue_scripts');?>
    
    <script>
    var wpspjq=jQuery.noConflict();
		var wpsp_data = <?php $wpsp_template->js_data()?>;
    var link = true;
    </script>
    <style type="text/css">
         
				 <?php 
				 // Custom CSS
				 echo stripslashes($wpsupportplus->functions->get_custom_css());
				 
				 //Customize General
				 $customize_general = $wpsupportplus->functions->get_customize_general();
				 foreach ($customize_general as $css) {
				 		echo stripslashes($css);
				 }
				 
				 //Ticket List
				 $customize_ticket_list = $wpsupportplus->functions->get_customize_ticket_list();
				 foreach ($customize_ticket_list as $css) {
				 		echo stripslashes($css);
				 }
				 
				 do_action('wpsp_custom_css');
				 ?>
				 
    </style>
    
  </head>
  <body id="wpsp_body">
    
    <?php
    $wpsp_template->get_header();
    
    $current_page = isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : $wpsp_template->default_support_page();
    
    switch ($current_page){
        case 'tickets': include( WPSP_ABSPATH . 'template/tickets/tickets.php' );
            break;
        case 'sign-in': include( WPSP_ABSPATH . 'template/header/sign-in.php' );
            break;
        case 'sign-out': include( WPSP_ABSPATH . 'template/header/sign-out.php' );
            break;
        default : do_action( 'wpsp_include_page', $current_page);
            break;
    }
    
    $wpsp_template->get_footer();
    ?>
  </body>
</html>