<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $current_user, $wpdb, $is_wpsp_template;

$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();
$enable_default_login=$wpsupportplus->functions->is_enable_default_login();

do_action('action_before_sign_in');

if( !$wpsp_user_session ):
?>
<div class="container">
<?php 
if(!$enable_default_login){
	if($wpsupportplus->functions->get_default_login()){?>
    <form class="form-signin" method="post" action="" >
        
        <h2 class="form-signin-heading"><?php echo _e('Please sign in','wp-support-plus-responsive-ticket-system')?></h2>
        
        <div id="wpsp_sign_in_notice"></div>
        
        <label class="sr-only"><?php echo _e('Username or email','wp-support-plus-responsive-ticket-system')?></label>
        <input id="inputEmail" name="username" class="form-control" placeholder="<?php echo _e('Username or email','wp-support-plus-responsive-ticket-system')?>" required="" autofocus="" autocomplete="off" type="text" value="<?php echo isset($_POST['username']) ? esc_attr($_POST['username']) : '';?>">
        
        <label for="inputPassword" class="sr-only"><?php echo _e('Password','wp-support-plus-responsive-ticket-system')?></label>
        <input id="inputPassword" name="password" class="form-control" placeholder="<?php echo _e('Password','wp-support-plus-responsive-ticket-system')?>" required="" autocomplete="off" type="password">
        
        <div class="checkbox">
            <label>
                <input name="remember" value="remember-me" type="checkbox"> <?php echo _e('Remember me','wp-support-plus-responsive-ticket-system')?>
            </label>
            <div class="pull-right forgot-password">
                <a href="<?php echo wp_lostpassword_url()?>"><?php echo _e('Forgot your password?','wp-support-plus-responsive-ticket-system')?></a>
            </div>
        </div>
        
        <input type="hidden" name="action" value="wpsp_signin" />
        <input type="hidden" name="nonce" value="<?php echo wp_create_nonce()?>" />
        
        <button class="btn btn-lg btn-success btn-block" type="submit"><?php _e('Sign in','wp-support-plus-responsive-ticket-system')?></button>
    </form>
	<?php } else {	
    $login_url=wp_login_url($redirect = '', $force_reauth = false);
		if ($wpsupportplus->functions->get_custom_login_redirect_url()){ 
					$login_url= $wpsupportplus->functions->get_custom_login_redirect_url();
		}
    ?>
    <a href="<?php echo htmlentities($login_url); ?>" id="wpsp_login_link"><b ><center style="margin-top:100px;"><?php _e('Click Here to Login','wp-support-plus-responsive');?></center></b></a>
    <?php
		}
	}
	?>
    
    <?php if($wpsupportplus->functions->is_allow_guest_ticket()):?>
    
        <form class="form-signin" method="post" action="" >
               <?php if(!$enable_default_login){?>
								 <div id="wpsp_or_guest_login" class="col-md-12" style="padding-left: 0px;padding-right: 0px;">
		                 <div class="col-md-4" style="padding-left: 0px;padding-right: 0px;">
		                     <hr style="border-top: 1px solid #000;">
		                 </div>
		                 <div class="col-md-4" style="padding-left: 0px;padding-right: 0px;">
		                     <h4 style="text-align: center"><?php _e('OR','wp-support-plus-responsive-ticket-system')?></h4>
		                 </div>
		                 <div class="col-md-4" style="padding-left: 0px;padding-right: 0px;">
		                     <hr style="border-top: 1px solid #000;">
		                 </div>
		             </div>
							<?php }?>
            
							<div class="col-md-12" style="padding-left: 0px;padding-right: 0px;">
                <h2 class="form-signin-heading" style="">
									<?php echo _e('Guest login','wp-support-plus-responsive-ticket-system')?>
									<span class="badge" style="cursor:pointer;" data-toggle="tooltip" data-placement="top" title="<?php _e('No user account required. Create or access tickets created earlier by guest login.','wp-support-plus-responsive-ticket-system')?>">?</span>
								</h2>

                <div id="wpsp_guest_sign_in_notice"></div>

                <label class="sr-only"><?php echo _e('Name','wp-support-plus-responsive-ticket-system')?></label>
                <input name="guest_name" class="form-control" placeholder="<?php echo _e('Your Name','wp-support-plus-responsive-ticket-system')?>" required="" autofocus="" autocomplete="off" type="text" value="<?php echo isset($_POST['guest_name']) ? esc_attr($_POST['guest_name']) : '';?>">

                <label class="sr-only"><?php echo _e('Email','wp-support-plus-responsive-ticket-system')?></label>
                <input name="guest_email" class="form-control" placeholder="<?php echo _e('Your Email','wp-support-plus-responsive-ticket-system')?>" required="" autocomplete="off" type="email" value="<?php echo isset($_POST['guest_email']) ? esc_attr($_POST['guest_email']) : '';?>">

                <input type="hidden" name="action" value="wpsp_guest_signin" />
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce()?>" />

                <button class="btn btn-lg btn-success btn-block" style="margin-top: 10px;" type="submit"><?php _e('Sign in','wp-support-plus-responsive-ticket-system')?></button>
            </div>

        </form>
    <?php endif;?>

