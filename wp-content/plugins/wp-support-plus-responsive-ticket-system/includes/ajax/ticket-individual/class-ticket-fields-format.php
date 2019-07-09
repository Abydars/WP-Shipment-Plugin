<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Ticket_Fields_Format' ) ) :
    
    class WPSP_Ticket_Fields_Format {
    
        var $ticket;
        
        public function __construct($ticket) {
            $this->ticket = $ticket;
        }

                /**
         * Print form element
         */
        public function print_field($custom_field){
            
            switch ($custom_field->field_type){
                case 1: $this->print_text_field($custom_field);
                    break;
                case 2: $this->print_drop_down($custom_field);
                    break;
                case 3: $this->print_checkbox($custom_field);
                    break;
                case 4: $this->print_radio_button($custom_field);
                    break;
                case 5: $this->print_textarea($custom_field);
                    break;
                case 6: $this->print_date($custom_field);
                    break;
                case 8: $this->print_attachment($custom_field);
                    break;
                case 9: $this->print_url($custom_field);
                    break;
                case 10: $this->print_email($custom_field);
                    break;
                default: do_action( 'wpsp_print_ticket_field', $this, $custom_field );
            }
            
        }
        
        
        /**
         * Print text-field type custom field
         */
        public function print_text_field($custom_field){
        
            global $wpdb, $wpsupportplus;
            $col_name = 'cust'.$custom_field->id;
            ?>
            <div class="form-group">
                <label><?php echo $wpsupportplus->functions->get_ticket_form_label($custom_field->id)?></label>
                <input type="text" class="form-control" name="<?php echo $col_name?>" value="<?php echo htmlspecialchars( stripslashes( $this->ticket->$col_name ), ENT_QUOTES )?>"/>
            </div>
            <?php
        }
        
        /**
         * Print drop-down type custom field
         */
        public function print_drop_down($custom_field){
            
            global $wpdb, $wpsupportplus;
            $col_name = 'cust'.$custom_field->id;
            $options = unserialize($custom_field->field_options);
            ?>
            <div class="form-group">
                <label><?php echo $wpsupportplus->functions->get_ticket_form_label($custom_field->id)?></label>
                <select class="form-control" name="<?php echo $col_name?>">
                    <option value=""></option>
                    <?php
                    foreach( $options as $key => $val ){
                        
                        $value = trim(stripcslashes($key));
                        $selected = $value == $this->ticket->$col_name ? 'selected="selected"' : '';
                        ?>
                        <option <?php echo $selected?> value="<?php echo $value?>"><?php echo stripcslashes($val)?></option>
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
        public function print_checkbox($custom_field){
            
            global $wpdb, $wpsupportplus;
            $col_name = 'cust'.$custom_field->id;
            $options = unserialize($custom_field->field_options);
            $set_values = explode('|||', $this->ticket->$col_name);
            foreach( $set_values as $key=>$val ){
                $set_values[$key] = trim($val);
            }
            ?>
            <div class="form-group">
                
                <label><?php echo $wpsupportplus->functions->get_ticket_form_label($custom_field->id)?></label><br>
                <?php
                foreach( $options as $key => $val ){
                    
                    $value = trim(stripcslashes($key));
                    $checked = in_array($value, $set_values) ? 'checked="checked"' : '';
                    ?>
                    <input <?php echo $checked?> type="checkbox" name="<?php echo $col_name?>[]" value="<?php echo $value?>" /> <?php echo stripcslashes($val)?><br>
                    <?php
                }
                ?>
                    
            </div>
            <?php
        }
        
        /**
         * Print radio button type custom field
         */
        public function print_radio_button($custom_field){
            
            global $wpdb, $wpsupportplus;
            $col_name = 'cust'.$custom_field->id;
            $options = unserialize($custom_field->field_options);
            $set_value = trim( $this->ticket->$col_name );
            ?>
            <div class="form-group">
                
                <label><?php echo $wpsupportplus->functions->get_ticket_form_label($custom_field->id)?></label><br>
                <?php
                foreach( $options as $key => $val ){
                    
                    $value = trim(stripcslashes($key));
                    $checked = $value == $set_value ? 'checked="checked"' : '';
                    ?>
                    <input <?php echo $checked?> type="radio" name="<?php echo $col_name?>" value="<?php echo $value?>" /> <?php echo stripcslashes($val)?><br>
                    <?php
                }
                ?>
                    
            </div>
            <?php
        }
        
        /**
         * Print Textarea type custom field
         */
        public function print_textarea($custom_field){
            
            global $wpdb, $wpsupportplus;
            $col_name = 'cust'.$custom_field->id;
            ?>
            <div class="form-group">
                <label><?php echo $wpsupportplus->functions->get_ticket_form_label($custom_field->id)?></label>
                <textarea class="form-control" name="<?php echo $col_name?>" ><?php echo stripslashes( $this->ticket->$col_name )?></textarea>
            </div>
            <?php
        }
        
        /**
         * Print date type custom field
         */
        public function print_date($custom_field){
        
            global $wpdb, $wpsupportplus;
            
            $col_name = 'cust'.$custom_field->id;
            
            $val = $this->ticket->$col_name;
            
            if($val){
                
                $format = str_replace('dd','d',$wpsupportplus->functions->get_date_format());
                $format = str_replace('mm','m',$format);
                $format = str_replace('yy','Y',$format);
                
                $date = date_create_from_format('Y-m-d H:i:s', $val);
                
                $val = $date->format($format);
                
            }
            
            ?>
            <div class="form-group">
                <label><?php echo $wpsupportplus->functions->get_ticket_form_label($custom_field->id)?></label>
                <input type="text" class="form-control wpsp_date" name="<?php echo $col_name?>" value="<?php echo $val?>"/>
            </div>
            <?php
        }

        /**
         * Print url type custom field
         */
        public function print_url($custom_field){
        
            global $wpdb, $wpsupportplus;
            $col_name = 'cust'.$custom_field->id;
            ?>
            <div class="form-group">
                <label><?php echo $wpsupportplus->functions->get_ticket_form_label($custom_field->id)?></label>
                <input type="text" class="form-control" name="<?php echo $col_name?>" value="<?php echo stripslashes( $this->ticket->$col_name )?>"/>
            </div>
            <?php
        }

        /**
         * Print attachment custom field type
         */
        public function print_attachment($custom_field){
            
            global $wpdb, $wpsupportplus;
            $col_name = 'cust'.$custom_field->id;
            $set_values=array();
            
            if(!empty($this->ticket->$col_name))
                $set_values = explode('|||', $this->ticket->$col_name);
            
            $col_name = 'cust_'.$custom_field->id;
            ?>
            <div class="form-group">
                <label><?php echo $wpsupportplus->functions->get_ticket_form_label($custom_field->id)?></label><br>
                <fieldset id="cust_attachment_<?php echo $custom_field->id?>" class="scheduler-border cust_attachment">
                    <legend class="scheduler-border"> <?php _e('Attach Files', 'wp-support-plus-responsive-ticket-system')?> (<span onclick="cust_attach(this,<?php echo htmlentities($custom_field->id)?>);" class="glyphicon glyphicon-plus attach_plus"></span>) </legend>
                    
                    <?php
                    foreach( $set_values as $key=>$val ){
                        
                        $val = trim($val);
                        $filename = $wpdb->get_var( "SELECT filename FROM {$wpdb->prefix}wpsp_attachments WHERE id=".$val );
                        ?>
                        <div class="col-md-4 wpsp_attachment inner_control">
                            <div class="progress">
                                <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                                    <?php echo $filename?>
                                </div>
                            </div>
                            <img onclick="attachment_cancel(this,true);" class="attachment_cancel" src="<?php echo WPSP_PLUGIN_URL."asset/images/icons/close.png" ?>" style="">
                            <input type="hidden" name="<?php echo $col_name?>[]" value="<?php echo $val?>">
                        </div>
                        <?php
                        
                    }
                    ?>
                    
                </fieldset>
            </div>
            <?php
        
        }
        
        /**
         * Print email type custom field
         */
        public function print_email($custom_field){
        
            global $wpdb, $wpsupportplus;
            $col_name = 'cust'.$custom_field->id;
            ?>
            <div class="form-group">
                <label><?php echo $wpsupportplus->functions->get_ticket_form_label($custom_field->id)?></label>
                <input type="text" class="form-control" name="<?php echo $col_name?>" value="<?php echo stripslashes( $this->ticket->$col_name )?>"/>
            </div>
            <?php
        }
    
    }
    
endif;