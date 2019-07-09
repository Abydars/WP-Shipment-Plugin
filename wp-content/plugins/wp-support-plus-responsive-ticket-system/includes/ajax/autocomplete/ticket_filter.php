<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus;

$s              = isset($_REQUEST['term']) ? sanitize_text_field($_REQUEST['term']) : '';
$field_key      = isset($_REQUEST['field_key']) ? sanitize_text_field($_REQUEST['field_key']) : '';

if( !$field_key ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$response = array();

if(!is_numeric($field_key)){
    
    switch ($field_key){
        
        case 'id' : include WPSP_ABSPATH . 'includes/ajax/autocomplete/filters/id.php';
            break;
        case 'status' : include WPSP_ABSPATH . 'includes/ajax/autocomplete/filters/status.php';
            break;
        case 'category' : include WPSP_ABSPATH . 'includes/ajax/autocomplete/filters/category.php';
            break;
        case 'priority' : include WPSP_ABSPATH . 'includes/ajax/autocomplete/filters/priority.php';
            break;
        case 'raised_by' : include WPSP_ABSPATH . 'includes/ajax/autocomplete/filters/raised_by.php';
            break;
        case 'user_type' : include WPSP_ABSPATH . 'includes/ajax/autocomplete/filters/user_type.php';
            break;
        case 'created_agent' : include WPSP_ABSPATH . 'includes/ajax/autocomplete/filters/created_agent.php';
            break;
        case 'assigned_agent' : include WPSP_ABSPATH . 'includes/ajax/autocomplete/filters/assigned_agent.php';
            break;
				default:
						$response = apply_filters( 'wpsp_filter_autocomplete_non_numeric', $response, $field_key );
    }
    
} else {
    
    $custom_field = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_custom_fields where id=".$field_key);

    switch ($custom_field->field_type){
        
        case 1  :
        case 10 :
        default :
            
            $sql = "SELECT distinct t.cust".$field_key." as field FROM ".$wpdb->prefix.'wpsp_ticket t ';

            /**
             * Join part
             */
            $join_str = '';
            $joins = $wpsupportplus->functions->get_ticket_filter_joins();
            foreach ( $joins as $join ){

                $join_str .= $join['type'].' JOIN '.$join['table'].' '.$join['char'].' ON '.$join['on'].' ';
            }

            /**
             * Where Part
             */
            $where = "WHERE 1=1 ";
            $ticket_restrict_rules = $wpsupportplus->functions->get_ticket_list_restrict_rules();
            $where .= "AND ( ". implode(' OR ', $ticket_restrict_rules)." ) ";
            $where .= "AND t.cust".$field_key." LIKE '%".$s."%' ";
            $where .= "AND t.cust".$field_key." <> '' ";
            
            /**
             * Order By Part
             */
            $orderby = "ORDER BY t.cust".$field_key." asc ";

            /**
             * Limit part
             */
            $limit = 'LIMIT 5 OFFSET 0';

            /**
             * Combining Query
             */
            $sql = $sql . $join_str . $where . $orderby . $limit;
            
            $fields     = $wpdb->get_results($sql);
            foreach ($fields as $field){

                $response[] = array(
                    'label'     => $field->field,
                    'field_key' => $field_key,
                    'value'     => $field->field
                );
            }
            break;
            
        case 2  :
        case 3  :
        case 4  :
            
            $fields = unserialize( $custom_field->field_options );
            $found  = preg_grep('/'.$s.'/i', $fields);
            $counter = 0;
            foreach ( $found as $val ){
                
                $response[] = array(
                    'label'     => $val,
                    'field_key' => $field_key,
                    'value'     => $val
                );
                
                $counter++;
                if($counter == 5) break;
            }
            break;
            
    }
    
    $response = apply_filters( 'wpsp_filter_autocomplete', $response, $field_key, $custom_field );
    
}

if(!$response){
    $response[] = array(
        'label'     => __('No results found!', 'wp-support-plus-responsive-ticket-system'),
        'field_key' => '',
        'value'     => 0
    );
}

$response = apply_filters( 'wpsp_autocomplete_ticket_filter_results', $response );

echo json_encode($response);
