<?php
global $current_user;
$wpsp_ticket_active_pre_value = get_option('wpsp_ticket_active_pre_value');

$sql = "SELECT t.id as id FROM ".$wpdb->prefix.'wpsp_ticket t ';

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
$where = "WHERE 1=1 AND t.active=".$wpsp_ticket_active_pre_value['ticket_active']." ";

if(!$wpsupportplus->functions->is_allow_staff_read_all_ticket() || !$wpsupportplus->functions->is_staff($current_user)){
    $ticket_restrict_rules = $wpsupportplus->functions->get_ticket_list_restrict_rules();
    $where .= "AND ( ". implode(' OR ', $ticket_restrict_rules)." ) ";
}

$where .= 'AND ( ';
$keywords = explode(' ', $s);
$search = array();
foreach ($keywords as $keyword) {
    $search[] = "t.id LIKE '%" . $keyword . "%'";
}
$where .= implode(' AND ', $search);
$where .= ' ) ';

/**
 * Order By Part
 */
$orderby = 'ORDER BY t.id asc ';

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
$ids = $wpdb->get_results($sql);

foreach ($ids as $id){
    
    $response[] = array(
        'label'     => $id->id,
        'field_key' => $field_key,
        'value'     => $id->id
    );
}

