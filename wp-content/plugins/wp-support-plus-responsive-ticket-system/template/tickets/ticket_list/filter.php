<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpsupportplus, $wpdb, $current_user;

$list_keys = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_ticket_list_order ORDER BY load_order");

$filters = $wpsupportplus->functions->get_ticket_filters();

$ticket_filter = $wpsupportplus->functions->get_current_ticket_filter_applied();

$page = isset($_REQUEST['page_no']) ? intval(sanitize_text_field($_REQUEST['page_no'])) : 0;
if(!$page){
    $ticket_filter['page'] = 1;
} else {
    $ticket_filter['page'] = $page;
}

$wpsp_ticket_active_pre_value = get_option('wpsp_ticket_active_pre_value');
$hide_selected = '';
$show_selected = '';

?>

<div class="col-md-12" id="filter_header_container">
    <h4>
        <?php _e('Apply Filters','wp-support-plus-responsive-ticket-system')?>
    </h4>
    <button type="button" class="btn btn-sm btn-info" onclick="wpsp_display_saved_filters();"><?php _e('Saved Filters','wp-support-plus-responsive-ticket-system')?></button>
    <button type="button" class="btn btn-sm btn-info" onclick="btn_reset_ticket_filter(this);"><?php _e('Reset Filter','wp-support-plus-responsive-ticket-system')?></button>
</div>

<div class="col-md-12" style="padding-left: 0px;padding-right: 0px;">
    <hr>
</div>