</div>
<?php
endif;

if ( isset($_REQUEST['wpsp_signin_response']) ) {
	
	if ($is_wpsp_template) {
		?>
		<script type="text/javascript">
			wpspjq('#wpsp_sign_in_notice').html('<?php echo html_entity_decode($_REQUEST['wpsp_signin_response']['messege'])?>');
			wpspjq('#inputPassword').val('');
			<?php if($_REQUEST['wpsp_signin_response']['success']):?>
					window.location.href = '<?php echo urldecode($_REQUEST['redirect_to'])?>';
			<?php endif;?>
		</script>
		<?php
	} else {
		add_action('wp_footer', 'wpsp_print_signin_script', 900000000000 );
		function wpsp_print_signin_script(){
			?>
			<script type="text/javascript">
				wpspjq('#wpsp_sign_in_notice').html('<?php echo html_entity_decode($_REQUEST['wpsp_signin_response']['messege'])?>');
				wpspjq('#inputPassword').val('');
				<?php if($_REQUEST['wpsp_signin_response']['success']):?>
						window.location.href = '<?php echo urldecode($_REQUEST['redirect_to'])?>';
				<?php endif;?>
			</script>
			<?php
		}
	}
	
}

if ( isset($_REQUEST['wpsp_guest_signin_response']) ) {
	
	if ($is_wpsp_template) {
		?>
		<script type="text/javascript">
			wpspjq('#wpsp_guest_sign_in_notice').html('<?php echo html_entity_decode($_REQUEST['wpsp_guest_signin_response']['messege']);?>');
			<?php if($_REQUEST['wpsp_guest_signin_response']['success']):?>
					window.location.href = '<?php echo urldecode($_REQUEST['redirect_to'])?>';
			<?php endif;?>
		</script>
		<?php
	} else {
		add_action('wp_footer', 'wpsp_print_guest_signin_script', 900000000000 );
		function wpsp_print_guest_signin_script(){
			?>
			<script type="text/javascript">
				wpspjq('#wpsp_guest_sign_in_notice').html('<?php echo html_entity_decode($_REQUEST['wpsp_guest_signin_response']['messege']);?>');
				<?php if($_REQUEST['wpsp_guest_signin_response']['success']):?>
						window.location.href = '<?php echo urldecode($_REQUEST['redirect_to'])?>';
				<?php endif;?>
			</script>
			<?php
		}
	}
}

if ($is_wpsp_template) {
	?>
	<script>
	wpspjq('[data-toggle="tooltip"]').tooltip();
	</script>
	<?php
} else {
	add_action('wp_footer', 'wpsp_print_signin_toggle_script', 900000000000 );
	function wpsp_print_signin_toggle_script(){
		if(isset($_REQUEST['wpsp_guest_signin_response'])):
		?>
		<script type="text/javascript">
			wpspjq('#wpsp_guest_sign_in_notice').html('<?php echo html_entity_decode($_REQUEST['wpsp_guest_signin_response']['messege']);?>');
			<?php if($_REQUEST['wpsp_guest_signin_response']['success']):?>
					window.location.href = '<?php echo urldecode($_REQUEST['redirect_to'])?>';
			<?php endif;?>
		</script>
		<?php
	  endif;
	}
}