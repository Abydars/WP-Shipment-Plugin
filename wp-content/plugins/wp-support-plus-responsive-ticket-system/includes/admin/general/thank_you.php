<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb;

?>

<div id="tab_container">
    <form method="post" action="">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="update_setting" value="thank_you_page">
        <?php wp_nonce_field('wpbdp_tab_general_section_general'); ?>

        <div style="clear: both;padding:5px 0">
            <h3><?php _e('Thank You Page after create ticket','wp-support-plus-responsive-ticket-system');?></h3>
            <b><?php _e('Title','wp-support-plus-responsive-ticket-system');?></b>
            <input type="text" name="thank_you_page[title]" value="<?php echo stripcslashes($wpsupportplus->functions->get_thank_you_page_title());?>" style="width:100%;margin-top:10px;">
        </div>

        <?php
            $editor_id = 'body';
            $settings  = array('textarea_name' => 'thank_you_page[body]','editor_height' =>'200','media_buttons' => true);
            $content   = stripcslashes($wpsupportplus->functions->get_thank_you_page_body());
            wp_editor( $content, $editor_id, $settings );
        ?>

        <p class="wpsp_list_template_tags">

						<b><?php _e('Template Tags', 'wpsp-en')?></b><br>
						{ticket_id} - <?php _e('Ticket ID','wp-support-plus-responsive-ticket-system');?><br>
            {ticket_subject} - <?php _e('Ticket Subject','wp-support-plus-responsive-ticket-system');?><br>
            {ticket_description} - <?php _e('Ticket description given while creating ticket','wp-support-plus-responsive-ticket-system');?><br>
            {ticket_status} - <?php _e('Ticket status','wp-support-plus-responsive-ticket-system');?><br>
            {ticket_category} - <?php _e('Ticket Category','wp-support-plus-responsive-ticket-system');?><br>
            {ticket_priority} - <?php _e('Ticket Priority','wp-support-plus-responsive-ticket-system');?><br>
            {customer_name} - <?php _e('Name of ticket creator','wp-support-plus-responsive-ticket-system');?><br>
            {customer_email} - <?php _e('Email of ticket creator','wp-support-plus-responsive-ticket-system');?><br>
            {time_created} - <?php _e('Ticket create time','wp-support-plus-responsive-ticket-system');?><br>
            {agent_created} - <?php _e('Name of the agent who have created ticket on behalf of user. Empty if ticket created by user himself.','wp-support-plus-responsive-ticket-system');?><br>
            {assigned_agent} - <?php _e('Name(s) of agent assigned to ticket.','wp-support-plus-responsive-ticket-system');?><br>
						{reply_user_name} - <?php _e('Name of user posted last reply.','wp-support-plus-responsive-ticket-system');?><br>
						{reply_user_email} - <?php _e('Email of user posted last reply.','wp-support-plus-responsive-ticket-system');?><br>
						{reply_description} - <?php _e('Last reply description.','wp-support-plus-responsive-ticket-system');?><br>
						{ticket_history} - <?php _e('History of communication in ticket.','wp-support-plus-responsive-ticket-system');?><br>
						{updated_by} - <?php _e('Name of user doing current activity.','wp-support-plus-responsive-ticket-system');?><br>
						{ticket_url} - <?php _e('URL of the ticket.','wp-support-plus-responsive-ticket-system');?><br>

            <?php do_action( 'print_custom_fields_template_tags', 'create_ticket' );?>

            <?php $wpsupportplus->functions->print_custom_fields_template_tags();?>

        </p>
				
				<div style="margin-top:40px;">
					 <h3><?php _e('Redirect link', 'wp-support-plus-responsive-ticket-system'); ?></h3>
							<div>
							 <?php
									$checked = $wpsupportplus->functions->get_guest_ticket_redirect() ? 'checked="checked"' : '';
									?>
									<input <?php echo $checked?> type="checkbox" name="thank_you_page[guest_ticket_redirect]" value="1" />
									<?php _e('Redirect to below link after submit(above message wont display)','wp-support-plus-responsive-ticket-system')?><br>								
						 </div>
					 <div>
						 <input type="text" style="width:500px;" name="thank_you_page[guest_ticket_redirect_url]" value="<?php echo $wpsupportplus->functions->get_guest_ticket_redirect_url(); ?>"><br>

					 </div>
							
       </div>

        <p class="submit">
            <input id="submit" class="button button-primary" name="submit" value="<?php _e('Save Changes', 'wp-support-plus-responsive-ticket-system'); ?>" type="submit">
        </p>

    </form>

</div>
