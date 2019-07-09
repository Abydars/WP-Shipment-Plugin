<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb;

$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_fields");

foreach ( $results as $field ) {
    
    if( $field->field_type != 8 ) {
        ?>
        {cust<?php echo $field->id?>} - <?php echo $field->label?><br>
        <?php
    }

}
