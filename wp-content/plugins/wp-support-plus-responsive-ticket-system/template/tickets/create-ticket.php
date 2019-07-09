<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$cap=$wpsupportplus->functions-> toggle_button_disable_captcha();
$user_id = $current_user->ID;
$captcha=false;
if($cap=='all'){
	$captcha=true;	
}else if( !$user_id && $cap=='guest'){
	$captcha=true;
}

if(!isset($_REQUEST['action'])) :

    global $wpsupportplus, $current_user, $wpdb;

    $user_id = $current_user->ID;
    if( $user_id ){
        $user = get_userdata($user_id);
        $user_name  = $user->display_name;
        $user_email = $user->user_email;
    }

    $nonce = wp_create_nonce();

    $form_fields = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_ticket_form_order ORDER BY load_order");

    include_once WPSP_ABSPATH . 'template/tickets/class-ticket-form.php';

    $ticket_form = new WPSP_Create_Ticket_Form();
		
		$language = array("azb","ar","he","fa-IR");
		
		$current_site_language = get_bloginfo("language");
		
		$rtl_css = '';
		
		if (in_array($current_site_language, $language) &&  is_rtl()){
			
				$rtl_css = "direction: rtl;";
				
		}

    ?>

    <div class="container" style="<?php echo $rtl_css; ?>">

        <h2 class="page-header"><?php _e('Create New Ticket', 'wp-support-plus-responsive-ticket-system')?></h2>

        <?php do_action( 'wpsp_before_create_ticket' );?>

        <form id="frm_create_ticket" action="javascript:wpsp_submit_ticket();" method="post" onsubmit="return validate_user_create_ticket();">
					  <?php
            /**
             * Create ticket for other user. Available to agent only
             */
            if( $current_user->has_cap('wpsp_agent') || $current_user->has_cap('wpsp_supervisor') || $current_user->has_cap('wpsp_administrator') ):

                $default_create_ticket_as_type = apply_filters( 'wpsp_default_create_ticket_as_type', 1 );
                if(!$default_create_ticket_as_type){
                    $user_id = 0;
                }
                ?>
                <div class="form-group col-md-4" id="wpsp_ct_change_user">
                    <label class="label label-default"><?php _e('Create Ticket As', 'wp-support-plus-responsive-ticket-system')?></label><br>
                    <select class="form-control" id="create_ticket_as" name="create_ticket_as" onchange="change_create_ticket_as_type(this,<?php echo $current_user->ID?>,'<?php echo $current_user->display_name?>')">
                        <option <?php echo ($default_create_ticket_as_type==1)?'selected="selected"' : ''?> value="1"><?php _e('Registered User', 'wp-support-plus-responsive-ticket-system')?></option>
                        <option <?php echo ($default_create_ticket_as_type==0)?'selected="selected"' : ''?> value="0"><?php _e('Guest', 'wp-support-plus-responsive-ticket-system')?></option>
                    </select>
                </div>
                <div class="form-group regi-field col-md-8" style="<?php echo !($default_create_ticket_as_type)?'display:none;':''?>">
                    <label class="label label-default"><?php _e('Choose User', 'wp-support-plus-responsive-ticket-system')?></label><br>
                    <input id="regi_user_autocomplete" type="text" class="form-control" value="<?php echo $current_user->display_name?>" autocomplete="off" placeholder="<?php _e('Search user ...', 'wp-support-plus-responsive-ticket-system')?>" />
                </div>
                <div data-field ="text" id="guest_name" class="form-group guest-field col-md-4 <?php echo (!$default_create_ticket_as_type)?'wpsp_require':''?>" style="<?php echo ($default_create_ticket_as_type)?'display:none;':''?>">
                    <label class="label label-default"><?php _e('Guest Name', 'wp-support-plus-responsive-ticket-system')?></label>  <span class="fa fa-snowflake-o"></span><br>
                    <input type="text" class="form-control" name="guest_name"/>
                </div>
                <div data-field ="email" id="guest_email" class="form-group guest-field col-md-4 <?php echo (!$default_create_ticket_as_type)?'wpsp_require':''?>" style="<?php echo ($default_create_ticket_as_type)?'display:none;':''?>">
                    <label class="label label-default"><?php _e('Guest Email', 'wp-support-plus-responsive-ticket-system')?></label>  <span class="fa fa-snowflake-o"></span><br>
                    <input type="text" class="form-control" name="guest_email"/>
                </div>

                <?php

            endif;

            /**
             * Start actual ticket form
             */
            foreach( $form_fields as $field ){

                if($field->status){
                    $ticket_form->print_field($field);
                }
            }

            ?>					
						<?php if($wpsupportplus->functions->is_staff($current_user)){																	
						?>
						<div class="form-group col-md-12" id="wpsp_silent_create_ticket">
							<input type="checkbox" id="agent_silent_create" name="agent_silent_create" value="1" />	
							<span class="label label-default" style="font-size: 13px;"><?php _e('Don\'t Notify Owner','wp-support-plus-responsive-ticket-system');?></span><br>
						</div>
						<?php 
					  }?>
						<?php if($captcha){?>
						<div class="col-sm-2 captcha_container" style="margin-bottom:10px; display:flex;clear:both;">
							<div style="width:25px;">
								<input type="checkbox" onchange="get_captcha_code(this);" class="wpsc_checkbox" value="1">
								<img id="captcha_wait" style="width:16px;display:none;" src="<?php echo WPSP_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>" alt="">
							</div>
							<div><?php _e("I'm not a robot",'wp-support-plus-responsive-ticket-system')?></div>
						</div>
					<?php }?>
            <input type="hidden" id="user_id" name="user_id" value="<?php echo $user_id?>" />
            <input type="hidden" name="agent_created" value="<?php echo $current_user->ID?>" />
						<input type="hidden" id="wpsp_nonce" name="nonce" value="<?php echo wp_create_nonce()?>" />
            <input type="hidden" name="action" value="wpsp_submit_ticket" />
            <input type="file" id="image_upload" class="hidden" onchange="">
            <input type="file" id="attachment_upload" class="hidden" onchange="">
						<input type="hidden" id="captcha_code" name="captcha_code" value="">
						
            <div class="form-group col-md-12 wpsp_form_btn_bottom">

                <div class="form-group col-md-2 inner_control">
                    <button type="submit" class="btn btn-success form-control"><?php _e('Submit Ticket', 'wp-support-plus-responsive-ticket-system')?></button>
                </div>

                <div class="form-group col-md-2 inner_control">
                    <button type="button" class="btn btn-default form-control" onclick="reset_create_ticket();"><?php _e('Reset Form', 'wp-support-plus-responsive-ticket-system')?></button>
                </div>

            </div>

        </form>

    </div>
    <?php

