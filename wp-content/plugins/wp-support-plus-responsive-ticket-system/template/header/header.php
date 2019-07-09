<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $current_user, $wpdb, $wpsp_template,$wpsp_url;

$wpsp_general_settings_advanced = get_option('wpsp_general_settings_advanced');
$signout = isset($wpsp_general_settings_advanced['signout']) ? $wpsp_general_settings_advanced['signout'] : 1;
$signin = isset($wpsp_general_settings_advanced['signin']) ? $wpsp_general_settings_advanced['signin'] : 1;
$signup = isset($wpsp_general_settings_advanced['signup']) ? $wpsp_general_settings_advanced['signup'] : 1;

$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();
$signup_url        = wp_registration_url();
$signin_url        = $wpsupportplus->functions->get_support_page_url(array('page'=>'sign-in'));
$signout_url       = $wpsupportplus->functions->get_support_page_url(array('page'=>'sign-out'));

$current_page   = isset($_REQUEST['page']) ? sanitize_text_field($_REQUEST['page']) : $wpsp_template->default_support_page();

$header_menu = $wpsupportplus->functions->get_support_page_menus();

?>
<div id="site_logo_container" class="row">
	
</div>
<div>
		<nav class="navbar navbar-inverse navigation-clean-button support_page_header wpsp_header">
				<div class="container">
						<div class="navbar-header">
								<a class="site_logo" href="<?php echo get_home_url();?>" title="<?php _e('Back to website', 'wp-support-plus-responsive-ticket-system')?>">
										<img src="<?php echo $wpsupportplus->functions->get_upload_logo();?>">
								</a>
								<button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navcol-1"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
						</div>
						<div class="collapse navbar-collapse" id="navcol-1">
								<ul class="nav navbar-nav">
									<?php
									foreach ($header_menu as $menu):

											?>
											<li role="presentation">
													<a class="wpsp_header_menu_item" href="<?php echo htmlentities($menu->redirect_url)?>">
															<?php 
																if($menu->icon){
																	?>
																	<img class="header_menu_icon" src="<?php echo $menu->icon?>" />
																	<?php
																}
															 ?>
															<?php echo $menu->name?>
													</a>
											</li>
											<?php

									endforeach;
									?>
								</ul>
								
								<?php 
								$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();
								if ( $wpsp_user_session ) {
									
									?>
									<p class="navbar-text navbar-right actions">
										<b data-toggle="tooltip" data-placement="top" title="<?php echo $wpsp_user_session['name']; ?>"><?php echo get_avatar( $wpsp_user_session['email'], 40, '', '', array('class'=>'img-circle') )?></b>
										<?php 
									if($signout){ ?>	
										<a class="btn action-button wpsp_header_button" id="wpsp_sign_out" role="button" href="<?php echo $signout_url?>"><?php _e('Sign Out', 'wp-support-plus-responsive-ticket-system')?></a>
									<?php } ?>
									</p>
									<?php
									
								} else {
									
									?>
									<p class="navbar-text navbar-right actions">
										<?php 
									if($signin)
									{?>	
										<a class="btn action-button wpsp_header_button" role= "button" href="<?php echo $signin_url?>"><?php _e('Sign In', 'wp-support-plus-responsive-ticket-system')?></a> 
									<?php } ?>
									
									<?php 
										if($signup){ ?>	
											<a class="btn action-button wpsp_header_button" id="wpsp_sign_up" role="button" href="<?php echo $signup_url?>"><?php _e('Sign Up', 'wp-support-plus-responsive-ticket-system')?></a>
									<?php } ?>
									</p>
									<?php
									
								}
								?>
								
						</div>
				</div>
		</nav>
</div>
