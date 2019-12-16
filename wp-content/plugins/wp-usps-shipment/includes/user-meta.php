<?php

class WPSP_USPS_UserMeta
{
	function handle_admin_errors()
	{
		if ( isset( $_GET['e'] ) ) {
			add_action( 'admin_notices', function () {
				$class   = 'notice notice-error';
				$message = __( urldecode( $_GET['e'] ), 'sample-text-domain' );

				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
			} );
		}
	}

	function extra_user_profile_fields( $user )
	{
		include 'templates/user-fields.php';
	}

	function save_extra_user_profile_fields( $user_id )
	{
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		$users = get_users( [
			                    'meta_key'     => 'usps_customer_id',
			                    'meta_value'   => $_POST['usps_customer_id'],
			                    'meta_compare' => '=',
			                    'exclude'      => [ $user_id ]
		                    ] );

		if ( ! empty( $users ) ) {
			$assigned_to = $users[0];
			$message     = __( "Customer ID already assigned to {$assigned_to->display_name}.", WPSP_LANG );

			wp_redirect( add_query_arg( [ 'e' => urlencode( $message ), 'user_id' => $user_id ] ) );
			die;
		} else {
			update_user_meta( $user_id, 'usps_customer_id', $_POST['usps_customer_id'] );
		}
	}

	static function get_field( $id, $key )
	{
		$value = get_user_meta( $id, $key, true );

		return $value;
	}
}