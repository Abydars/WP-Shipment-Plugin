<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $wpsupportplus;
$status         = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_custom_status where id=".$ticket->status_id );
$priority       = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_custom_priority where id=".$ticket->priority_id );
$category_name  = $wpdb->get_var( "select name from {$wpdb->prefix}wpsp_catagories where id=".$ticket->cat_id );
$ipaddr         = $wpdb->get_var("select ip_address from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id);

include_once WPSP_ABSPATH . 'template/tickets/open-ticket/class-fields-formatting.php';
$fields_format = new WPSP_Ticket_Field_Formatting( $ticket_id, $ticket );

$custom_status_localize   = get_option('wpsp_custom_status_localize');
$custom_category_localize = get_option('wpsp_custom_category_localize');
$custom_priority_localize = get_option('wpsp_custom_priority_localize');
$default_widget_order=get_option('wpsp_ticket_widget_order');

do_action( 'wpsp_before_open_ticket_sidebar_module', $ticket );
?>
<?php
foreach($default_widget_order as $key=>$value){

if($key=='ticket-status'){
?>
<div class="sidebar-module" id="tic_status_sidebar">

    <h4>

        <?php _e('Ticket Status','wp-support-plus-responsive-ticket-system');?>
				
				<?php if( $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'change_status' ) && $ticket->active != 0 ):?>
		            
		    		<button onclick="change_ticket_status(<?php echo htmlentities($ticket->id)?>);" class="btn btn-default btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></button>
		        
		    <?php endif;?>
				
		</h4>
    
    <hr class="sidebar-module-hr">

    <div class="ticket_status_sidebar">
        <strong><?php _e('Status','wp-support-plus-responsive-ticket-system');?>:</strong> <span class="wpsp_admin_label" style="background-color:<?php echo $status->color?>;"><?php echo stripcslashes($custom_status_localize['label_'.$ticket->status_id])?></span>
    </div>

    <div class="ticket_status_sidebar">
        <strong><?php _e('Category','wp-support-plus-responsive-ticket-system');?>:</strong> <?php echo stripcslashes($custom_category_localize['label_'.$ticket->cat_id])?>
    </div>

    <div class="ticket_status_sidebar">
        <strong><?php _e('Priority','wp-support-plus-responsive-ticket-system');?>:</strong> <span class="wpsp_admin_label" style="background-color:<?php echo $priority->color?>;"><?php echo stripcslashes($custom_priority_localize['label_'.$ticket->priority_id])?></span>
    </div>

</div>
<?php
}
?>
<?php do_action( 'wpsp_after_change_status_sidebar_module', $ticket,$key );?>
<?php if($key=='raised-by'){ ?>
<?php if( $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'view_raised_by' ) ):?>

    <div class="sidebar-module" id="tic_raisedby_sidebar">
        <h4>
            <?php _e('Raised By','wp-support-plus-responsive-ticket-system');?>
						
						<?php if( $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'change_raised_by' ) && $ticket->active != 0 ):?>

		        		<button onclick="get_change_raised_by(<?php echo $ticket->id?>);" class="btn btn-default btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></button>

		        <?php endif;?>
						
			  </h4>
    
        <hr class="sidebar-module-hr">

        <table id="tbl_wpsp_sidebar_raisedby">
            <tr>

                <td>
                    <?php echo get_avatar( $ticket->guest_email, 80, '', '', array('class'=>'img-circle') )?>
                </td>

                <td style="padding-left: 5px;">

                  <strong><?php echo $ticket->guest_name?></strong><br>

                  <?php
                  
									if( $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'view_raised_by_email' ) ){
                      echo '<small>'.$ticket->guest_email.'</small><br>';
									}
									
									// All Tickets of this user
									if($wpsupportplus->functions->is_staff($current_user)){
										?>
										<span style="cursor:pointer;color: #337ab7" onclick="get_all_tickets(<?php echo $ticket->id?>,'<?php echo $ticket->guest_email?>');">
												<i class="fa fa-envelope wpsp_raisedby_icon" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="<?php echo __('All Tickets of this user','wp-support-plus-responsive-ticket-system') ?>"></i>
										</span>	
							  		<?php
									}
									
									// IP address of this user
								 	if($wpsupportplus->functions->is_staff($current_user) && !empty($ipaddr)){
										 $address="https://ipinfo.io/".$ipaddr;
							 	 		 ?>
										 	<a target='_blank' href="<?php echo $address ?>">
												<i class="fa fa-map-marker wpsp_raisedby_icon" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="<?php echo __('User location by IP address','wp-support-plus-responsive-ticket-system') ?>"></i>
											</a>
										 <?php 
							 		}

									// biography of this user
									$bio = get_user_meta( $ticket->created_by,'description' );
									if(!empty( $bio[0] )){
										?>
								 		<span style="cursor: pointer; color:#337ab7;" onclick="get_user_biography(<?php echo $ticket->id?>);">
											<i class="fa fa-address-book wpsp_raisedby_icon" aria-hidden="true" data-toggle="tooltip" data-placement="top" title="<?php echo __('Biographical Information','wp-support-plus-responsive-ticket-system') ?>"></i>
										</span>
										<?php
									}
									
									do_action('wpsp_raisedby_sidebar_module_icons', $ticket);
									
									?>
                </td>

            </tr>
        </table>

    </div>

