<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$current_section = isset( $_REQUEST['section'] ) ? sanitize_text_field( $_REQUEST['section'] ) : 'list-settings';

$ticket_list_sections = array (
    
    'list-settings' => array(
        'label' => __('List Settings','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/ticket-list/list-settings.php'
    ),
    'default-filters' => array(
        'label' => __('Default Filters','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/ticket-list/default-filters.php'
    ),
		'open-ticket-widget'=>array(
			  'label'=> __('Open Ticket Widgets','wp-support-plus-responsive-ticket-system'),
			  'file'=> WPSP_ABSPATH . 'includes/admin/ticket-list/open-ticket-widget.php'
		),
		'advanced-settings' => array(
        'label' => __('Advanced Settings','wp-support-plus-responsive-ticket-system'),
        'file'  => WPSP_ABSPATH . 'includes/admin/ticket-list/advanced-settings.php'
    )
    
);
$sections = apply_filters( 'wpsp_settings_ticket_list_sections', $ticket_list_sections );

?>
<div>
    <ul class="subsubsub">
        <?php
        $numItems = count($sections);
        $i = 0;
        foreach ($sections as $key => $section){
            $section_class = $key == $current_section ? 'current' : '';
            $section_href='admin.php?page=wp-support-plus&setting=ticket-list&section='.$key;
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
