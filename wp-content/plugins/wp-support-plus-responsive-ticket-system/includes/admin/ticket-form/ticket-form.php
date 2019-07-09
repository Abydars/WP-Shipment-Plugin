<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$current_section = isset( $_REQUEST['section'] ) ? sanitize_text_field( $_REQUEST['section'] ) : 'form-management';

$custom_form_sections = array (
    
    'form-management' => array(
        'label' => __('Manage Form','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/ticket-form/form-management.php'
    ),
    'custom-fields' => array(
        'label' => __('Custom Fields','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/ticket-form/custom-fields.php'
    )
    
);
$sections = apply_filters( 'wpsp_settings_custom_form_sections', $custom_form_sections );

?>
<div>
    <ul class="subsubsub">
        <?php
        $numItems = count($sections);
        $i = 0;
        foreach ($sections as $key => $section){
            $section_class = $key == $current_section ? 'current' : '';
            $section_href='admin.php?page=wp-support-plus&setting=ticket-form&section='.$key;
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
