<?php

$sql = "SELECT distinct t.guest_email as field_key, t.guest_name as field_name FROM ".$wpdb->prefix.'wpsp_ticket t ';

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

$where .= 'AND ( ';
$keywords = explode(' ', $s);
$search_email = array();
$search_name = array();
foreach ($keywords as $keyword) {
    
    $search_email[] = "t.guest_email LIKE '%" . $keyword . "%'";
    $search_name[] = "t.guest_name LIKE '%" . $keyword . "%'";
    
}

$where .= '( '.implode(' AND ', $search_email).' ) OR ( '.implode(' AND ', $search_name).' ) ';

$where .= ' ) ';

/**
 * Order By Part
 */
$orderby = 'ORDER BY t.guest_name asc ';

/**
 * Limit part
 */
$limit = 'LIMIT 5 OFFSET 0';

/**
 * Combining Query
 */
$sql = $sql . $join_str . $where . $orderby . $limit;

/**
 * Getting results.
 */
$fields = $wpdb->get_results($sql);

foreach ($fields as $field){
    
    if($field->field_name){
        
        $response[] = array(
            'label'     => $field->field_name,
            'field_key' => $field_key,
            'value'     => $field->field_key
        );
    }
}

