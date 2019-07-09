<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $current_user, $wpdb, $wpsp_template;

$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();
$signup_url     = wp_registration_url();
$signin_url     = $wpsupportplus->functions->get_support_page_url(array('page'=>'sign-in'));
$signout_url    = $wpsupportplus->functions->get_support_page_url(array('page'=>'sign-out'));

if( $wpsp_user_session ){
    ?>

<div class="navbar-right wpsp_user_info dropdown">
    <div class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" >
        <?php echo get_avatar( $wpsp_user_session['email'], 40, '', '', array('class'=>'img-circle') )?>
        <span class="caret hidden-xs"></span>
        <span class="visible-xs"><strong><?php echo $wpsp_user_session['name'];?></strong></span>
    </div>
    <ul class="dropdown-menu">
        <li>
            <div style="text-align: center;"><?php echo get_avatar( $wpsp_user_session['email'], 80, '', '', array('class'=>'img-circle') )?></div>
            <div style="text-align: center; color: #52545B;">
                <strong><?php echo $wpsp_user_session['name'];?></strong>
            </div>
        </li>
        <li class="divider" role="separator"></li>
        <li style="text-align: center;"><a href="<?php echo $signout_url?>"><?php _e('Sign out', 'wp-support-plus-responsive-ticket-system') ?></a></li>
    </ul>
</div>

    <?php
} else {
    ?>

<ul class="nav navbar-nav navbar-right">
    <li id="wpsp-sign-up-link"><a href="<?php echo $signup_url?>"><span class="glyphicon glyphicon-user"></span> <?php _e('Sign Up', 'wp-support-plus-responsive-ticket-system') ?></a></li>
    <li id="wpsp-sign-in-link"><a href="<?php echo $signin_url?>"><span class="glyphicon glyphicon-log-in"></span> <?php _e('Login', 'wp-support-plus-responsive-ticket-system') ?></a></li>
</ul>

    <?php
}
