<?php

class WPSP_UserMeta
{
	function extra_user_profile_fields( $user )
	{
		$addresses = WPSP_Address::get_addresses_by_customer( $user->ID );
		$addresses = array_merge( $addresses, WPSP_Address::get_addresses_no_customer() );
		$carriers  = apply_filters( "wpsp_shipment_carriers", [] );

		include 'templates/user-fields.php';
	}

	function save_extra_user_profile_fields( $user_id )
	{
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$carriers = apply_filters( "wpsp_shipment_carriers", [] );

		update_user_meta( $user_id, 'default_address', $_POST['default_address'] );
		update_user_meta( $user_id, 'account_funds', $_POST['account_funds'] );
		update_user_meta( $user_id, 'alternate_address', $_POST['alternate_address'] );
		update_user_meta( $user_id, 'fax_number', $_POST['fax_number'] );

		foreach ( $carriers as $k => $carrier ) {
			if ( isset( $_POST["{$k}_rate"] ) ) {
				update_user_meta( $user_id, "{$k}_rate", $_POST["{$k}_rate"] );
			}
		}
	}
}