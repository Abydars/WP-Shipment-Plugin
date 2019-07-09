<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$current_section = isset( $_REQUEST['section'] ) ? sanitize_text_field( $_REQUEST['section'] ) : 'general-settings';

$general_sections = array (
    
    'general-settings' => array(
        'label' => __('General Settings','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/general/general-settings.php'
    ),
    'categories' => array(
        'label' => __('Categories','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/general/categories.php'
    ),
    'statuses' => array(
        'label' => __('Statuses','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/general/statuses.php'
    ),
    'priorities' => array(
        'label' => __('Priorities','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/general/priorities.php'
    ),
    'support-button' => array(
        'label' => __('Support Button','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/general/support-button.php'
    ),
    'custom-css' => array(
        'label' => __('Custom CSS','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/general/custom-css.php'
    ),
    'footer-text' => array(
        'label' => __('Footer Text','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/general/footer-text.php'
    ),
    'thank_you_page' => array(
        'label' => __('Thank you page','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/general/thank_you.php'
    ),
		'support-page-menu' => array(
        'label' => __('Support Page Menu','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/general/support_page_menu.php'
    ),
		'general-advanced-settings' => array(
        'label' => __('Advanced Settings','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/general/general-advanced-settings.php'
    )
    
    
);
$sections = apply_filters( 'wpsp_settings_general_sections', $general_sections );

?>
<div>
    <ul class="subsubsub">
        <?php
        $numItems = count($sections);
        $i = 0;
        foreach ($sections as $key => $section){
            $section_class = $key == $current_section ? 'current' : '';
            $section_href='admin.php?page=wp-support-plus&setting=general&section='.$key;
            $section_saperator=(++$i != $numItems)?' |':'';
            echo '<li><a class="'.$section_class.'" href="'.$section_href.'">'.$section['label'].'</a></li>'.$section_saperator;
        }
        ?>
    </ul>
</div>
<?php

if( isset( $sections[$current_section] ) ) {
    include( $sections[$current_section]['file'] );
}
