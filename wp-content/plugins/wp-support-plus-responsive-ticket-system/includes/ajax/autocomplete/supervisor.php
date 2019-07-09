<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus;

$s          = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
$exclude    = isset($_REQUEST['exclude']) && is_array($_REQUEST['exclude']) && $_REQUEST['exclude'] ? $wpsupportplus->functions->sanitize_string_array($_REQUEST['exclude']) : array();

$sql = "select "
                . "u.ID as id, "
                . "u.display_name as name "
                . "FROM {$wpdb->prefix}wpsp_users w "
                . "INNER JOIN  {$wpdb->base_prefix}users u ON u.ID = w.user_id ";

/**
 * Where Part
 */
$where = "WHERE 1=1 AND w.role=2 ";

if ($s) {

    $where .= 'AND ( ';
    $keywords = explode(' ', $s);
    $search = array();
    foreach ($keywords as $keyword) {
        $search[] = "u.user_login LIKE '%" . $keyword . "%'";
        $search[] = "u.display_name LIKE '%" . $keyword . "%'";
        $search[] = "u.user_email LIKE '%" . $keyword . "%'";
    }
    $where .= implode(' OR ', $search);
    $where .= ' ) ';
}

if( $exclude ){
    $exclude_str = implode(',', $exclude);
    $where .= 'AND u.ID NOT IN('.$exclude_str.') ';
}

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
$users = $wpdb->get_results($sql);

$active = TRUE;
foreach ($users as $user){
    $class = $active ? 'active-result heightligted' : 'active-result';
    $active =FALSE;
    ?>
    <li onclick="wpsp_autocomplete_res_choose('<?php echo htmlentities($input_id)?>','supervisors','<?php echo $user->id?>')" onmouseover="wpsp_autocomplete_res_mouseover( '<?php echo htmlentities($input_id)?>', this );" class="<?php echo $class?>"><?php echo $user->name?></li>
    <?php
}
