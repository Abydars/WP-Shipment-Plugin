<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$cu = wp_get_current_user();
if (!$cu->has_cap('manage_options')) exit; // Exit if current user is not admin

global $wpdb;
$wpsp_canned_reply_template=get_option( 'wpsp_canned_reply_template' );
?>
<br>
<span class="label label-info wpsp_title_label">
<?php _e("Available Templates For Reply", 'wpsp-canned-reply' );?>
</span><br><br>
<div class='template_display'>
<?php 
foreach ($wpsp_canned_reply_template['templates'] as $key=>$val){
        echo '{'.$key.'} - '.__($val,'wpsp-canned-reply').'<br>';
}
?>
</div>
<br>