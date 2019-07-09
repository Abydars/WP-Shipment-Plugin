<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$ticket_filter = $wpsupportplus->functions->get_current_ticket_filter_applied();

?>

<div id="individual-ticket-action-container">

    <a href="<?php echo $wpsupportplus->functions->get_support_page_url(array('page'=>'tickets','section'=>'ticket-list','page_no'=>$ticket_filter['page'],'dc'=>time()));?>" class="btn btn-default btn-sm"><i class="fa fa-long-arrow-left" aria-hidden="true"></i> <?php _e('Back to tickets','wp-support-plus-responsive-ticket-system');?></a>

    <?php do_action('wpsp_before_ticket_individual_action_btn',$ticket);?>
		
		<?php if($wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'close_ticket' ) && $ticket->active != 0 ) :?>

				<button class="btn btn-default btn-sm" onclick="get_close_ticket(<?php echo $ticket->id?>);"><i class="fa fa-check" aria-hidden="true"></i> <?php _e('Close Ticket','wp-support-plus-responsive-ticket-system');?></button>

		<?php endif;?>

    <?php if($wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'delete_ticket' ) && $ticket->active != 0 ) :?>

    		<button onclick="get_delete_ticket(<?php echo $ticket->id;?>);" class="btn btn-default btn-sm"><i class="fa fa-trash-o" aria-hidden="true"></i> <?php _e('Delete Ticket','wp-support-plus-responsive-ticket-system');?></button>

    <?php endif;?>


		<?php if($wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'clone_ticket' ) && $ticket->active != 0 ) :?>

				<button onclick="get_clone_ticket(<?php echo $ticket->id?>)" class="btn btn-default btn-sm"><i class="fa fa-clone" aria-hidden="true"></i> <?php _e('Clone Ticket','wp-support-plus-responsive-ticket-system');?></button>

		<?php endif;?>
		
		<?php if($wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'restore_ticket' ) && $ticket->active == 0 ) :?>

	  		<button onclick="get_restore_ticket(<?php echo $ticket->id?>)" class="btn btn-default btn-sm"><i class="fa fa-window-restore" aria-hidden="true"></i> <?php _e('Restore','wp-support-plus-responsive-ticket-system');?></button>

		<?php endif;?>
		
		<?php if($wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'permanent_delete_ticket' ) && $ticket->active == 0 ) :?>

	  		<button onclick="get_permanent_delete_ticket(<?php echo $ticket->id?>)" class="btn btn-default btn-sm"><i class="fa fa-trash-o" aria-hidden="true"></i> <?php _e('Delete Permanently','wp-support-plus-responsive-ticket-system');?></button>

		<?php endif;?>

    <?php do_action('wpsp_after_ticket_individual_action_btn',$ticket);?>

</div>

<h4>
    [<?php echo ($wpsupportplus->functions->get_ticket_lable())?> <?php echo stripslashes($wpsupportplus->functions->get_ticket_id_prefix()); echo $ticket->id?>] <?php echo htmlspecialchars_decode(stripcslashes($ticket->subject));?>

    <?php if($wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'edit_subject' ) && $ticket->active != 0 ) :?>

        <button onclick="get_edit_subject(<?php echo $ticket->id?>)" class="btn btn-default btn-sm"><i class="fa fa-pencil" aria-hidden="true"></i></button>

    <?php endif;?>
  
</h4>
   <?php do_action('wpsp_after_subject_individual_ticket_open');?>
<hr>
<?php
$wpsp_settings_general= get_option('wpsp_settings_general');
$position=$wpsp_settings_general['reply_form_position'];
$order='desc';
if($position==0){
    $order='asc';
}

$ticket_threads = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_ticket_thread where ticket_id=".$ticket_id." order by create_time ".$order);


include_once WPSP_ABSPATH . 'template/tickets/open-ticket/class-threads-formatting.php';

$thread_format = new WPSP_Ticket_Thread_Formatting( $ticket_id, $ticket );

if($position==1){
    $thread_format->call_thread_actions();
}

$thread_format->get_threads($ticket_threads);

if($position==0){
    $thread_format->call_thread_actions();
}
