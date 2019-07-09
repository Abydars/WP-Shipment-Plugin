<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Ticket_Thread_Formatting' ) ) :

    class WPSP_Ticket_Thread_Formatting {

        var $ticket_id;
        var $ticket;
        var $cap_view_log                   = false;
        var $cap_view_note                  = false;
        var $cap_edit_thread                = false;
        var $cap_delete_thread              = false;
        var $cap_thread_email               = false;
				var $cap_new_thread									=	false;

        public function __construct($ticket_id,$ticket){

            global $wpsupportplus;

            $this->ticket_id    = $ticket_id;
            $this->ticket       = $ticket;
            $this->cap_new_thread                = $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'new_thread' );
            $this->cap_view_log                   = $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'view_log' );
            $this->cap_view_note                  = $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'view_note' );
            $this->cap_edit_thread                = $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'edit_thread' );
            $this->cap_delete_thread              = $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'delete_thread' );
            $this->cap_thread_email               = $wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'thread_email' );
						
        }

        public function call_thread_actions(){

            global $wpsupportplus, $current_user,$wpdb;
						
            $reply_actions = array();
						$custom_status = $wpsupportplus->functions->toggle_button_reply_closed_tickets();
						
						$closed_status = $wpsupportplus->functions->get_close_btn_status();
						$flag=true;
						if(!$wpsupportplus->functions->is_staff($current_user)){
								if($this->ticket->status_id==$closed_status && !$custom_status){
										$flag=false;
								}
						}
						
            if($wpsupportplus->functions->cu_has_cap_ticket( $this->ticket, 'post_reply' ) && $flag){

                $reply_actions['post_reply'] = '<span onclick="show_ticket_reply_form();">'.__("Post Reply","wp-support-plus-responsive-ticket-system").'</span>';
            }

            if($wpsupportplus->functions->cu_has_cap_ticket( $this->ticket, 'add_note' )){

                $reply_actions['add_note'] = '<span onclick="show_ticket_add_note_form();">'.__("Add Private Note","wp-support-plus-responsive-ticket-system").'</span>';
            }
				    
            ?>
            <input type="file" id="image_upload" class="hidden" onchange="">
            <input type="file" id="attachment_upload" class="hidden" onchange="">
            <input type="hidden" id="wpsp_nonce" value="<?php echo wp_create_nonce()?>" />

            <?php if($reply_actions) :?>

                <div class="well well-sm thread_actions col-md-12">
                    <?php echo implode(' / ', $reply_actions);?>
                </div>

            <?php endif;?>

            <form id="frm_ticket_reply" onsubmit="return false;">

                <input type="hidden" name="action" value="wpsp_ticket_reply" />
                <input type="hidden" name="ticket_id" value="<?php echo $this->ticket_id?>" />
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce($this->ticket_id)?>" />

                <div id="reply_ticket_form_container" class="col-md-12 rich_form_container">
                    <?php do_action('wpsp_ticket_reply_form_top');?>
                    <div class="col-md-12 form-group">
                        <?php $this->print_editor('reply');?>
                    </div>
                    <div class="col-md-12 form-group">
                        <input class="form-control" type="text" name="bcc" value="" placeholder="<?php _e('BCC (Comma separated list)','wp-support-plus-responsive-ticket-system');?>" />
                    </div>
                    <?php do_action('wpsp_ticket_reply_form_bottom');?>
                    <div class="col-md-3 col-md-offset-6 form-group">
                        <button onclick="submit_ticket_reply()" class="form-control btn btn-success" id="wpsp_submit_ticket_reply_button" type="button"><?php _e('Submit Reply','wp-support-plus-responsive-ticket-system');?></button>
                    </div>
                    <div class="col-md-3 form-group">
                        <button class="form-control btn btn-info" id="wpsp_submit_ticket_cancel_button" type="button" onclick="wpspjq('.rich_form_container').slideUp();"><?php _e('Cancel','wp-support-plus-responsive-ticket-system');?></button>
                    </div>
                </div>
            </form>

            <?php if( $wpsupportplus->functions->is_staff($current_user) ) :?>
            <form id="frm_ticket_note">

                <input type="hidden" name="action" value="wpsp_add_ticket_note" />
                <input type="hidden" name="ticket_id" value="<?php echo $this->ticket_id?>" />
                <input type="hidden" name="nonce" value="<?php echo wp_create_nonce($this->ticket_id)?>" />

                <div id="add_note_form_container" class="col-md-12 rich_form_container">
                    <?php do_action('wpsp_ticket_note_form_top');?>
                    <div class="col-md-12 form-group">
                        <?php $this->print_editor('note');?>
                    </div>
                    <?php do_action('wpsp_ticket_note_form_bottom');?>
                    <div class="col-md-3 col-md-offset-6 form-group">
                        <button onclick="submit_ticket_note()" class="form-control btn btn-success" id="wpsp_submit_ticket_note_button" type="button"><?php _e('Submit Note','wp-support-plus-responsive-ticket-system');?></button>
                    </div>
                    <div class="col-md-3 form-group">
                        <button class="form-control btn btn-info" id="wpsp_submit_ticket_note_cancel_button" type="button" onclick="wpspjq('.rich_form_container').slideUp();"><?php _e('Cancel','wp-support-plus-responsive-ticket-system');?></button>
                    </div>
                </div>
            </form>
            <?php
            endif;
						
						$this->print_footer_script();

        }

        public function get_threads($ticket_threads){

            foreach ( $ticket_threads as $thread ){

                switch ($thread->is_note){
                    case '0': $this->print_thread($thread);
                        break;
                    case '1': $this->print_note($thread);
                        break;
                    case '2': $this->change_assign_agent_log($thread);
                        break;
                    case '3': $this->change_status_log($thread);
                        break;
                    case '4': $this->change_category_log($thread);
                        break;
                    case '5': $this->change_priority_log($thread);
                        break;
                    case '6': $this->change_raised_by($thread);
                        break;
										case '7': $this->change_status_after_days($thread);
                      	break;
												
                }

            }

        }

        function print_thread($thread){

            global $wpdb, $wpsupportplus;

            $date       = date_i18n( $wpsupportplus->functions->get_display_date_format(), strtotime( get_date_from_gmt( $thread->create_time, 'Y-m-d H:i:s') ) ) ;
            $attachments = $thread->attachment_ids ? explode(',', $thread->attachment_ids) : array();

            ?>
            <div class="panel panel-primary col-md-12 wpsp_ticket_reply_thread" style="padding: 0px;">
                <div class="panel-heading col-md-12">
                    <div class="col-md-8 ticket_thread_title">
                        <div class="col-md-12" style="padding-left:0px;">
                        	<div class="col-md-2 col-sm-2 col-xs-2 wpsp_thread_avatar" style="padding-left: 0px;padding-right: 0px; width:50px;">
                        		<?php echo get_avatar( $thread->guest_email, 40, '', '', array('class'=>'img-circle') )?>
                        	</div>
													<div class="col-md-10 wpsp_hthread_info" style="padding-left:0px;">
														<strong><?php echo $thread->guest_name?></strong><br>

                            <?php if( $this->cap_thread_email ) :?>

                            <small class="wpsp_thread_email"><?php echo $thread->guest_email?></small>&nbsp;

                            <?php endif;?>

                            <small><?php echo $date?></small>
													</div>
                        </div>
                    </div>
                    <div class="col-md-4 ticket_thread_actions">

                        <?php do_action(' wpsp_before_thread_action_icons', $this, $thread )?>

                        <?php if($this->cap_edit_thread) :?>

                            <i onclick="get_edit_thread(<?php echo htmlentities($this->ticket_id)?>,<?php echo htmlentities($thread->id)?>);" class="fa fa-edit thread_action_icon" aria-hidden="true" data-toggle="tooltip" data-placement="left" title="<?php _e('Edit this thread','wp-support-plus-responsive-ticket-system');?>"></i>&nbsp;&nbsp;

                        <?php endif;?>
                        
												<?php if($this->cap_new_thread) :?>
												
													<i onclick="get_new_thread(<?php echo $this->ticket_id ?>,<?php echo $thread->id?>);" class="fa fa-plus-square" style="color:white;cursor:pointer;" aria-hidden="true" data-toggle="tooltip" data-placement="left" title="<?php _e('New Ticket from this thread','wp-support-plus-responsive-ticket-system');?>"></i>&nbsp;&nbsp;
                         
												<?php endif; ?>
												 
												<?php if($this->cap_delete_thread) :?>

                            <i onclick="get_delete_thread(<?php echo htmlentities($this->ticket_id)?>,<?php echo htmlentities($thread->id)?>);" class="fa fa-trash-o thread_action_icon" aria-hidden="true" data-toggle="tooltip" data-placement="left" title="<?php _e('Delete this thread','wp-support-plus-responsive-ticket-system');?>"></i>

                        <?php endif;?>

                        <?php do_action(' wpsp_after_thread_action_icons', $this, $thread )?>

                    </div>
                </div>
                <div class="panel-body col-md-12 wpsp_ticket_thread_content">

                    <div class="col-md-12 wpsp_ticket_thread_body" style="padding: 0px;">
                        <?php echo htmlspecialchars_decode(stripslashes($thread->body),ENT_QUOTES)?>
                    </div>

                    <div onclick="wpsp_ticket_thread_expander_toggle(this);" class="col-md-12 wpsp_ticket_thread_expander" style="padding: 0px; display: none;">
                        View More ...
                    </div>

                    <?php if($attachments):?>
                        <div class="col-md-12 wpsp_ticket_thread_attachment">
                            <strong><?php _e('Attachments','wp-support-plus-responsive-ticket-system');?>:</strong><br>
                            <table>
                                <tbody>

                                    <?php foreach( $attachments as $attachment ):

                                        $attach = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_attachments where id=".$attachment);

                                        $upload_dir = wp_upload_dir();

                                        $download_url   = $wpsupportplus->functions->get_support_page_url(array('wpsp_attachment'=>$attach->id,'dc'=>time()));
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo htmlentities($download_url)?>" target="_blank"><i class="fa fa-download" aria-hidden="true" title="<?php _e('Download','wp-support-plus-responsive-ticket-system');?>"></i></a>
                                            </td>
                                            <td><?php echo $attach->filename?></td>
                                        </tr>

                                    <?php endforeach;?>

                                </tbody>
                            </table>
                        </div>
                    <?php endif;?>

                </div>
            </div>
            <?php
        }

        function print_note($thread){

            global $wpdb, $wpsupportplus, $current_user;

            if(!$this->cap_view_note) return;

            $date       = date_i18n( $wpsupportplus->functions->get_display_date_format(), strtotime( get_date_from_gmt( $thread->create_time, 'Y-m-d H:i:s') ) ) ;
            $attachments = $thread->attachment_ids ? explode(',', $thread->attachment_ids) : array();

            ?>
            <div class="panel panel-warning col-md-12 wpsp_ticket_thread_note" style="padding: 0px;">
                <div class="panel-heading col-md-12 wpsp_ticket_note_thread">
                    <div class="col-md-8 ticket_note_thread_title">
												<div class="col-md-12" style="padding-left:0px;">
													<div class="col-md-2 col-sm-2 col-xs-2 wpsp_thread_avatar" style="padding-left: 0px;padding-right: 0px; width:50px;">
														<?php echo get_avatar( $thread->guest_email, 40, '', '', array('class'=>'img-circle') )?>
													</div>
													<div class="col-md-10 wpsp_hthread_info" style="padding-left:0px;">
															<strong><?php echo $thread->guest_name?></strong><br>

                              <?php if( $this->cap_thread_email ) :?>

                              <small class="wpsp_thread_email"><?php echo $thread->guest_email?></small>&nbsp;

                              <?php endif;?>

                              <small><?php echo $date?></small>

													</div>
												</div>
                    </div>
                    <div class="col-md-4 ticket_thread_actions">

                        <?php do_action(' wpsp_before_thread_action_icons', $this, $thread )?>

                        <?php if($this->cap_edit_thread) :?>

                            <i onclick="get_edit_thread(<?php echo $this->ticket_id?>,<?php echo $thread->id?>);" class="fa fa-edit thread_action_icon" aria-hidden="true" data-toggle="tooltip" data-placement="left" title="<?php _e('Edit this thread','wp-support-plus-responsive-ticket-system');?>"></i>&nbsp;&nbsp;

                        <?php endif;?>
												
												<?php if($this->cap_new_thread) :?> 
												
												<i onclick="get_new_thread(<?php echo htmlentities($this->ticket_id)?>,<?php echo htmlentities($thread->id)?>);"  class="fa fa-plus-square" style="color:#8a6d3b;; cursor:pointer;" aria-hidden="true" data-toggle="tooltip" data-placement="left" title="<?php  _e('New Ticket from this thread','wp-support-plus-responsive-ticket-system');?>"></i>&nbsp;&nbsp;

											<?php endif;?>			

                        <?php if($this->cap_delete_thread) :?>

                            <i onclick="get_delete_thread(<?php echo $this->ticket_id?>,<?php echo $thread->id?>);" class="fa fa-trash-o thread_action_icon" aria-hidden="true" data-toggle="tooltip" data-placement="left" title="<?php _e('Delete this thread','wp-support-plus-responsive-ticket-system');?>"></i>

                        <?php endif;?>

                        <?php do_action(' wpsp_after_thread_action_icons', $this, $thread )?>

                    </div>
                </div>
                <div class="panel-body col-md-12 wpsp_ticket_thread_content">

                    <p class="bg-default wpsp_ticket_note_label"><strong><?php _e('Private Note:','wp-support-plus-responsive-ticket-system')?></strong> <?php _e('Visible to support staff only!','wp-support-plus-responsive-ticket-system')?></p>

                    <div class="col-md-12 wpsp_ticket_thread_body" style="padding: 0px;">
                        <?php echo htmlspecialchars_decode(stripslashes($thread->body), ENT_QUOTES)?>
                    </div>

                    <div onclick="wpsp_ticket_thread_expander_toggle(this);" class="col-md-12 wpsp_ticket_thread_expander" style="padding: 0px; display: none;">
                        View More ...
                    </div>

                    <?php if($attachments):?>
                        <div class="col-md-12 wpsp_ticket_thread_attachment">
                            <strong><?php _e('Attachments','wp-support-plus-responsive-ticket-system');?>:</strong><br>
                            <table>
                                <tbody>

                                    <?php foreach( $attachments as $attachment ):

                                        $attach = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_attachments where id=".$attachment);

                                        $upload_dir = wp_upload_dir();
																				
																				$download_url   = $wpsupportplus->functions->get_support_page_url(array('wpsp_attachment'=>$attach->id,'dc'=>time()));
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo $download_url?>" target="_blank"><i class="fa fa-download" aria-hidden="true" title="<?php _e('Download','wp-support-plus-responsive-ticket-system');?>"></i></a>
                                            </td>
                                            <td><?php echo $attach->filename?></td>
                                        </tr>

                                    <?php endforeach;?>

                                </tbody>
                            </table>
                        </div>
                    <?php endif;?>

                </div>
            </div>
            <?php
        }

        function change_assign_agent_log($thread){

            global $wpdb, $wpsupportplus, $current_user;

            if(!$this->cap_view_log) return;

            $date   = date_i18n( $wpsupportplus->functions->get_display_date_format(), strtotime( get_date_from_gmt( $thread->create_time, 'Y-m-d H:i:s') ) ) ;
            $agents = explode( ',', $thread->body );

            $agent_name = array();
            foreach ( $agents as $agent ){
                $user = get_userdata($agent);
                if($user){
                    $agent_name[] = $user->display_name;
                } else {
                    $agent_name[] = __('None','wp-support-plus-responsive-ticket-system');
                }
            }

            $agent_name = implode(', ', $agent_name);

            ?>
            <div class="col-md-12 wpsp_ticket_thread_log">
                <div class="col-md-6 col-md-offset-3">
                    <p class="bg-info">

                        <?php
                        printf( esc_html__( '%1$s changed assigned agent to %2$s', 'wp-support-plus-responsive-ticket-system' ), '<strong>'.$thread->guest_name.'</strong>', '<strong>'.$agent_name.'</strong>' );
                        ?>
                        <br><small><?php echo $date?></small>

                    </p>
                </div>
            </div>
            <?php
        }

        function change_status_log($thread){

            global $wpdb, $wpsupportplus, $current_user;

            if(!$this->cap_view_log) return;

            $date           = date_i18n( $wpsupportplus->functions->get_display_date_format(), strtotime( get_date_from_gmt( $thread->create_time, 'Y-m-d H:i:s') ) ) ;
            $status_name    = $wpdb->get_var( "select name from {$wpdb->prefix}wpsp_custom_status where id=".$thread->body );

            ?>
            <div class="col-md-12 wpsp_ticket_thread_log">
                <div class="col-md-6 col-md-offset-3">
                    <p class="bg-info">

                        <?php
												$custom_status_localize = get_option('wpsp_custom_status_localize');
                        printf( esc_html__( '%1$s changed status to %2$s', 'wp-support-plus-responsive-ticket-system' ), '<strong>'.$thread->guest_name.'</strong>', '<strong>'.stripcslashes($custom_status_localize['label_'.$thread->body]).'</strong>' );
                        ?>
                        <br><small><?php echo $date?></small>

                    </p>
                </div>
            </div>
            <?php
        }

        function change_category_log($thread){

            global $wpdb, $wpsupportplus, $current_user;

            if(!$this->cap_view_log) return;

            $date           = date_i18n( $wpsupportplus->functions->get_display_date_format(), strtotime( get_date_from_gmt( $thread->create_time, 'Y-m-d H:i:s') ) ) ;
            $category_name  = $wpdb->get_var( "select name from {$wpdb->prefix}wpsp_catagories where id=".$thread->body );

            ?>
            <div class="col-md-12 wpsp_ticket_thread_log">
                <div class="col-md-6 col-md-offset-3">
                    <p class="bg-info">

                        <?php
												$custom_category_localize = get_option('wpsp_custom_category_localize');
                        printf( esc_html__( '%1$s changed category to %2$s', 'wp-support-plus-responsive-ticket-system' ), '<strong>'.$thread->guest_name.'</strong>', '<strong>'.stripcslashes($custom_category_localize['label_'.$thread->body]).'</strong>' );
                        ?>
                        <br><small><?php echo $date?></small>

                    </p>
                </div>
            </div>
            <?php
        }

        function change_priority_log($thread){

            global $wpdb, $wpsupportplus, $current_user;

            if(!$this->cap_view_log) return;

            $date           = date_i18n( $wpsupportplus->functions->get_display_date_format(), strtotime( get_date_from_gmt( $thread->create_time, 'Y-m-d H:i:s') ) ) ;
            $priority_name  = $wpdb->get_var( "select name from {$wpdb->prefix}wpsp_custom_priority where id=".$thread->body );

            ?>
            <div class="col-md-12 wpsp_ticket_thread_log">
                <div class="col-md-6 col-md-offset-3">
                    <p class="bg-info">

                        <?php
												$custom_priority_localize = get_option('wpsp_custom_priority_localize');
                        printf( esc_html__( '%1$s changed priority to %2$s', 'wp-support-plus-responsive-ticket-system' ), '<strong>'.$thread->guest_name.'</strong>', '<strong>'.stripcslashes($custom_priority_localize['label_'.$thread->body]).'</strong>' );
                        ?>
                        <br><small><?php echo $date?></small>

                    </p>
                </div>
            </div>
            <?php
        }

        function change_raised_by($thread){

            global $wpdb, $wpsupportplus, $current_user;

            if(!$this->cap_view_log) return;

            $date = date_i18n( $wpsupportplus->functions->get_display_date_format(), strtotime( get_date_from_gmt( $thread->create_time, 'Y-m-d H:i:s') ) ) ;
            $user = get_userdata($thread->created_by);

            ?>
            <div class="col-md-12 wpsp_ticket_thread_log">
                <div class="col-md-6 col-md-offset-3">
                    <p class="bg-info">

                        <?php
                        printf( esc_html__( '%1$s changed raised by to %2$s', 'wp-support-plus-responsive-ticket-system' ), '<strong>'.$user->display_name.'</strong>', '<strong>'.$thread->guest_name.'</strong>' );
                        ?>
                        <br><small><?php echo $date?></small>

                    </p>
                </div>
            </div>
            <?php
        }
				
				function change_status_after_days($thread){
					global $wpdb, $wpsupportplus, $current_user;

					if(!$this->cap_view_log) return;

					$date = date_i18n( $wpsupportplus->functions->get_display_date_format(), strtotime( get_date_from_gmt( $thread->create_time, 'Y-m-d H:i:s') ) ) ;
					
					$status_name    						= $wpdb->get_var( "select name from {$wpdb->prefix}wpsp_custom_status where id=".$thread->body );
					$general_advanced_settings 	= get_option('wpsp_general_settings_advanced');
					$diffDay     								= $general_advanced_settings['selected_status_ticket_close'];
					?>
					<div class="col-md-12 wpsp_ticket_thread_log">
							<div class="col-md-6 col-md-offset-3">
									<p class="bg-info">

											<?php
											printf( esc_html__( 'Ticket %1$s after inactivity for %2$s day(s)', 'wp-support-plus-responsive-ticket-system' ),'<strong>'.$status_name.'</strong>','<strong>'.$diffDay.'</strong>' );
											?>
											<br><small><?php echo $date ?></small>

									</p>
							</div>
					</div>
					<?php
				}

        function print_editor($editor){

            global $wpdb, $wpsupportplus;
            ?>
            <textarea id="ticket_<?php echo $editor?>_editor" class="form-control" name="editor"></textarea>
            <?php do_action('wpsp_after_open_ticket_editor',$editor);?>
						<fieldset id="ticket_<?php echo $editor?>_editor_attachment" class="scheduler-border" style="display:none; border: 1px solid #000 !important;">
                <legend class="scheduler-border"> <?php _e('Attach Files', 'wp-support-plus-responsive-ticket-system')?> (<span onclick="<?php echo $editor?>_ticket_desc_attach()" class="glyphicon glyphicon-plus attach_plus"></span>) </legend>

            </fieldset>
						
						<?php

        }
				
				function print_footer_script(){
					
						global $is_wpsp_template;
						if ($is_wpsp_template) {
							$this->wpsp_ct_print_inline_script_reply_form();
							$this->wpsp_ct_print_inline_script_note_form();
						} else {
							add_action('wp_footer', array($this,'wpsp_ct_print_inline_script_reply_form'), 900000000000 );
							add_action('wp_footer', array($this,'wpsp_ct_print_inline_script_note_form'), 900000000000 );
						}
					
				}
				
				function wpsp_ct_print_inline_script_reply_form(){
					
					$editor='reply';
					$locale = get_locale();
					?>
					<script type="text/javascript">
					jQuery(document).ready(function(){

							tinymce.init({
									selector: '#ticket_<?php echo $editor?>_editor',
									body_id: '<?php echo $editor?>_editor',
									menubar: false,
									height : '200',
									plugins: [
											'lists link image directionality'
									],
									image_advtab: true,
									toolbar: 'bold italic underline blockquote | alignleft aligncenter alignright | bullist numlist | rtl | link image attachment canned',
									branding: false,
									autoresize_bottom_margin: 20,
									browser_spellcheck : true,
									relative_urls : false,
									remove_script_host : false,
									convert_urls : true,
									file_picker_callback: function(callback, value, meta) {

											var source_obj = wpspjq(document).find('.mce-textbox')[0];

											if (meta.filetype == 'image') {
													
													wpspjq('#image_upload').unbind('change');
													wpspjq('#image_upload').on('change', function() {

															var flag = false;
															var file = this.files[0];
															wpspjq('#image_upload').val('');
															var allowedExtension = ['JPEG','JPG','PNG','GIF','BMP','jpeg', 'jpg', 'png', 'gif', 'bmp'];
															var file_name_split = file.name.split('.');
															var file_extension = file_name_split[file_name_split.length-1];

															if (!flag && wpspjq.inArray(file_extension, allowedExtension) == -1){
																	flag = true;
																	alert("<?php _e("Only jpeg, jpg, png, gif, bmp formats are allowed.",'wp-support-plus-responsive-ticket-system')?>");
															}

															if(!flag){

																	wpspjq(source_obj).val('<?php _e('Uploading...', 'wp-support-plus-responsive-ticket-system')?>( 0% )');

																	var data = new FormData();
																	data.append('img', file);
																	data.append('action', 'wpsp_upload_image');
																	data.append('nonce', wpspjq('#wpsp_nonce').val().trim());

																	wpspjq.ajax({
																			type: 'post',
																			url: wpsp_data.ajax_url,
																			data: data,
																			xhr: function(){
																					var xhr = new window.XMLHttpRequest();
																					xhr.upload.addEventListener("progress", function(evt){
																							if (evt.lengthComputable) {
																									var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
																									wpspjq(source_obj).val('<?php _e('Uploading...', 'wp-support-plus-responsive-ticket-system')?>( '+percentComplete+'% )');
																							}
																					}, false);
																					return xhr;
																			},
																			processData: false,
																			contentType: false,
																			success: function(response) {
																					var return_obj=JSON.parse(response);
																					callback(return_obj.url, {alt: file_name_split[0]});
																			}
																	});
															}
													});
													wpspjq('#image_upload').trigger('click');
											}
									},
									setup: function (editor) {
											editor.addButton('attachment', {
													image: wpsp_data.attachment_icon,
													tooltip:wpsp_data.attachment_tooltip,
													onclick: function () {
															<?php echo $editor?>_ticket_desc_attach();
													}
											});

											<?php do_action('wpsp_tynymce_btn') ?>
									}
							});

					});
					</script>
					<?php
				}
				
				function wpsp_ct_print_inline_script_note_form(){
					
					$editor='note';
					$locale = get_locale();
					?>
					<script type="text/javascript">
					jQuery(document).ready(function(){

							tinymce.init({
									selector: '#ticket_<?php echo $editor?>_editor',
									body_id: '<?php echo $editor?>_editor',
									menubar: false,
									height : '200',
									plugins: [
											'lists link image directionality'
									],
									image_advtab: true,
									toolbar: 'bold italic underline blockquote | alignleft aligncenter alignright | bullist numlist | rtl | link image attachment canned',
									branding: false,
									autoresize_bottom_margin: 20,
									browser_spellcheck : true,
									relative_urls : false,
									remove_script_host : false,
									convert_urls : true,
									file_picker_callback: function(callback, value, meta) {

											var source_obj = wpspjq(document).find('.mce-textbox')[0];

											if (meta.filetype == 'image') {
													
													wpspjq('#image_upload').unbind('change');
													wpspjq('#image_upload').on('change', function() {

															var flag = false;
															var file = this.files[0];
															wpspjq('#image_upload').val('');
															var allowedExtension = ['JPEG','JPG','PNG','GIF','BMP','jpeg', 'jpg', 'png', 'gif', 'bmp'];
															var file_name_split = file.name.split('.');
															var file_extension = file_name_split[file_name_split.length-1];

															if (!flag && wpspjq.inArray(file_extension, allowedExtension) == -1){
																	flag = true;
																	alert("<?php _e("Only jpeg, jpg, png, gif, bmp formats are allowed.",'wp-support-plus-responsive-ticket-system')?>");
															}

															if(!flag){

																	wpspjq(source_obj).val('<?php _e('Uploading...', 'wp-support-plus-responsive-ticket-system')?>( 0% )');

																	var data = new FormData();
																	data.append('img', file);
																	data.append('action', 'wpsp_upload_image');
																	data.append('nonce', wpspjq('#wpsp_nonce').val().trim());

																	wpspjq.ajax({
																			type: 'post',
																			url: wpsp_data.ajax_url,
																			data: data,
																			xhr: function(){
																					var xhr = new window.XMLHttpRequest();
																					xhr.upload.addEventListener("progress", function(evt){
																							if (evt.lengthComputable) {
																									var percentComplete = Math.floor((evt.loaded / evt.total) * 100);
																									wpspjq(source_obj).val('<?php _e('Uploading...', 'wp-support-plus-responsive-ticket-system')?>( '+percentComplete+'% )');
																							}
																					}, false);
																					return xhr;
																			},
																			processData: false,
																			contentType: false,
																			success: function(response) {
																					var return_obj=JSON.parse(response);
																					callback(return_obj.url, {alt: file_name_split[0]});
																			}
																	});
															}
													});
													wpspjq('#image_upload').trigger('click');
											}
									},
									setup: function (editor) {
											editor.addButton('attachment', {
													image: wpsp_data.attachment_icon,
													tooltip:wpsp_data.attachment_tooltip,
													onclick: function () {
															<?php echo $editor?>_ticket_desc_attach();
													}
											});

											<?php do_action('wpsp_tynymce_btn') ?>
									}
							});

					});
					</script>
					<?php
				}

    }

endif;
