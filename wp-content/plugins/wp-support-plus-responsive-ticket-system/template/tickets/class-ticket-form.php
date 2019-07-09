<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Create_Ticket_Form' ) ) :

    class WPSP_Create_Ticket_Form {

        /**
         * Print form element
         */
        public function print_field($field){

            global $wpdb, $wpsupportplus;


            if(!is_numeric($field->field_key)){   // default values

                switch ($field->field_key){
                    case 'ds' : $this->print_subject($field);
                        break;
                    case 'dd' : $this->print_description($field);
                        break;
                    case 'dc' : $this->print_category($field);
                        break;
                    case 'dp' : $this->print_priority($field);
                        break;
                }

            } else {  //custom fields

                $custom_field = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_custom_fields where id=".$field->field_key);
                 // error_log(print_r($custom_field,true));
                switch ($custom_field->field_type){
                    case 1: $this->print_text_field($field, $custom_field);
                        break;
                    case 2: $this->print_drop_down($field, $custom_field);
                        break;
                    case 3: $this->print_checkbox($field, $custom_field);
                        break;
                    case 4: $this->print_radio_button($field, $custom_field);
                        break;
                    case 5: $this->print_textarea($field, $custom_field);
                        break;
                    case 6: $this->print_date($field, $custom_field);
                        break;
                    case 8: $this->print_attachment($field, $custom_field);
                        break;
                    case 9: $this->print_url($field, $custom_field);
                        break;
                    case 10: $this->print_email($field, $custom_field);
                        break;
                    default: do_action( 'wpsp_print_field', $field, $custom_field );
                }

            }

        }

        /**
         * Print Subject
         */
        public function print_subject($field){

            global $wpdb, $wpsupportplus;
            ?>
            <div data-field ="text" id="subject" class="form-group col-md-<?php echo ($field->full_width)?'12':'6'?> wpsp_require">
                <label class="label label-default"><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></label>  <span class="fa fa-snowflake-o"></span><br>
                <input type="text" class="form-control" name="subject"/>
            </div>
            <?php

        }

        /**
         * Print Description
         */
        public function print_description($field){

            global $wpdb, $wpsupportplus, $is_wpsp_template;
            $locale = get_locale();
            ?>
            <div id="description_container" data-field ="tinymce" id="cust_<?php echo $field->field_key?>" class="form-group col-md-<?php echo ($field->full_width)?'12':'6'?> wpsp_require">
                <label class="label label-default"><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></label>  <span class="fa fa-snowflake-o"></span><br>
                <textarea id="description" class="wpsp_reach_text form-control" name="description"></textarea>
                <?php do_action('wpsp_create_ticket_form_after_description');?>
								<fieldset id="description_attachment" class="scheduler-border" style="display:none;">
                    <legend class="scheduler-border"> <?php _e('Attach Files', 'wp-support-plus-responsive-ticket-system')?> (<span onclick="create_ticket_desc_attach();" id="desc_attach_plus" class="glyphicon glyphicon-plus attach_plus"></span>) </legend>

                </fieldset>
            </div>
						<?php
						if ($is_wpsp_template) {
							$this->wpsp_ct_print_inline_script();
						} else {
							add_action('wp_footer', array($this,'wpsp_ct_print_inline_script'), 900000000000 );
						}

        }
				
				function wpsp_ct_print_inline_script(){
					$locale = get_locale();
					?>
					<script>
							jQuery(document).ready(function(){
								tinymce.init({
										selector: '.wpsp_reach_text',
										body_id: 'description_editor',
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
																create_ticket_desc_attach();
														}
												});
												<?php do_action('wpsp_tynymce_btn_for_create_ticket') ?>
										}
								});
							});
					</script>
					<?php
				}

        /**
         * Print category drop-down
         */
        public function print_category($field){

            global $wpsupportplus, $wpdb;
            $results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_catagories ORDER BY load_order");
						$custom_category_localize = get_option('wpsp_custom_category_localize');
            ?>
            <div data-field ="drop-down" id="category" class="form-group col-md-<?php echo ($field->full_width)?'12':'6'?> wpsp_require">
                <label class="label label-default"><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></label>  <span class="fa fa-snowflake-o"></span><br>
                <select class="form-control" name="category" onchange="create_ticket_cng_cat(this);">
                    <option value=""></option>
                    <?php
                    foreach( $results as $category ){
                        ?>
                        <option value="<?php echo $category->id?>"><?php echo stripcslashes($custom_category_localize['label_'.$category->id])?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <?php
        }

        /**
         * Print priority drop-down
         */
        public function print_priority($field){

            global $wpsupportplus, $wpdb;
            $results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_priority ORDER BY load_order");
						$custom_priority_localize = get_option('wpsp_custom_priority_localize');
            ?>
            <div data-field ="drop-down" id="priority" class="form-group col-md-<?php echo ($field->full_width)?'12':'6'?> wpsp_require">
                <label class="label label-default"><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></label>  <span class="fa fa-snowflake-o"></span><br>
                <select class="form-control" name="priority">
                    <option value=""></option>
                    <?php
                    foreach( $results as $priority ){
                        ?>
                        <option value="<?php echo $priority->id?>"><?php echo stripcslashes($custom_priority_localize['label_'.$priority->id])?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <?php
        }

        /**
         * Print text-field type custom field
         */
        public function print_text_field($field, $custom_field){

            global $wpdb, $wpsupportplus;
            $class = $custom_field->required ? 'wpsp_require' : '';
            if( $custom_field->field_categories != 0 ){
                $class = 'hidden cat_depend';
            }
            ?>
            <div data-field ="text" id="cust_<?php echo $field->field_key?>" data-required="<?php echo $custom_field->required?>" class="form-group col-md-<?php echo ($field->full_width)?'12':'6'?> <?php echo $class?> ">
                <label class="label label-default"><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></label>  <span style="<?php echo $custom_field->required ? '' : 'display:none;'?>" class="fa fa-snowflake-o"></span><br>
								<div class="wpsp_ct_custom_instruction"><small><?php echo htmlspecialchars_decode( stripslashes($custom_field->instructions));?></small></div>
                <input type="text" class="form-control" name="cust_<?php echo $field->field_key?>"/>
            </div>
            <?php
        }

        /**
         * Print drop-down type custom field
         */
        public function print_drop_down($field, $custom_field){

            global $wpdb, $wpsupportplus;
            $options = unserialize($custom_field->field_options);
            $class = $custom_field->required ? 'wpsp_require' : '';
            if( $custom_field->field_categories != 0 ){
                $class = 'hidden cat_depend';
            }
            ?>
            <div data-field ="drop-down" id="cust_<?php echo $field->field_key?>" data-required="<?php echo $custom_field->required?>" class="form-group col-md-<?php echo ($field->full_width)?'12':'6'?> <?php echo $class?> ">
                <label class="label label-default"><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></label>  <span style="<?php echo $custom_field->required ? '' : 'display:none;'?>" class="fa fa-snowflake-o"></span><br>
								<div class="wpsp_ct_custom_instruction"> <small> <?php echo htmlspecialchars_decode( stripslashes($custom_field->instructions));?></small></div>
                <select class="form-control" name="cust_<?php echo $field->field_key?>">
                    <option value=""></option>
                    <?php
                    foreach( $options as $key => $val ){
                        ?>
                        <option value="<?php echo htmlspecialchars( stripcslashes($key), ENT_QUOTES )?>"><?php echo stripcslashes($val)?></option>
                        <?php
                    }
                    ?>
                </select>
            </div>
            <?php
        }

        /**
         * Print checkbox type custom field
         */
        public function print_checkbox($field, $custom_field){

            global $wpdb, $wpsupportplus;
            $options = unserialize($custom_field->field_options);
            $class = $custom_field->required ? 'wpsp_require' : '';
            if( $custom_field->field_categories != 0 ){
                $class = 'hidden cat_depend';
            }
            ?>
            <div data-field ="checkbox" id="cust_<?php echo $field->field_key?>" data-required="<?php echo $custom_field->required?>" class="form-group col-md-<?php echo ($field->full_width)?'12':'6'?> <?php echo $class?> ">
                <label class="label label-default"><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></label>  <span style="<?php echo $custom_field->required ? '' : 'display:none;'?>" class="fa fa-snowflake-o"></span><br>
                <div class="wpsp_ct_custom_instruction"> <small><?php echo htmlspecialchars_decode( stripslashes($custom_field->instructions));?></small></div>
                
								<?php
                foreach( $options as $key => $val ){
                    ?>
                    <div class="form-group col-md-6 inner_control">
                        <span class="button-checkbox">
                            <button type="button" class="btn form-control" data-color="success"><?php echo stripcslashes($val)?></button>
                            <input type="checkbox" name="cust_<?php echo $field->field_key?>[]" value="<?php echo htmlspecialchars(stripcslashes($key),ENT_QUOTES)?>" class="hidden" />
                        </span>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }

        /**
         * Print radio button type custom field
         */
        public function print_radio_button($field, $custom_field){

            global $wpdb, $wpsupportplus;
            $options = unserialize($custom_field->field_options);
            $class = $custom_field->required ? 'wpsp_require' : '';
            if( $custom_field->field_categories != 0 ){
                $class = 'hidden cat_depend';
            }
            ?>
            <div data-field ="radio-button" id="cust_<?php echo $field->field_key?>" data-required="<?php echo $custom_field->required?>" class="form-group col-md-<?php echo ($field->full_width)?'12':'6'?> <?php echo $class?> ">
                <label class="label label-default"><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></label>  <span style="<?php echo $custom_field->required ? '' : 'display:none;'?>" class="fa fa-snowflake-o"></span><br>
                <div class="wpsp_ct_custom_instruction"> <small><?php echo htmlspecialchars_decode( stripslashes($custom_field->instructions));?></small></div>
								               
								<?php
                foreach( $options as $key => $val ){
                    ?>
                    <div class="form-group col-md-6 inner_control">
                        <span class="button-radio">
                            <button type="button" class="btn form-control" data-color="success"><?php echo stripcslashes($val)?></button>
                            <input type="radio" name="cust_<?php echo $field->field_key?>" value="<?php echo htmlspecialchars(stripcslashes($key),ENT_QUOTES)?>" class="hidden" />
                        </span>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }

        /**
         * Print Textarea type custom field
         */
        public function print_textarea($field, $custom_field){

            global $wpdb, $wpsupportplus;
            $class = $custom_field->required ? 'wpsp_require' : '';
            if( $custom_field->field_categories != 0 ){
                $class = 'hidden cat_depend';
            }
            ?>
            <div data-field ="textarea" id="cust_<?php echo $field->field_key?>" data-required="<?php echo $custom_field->required?>" class="form-group col-md-<?php echo ($field->full_width)?'12':'6'?> <?php echo $class?> ">
                <label class="label label-default"><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></label>  <span style="<?php echo $custom_field->required ? '' : 'display:none;'?>" class="fa fa-snowflake-o"></span><br>
                <textarea class="form-control" name="cust_<?php echo $field->field_key?>"></textarea>
            </div>
            <?php
        }

        /**
         * Print date type custom field
         */
        public function print_date($field, $custom_field){

            global $wpdb, $wpsupportplus;
            $class = $custom_field->required ? 'wpsp_require' : '';
            if( $custom_field->field_categories != 0 ){
                $class = 'hidden cat_depend';
            }
            ?>
            <div data-field ="date" id="cust_<?php echo $field->field_key?>" data-required="<?php echo $custom_field->required?>" class="form-group col-md-<?php echo ($field->full_width)?'12':'6'?> <?php echo $class?> ">
                <label class="label label-default"><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></label>  <span style="<?php echo $custom_field->required ? '' : 'display:none;'?>" class="fa fa-snowflake-o"></span><br>
                <div class="wpsp_ct_custom_instruction"> <small><?php echo htmlspecialchars_decode( stripslashes($custom_field->instructions));?></small></div>
								<input type="text" class="form-control wpsp_date" name="cust_<?php echo $field->field_key?>"/>
            </div>
            <?php
        }

        /**
         * Print url type custom field
         */
        public function print_url($field, $custom_field){

            global $wpdb, $wpsupportplus;
            $class = $custom_field->required ? 'wpsp_require' : '';
            if( $custom_field->field_categories != 0 ){
                $class = 'hidden cat_depend';
            }
            ?>
            <div data-field ="url" id="cust_<?php echo $field->field_key?>" data-required="<?php echo $custom_field->required?>" class="form-group col-md-<?php echo ($field->full_width)?'12':'6'?> <?php echo $class?> ">
                <label class="label label-default"><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></label>  <span style="<?php echo $custom_field->required ? '' : 'display:none;'?>" class="fa fa-snowflake-o"></span><br>
                <div class="wpsp_ct_custom_instruction"><small><?php echo htmlspecialchars_decode( stripslashes($custom_field->instructions));?></small></div>
								<input type="text" class="form-control" name="cust_<?php echo $field->field_key?>"/>
            </div>
            <?php
        }

        /**
         * Print attachment custom field type
         */
        public function print_attachment($field, $custom_field){

            global $wpdb, $wpsupportplus;
            $class = $custom_field->required ? 'wpsp_require' : '';
            if( $custom_field->field_categories != 0 ){
                $class = 'hidden cat_depend';
            }
            ?>
            <div data-field="attachment" id="cust_<?php echo $field->field_key?>" data-required="<?php echo $custom_field->required?>" class="form-group col-md-<?php echo ($field->full_width)?'12':'6'?> <?php echo $class?> ">
                <label class="label label-default"><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></label>  <span style="<?php echo $custom_field->required ? '' : 'display:none;'?>" class="fa fa-snowflake-o"></span><br>
								<div class="wpsp_ct_custom_instruction"><small> <?php echo htmlspecialchars_decode( stripslashes($custom_field->instructions));?></small> </div>
                <fieldset id="cust_attachment_<?php echo $field->field_key?>" class="scheduler-border cust_attachment">
                    <legend class="scheduler-border"> <?php _e('Attach Files', 'wp-support-plus-responsive-ticket-system')?> (<span onclick="cust_attach(this,<?php echo $field->field_key?>);" class="glyphicon glyphicon-plus attach_plus"></span>) </legend>
                </fieldset>
            </div>
            <?php

        }

        /**
         * Print email type custom field
         */
        public function print_email($field, $custom_field){

            global $wpdb, $wpsupportplus;
            $class = $custom_field->required ? 'wpsp_require' : '';
            if( $custom_field->field_categories != 0 ){
                $class = 'hidden cat_depend';
            }
            ?>
            <div data-field ="email" id="cust_<?php echo $field->field_key?>" data-required="<?php echo $custom_field->required?>" class="form-group col-md-<?php echo ($field->full_width)?'12':'6'?> <?php echo $class?> ">
                <label class="label label-default"><?php echo $wpsupportplus->functions->get_ticket_form_label($field->field_key)?></label>  <span style="<?php echo $custom_field->required ? '' : 'display:none;'?>" class="fa fa-snowflake-o"></span><br>
								<div class="wpsp_ct_custom_instruction"><small> <?php echo htmlspecialchars_decode( stripslashes($custom_field->instructions));?></small> </div>								
								<input type="text" class="form-control" name="cust_<?php echo $field->field_key?>"/>
            </div>
            <?php
        }

    }

endif;
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
