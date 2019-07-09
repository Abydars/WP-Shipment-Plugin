<?php

$sql = "SELECT * FROM ".$wpdb->prefix.'wpsp_custom_priority ';

                
/**
 * Where Part
 */
$where = "WHERE 1=1 ";

$where .= 'AND ( ';
$keywords = explode(' ', $s);
$search = array();
foreach ($keywords as $keyword) {
    $search[] = "name LIKE '%" . $keyword . "%'";
}
$where .= implode(' AND ', $search);
$where .= ' ) ';

/**
 * Order By Part
 */
$orderby = 'ORDER BY name asc ';

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
$priorities = $wpdb->get_results($sql);

$custom_priority_localize = get_option('wpsp_custom_priority_localize');

foreach ($priorities as $priority){
    
    $response[] = array(
        'label'     => $custom_priority_localize['label_'.$priority->id],
        'field_key' => $field_key,
        'value'     => $priority->id
    );
}

