<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$ticket_id  	= isset($_POST['ticket_id']) 		? intval(sanitize_text_field($_POST['ticket_id'])) : 0 ;
$guest_email 	= isset($_POST['guest_email']) 	? sanitize_text_field($_POST['guest_email']) : '' ;
$nonce      	= isset($_POST['nonce']) 				? sanitize_text_field($_POST['nonce']) : '' ;

/**
 * Check nonce
 */
if( !wp_verify_nonce( $nonce, $ticket_id ) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

$select_field_list = $wpsupportplus->functions->get_user_all_tickets_select_field_list();

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
$where = "WHERE guest_email='".$guest_email."' AND t.active=1 ";

/**
 * Combining Query
 */
$sql = $sql . $join_str . $where;

/**
 * Getting results.
 */
$tickets = $wpdb->get_results($sql);

$sql = "select * from {$wpdb->prefix}wpsp_ticket_list_order where customer_ticket=1 ORDER BY load_order";

$list_keys = $wpdb->get_results($sql);

include_once WPSP_ABSPATH . 'includes/ajax/ticket-list/class-ticket-list-formating.php';
$list_format = new WPSP_Ticket_List_Formating();

$customer_name = $wpdb->get_var("SELECT guest_name FROM {$wpdb->prefix}wpsp_ticket WHERE guest_email='".$guest_email."' AND id=".$ticket_id);
$modal_title   = $customer_name.' ('.__('All Tickets','wp-support-plus-responsive-ticket-system').')';


ob_start();

?>

<form>
<div class="table-responsive">
		<table id="tbl_ticket_created" class="table table-striped table-bordered" cellspacing="0" width="100%">
				<thead>
				<tr>
					<?php foreach ($list_keys as $key => $keys) {?>
						<th>
							<?php echo $wpsupportplus->functions->get_ticket_list_column_label($keys->field_key)?></th>
					<?php	
					}?>
				 
				</tr>

				</thead>

				<tbody>
				     <?php
							 foreach ($tickets as $ticket){?>
									
									 <tr data-href="<?php echo $wpsupportplus->functions->get_support_page_url(array('page'=>'tickets','section'=>'ticket-list','action'=>'open-ticket','id'=>$ticket->ID,'dc'=>time()))?>" onclick="if(link)open_ticket_redirect(this);">
								 	 <?php 
									 foreach ( $list_keys as $key ){

 											$val = $ticket->{'field_'.$key->field_key};

 											$field_type = is_numeric($key->field_key) ? $wpsupportplus->functions->get_custom_field_type($key->field_key) : $key->field_key ;

 										?>
 										<td class="wpsp_td_field_<?php echo $key->field_key?>"><?php $list_format->format($val, $field_type, $ticket)?></td>
 										<?php
 								}
								?>
								</tr>
								<?php
							}
							?>
				</tbody>
		</table>
</div>
</form>
<link rel="stylesheet" type="text/css" href="<?php echo WPSP_PLUGIN_URL.'asset/library/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSP_PLUGIN_URL.'asset/library/DataTables/datatables.min.js';?>"></script>
<script>
    wpspjq('#tbl_ticket_created').DataTable();
</script>
<?php

$modal_body = ob_get_clean();

$modal_footer = "";

$response = array(
    'title'     => $modal_title,
    'body'      => $modal_body,
		'footer'    => $modal_footer
);

echo json_encode($response);
