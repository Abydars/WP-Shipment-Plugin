<?php

$sql = "SELECT DISTINCT type as name FROM ".$wpdb->prefix.'wpsp_ticket ';

                
/**
 * Where Part
 */
$where = "WHERE 1=1 ";

$where .= 'AND ( ';
$keywords = explode(' ', $s);
$search = array();
foreach ($keywords as $keyword) {
    $search[] = "type LIKE '%" . $keyword . "%'";
}
$where .= implode(' AND ', $search);
$where .= ' ) ';

/**
 * Order By Part
 */
$orderby = 'ORDER BY type asc ';

/**
 * Limit part
 */
$limit = 'LIMIT 5 OFFSET 0';

/**
 * Combining Query
 */
$sql = $sql . $where . $orderby . $limit;

/**
 * Getting results.
 */
$types = $wpdb->get_results($sql);

foreach ($types as $type){
    
    $response[] = array(
        'label'     => ucfirst($type->name),
        'field_key' => $field_key,
        'value'     => $type->name
    );
}

