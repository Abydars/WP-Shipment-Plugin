<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$addon_count = apply_filters( 'wpsp_addon_count', 0 );
?>
<div id="tab_container" style="width: 100%; text-align: left; margin-top: 50px;clear:both; margin-bottom:20px;">
    
		<form method="post">
			
			<input type="hidden" name="action" value="update"/>
			<input type="hidden" name="update_setting" value="settings_addon_licenses"/>
			<?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>
			
			<table class="form-table">
				<tbody>
					<?php do_action('wpsp_addon_license_setting');?>
				<tbody>
			</table>
			
			<?php if ( $addon_count > 0 ): ?>
			<p class="submit">
					<input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">
			</p>
			<?php endif; ?>
				
		</form>
		
</div>