<?php


class WPSP_Customer
{
	static function get_alt_email_address( $id )
	{
		$value = get_user_meta( $id, 'alternate_address', true );

		return $value;
	}

	static function get_account_funds( $id )
	{
		$value = get_user_meta( $id, 'account_funds', true );

		if ( $value === false ) {
			$value = 0;
		}

		return floatval( $value );
	}

	static function deduct_funds( $id, $amount )
	{
		$value = self::get_account_funds( $id ) - $amount;
		update_user_meta( $id, 'account_funds', $value );
	}

	static function add_funds( $id, $amount )
	{
		$value = self::get_account_funds( $id ) + $amount;
		update_user_meta( $id, 'account_funds', $value );
	}

	static function get_ups_markup_rate( $id )
	{
		$value = get_user_meta( $id, 'ups_rate', true );

		if ( $value === false ) {
			$value = 0;
		}

		return floatval( $value );
	}

	static function get_usps_markup_rate( $id )
	{
		$value = get_user_meta( $id, 'usps_rate', true );

		if ( $value === false ) {
			$value = 0;
		}

		return floatval( $value );
	}

	static function get_fedex_markup_rate( $id )
	{
		$value = get_user_meta( $id, 'fedex_rate', true );

		if ( $value === false ) {
			$value = 0;
		}

		return floatval( $value );
	}

	static function get_fax_number( $id )
	{
		$value = get_user_meta( $id, 'fax_number', true );

		return $value;
	}

	static function get_default_address( $id )
	{
		$value = get_user_meta( $id, 'default_address', true );

		return $value;
	}

	static function get_customer( $id )
	{
		$user = get_user_by( 'id', $id );

		return $user;
	}

	static function get_customer_by_email( $email )
	{
		$user = get_user_by( 'email', $email );

		return $user;
	}

	static function get_customers()
	{
		$users = get_users( [ 'number' => - 1 ] );

		return $users;
	}

	static function get_customer_initials( $id )
	{
		$customer = self::get_customer( $id );

		return WPSP_Helper::get_initials( $customer->display_name );
	}
}