<div class="col-md-12" id="filter_body_container" style="padding-left: 0px;padding-right: 0px;">
    
    <form id="ticket_filter" onsubmit="false">

        <input type="hidden" name="action" value="wpsp_get_tickets" />
        <input type="hidden" id="wpsp_nonce" name="nonce" value="<?php echo wp_create_nonce()?>" />
        <input type="hidden" id="page_no" name="filter[page]" value="<?php echo $ticket_filter['page']?>" />
        <input type="hidden" id="search" name="filter[s]" value="<?php echo $ticket_filter['s']?>" />
        <input type="hidden" id="wpsp_filter_sort_by" name="filter[order_by]" value="<?php echo $ticket_filter['order_by']?>" />
        <input type="hidden" id="wpsp_filter_sort_by_order" name="filter[orderby_order]" value="<?php echo $ticket_filter['orderby_order']?>" />

        <?php
        foreach ($filters as $filter){

            ?>
            <div class="form-group col-md-12">
                <div class="form-group col-md-12 inner_control">
                    <strong><?php echo $wpsupportplus->functions->get_ticket_list_column_label($filter->field_key)?>:</strong>
                </div>
                <?php
                if( $filter->join_relation == 'LIKE' ){
                    ?>
                    <div id="filter_<?php echo $filter->field_key?>" class="form-group col-md-12 inner_control">
												<?php 
												if($filter->field_key == 'deleted_ticket'){ 
														
														if($wpsp_ticket_active_pre_value['ticket_active'] == 0){
															$show_selected = 'selected=selected';
														}else{
															$hide_selected = 'selected=selected';
														}
														?>
												    <select id="wpsp_select_deleted_ticket_filter_val" name="wpsp_select_deleted_ticket_filter_val" class="form-control" onchange="wpsp_deleted_ticket_filter()">
										          <option <?php echo $hide_selected ?> value="1" ><?php _e('OFF','wp-support-plus-responsive-ticket-system')?></option>
										          <option <?php echo $show_selected ?> value="0"><?php _e('ON','wp-support-plus-responsive-ticket-system')?></option>
							              </select><?php
												}else{ ?>
                        	<img data-field_key="<?php echo 'filter_wait_'.$filter->field_key?>" src="<?php echo WPSP_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>" height="30" width="20" align="right" style="display:none;margin-top:4px; margin-bottom: -36px; margin-left: 200px;padding-top: 10px;" />
													<input data-field_key="<?php echo $filter->field_key?>" class="form-control filter_autocomplete" type="text" autocomplete="off" placeholder="<?php _e('Search...','wp-support-plus-responsive-ticket-system')?>" />
												<?php }
											  if( isset($ticket_filter['elements']) && isset($ticket_filter['elements'][$filter->field_key]) ){

                            foreach ( $ticket_filter['elements'][$filter->field_key]['val'] as $key=>$val ){

                                $label = $ticket_filter['elements'][$filter->field_key]['label'][$key];
                                ?>
                                <div class="wpsp_autocomplete_choice_item">
                                    <?php echo $label?> <span onclick="wpsp_autocomplete_choice_item_delete(this)" class="fa fa-times wpsp_autocomplete_choice_item_delete"></span>
                                    <input type="hidden" name="filter[elements][<?php echo $filter->field_key?>][label][]" value="<?php echo htmlentities($label)?>">
                                    <input type="hidden" name="filter[elements][<?php echo $filter->field_key?>][val][]" value="<?php echo htmlentities($val)?>">
                                </div>
                                <?php

                            }

                        }
                        ?>
                    </div>
                    <?php
                } else {

                    $from_date  = isset($ticket_filter['elements'][$filter->field_key]['from']) ? $ticket_filter['elements'][$filter->field_key]['from'] : '';
                    $to_date    = isset($ticket_filter['elements'][$filter->field_key]['to']) ? $ticket_filter['elements'][$filter->field_key]['to'] : '';
                    ?>
                    <div class="form-group col-md-6 inner_control">
                        <input data-key="<?php echo $filter->field_key?>" class="form-control wpsp_date date_filter filter_<?php echo $filter->field_key?>" name="filter[elements][<?php echo $filter->field_key?>][from]" value="<?php echo $from_date?>" type="text" autocomplete="off" placeholder="<?php _e('From','wp-support-plus-responsive-ticket-system')?>" />
                    </div>
                    <div class="form-group col-md-6 inner_control">
                        <input data-key="<?php echo $filter->field_key?>" class="form-control wpsp_date date_filter filter_<?php echo $filter->field_key?>" name="filter[elements][<?php echo $filter->field_key?>][to]" value="<?php echo $to_date?>" type="text" autocomplete="off" placeholder="<?php _e('To','wp-support-plus-responsive-ticket-system')?>" />
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
				do_action( 'wpsp_add_new_ticket_filter' );
        ?>

    </form>
</div>

<?php 
if($wpsupportplus->functions->is_staff($current_user)){
    ?>
    <form id="frm_save_filter_widget" onsubmit="return false;">
        <div id="save_filter_widget" class="form-group col-md-12">
            <strong><?php _e('Save Current Filter','wp-support-plus-responsive-ticket-system')?>:</strong>
            <input class="form-control" type="text" name="filter_name" placeholder="<?php _e('Filter Name','wp-support-plus-responsive-ticket-system')?>" />
            <?php
            if($wpsupportplus->functions->is_administrator($current_user)){
                ?>
                <select class="form-control" name="filter_type">
                    <option value="private"><?php _e('Private','wp-support-plus-responsive-ticket-system')?></option>
                    <option value="public"><?php _e('Public','wp-support-plus-responsive-ticket-system')?></option>
                </select>
                <?php
            }
            ?>
            <button class="form-control btn btn-success" type="button" onclick="save_filter();"><?php _e('Submit','wp-support-plus-responsive-ticket-system')?></button>
        </div>
    </form>
    <?php
}

if ($is_wpsp_template) {
	wpsp_print_page_inline_script_filter();
} else {
	add_action('wp_footer', 'wpsp_print_page_inline_script_filter', 900000000000 );
}
function wpsp_print_page_inline_script_filter(){
	?>
	<script>
	    (function() {
	        get_tickets();
	    }());
	</script>
	<?php
}
