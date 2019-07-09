<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $current_user, $wpdb;

$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();

$ticket_filter = isset($_POST['filter']) && is_array($_POST['filter']) ? $wpsupportplus->functions->sanitize_string_array($_POST['filter']) : array();

$ticket_active = isset( $_POST['wpsp_select_deleted_ticket_filter_val'] ) ? sanitize_text_field( $_POST['wpsp_select_deleted_ticket_filter_val'] ) : 1;
$wpsp_ticket_active_pre_value = get_option('wpsp_ticket_active_pre_value');
$wpsp_ticket_active_pre_value['ticket_active'] = $ticket_active;

if( !$ticket_filter ){
    die(__('Filter not found!', 'wp-support-plus-responsive-ticket-system'));
}

@setcookie("wpsp_ticket_filters", base64_encode(json_encode($ticket_filter)), 0, COOKIEPATH);

$filter_orderby = sanitize_text_field($ticket_filter['order_by']);
$filter_orderby_order = sanitize_text_field($ticket_filter['orderby_order']);

$default_filters = $wpsupportplus->functions->get_default_filters();

$s = isset($ticket_filter['s']) ? sanitize_text_field($ticket_filter['s']) : '';

$select_field_list = $wpsupportplus->functions->get_tickets_select_field_list();

$per_page = $wpsupportplus->functions->is_staff($current_user) ? $default_filters['agent_par_page_tickets'] : $default_filters['customer_par_page_tickets'] ;
$current_page = sanitize_text_field($ticket_filter['page']);

$date_format = str_replace('dd','d',$wpsupportplus->functions->get_date_format());
$date_format = str_replace('mm','m',$date_format);
$date_format = str_replace('yy','Y',$date_format);

$sql = "SELECT SQL_CALC_FOUND_ROWS t.id as ID, ".$select_field_list." FROM ".$wpdb->prefix.'wpsp_ticket t ';

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
$where = "WHERE t.active = ".$wpsp_ticket_active_pre_value['ticket_active'].' ';

update_option('wpsp_ticket_active_pre_value',$wpsp_ticket_active_pre_value );

$staff_to_read_all_ticket=$wpsupportplus->functions->is_allow_staff_read_all_ticket();
if( !$wpsupportplus->functions->is_staff($current_user) || ( $wpsupportplus->functions->is_staff($current_user) && !$staff_to_read_all_ticket ) ){

    $ticket_restrict_rules = $wpsupportplus->functions->get_ticket_list_restrict_rules();
    $where .= "AND ( ". implode(' OR ', $ticket_restrict_rules)." ) ";
}

$element_flag = false;

if( isset($ticket_filter['elements']) ){

    foreach ( $ticket_filter['elements'] as $key=>$element ){

        $element_db = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_ticket_list_order where field_key='".$key."'");

        if( $element_db->join_relation == 'LIKE' ){

            if( $element_db->field_key == 'assigned_agent' ){

                $element_flag = true;

                $rlike = array();
                foreach ( $element['val'] as $agent_id ){

                    $rlike[] = "{$element_db->join_match} RLIKE '(^|,)".$agent_id."(,|$)'";

                }

                $where .= "AND ( ". implode(' OR ', $rlike)." ) ";



            } else {

                $val = sanitize_text_field(implode("','", $element['val']));
                if( $val != '' ){

                    $element_flag = true;
                    $where .= "AND {$element_db->join_match} IN('".$val."') ";

                }

            }

        } else {

            $from   = sanitize_text_field($element['from']);
            $to     = sanitize_text_field($element['to']);

            if( $from && $to ){

                $element_flag = true;

                $date   = date_create_from_format($date_format, $from);
                $from   = $date->format('Y-m-d').' 00:00:00';

                $date   = date_create_from_format($date_format, $to);
                $to     = $date->format('Y-m-d').' 23:59:59';

                $local_time_zone = $wpsupportplus->functions->tz_offset_to_hrs(get_option('gmt_offset'));

                $where .= "AND ( STR_TO_DATE(LEFT( CONVERT_TZ({$element_db->join_match},'+00:00','$local_time_zone'), LOCATE( ' ', CONVERT_TZ({$element_db->join_match},'+00:00','$local_time_zone') )),'%Y-%m-%d %H:%i:%s') BETWEEN '$from' AND '$to' ) ";

            }
        }
    }
}