endif;
?><div id="wpsp-admin-popup-wait-load-thank" style="display:none; text-align: center;">
		<img src="<?php echo WPSP_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>" />
</div>

<?php
if( isset($_REQUEST['action']) && $_REQUEST['action'] == 'thankyou' ) :

    $ticket_id = isset($_REQUEST['ticket_id']) ? intval( sanitize_text_field($_REQUEST['ticket_id'])) : 0;

    if($ticket_id){
				$ticket = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_ticket WHERE id=".$ticket_id);
				?>
        <div class="container" id="wpsp_thank_you_page_container">
            <?php
						$wpsp_thank_you_page=get_option('wpsp_thank_you_page');
						$thank_you_page_title=isset($wpsp_thank_you_page['title']) ? stripcslashes( $wpsp_thank_you_page['title']):'';
						echo '<h1>'.$thank_you_page_title.'</h1>';
						
						$thank_you_page_body = wpautop(stripcslashes($wpsp_thank_you_page['body']));

            $thank_you_page_body = $wpsupportplus->functions->replace_template_tags( $thank_you_page_body, $ticket );

            echo '<div id="wpsp_thank_you_page_body"> '.$thank_you_page_body.'</div>';
            ?>

            <div style="clear: both;padding:15px 0">
                <a href="<?php echo $wpsupportplus->functions->get_support_page_url(array('page'=>'tickets','section'=>'ticket-list','action'=>'open-ticket','id'=>$ticket_id));?>"><button class="btn btn-sm btn-info" type="submit"><?php _e('View Ticket','wp-support-plus-responsive-ticket-system');?></button></a>&nbsp;&nbsp;&nbsp;
                <a href="<?php echo $wpsupportplus->functions->get_support_page_url(array('page'=>'tickets','section'=>'ticket-list'));?>"><button class="btn btn-sm btn-info" type="submit"><?php _e('Ticket List','wp-support-plus-responsive-ticket-system');?></button></a>&nbsp;&nbsp;&nbsp;
                <a href="<?php echo $wpsupportplus->functions->get_support_page_url(array('page'=>'tickets','section'=>'create-ticket'));?>"><button class="btn btn-sm btn-info" type="submit"><?php _e('Create New Ticket','wp-support-plus-responsive-ticket-system');?></button></a>
            </div>

        </div>
    <?php

    }

