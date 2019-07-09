<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$current_section = isset( $_REQUEST['section'] ) ? sanitize_text_field( $_REQUEST['section'] ) : 'addon-advert';

$addon_sections = array (
		'addon-advert' => array(
				'label' => __('Add-ons & Offers','wp-support-plus-responsive-ticket-system'),
				'file'  => WPSP_ABSPATH . 'includes/admin/addons/addon_offers.php'
		),
    'license-settings' => array(
        'label' => __('Licenses','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/addons/wpsp_license_settings.php'
    )
);

$addon_sections = apply_filters( 'wpsp_addon_submenus_sections', $addon_sections );

?>
<div style="width: 100%; text-align: left; margin-bottom: 20px;">
    <ul class="subsubsub">
      <?php
        $numItems=count($addon_sections);
        $i=0;
        foreach ($addon_sections as $key => $section){
            $section_class = $key == $current_section ? 'current' : '';
            $section_href='admin.php?page=wp-support-plus&setting=addons&section='.$key;
            $section_saperator=(++$i != $numItems)?' |':'';
            echo '<li><a class="'.$section_class.'" href="'.$section_href.'">'.$section['label'].'</a></li>'.$section_saperator;
        }
      ?>
     </ul>
</div>
<?php

if( isset( $addon_sections[$current_section] ) ) {
    include( $addon_sections[$current_section]['file'] );
}