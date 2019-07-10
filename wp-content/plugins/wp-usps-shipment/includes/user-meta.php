<?php

class WPSP_USPS_UserMeta
{
	function extra_user_profile_fields( $user )
	{
		include 'templates/user-fields.php';
	}

	function save_extra_user_profile_fields( $user_id )
	{
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		update_user_meta( $user_id, 'usps_customer_id', $_POST['usps_customer_id'] );
	}

	static function get_field( $id, $key )
	{
		$value = get_user_meta( $id, $key, true );

		return $value;
	}
}