<?php endif;?>
<?php
}
?>
<?php do_action( 'wpsp_after_change_raisedby_sidebar_module', $ticket,$key );?>
<?php if($key=='assigned-agent'){ ?>
<?php if( $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'view_assign_agent' ) ):?>

    <div class="sidebar-module" id="tic_assign_agent_sidebar">
        <h4>
            <?php _e('Assigned Agents','wp-support-plus-responsive-ticket-system');?>
						
						<?php if( $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'change_assign_agent' ) && $ticket->active != 0 ):?>

		        		<button onclick="get_assign_agent(<?php echo $ticket->id?>);" class="btn btn-default btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></button>

		        <?php endif;?>

        </h4>
        <hr class="sidebar-module-hr">
				<table id="wpsp_ticket_assigned_agents">
				<?php
        $agents = $ticket->assigned_to ? explode(',', $ticket->assigned_to ) : array();

        foreach ($agents as $agent){

            $user = get_userdata($agent);
						$avatar = get_avatar( $user->user_email, 20, '', '', array('class'=>'img-circle') );
						
            ?>

            <tr>
								<td style="width:25px !important;"><div style="padding:2px 0;"><?php echo $avatar;?> </div> </td>
								<td><div style="font-size:16px; padding:0 5px;"><?php echo $user->display_name;?></div> </td>
						</tr>
						<?php

        }

        if(!$agents){
            _e('None','wp-support-plus-responsive-ticket-system');
        }

        ?>
				</table>
    </div>

<?php endif;?>
<?php
}
?>
<?php do_action( 'wpsp_after_assign_agent_sidebar_module', $ticket,$key );?>
 <?php if($key=='agent-only-fields'){ ?>
<?php if( $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'view_agent_fields' ) ):?>

    <div id="sidebar-module-agent-fields" class="sidebar-module">

        <h4>
            <?php _e('Agent Only Fields','wp-support-plus-responsive-ticket-system');?>
						
						<?php if( $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'change_agent_fields' ) && $ticket->active != 0 ):?>

		        		<button onclick="get_agent_fields(<?php echo htmlentities($ticket->id)?>);" class="btn btn-default btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></button>

		        <?php endif;?>
						
				</h4>
        <hr class="sidebar-module-hr">

        <?php

        $sql = "SELECT * FROM {$wpdb->prefix}wpsp_custom_fields WHERE isVarFeild=1";

        $agent_fields = $wpdb->get_results( $sql );

				if( $agent_fields ) {

            foreach ( $agent_fields as $custom_field ){

                ?>
                <div class="ticket-field-sidebar">

                    <strong><?php echo $wpsupportplus->functions->get_ticket_list_column_label($custom_field->id)?>:</strong>

                    <?php

                    if( $wpsupportplus->functions->is_new_line_field_type($custom_field->field_type) ){

                        echo '<br>';
                    }

                    $fields_format->get_field_val($custom_field);

                    ?>

                </div>
                <?php

            }

        } else {

            _e('No Agent Only fields','wp-support-plus-responsive-ticket-system');
        }

        ?>

    </div>

