<?php
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

if ( !class_exists('WPSP_EN_Mailer') ) :

  /**
   * Process WP Support Plus Email Notifications
   */
  class WPSP_EN_Mailer {

    var $to;
    var $subject;
    var $body;
    var $headers = array();
    var $ticket;
    var $notification;
    var $is_send = true;

    function __construct( $notification, $ticket ){

      $this->notification = $notification;
      $this->ticket       = $ticket;
      $this->check_condition();
      $this->set_subject();
      $this->set_body();
      $this->set_header();

      if ($this->is_send) {
        
        wp_mail( $this->to, $this->subject, $this->body, $this->headers );
      
      }

    }

    public function set_subject(){

      global $wpdb, $wpsupportplus, $current_user;
      $ticket_prefix = $wpsupportplus->functions->get_ticket_id_prefix();
      $this->subject = '['.$wpsupportplus->functions->get_ticket_lable().' '.$ticket_prefix.$this->ticket->id.'] '.$wpsupportplus->functions->replace_template_tags( $this->notification['subject'], $this->ticket );
  
    }

    public function set_body(){

      global $wpdb, $wpsupportplus, $current_user;
      $body       = wpautop(stripcslashes($this->notification['description']));
      $this->body = $wpsupportplus->functions->replace_template_tags( $body, $this->ticket );

    }

    public function set_header(){

      global $wpdb, $wpsupportplus, $current_user;
      $email_notification = get_option('wpsp_email_notification') ? get_option('wpsp_email_notification') : array();
      $emails             = $this->get_emails($email_notification);

      if (!$this->is_send) {
        return;
      }

      $headers   = array();
      $headers[] = 'Content-Type: text/html; charset=UTF-8';
      $headers[] = 'From: '.$email_notification['from_name'].' <'.$email_notification['from_email'].'>';
      $headers[] = 'Reply-To:'.$email_notification['reply_to'];
      
      foreach ($emails as $email) {
        $headers[] = 'BCC: '.$email;
      }

      $this->headers = apply_filters( 'wpsp_en_mailer_headers', $headers, $this );

    }

    public function get_emails($email_notification){

      global $wpdb, $wpsupportplus, $current_user;
      $emails = array();
      $recipients =  isset($this->notification['recipients']) && is_array($this->notification['recipients']) ? $this->notification['recipients'] : array();

      if (in_array('customer', $recipients) && !isset($_POST['agent_silent_create'])) {
        $emails[] = $this->ticket->guest_email;
      }

      if (in_array('agent', $recipients)) {
        $assigned_agents = $this->ticket->assigned_to != 0 ? explode( ',', $this->ticket->assigned_to ) : array();
        foreach ($assigned_agents as $agent_id) {
          $agent    = get_userdata($agent_id);
          $emails[] = $agent->user_email;
        }
      }

      if (in_array('supervisor', $recipients)) {
        $supervisor_str = $wpdb->get_var("select supervisor from {$wpdb->prefix}wpsp_catagories WHERE id=".$this->ticket->cat_id);
        $supervisors    = unserialize($supervisor_str);
        
          if(is_array($supervisors)){
            foreach ($supervisors as $supervisor_id) {
              $supervisor = get_userdata($supervisor_id);
              $emails[]   = $supervisor->user_email;
            }
          }
          
      }
     
      if (in_array('administrator', $recipients)) {
          
        $results = $wpdb->get_results("select user_id from {$wpdb->prefix}wpsp_users WHERE role=3");
        foreach ($results as $admin) {
          $admin    = get_userdata($admin->user_id);
          $emails[] = $admin->user_email;
        }
      }
      
      if(in_array('all_agents' ,$recipients )){
      
        $results =$wpdb->get_results("SELECT user_id FROM {$wpdb->prefix}wpsp_users WHERE role=1");
        foreach($results as $all_agents){
          $all_agents  = get_userdata($all_agents->user_id);
          $emails[]    = $all_agents->user_email;
        }
      }
      if ( isset($_POST['bcc']) ) {
        $reply_bcc = explode(',',$_POST['bcc']);
        foreach ($reply_bcc as $bcc) {
          if( trim($bcc) ){
             $emails[]= trim($bcc);
          }
        }
      }
    
      $emails = array_unique($emails);
      
      if ( isset($email_notification['from_email']) && in_array($email_notification['from_email'], $emails)) {
        unset($emails[array_search($email_notification['from_email'],$emails)]);
      }
      
      if ( isset($email_notification['reply_to']) && in_array($email_notification['reply_to'], $emails)) {
        unset($emails[array_search($email_notification['reply_to'],$emails)]);
      }
      
      if ( isset($email_notification['ignore_emails']) ) {
        $ignore_emails = explode(PHP_EOL,$email_notification['ignore_emails']);
        foreach ($ignore_emails as $key => $val) {
           if(in_array(trim($val),$emails)){
              unset($emails[array_search(trim($val),$emails)]);
           }  
        }
      }
        
      $emails = $this->remove_current_user_email($emails);
     
      $emails = apply_filters( 'wpsp_en_mailer_emails', $emails, $this );

      if (!$emails) {
        $this->is_send = false;
      } else {
        reset($emails);
        $first_key = key($emails);
        $this->to  = $emails[$first_key];
        unset($emails[$first_key]);
      }
      
      return $emails;  
    }

    public function remove_current_user_email($emails){

      global $wpdb, $wpsupportplus, $current_user;

      if ($this->notification['type']=='rep_tkt') {
        $reply_email = $wpdb->get_var("SELECT guest_email FROM {$wpdb->prefix}wpsp_ticket_thread WHERE ticket_id=".$this->ticket->id." AND is_note=0 ORDER BY create_time DESC LIMIT 0,1");
        if (in_array($reply_email, $emails)) {
          unset($emails[array_search($reply_email,$emails)]);
        }
      
      }      

      if ( $this->notification['type']=='cng_sts' || $this->notification['type']=='asgn_agnt' || $this->notification['type']=='del_tkt' || $this->notification['type']=='note_tkt' ) {
        if (in_array($current_user->user_email, $emails)) {
          unset($emails[array_search($current_user->user_email,$emails)]);  
        }
      }

      return $emails;
      
    }

    public function check_condition(){

      global $wpdb, $wpsupportplus, $current_user;
      $conditional_fields = $wpsupportplus->functions->get_conditional_fields();
      $conditions =  isset($this->notification['condition']) && is_array($this->notification['condition']) ? $this->notification['condition'] : array();
      if ($conditions) {

        $diif_fields = array();

        foreach ($conditions as $field_key => $options) {

          $same_fields = array();
          foreach ($options as $option) {

            $same_flag = false;

            if ( !is_numeric($field_key) && $conditional_fields[$field_key]['type']=='text') {
              if(preg_match('/'.$option.'/i', $this->ticket->$field_key)){
                $same_flag = true;
              }
            }

            if ( is_numeric($field_key) && $conditional_fields[$field_key]['type']=='text') {
              $key = 'cust'.$field_key;
              if(preg_match('/'.$option.'/i', $this->ticket->$key)){
                $same_flag = true;
              }
            }

            if ( !is_numeric($field_key) && $conditional_fields[$field_key]['type']=='drop-down') {

              $field_options = explode( '|||', $this->ticket->$field_key);
              foreach ($field_options as $value) {
                if( $value == $option){
                  $same_flag = true;
                  break;
                }
              }

            }

            if ( is_numeric($field_key) && $conditional_fields[$field_key]['type']=='drop-down') {

              $key = 'cust'.$field_key;
              $field_options = explode( '|||', $this->ticket->$key);
              foreach ($field_options as $value) {
                if( $value == $option){
                  $same_flag = true;
                  break;
                }
              }

            }

            $same_fields[] = $same_flag;

          }

          $diff_flag = false;
          if( $this->notification['same_condition_relation']=='AND' && !in_array( false , $same_fields ) ){
            $diff_flag = true;
          }
          if( $this->notification['same_condition_relation']=='OR' && in_array( true , $same_fields ) ){
            $diff_flag = true;
          }
          $diif_fields[] = $diff_flag;

        }

        $outer_flag = false;
        if( $this->notification['diff_condition_relation']=='AND' && !in_array( false , $diif_fields ) ){
          $outer_flag = true;
        }
        if( $this->notification['diff_condition_relation']=='OR' && in_array( true , $diif_fields ) ){
          $outer_flag = true;
        }

        $this->is_send = $outer_flag;

      }

    }

  }


endif;
