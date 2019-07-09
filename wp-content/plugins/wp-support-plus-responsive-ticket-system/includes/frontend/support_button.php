<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus,$wpdb;

$support_btn_settings   = $wpsupportplus->functions->get_support_btn_settings();
$header_menu            = $wpsupportplus->functions->get_header_menu();
$support_icon           = apply_filters( 'wpsp_support_btn_icon', $support_btn_settings['img_url'] );

$slider_menu=$wpsupportplus->functions->get_custom_slider_menus();
$general=get_option('wpsp_settings_general');
$target='';
if($general['support_btn_new_tab']){
		$target='target="_blank"';
}
if($support_btn_settings['allow_support_btn']):
?>

    <img id="wpsp_helpdesk_agent" src="<?php echo $support_icon?>" />
    
    <div id="wpsp_helpdesk_widget">
        
        <div  id="support_button_outer_div" style="width: 100%; overflow: hidden; background-color: #239FDB; padding: 5px 15px 5px 10px; font-weight: bold;">
            <span><?php echo $support_btn_settings['support_btn_label']?></span>
            <img id="wpsp_helpdesk_widget_minimize" src="<?php echo WPSP_PLUGIN_URL.'asset/images/icons/minimize.png'?>" />
        </div>
        
        <?php
        foreach($slider_menu as $menu){
            ?>
            <a href="<?php echo htmlentities($menu->redirect_url); ?>" <?php echo $target?>>
                <div class="wpsp_helpdesk_widget_menu_item">
                    <table>
                        <tr>
                            <td class="menu_item_icon"><img src="<?php echo $menu->menu_icon;?>" /></td>
                            <td class="menu_item_label"><?php echo $menu->menu_text;?></td>
                        </tr>
                    </table>
                </div>
            </a>
            <?php
        }
        ?>
        
    </div>
    
    <?php

endif;
