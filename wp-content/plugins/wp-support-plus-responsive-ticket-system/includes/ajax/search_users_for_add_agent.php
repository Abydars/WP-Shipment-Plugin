<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Check nonce
 */
if( !isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce']) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$s = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
$s = $s ? '*'.$s.'*' : '';

$site_users=$wpdb->get_results("SELECT user_id FROM {$wpdb->prefix}wpsp_users");

$site_users_array=array();
foreach ($site_users as $key => $value) {
	$site_users_array[]=$value->user_id;
}

$args=array(
	 'blog_id'      => $GLOBALS['blog_id'],
	 'exclude'      => $site_users_array,
	 'orderby'      => 'display_name',
	 'order'        => 'ASC',
	 'search'       => $s,
	 'number'       => '5',
);
$all_users = get_users( $args );

$active = TRUE;
foreach ($all_users as $user){
    $class = $active ? 'wpsp-autocomplete-result-item wpsp-autocomplete-result-item-selected' : 'wpsp-autocomplete-result-item';
    $active =FALSE;
    ?>
    <div id="<?php echo $user->data->ID?>" onclick="wpsp_aut_choose_item(this)" onmouseover="wpsp_aut_item_select(this);" class="<?php echo $class?>"><?php echo $user->data->display_name?></div>
    <?php
}
