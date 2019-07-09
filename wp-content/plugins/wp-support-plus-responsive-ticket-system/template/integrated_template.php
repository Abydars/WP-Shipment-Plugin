<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$GLOBALS['wpsp_template'] = new WPSP_Template_Functions();

global $wpsupportplus, $current_user, $wpdb, $wpsp_template;

$is_wpsp_template = false;
$GLOBALS['is_wpsp_template'] = false;

$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();
$signup_url        = wp_registration_url();
$signin_url        = $wpsupportplus->functions->get_support_page_url(array('page'=>'sign-in'));
$signout_url       = $wpsupportplus->functions->get_support_page_url(array('page'=>'sign-out'));

?>

<div class="bootstrap-iso">
  
    <script>
    var wpsp_data = <?php $wpsp_template->js_data()?>;
    var link = true;
    </script>
    <style type="text/css">
          
          .modal-backdrop{
            display: none !important;
          }
          
         <?php 
         // Custom CSS
         echo stripslashes($wpsupportplus->functions->get_custom_css());
         echo stripslashes($wpsupportplus->functions->get_theme_intrgration());
         
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

    <?php
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

    ?>
    <div style="margin-bottom:40px;"></div>

</div>
<?php
