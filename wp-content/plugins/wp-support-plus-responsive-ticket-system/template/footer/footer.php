<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $current_user, $wpdb;
?>
<?php  
$wpsp_settings_general = get_option('wpsp_settings_general');
$allow_powered_by_text = isset($wpsp_settings_general['allow_powered_by_text']) ? $wpsp_settings_general['allow_powered_by_text'] : 1;
?>
<div style="margin-top: 20px;"></div>
<div id="footer" class="navbar navbar-inverse">
		<div class="container-fluid" style="margin-top: 15px;">
				<div id="footer_text"><?php do_action('wpsp_footer_text')?></div>
						<?php if($allow_powered_by_text == 1){ ?>
											<p id="branding" class="text-muted">Powered by <a href="https://www.wpsupportplus.com" target="_blank">WP Support Plus</a></p>
						<?php } ?>
				</div>
</div>