if($s){

    $element_flag = TRUE;
    $where .= 'AND ( ';
    $keywords = explode(' ', $s);
    $search = array();
    foreach ($keywords as $keyword) {

        $search[] = "t.subject LIKE '%" . $keyword . "%'";

    }
    $where .= implode(' AND ', $search);
    $where .= ' ) ';
}

if( !$element_flag ) {

    if($wpsupportplus->functions->is_staff($current_user)){

        $hide_statuses = isset($default_filters['agent_hide_statuses']) ? $default_filters['agent_hide_statuses'] : array();

    } else {

        $hide_statuses = isset($default_filters['customer_hide_statuses']) ? $default_filters['customer_hide_statuses'] : array();

    }

    if($hide_statuses){

        $where .= "AND t.status_id NOT IN(". implode(',', $hide_statuses).") ";

    }

}

/**
 * Order By Part
 */
$orderby = 'ORDER BY '.$filter_orderby.' '.$filter_orderby_order.' ';

/**
 * Limit part
 */
$limit = 'LIMIT '.$per_page.' OFFSET '.($current_page*$per_page-$per_page) ;

/**
 * Combining Query
 */
$sql = $sql . $join_str . $where . $orderby . $limit;


/**
 * Getting results.
 */
$tickets = $wpdb->get_results($sql);

/**
 * Total rows found
 */
$total_items = $wpdb->get_var("SELECT FOUND_ROWS()");

$total_pages = ceil($total_items/$per_page);

$sql = "select * from {$wpdb->prefix}wpsp_ticket_list_order where customer_visible=1 ORDER BY load_order";
if($wpsupportplus->functions->is_staff($current_user)){
    $sql = "select * from {$wpdb->prefix}wpsp_ticket_list_order where agent_visible=1 ORDER BY load_order";
}
$list_keys = $wpdb->get_results($sql);

include_once WPSP_ABSPATH . 'includes/ajax/ticket-list/class-ticket-list-formating.php';
$list_format = new WPSP_Ticket_List_Formating();

