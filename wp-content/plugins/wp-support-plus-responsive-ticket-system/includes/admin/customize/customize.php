<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

$current_section = isset( $_REQUEST['section'] ) ? sanitize_text_field( $_REQUEST['section'] ) : 'general';

$customize_sections = array (
    
    'general' => array(
        'label' => __('General','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/customize/general.php'
    ),
    'ticket_list' => array(
        'label' => __('Ticket List','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/customize/ticket_list.php'
    )
    
);
$sections = apply_filters( 'wpsp_customize_general_sections', $customize_sections );

?>
<div>
    <ul class="subsubsub">
        <?php
        $numItems = count($sections);
        $i = 0;
        foreach ($sections as $key => $section){
            $section_class = $key == $current_section ? 'current' : '';
            $section_href='admin.php?page=wp-support-plus&setting=customize&section='.$key;
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
