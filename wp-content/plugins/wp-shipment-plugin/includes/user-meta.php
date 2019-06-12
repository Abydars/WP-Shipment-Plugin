<?php

class WPSP_UserMeta
{
	/*
	 * TASK FOR ARSALAN
	 *
	 * User ki fields save nae karai hen?
	 *
	 */

	function extra_user_profile_fields( $user )
	{
		include 'templates/user-fields.php';
	}
}