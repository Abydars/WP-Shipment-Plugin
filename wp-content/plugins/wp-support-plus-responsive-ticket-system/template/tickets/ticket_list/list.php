<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb, $current_user;

?>

<div class="form-group col-md-12">
    <div class="inner-addon left-addon">
        <button type="button" class="btn wpsp_mobile_filter" onclick="wpsp_display_saved_filters();"><span class="fa fa-filter"></span></button>
        <input type="text" class="form-control" onkeyup="ticket_list_search(this);" placeholder="<?php _e('Search...','wp-support-plus-responsive-ticket-system')?>" value="<?php echo $ticket_filter['s']?>" />
        <i class="glyphicon glyphicon-search wpsp_ticket_list_search"></i>
    </div>
</div>

<div id="wpsp_ticket_list_container" class="col-md-12"></div>


<!--Modal Bodies-->
<div class="modal fade  modal-saved-filter" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="gridSystemModalLabel"><?php _e('Saved Filters', 'wp-support-plus-responsive-ticket-system') ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-12">
                        <select id="cmb_ticket_filters" class="form-control">
                            <option data-agent_visibility="0" value=""><?php _e('Default', 'wp-support-plus-responsive-ticket-system')?></option>
                            <?php
                            $public_filters = $wpsupportplus->functions->get_public_filters();
                            foreach ( $public_filters as $key=>$val ){
                                ?>
                                <option data-agent_visibility="1" value="<?php echo $key?>"><?php echo $val['label']?></option>
                                <?php
                            }
                            ?>
                            <?php
                            $private_filters = $wpsupportplus->functions->get_private_filters();
                            foreach ( $private_filters as $key=>$val ){
                                ?>
                                <option data-agent_visibility="2" value="<?php echo $key?>"><?php echo $val['label']?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="btn_apply_ticket_filter();"><?php _e('Apply', 'wp-support-plus-responsive-ticket-system')?></button>
                <?php
                if($wpsupportplus->functions->is_staff($current_user)){
                    ?>
                    <button class="btn btn-danger" onclick="delete_ticket_filter();"><?php _e('Delete', 'wp-support-plus-responsive-ticket-system')?></button>
                    <?php
                }
                ?>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="ajax_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridSystemModalLabel"></h4>
      </div>
        <div class="modal-body" style="max-height: 300px; overflow: auto;"></div>
      <div class="modal-footer"></div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->