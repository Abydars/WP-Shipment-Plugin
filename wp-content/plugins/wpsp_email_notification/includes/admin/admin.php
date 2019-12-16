<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

final class WPSP_EN_Backend {

	function wpsp_en_actions(){

		global $wpdb, $wpsupportplus, $current_user;

		if( $current_user->has_cap('manage_options') && isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'wp-support-plus' && isset( $_REQUEST['wpsp_en_action'] ) ) :

			$wpsp_en_action = sanitize_text_field( $_REQUEST['wpsp_en_action'] );
			switch ( $wpsp_en_action ) {

				case 'insert_notification':
					include WPSP_EN_DIR . 'includes/admin/insert_notification.php';
					break;

				case 'delete_notification':
					include WPSP_EN_DIR . 'includes/admin/delete_notification.php';
					break;

				case 'clone_notification':
					include WPSP_EN_DIR . 'includes/admin/clone_notification.php';
					break;

				case 'update_notification':
					include WPSP_EN_DIR . 'includes/admin/update_notification.php';
					break;

			}

		endif;
	}

	function loadScripts(){

		if( isset( $_REQUEST['page'] ) && $_REQUEST['page'] == 'wp-support-plus' ) :

			wp_enqueue_script( 'wpsp_en_js', WPSP_EN_URL.'asset/js/admin.js?version='.WPSP_EN_VERSION );
			wp_enqueue_style( 'wpsp_en_css', WPSP_EN_URL . 'asset/css/admin.css?version='.WPSP_EN_VERSION );

		endif;

	}

	function add_submenus_section($addon_sections){

		$addon_sections['email_notification'] = array(
        'label' => __('Email Notifications','wpsp-en'),
        'file'  => WPSP_EN_DIR . 'includes/admin/email_notification.php'
    );
    return $addon_sections;

	}

	public function setting_update(){

    $setting = sanitize_text_field( $_REQUEST['update_setting'] );
    if ( $setting === 'settings_email_notification' ){
        update_option( 'wpsp_email_notification', $_POST['email_notification'] );
    }

  }

	public function get_email_types(){

		$email_types = array(
			'crt_tkt'   => __( 'Create New Ticket', 'wpsp-en' ),
			'rep_tkt'   => __( 'Reply Ticket', 'wpsp-en' ),
			'cng_sts'   => __( 'Change Status', 'wpsp-en' ),
			'asgn_agnt' => __( 'Assign Agent', 'wpsp-en' ),
			'del_tkt'   => __( 'Delete Ticket', 'wpsp-en' ),
			'note_tkt'  => __( 'Private Note','wpsp-en')
		);

		return apply_filters( 'wpsp_en_email_types', $email_types );

	}

	public function get_option_value_label($field_key,$value,$conditional_fields){

		global $wpdb, $wpsupportplus, $current_user;

		if ( $conditional_fields[$field_key]['type']=='text' ) {

			return $value;

		} else if ( is_numeric($field_key) ) {

			return apply_filters( 'wpsp_en_get_option_value_label', $value, $field_key, $conditional_fields );

		} else {

			if ( $field_key=='status_id' ) {
				$label = $wpdb->get_var("select name from {$wpdb->prefix}wpsp_custom_status WHERE id=".$value);
				return stripcslashes($label);
			}

			if ($field_key=='cat_id') {
				$label = $wpdb->get_var("select name from {$wpdb->prefix}wpsp_catagories WHERE id=".$value);
				return stripcslashes($label);
			}

			if ($field_key=='priority_id') {
				$label = $wpdb->get_var("select name from {$wpdb->prefix}wpsp_custom_priority WHERE id=".$value);
				return stripcslashes($label);
			}

		}
	}

	public function get_field_options(){

		global $wpdb, $wpsupportplus, $current_user;

		$field_key =  isset($_REQUEST['field_key']) ? sanitize_text_field($_REQUEST['field_key']) : '';

		if ( !$field_key || !$current_user->has_cap('manage_options')) {
			die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
		}

		?>
		<option value=""><?php _e('Select Option', 'wpsp-en');?></option>
		<?php

		if ($field_key=='status_id') {
			$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_status ORDER BY load_order");
			foreach ( $results as $status ) {
				?>
				<option value="<?php echo $status->id?>"><?php echo stripcslashes($status->name)?></option>
				<?php
			}
		}
		if ($field_key=='cat_id') {
			$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_catagories ORDER BY load_order");
			foreach ( $results as $category ) {
				?>
				<option value="<?php echo $category->id?>"><?php echo stripcslashes($category->name)?></option>
				<?php
			}
		}
		if ($field_key=='priority_id') {
			$results = $wpdb->get_results("select * from {$wpdb->prefix}wpsp_custom_priority ORDER BY load_order");
			foreach ( $results as $priority ) {
				?>
				<option value="<?php echo $priority->id?>"><?php echo stripcslashes($priority->name)?></option>
				<?php
			}
		}
		if (is_numeric($field_key)) {

			$default_option_types = array(2,3,4);
			$custom_field = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_custom_fields WHERE id=".$field_key);
			if ( in_array($custom_field->field_type, $default_option_types) ) {
				$options = unserialize($custom_field->field_options);
				foreach( $options as $key => $val ){
					?>
					<option value="<?php echo trim(htmlspecialchars( stripcslashes($key), ENT_QUOTES ))?>"><?php echo trim(stripcslashes($val))?></option>
					<?php
				}
			} else {
				do_action( 'wpsp_en_get_field_options', $field_key, $custom_field );
			}
		}
		die();

	}
	
	public function wpsp_addon_count($addon_count){
		
			return ++$addon_count;
		
	}
	
	public function license_setting(){
			
			include WPSP_EN_DIR . 'includes/admin/license_setting.php';
			
	}
	
	public function license_setting_update(){
			
			$setting = sanitize_text_field( $_REQUEST['update_setting'] );
			
			if ( $setting === 'settings_addon_licenses' ){
					include WPSP_EN_DIR . 'includes/admin/license_update.php';
			}
			
	}


}
?>
