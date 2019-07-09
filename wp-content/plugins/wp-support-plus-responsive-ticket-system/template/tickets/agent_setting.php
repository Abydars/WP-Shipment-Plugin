<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpdb,$current_user, $wpsupportplus;
$signature=get_user_meta($current_user->ID,'wpsp_agent_signature',true);

$language = array("azb","ar","he","fa-IR");

$current_site_language = get_bloginfo("language");

$rtl_css = '';

if (in_array($current_site_language, $language) &&  is_rtl()){
	
		$rtl_css = "direction: rtl;";
		
}

if ( $wpsupportplus->functions->is_staff($current_user) ){
  ?>
  <div  id="agent_setting_container" style="<?php echo $rtl_css; ?>">
    <div class="row">
			<div class="col-md-12">
				<label class="label label-default"><?php _e('Signature','wp-support-plus-responsive-ticket-system') ?></label>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12">
				<div id="agent_signature">
		      <textarea id="agent_setting" class="wpsp_reach_text form-control" name="agent_setting"><?php echo stripcslashes(htmlspecialchars_decode($signature,ENT_QUOTES));?></textarea>
		    </div>
		    <button type="submit" onclick="set_agent_setting()" id="agent_setting_btn" class="btn btn-success"><?php _e('Save Settings','wp-support-plus-responsive-ticket-system') ?></button>
			</div>
		</div>
</div>
  <?php
		do_action('wpsp_agent_setting');
}

if ($is_wpsp_template) {
	wpsp_print_page_inline_script_as();
} else {
	add_action('wp_footer', 'wpsp_print_page_inline_script_as', 900000000000 );
}

function wpsp_print_page_inline_script_as(){
	?>
	<script>
	    (function() {
				tinymce.init({
						selector: '#agent_setting',
						body_id: 'wpsp_agent_setting',
						menubar: false,
						height : '180',
						plugins: [
								'lists link directionality'
						],
						toolbar: 'bold italic underline blockquote | alignleft aligncenter alignright | bullist numlist | rtl | link',
						branding: false,
						autoresize_bottom_margin: 20,
						browser_spellcheck : true,
						relative_urls : false,
						remove_script_host : false,
						convert_urls : true
				});
	    }());
	</script>
	<?php
}
