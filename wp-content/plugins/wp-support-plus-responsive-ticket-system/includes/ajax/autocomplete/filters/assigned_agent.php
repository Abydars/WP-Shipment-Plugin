<?php

$sql = "select "
                . "u.ID as id, "
                . "u.display_name as name "
                . "FROM {$wpdb->prefix}wpsp_users w "
                . "INNER JOIN  {$wpdb->base_prefix}users u ON u.ID = w.user_id ";

                
/**
 * Where Part
 */
$where = "WHERE 1=1 ";

$where .= 'AND ( ';

$keywords = explode(' ', $s);

$search_login           = array();
$search_display_name    = array();
$search_user_email      = array();

foreach ($keywords as $keyword) {
    $search_login[]         = "u.user_login LIKE '%" . $keyword . "%'";
    $search_display_name[]  = "u.display_name LIKE '%" . $keyword . "%'";
    $search_user_email[]    = "u.user_email LIKE '%" . $keyword . "%'";
}

$where .= '( '.implode(' AND ', $search_login).' ) OR ( '.implode(' AND ', $search_display_name).' ) OR ( '.implode(' AND ', $search_user_email).' ) ';

$where .= ' ) ';

/**
 * Order By Part
 */
$orderby = 'ORDER BY u.display_name asc ';

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
$agents = $wpdb->get_results($sql);

$response[] = array(
    'label'     => 'None',
    'field_key' => $field_key,
    'value'     => 0
);

foreach ($agents as $agent){
    
    $response[] = array(
        'label'     => $agent->name,
        'field_key' => $field_key,
        'value'     => $agent->id
    );
}