?>
<div class="row">

    <div class="col-md-9 wpsp_ticket_filter_actions" style="text-align: left;">
        <button type="button" class="btn btn-sm btn-default wpsp_btn_ticket_list_action visible-sm visible-xs" id="btn_mo_reset_filter" onclick="btn_reset_ticket_filter(this);"><?php _e('Reset Filter','wp-support-plus-responsive-ticket-system')?></button>
        <?php if(isset($default_filters['ticket_list_filter']) && $default_filters['ticket_list_filter']) {?>
								<button type="button" onclick="get_ticket_filter();"class="btn btn-sm btn-default hidden-xs hidden-sm wpsp_btn_ticket_list_action" id="btn_ticket_filter"><?php _e('Hide Filters','wp-support-plus-responsive-ticket-system')?></button>
				<?php
			} else{ ?>
								<button type="button" onclick="get_ticket_filter();"class="btn btn-sm btn-default hidden-xs hidden-sm wpsp_btn_ticket_list_action" id="btn_ticket_filter"><?php _e('Show Filters','wp-support-plus-responsive-ticket-system')?></button>
				<?php
			}
        if( $wpsupportplus->functions->cu_has_cap( 'bulk_change_status' ) ){
            ?>
            <button type="button" onclick="get_bulk_change_status('<?php echo wp_create_nonce()?>');" class="btn btn-sm btn-default wpsp_btn_ticket_list_action checkbox_depend disabled" id="btn_change_status"><?php _e('Change Status','wp-support-plus-responsive-ticket-system')?></button>
            <?php
        }
        ?>
        <?php
         if( $wpsupportplus->functions->cu_has_cap( 'bulk_assign_agent' ) ){
            ?>
            <button type="button" onclick="get_bulk_assign_agent('<?php echo wp_create_nonce()?>');"class="btn btn-sm btn-default wpsp_btn_ticket_list_action checkbox_depend disabled" id="btn_assign_agent"><?php _e('Assign Agent','wp-support-plus-responsive-ticket-system')?></button>
            <?php
        }
        ?>
        <?php
        if( $wpsupportplus->functions->cu_has_cap( 'bulk_delete_ticket' ) ){
            ?>
            <button type="button" onclick="get_delete_bulk_ticket('<?php echo wp_create_nonce()?>');"class="btn btn-sm btn-default wpsp_btn_ticket_list_action checkbox_depend disabled" id="btn_delete_ticket"><?php _e('Delete','wp-support-plus-responsive-ticket-system')?></button>
            <?php
        }
        ?>
				
        <?php do_action('wpsp_above_ticket_list');?>
    </div>

    <div class="col-md-3" style="text-align: right;">

        <?php
        if( $total_items > $per_page ){
            printf(__('<strong>%d</strong>-<strong>%d</strong> of <strong>%d</strong> Tickets','wp-support-plus-responsive-ticket-system'),(($current_page*$per_page-$per_page)+1),($current_page*$per_page),$total_items);
        } else {
            printf(__('<strong>%d</strong> Tickets','wp-support-plus-responsive-ticket-system'),$total_items);
        }
        ?>

    </div>

</div>
<div class="table-responsive">

    <table id="tbl_wpsp_ticket_list" class="table">
        <thead>
            <tr>
                <?php
                if($wpsupportplus->functions->is_staff($current_user)){
                    ?>
                    <th style="min-width:0px; max-width: 30px;">
                        <input id="chk_all_ticket_list" onchange="toggle_list_checkboxes(this);" type="checkbox" />
                    </th>
                    <?php
                }

								do_action( 'wpsp_th_after_checkbox_ticket_list' );

                foreach ( $list_keys as $key ){

                    $orderKey   = $key->join_compare;
                    $orderState = $key->join_compare == $filter_orderby ? 1 : 0;
                    $orderbyOrder = $orderState ? $filter_orderby_order : '';

                    $order_indicator = '';
                    if( $orderbyOrder && $orderbyOrder == 'ASC' ){
                        $order_indicator = '<i class="fa fa-arrow-down" aria-hidden="true"></i>';
                    }
                    if( $orderbyOrder && $orderbyOrder == 'DESC' ){
                        $order_indicator = '<i class="fa fa-arrow-up" aria-hidden="true"></i>';
                    }

                    ?>
                    <th data-orderKey="<?php echo $orderKey?>" data-orderState="<?php echo $orderState?>" data-orderbyOrder="<?php echo $orderbyOrder?>" class="wpsp_td_field_<?php echo $key->field_key?> wpsp_header_sort"><?php echo $wpsupportplus->functions->get_ticket_list_column_label($key->field_key)?> <?php echo $order_indicator?></th>
                    <?php

                }
								do_action( 'wpsp_ticket_list_add_th');
                ?>
            </tr>
        </thead>
        <tbody>

            <?php

						do_action( 'wpsp_before_ticket_list', $tickets, $list_keys, $list_format );

            foreach ( $tickets as $ticket ){
								
                $ticket_data_cap = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket->ID );
								$table_tr_css='';
								$table_tr_css=apply_filters('wpsp_ticket_list_tr_style',$table_tr_css,$ticket_data_cap);
                ?>
                <tr style="<?php echo $table_tr_css;?>" data-href="<?php echo $wpsupportplus->functions->get_support_page_url(array('page'=>'tickets','section'=>'ticket-list','action'=>'open-ticket','id'=>$ticket->ID,'dc'=>time()))?>" onclick="if(link)wpsp_redirect(this);">
                    <?php
                    if($wpsupportplus->functions->is_staff($current_user)){
                        ?>
                        <td scope="row" onmouseover="link=false;" onmouseout="link=true;" style="min-width:0px; max-width: 30px;">
                           <?php if( $wpsupportplus->functions->cu_has_cap_ticket( $ticket_data_cap, 'change_status' ) ):?>
                            <input type="checkbox" name="chk_ticket_list_item[]" class="chk_ticket_list_item" onchange="toggle_ticket_list_actions();" value="<?php echo htmlentities($ticket->ID) ?>" />
                           <?php endif;?>
                        </td>
                        <?php
                    }

										do_action( 'wpsp_td_after_checkbox_ticket_list', $ticket );

                    foreach ( $list_keys as $key ){

                        $val = $ticket->{'field_'.$key->field_key};

                        $field_type = is_numeric($key->field_key) ? $wpsupportplus->functions->get_custom_field_type($key->field_key) : $key->field_key ;

                        ?>
                        <td class="wpsp_td_field_<?php echo $key->field_key?>"><?php $list_format->format($val, $field_type, $ticket)?></td>
                        <?php
                    }
										do_action( 'wpsp_ticket_list_add_td', $ticket);
                    ?>
                </tr>
                <?php
            }

            if( !$total_items ){
                ?>
                <tr>
                    <td colspan="<?php echo count($list_keys)+1?>" style="text-align: center;"><?php _e('No Tickets found!','wp-support-plus-responsive-ticket-system')?></td>
                </tr>
                <?php
            }
            ?>

        </tbody>
    </table>
