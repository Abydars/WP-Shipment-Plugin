<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $current_user, $wpdb, $wpsp_template;

$signin_url      = $wpsupportplus->functions->get_support_page_url(array('page'=>'sign-in'));
$ticket_page_url = $wpsupportplus->functions->get_support_page_url();
$redirect_url    = $wpsp_template->is_guest_tickets_allowed() ? $ticket_page_url : $signin_url;

if( is_user_logged_in() ):

    wp_logout();

endif;

?>
<script>

    window.location.href = '<?php echo $redirect_url?>';

</script>