endif;

function wpsp_print_page_inline_script(){
	?>
	<script>
			jQuery(document).ready(function(){
					window.location.href = redirect_url;
			});
	</script>
	<?php
}
?>
<script>
/**
 * Validate logged-In user create ticket form
 */
function validate_user_create_ticket(){

    var flag = false;
    var errorMsg = '';
    var empty_labels = new Array();

    wpspjq('.wpsp_require').each(function(){

        var cust_field = wpspjq(this);
        switch(cust_field.attr('data-field')){
            case 'text' :
            case 'url' :
            case 'date' :
            case 'email': if(cust_field.find('input').val().trim() == '') empty_labels.push(cust_field.find('label').text());
                break;
            case 'drop-down': if(cust_field.find('select').val().trim() == '') empty_labels.push(cust_field.find('label').text());
                break;
            case 'tinymce':
                var editor_id = cust_field.find('textarea').attr('id');
                if(tinyMCE.get(editor_id).getContent().trim() == '') empty_labels.push(cust_field.find('label').text());
                break;
            case 'textarea':  if(cust_field.find('textarea').val().trim() == '') empty_labels.push(cust_field.find('label').text());
                break;
            case 'checkbox':
            case 'radio-button':
                var checked = false;
                cust_field.find('input').each(function(){
                    if(wpspjq(this).is(':checked')){
                        checked = true;
                        return;
                    }
                });
                if(!checked) empty_labels.push(cust_field.find('label').text());
                break;
            case 'attachment': if(cust_field.find('input').length == 0) empty_labels.push(cust_field.find('label').text());
                break;
        }

    });

    if(empty_labels.length){
        flag = true;
        errorMsg  = wpsp_data.lbl_enter_required+'\n\n- ';
        errorMsg += empty_labels.join('\n- ');
    }

    if(!flag){
        var wrong_emails = new Array();
        wpspjq('div[data-field=email]').each(function(){
            var email = wpspjq(this).find('input').val().trim();
            if( email != '' && !validateEmail(email) ){
                wrong_emails.push(wpspjq(this).find('label').text());
            }
        });
        if(wrong_emails.length){
            flag = true;
            errorMsg  = wpsp_data.lbl_wrong_email+'\n\n- ';
            errorMsg += wrong_emails.join('\n- ');
        }
    }

    if(!flag){
        var wrong_urls = new Array();
        wpspjq('div[data-field=url]').each(function(){
            var url = wpspjq(this).find('input').val().trim();
            if( url != '' && !validateURL(url) ){
                wrong_urls.push(wpspjq(this).find('label').text());
            }
        });
        if(wrong_urls.length){
            flag = true;
            errorMsg  = wpsp_data.lbl_wrong_url+'\n\n- ';
            errorMsg += wrong_urls.join('\n- ');
        }
    }
		
    <?php if($captcha){?>
    if(!flag){
      if (jQuery('#captcha_code').val().trim().length==0) {
  			alert('Please confirm you are not a robot!');
        return false;
  		}
    }
    <?php }?>
		
    if(!flag){
      wpspjq('#frm_create_ticket').hide();
      wpspjq('#wpsp-admin-popup-wait-load-thank').show();
        return true;
    } else {
        alert(errorMsg);
        return false;
    }
    
    

}
</script>