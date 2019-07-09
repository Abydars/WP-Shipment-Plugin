<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WPSP_Ticket_Field_Formatting' ) ) :

    class WPSP_Ticket_Field_Formatting {

        var $ticket_id;
        var $ticket;

        public function __construct($ticket_id,$ticket){

            global $wpsupportplus;

            $this->ticket_id    = $ticket_id;
            $this->ticket       = $ticket;

        }

        public function get_field_val( $custom_field ){

            global $wpsupportplus, $wpdb;

            ob_start();

            $field_key = 'cust'.$custom_field->id;

            switch ( $custom_field->field_type ){

                case '1' :
                case '2' :
                case '4' :

                    echo stripcslashes($this->ticket->$field_key);
                    break;

                case '3' :

                    $val = explode('|||', $this->ticket->$field_key);
                    echo stripcslashes( implode(', ', $val) );
                    break;

                case '5' :

                    echo nl2br( stripcslashes( $this->ticket->$field_key ) );
                    break;

                case '6' :

                    if($this->ticket->$field_key){
                        $date = date_i18n( $wpsupportplus->functions->get_custom_date_format(), strtotime( get_date_from_gmt( $this->ticket->$field_key, 'Y-m-d H:i:s') ) ) ;
                        echo $date;
                    }
                    break;

                case '8' :
                    $attachments=array();

                    if(!empty($this->ticket->$field_key))
                        $attachments = explode('|||', $this->ticket->$field_key);

                    if($attachments):?>
                        <div class="wpsp_ticket_thread_attachment" style="margin-top: -15px;">
                            <table>
                                <tbody>

                                    <?php foreach( $attachments as $attachment ):

                                        $attach         = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_attachments where id=".$attachment);
                                        $download_url   = $wpsupportplus->functions->get_support_page_url(array('wpsp_attachment'=>$attach->id));
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
                    <?php endif;
                    break;

                case '9' :

                    if($this->ticket->$field_key){
											
												if (!preg_match("~^(?:f|ht)tps?://~i", $this->ticket->$field_key)) {
														$url = "http://" . $this->ticket->$field_key;
												}else{
														$url = $this->ticket->$field_key;
												}
                        echo '<a href="'.$url.'" target="_blank">'.__('Click Here','wp-support-plus-responsive-ticket-system').'</a>';

                    }

                    break;

                case '10' :

                    if($this->ticket->$field_key){

                        echo '<a href="mailto:'.$this->ticket->$field_key.'">'.$this->ticket->$field_key.'</a>';

                    }
                    break;

                default : echo $this->ticket->$field_key;
                    break;

            }

            $value = ob_get_clean();

            echo apply_filters( 'wpsp_ticket_field_value', $value, $this, $custom_field );

        }

    }

endif;