</div>

<?php if($total_items) :?>
    <div class="row">
        <div class="col-md-4 col-md-offset-4" style="text-align: center;">
						<?php  
						$language = array("azb","ar","he","fa-IR");
						
						$current_site_language = get_bloginfo("language");
						
						$rtl_prev_page = '';
							
						if (in_array($current_site_language, $language) &&  is_rtl()){
								
								$rtl_prev_page = "fa fa-chevron-circle-right";
									
						}else{
							
							$rtl_prev_page = "fa fa-chevron-circle-left";
							
						}
						?>
            <button class="btn btn-default btn-sm" onclick="wpsp_ticket_prev_page();"><i class="<?php echo $rtl_prev_page; ?>" aria-hidden="true"></i></button>
            <?php
						$language = array("azb","ar","he","fa-IR");
						
						$current_site_language = get_bloginfo("language");
						
						$rtl_class_next_page = '';
							
						if (in_array($current_site_language, $language) &&  is_rtl()){
								
								$rtl_class_next_page = "fa fa-chevron-circle-left";
									
						}else{
							
							$rtl_class_next_page = "fa fa-chevron-circle-right";
							
						}
            printf(__('<strong>%d</strong> of <strong>%d</strong> Pages','wp-support-plus-responsive-ticket-system'), $current_page, $total_pages);
            ?>
            <button class="btn btn-default btn-sm" onclick="wpsp_ticket_next_page();"><i class="<?php echo $rtl_class_next_page; ?>" aria-hidden="true"></i></button>

        </div>
    </div>
<?php endif;?>

<script>
var total_pages = <?php echo $total_pages?>;

jQuery(function () {
    wpspjq('.wpsp_header_sort').click(function(){

        var key     = wpspjq(this).attr('data-orderKey');
        var state   = parseInt(wpspjq(this).attr('data-orderState'));
        var order   = wpspjq(this).attr('data-orderbyOrder');

        if( state === 1 ){
            if( order === 'ASC' ) {
                order = 'DESC';
            } else {
                order = 'ASC';
            }
        } else {
            order = 'ASC';
        }

        wpspjq('#wpsp_filter_sort_by').val(key);
        wpspjq('#wpsp_filter_sort_by_order').val(order);

        get_tickets();

    });
});

jQuery(function () {
  wpspjq('[data-toggle="tooltip"]').tooltip();
});
</script>
