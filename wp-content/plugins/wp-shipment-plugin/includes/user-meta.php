<?php

class WPSP_UserMeta
{
	function extra_user_profile_fields( $user )
	{
		include 'templates/user-fields.php';
	}

    function save_extra_user_profile_fields( $user_id ) {

        if ( !current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }

        update_user_meta( $user_id, 'account_funds', $_POST['account_funds'] );
        update_user_meta( $user_id, 'alternate_address', $_POST['alternate_address'] );
        update_user_meta( $user_id, 'fax_number', $_POST['fax_number'] );
        update_user_meta( $user_id, 'ups_rate', $_POST['ups_rate'] );
        update_user_meta( $user_id, 'usps_rate', $_POST['usps_rate'] );
        update_user_meta( $user_id, 'fedex_rate', $_POST['fedex_rate'] );
    }
}