<?php endif;?>
<?php } ?>
<?php do_action( 'wpsp_after_agent_fields_sidebar_module', $ticket ,$key);?>
 <?php if($key=='ticket-fields'){ ?>
<div class="sidebar-module" id="tic_fields_sidebar">

    <h4>
        <?php _e('Ticket Fields','wp-support-plus-responsive-ticket-system');?>
				
				<?php if($ticket->active != 0): ?>
        
		        <?php if( $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'change_fields' ) ):?>
		        
		            <button onclick="get_ticket_fields(<?php echo htmlentities($ticket->id)?>);" class="btn btn-default btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></button>
		        
		        <?php endif;?>
				
				<?php endif;?>
        
    </h4>
    <hr class="sidebar-module-hr">

    <?php

        $sql = "SELECT c.id as id, c.field_type as field_type FROM {$wpdb->prefix}wpsp_custom_fields c "
                . "INNER JOIN {$wpdb->prefix}wpsp_ticket_form_order f "
                . "ON c.id=f.field_key "
                . "WHERE 1=1 "
                . "AND c.isVarFeild=0 "
                . "AND ( "
                        . "c.field_categories = 0 OR c.field_categories RLIKE '(^|,)".$ticket->cat_id."(,|$)'"
                    . " ) "
                . "ORDER BY f.load_order ASC ";

        $ticket_fields = $wpdb->get_results( $sql );

        if( $ticket_fields ) {

            foreach ( $ticket_fields as $custom_field ){

                ?>
                <div class="ticket-field-sidebar">

                    <strong><?php echo $wpsupportplus->functions->get_ticket_list_column_label($custom_field->id)?>:</strong>

                    <?php

                    if( $wpsupportplus->functions->is_new_line_field_type($custom_field->field_type) ){

                        echo '<br>';
                    }

                    $fields_format->get_field_val($custom_field);

                    ?>

                </div>
                <?php

            }

        } else {

            _e('No Ticket fields','wp-support-plus-responsive-ticket-system');
        }

        ?>

</div>
<?php }?>
<?php }  ?>
<?php 
do_action( 'wpsp_after_open_ticket_sidebar_module', $ticket );

if ($is_wpsp_template) {
	wpsp_print_sidebar_inline_script();
} else {
	add_action('wp_footer', 'wpsp_print_sidebar_inline_script', 900000000000 );
}
function wpsp_print_sidebar_inline_script(){
	?>
	<script>

	    jQuery(function () {
	      wpspjq('[data-toggle="tooltip"]').tooltip();
	    });

	    jQuery(function () {
	        var viewer = ImageViewer();
	        wpspjq('.wpsp_ticket_thread_content img').click(function () {
	            var imgSrc = this.src;
	            viewer.show(imgSrc);
	        });
	    });

	    jQuery(document).ready(function (){

	        wpspjq(document).find('.wpsp_ticket_thread_body').each(function(){

	            var height = parseInt(wpspjq(this).height());

	            if( height > wpsp_data.ticket_thread_body_height ){
	                wpspjq(this).height(wpsp_data.ticket_thread_body_height);
	                wpspjq(this).parent().find('.wpsp_ticket_thread_expander').text(wpsp_data.lbl_view_more);
	                wpspjq(this).parent().find('.wpsp_ticket_thread_expander').show();
	            }

	        });

	        wpspjq('.wpsp_ticket_thread_content img').addClass('img-responsive');

	    });

	    function ticket_reply_extended_validations(){

	        $flag_validate = true;

	        <?php do_action('ticket_reply_extended_validations');?>

	        return $flag_validate;

	    }

	    function ticket_note_extended_validations(){

	        $flag_validate = true;

	        <?php do_action('ticket_note_extended_validations');?>

	        return $flag_validate;

	    }

	</script>
	<?php
}

?>

<?php ob_start();?>

<div id="reply_confirm_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridSystemModalLabel"><?php _e('Please Confirm','wp-support-plus-responsive-ticket-system')?></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12"><?php _e('Are you sure to post this reply?','wp-support-plus-responsive-ticket-system')?></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel','wp-support-plus-responsive-ticket-system')?></button>
        <button type="button" onclick="post_ticket_reply()" class="btn btn-primary"><?php _e('Confirm','wp-support-plus-responsive-ticket-system')?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php
$modal_content = ob_get_clean();
echo apply_filters( 'wpsp_reply_ticket_modal', $modal_content );
?>

<?php ob_start();?>

<div id="note_confirm_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="gridSystemModalLabel"><?php _e('Please Confirm','wp-support-plus-responsive-ticket-system')?></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12"><?php _e('Are you sure to add this note?','wp-support-plus-responsive-ticket-system')?></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel','wp-support-plus-responsive-ticket-system')?></button>
        <button type="button" onclick="post_ticket_note()" class="btn btn-primary"><?php _e('Confirm','wp-support-plus-responsive-ticket-system')?></button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<?php

$modal_content = ob_get_clean();

echo apply_filters( 'wpsp_note_ticket_modal', $modal_content );
?>

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
