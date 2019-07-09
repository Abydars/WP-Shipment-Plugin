<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $current_user;

?>

<div class="wpsp_installation_container">
    
    <h3>Upgrade Warning!</h3>
    
    <div class="wpsp_upgrade_terms_container">
        
        You are about to upgrade WP Support Plus latest version from your old installation version. 
        Our features and policies are updated in this version. Please go through below list for changes that we have made. 
        If you wish to upgrade to latest version and click on "AGREE & UPGRADE" button, you won't be able to roll-back 
        to old version. If you decide not to upgrade, you can download old version <a href="https://www.wpsupportplus.com/wp-content/uploads/2017/10/wpsp_8.0.7.zip">from here</a> and 
        replace in your plugin directory.<br><br>
        
        <strong>1. It will not use your theme anymore!</strong><br>
        In order to avoid conflict in design and JS to what we want to achieve, we have developed our own theme design for support page. 
        It is only applicable for page you choose in setting and not in any other part of your website. This page will only have support plus contents loaded.<br><br>
        
        <strong>2. Some free features are no longer free!</strong><br>
        We are company working full-time to improve this product. For us to continue this work in future, we have moved few features to paid version. Our paid version prices are affordable for everyone, so this is win-win situation for all.
        If you are our existing paid user, you do not need to pay anything.<br>
        Below is list of features which moved to paid version:
        <ul>
            <li>1. Email Notifications</li>
            <li>2. Automatic Agent Assign</li>
            <li>3. Canned Reply</li>
            <li>4. FAQ'S</li>
        </ul>
        
    </div>
    
    <button id="wpsp_upgrade_btn" onclick="wpsp_upgrade_start('<?php echo wp_create_nonce($current_user->ID);?>');" class="button button-primary">AGREE & UPGRADE</button>
    
</div>

<div id="wpsp_wait_html" style="display: none;">
    <div class="wpsp_filter_loading_icon" style="margin-top: 190px;">
        <img src="<?php echo WPSP_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>">
    </div>
    <div style="text-align: center;">Please do not close the window until upgrade process finish.</div>
    <div style="text-align: center; font-weight: bold;">
        <span id="wpsp_upgrade_complete_percentage">0</span>% Completed
    </div>
</div>
