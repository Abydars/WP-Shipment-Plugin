<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$field_id       = isset($_POST['load_id']) ? intval(sanitize_text_field($_POST['load_id'])) : 0;
$nonce          = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : 0;

/**
 * Check nonce
 */
if( !wp_verify_nonce($nonce, $field_id) ){
    die(__('Cheating huh?', 'wp-support-plus-responsive-ticket-system'));
}

global $wpsupportplus, $wpdb;

$old_values = $wpdb->get_row("select * from {$wpdb->prefix}wpsp_custom_fields where id=".$field_id);

$field_options = isset($_POST['field_options']) ? explode("\n", $_POST['field_options']) : array();

$field_options_temp = array();
foreach ($field_options as $field_option){
    $field_options_temp[trim($field_option)] = trim(sanitize_text_field($field_option));
}

$field_label            = sanitize_text_field($_POST['field_label']);
$field_instructions     = sanitize_text_field($_POST['field_instructions']);
$field_type             = intval(sanitize_text_field($_POST['field_type']));
$field_options          = serialize($field_options_temp);
$agent_only             = intval(sanitize_text_field($_POST['agent_only']));
$required               = intval(sanitize_text_field($_POST['required']));
$assigned_categories    = isset($_POST['category']) && is_array($_POST['category']) ? sanitize_text_field(implode(',', $_POST['category'])) : '0';

$values = apply_filters('wpsp_set_add_custom_field', array(
    'label'            => $field_label,
		'instructions'     =>$field_instructions,
    'required'         => $required,
    'field_type'       => $field_type,
    'field_options'    => $field_options,
    'field_categories' => $assigned_categories,
    'isVarFeild'       => $agent_only
));

$wpdb->update( $wpdb->prefix.'wpsp_custom_fields', $values, array('id'=>$field_id) );

/**
 * Update translation option for custom field label in order to support WPML
 */
$custom_fields_localize = get_option('wpsp_custom_fields_localize');
if (!$custom_fields_localize) {
    $custom_fields_localize = array();
}
$custom_fields_localize['label_'.$field_id] = $field_label;
update_option('wpsp_custom_fields_localize', $custom_fields_localize);

/**
 * Remove record from form management if agent only is enabled
 * Otherwise update translation option for custom field label in order to support WPML
 */
$form_manage_id = $wpdb->get_var("select id from {$wpdb->prefix}wpsp_ticket_form_order WHERE field_key=".$field_id);
if( $agent_only ){
    
    if($form_manage_id){
        $wpdb->delete( $wpdb->prefix.'wpsp_ticket_form_order', array('id'=>$form_manage_id) );
    }
    
} else if( !$form_manage_id ) {
    
    $order = (int) $wpdb->get_var("select max(load_order) as current_order from {$wpdb->prefix}wpsp_ticket_form_order");
    $order++;
    $values = apply_filters('wpsp_form_manage_setting_insert', array(
        'field_key' => $field_id,
        'status' => 1,
        'full_width' => 1,
        'load_order' => $order
    ));
    $wpdb->insert($wpdb->prefix . 'wpsp_ticket_form_order', $values);
    
} else {
    $custom_fields_localize = get_option('wpsp_custom_fields_localize');
    if (!$custom_fields_localize) {
        $custom_fields_localize = array();
    }
    $custom_fields_localize['label_'.$field_id] = $field_label;
    update_option('wpsp_custom_fields_localize', $custom_fields_localize);
}

/**
 * Insert in ticket list options if previous type was not allowed and current type allowed
 * Reason for this condition is if previous type was not allowed, it would not have been added already
 */
if( !$wpsupportplus->functions->is_allowed_ticket_list($old_values->field_type) && $wpsupportplus->functions->is_allowed_ticket_list($field_type) ){
    $order = (int) $wpdb->get_var("select max(load_order) as current_order from {$wpdb->prefix}wpsp_ticket_list_order");
    $order++;
    
    $join_relation = $field_type == 6 ? 'BETWEEN' : 'LIKE';
    $join_relation = apply_filters( 'wpsp_custom_field_join_relation', $join_relation, $field_type );
    
    $values = apply_filters('wpsp_form_manage_setting_insert', array(
        'field_key'     => $field_id,
        'join_match'    => 't.cust'.$field_id,
        'join_compare'  => apply_filters( 'wpsp_list_order_compare', 't.cust'.$field_id, $field_type ),
        'join_relation' => $join_relation,
        'load_order'    => $order
    ));
    $wpdb->insert($wpdb->prefix . 'wpsp_ticket_list_order', $values);
}

/**
 * Delete ticket list option for this custom field if previous type was allowed but not current
 */
if( !$wpsupportplus->functions->is_allowed_ticket_list($field_type) ){
    $wpdb->delete( $wpdb->prefix.'wpsp_ticket_list_order', array('field_key'=>$field_id) );
}

do_action('wpsp_after_edit_custom_field', $field_id);
