<?php

$sql = "SELECT * FROM ".$wpdb->prefix.'wpsp_catagories ';

                
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
$categories = $wpdb->get_results($sql);

$custom_category_localize = get_option('wpsp_custom_category_localize');

foreach ($categories as $category){
    
    $response[] = array(
        'label'     => $custom_category_localize['label_'.$category->id],
        'field_key' => $field_key,
        'value'     => $category->id
    );
}

