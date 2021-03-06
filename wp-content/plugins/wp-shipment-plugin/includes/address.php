<?php


class WPSP_Address
{

	public static function get_table_name()
	{
		global $wpdb;

		return $wpdb->prefix . 'addresses';
	}

	public static function get_addresses_by_customer( $customer_id )
	{
		global $wpdb;

		$table     = self::get_table_name();
		$addresses = $wpdb->get_results( "SELECT * FROM $table WHERE customer_id = $customer_id ORDER by id DESC" );

		if ( empty( $addresses ) ) {
			return array();
		}

		return $addresses;
	}

	public static function get_default_customer_address( $customer_id )
	{
		$value = get_user_meta( $customer_id, 'default_address', true );

		return $value;
	}

	public static function get_address_by_code( $code )
	{
		global $wpdb;

		$table      = self::get_table_name();
		$address_id = $wpdb->get_var( "SELECT id FROM $table WHERE address_code = '{$code}' ORDER by id DESC" );

		if ( empty( $address_id ) ) {
			return false;
		}

		return self::get_address( $address_id );
	}

	public static function get_addresses_no_customer()
	{
		global $wpdb;

		$table     = self::get_table_name();
		$addresses = $wpdb->get_results( "SELECT * FROM $table WHERE customer_id = 0 ORDER by id DESC" );

		if ( empty( $addresses ) ) {
			return array();
		}

		return $addresses;
	}

	public static function get_addresses()
	{
		global $wpdb;

		$table     = self::get_table_name();
		$addresses = $wpdb->get_results( "SELECT * FROM $table ORDER BY id DESC" );

		if ( empty( $addresses ) ) {
			return array();
		}

		return $addresses;
	}

	public static function get_address( $id )
	{
		global $wpdb;

		$table   = self::get_table_name();
		$address = $wpdb->get_row( "SELECT * FROM $table WHERE id = $id" );

		if ( empty( $address ) || ! json_decode( $address->data, true ) ) {
			return false;
		}

		$data = json_decode( $address->data, true );

		$address = array_merge( $data, array(
			"address_name" => $address->address_name,
			"address_id"   => $address->address_id,
			"customer_id"  => $address->customer_id,
			"is_verified"  => $address->is_verified,
			"id"           => $address->id
		) );

		return $address;
	}

	public static function get_address_id( $id )
	{
		global $wpdb;

		$table      = self::get_table_name();
		$address_id = $wpdb->get_var( "SELECT address_id FROM $table WHERE id = $id" );

		return $address_id;
	}

	public static function get_address_code( $id )
	{
		global $wpdb;

		$table        = self::get_table_name();
		$address_code = $wpdb->get_var( "SELECT address_code FROM $table WHERE id = $id" );

		return $address_code;
	}

	public static function delete_address( $id )
	{
		global $wpdb;

		return $wpdb->delete( self::get_table_name(), [
			'id' => $id
		] );
	}

	public static function store_address( $data )
	{
		global $wpdb;

		$is_first = false;
		$address  = $data;

		if ( ! empty( $data->customer ) ) {
			$addresses = self::get_addresses_by_customer( $data->customer );
			$is_first  = empty( $addresses );
		}

		if ( empty( $address->code ) ) {

			$initials = strtoupper( WPSP_Helper::str_random( 2, 'alphabetic' ) );

			if ( ! empty( $address->full_name ) ) {
				$initials = WPSP_Helper::get_initials( $address->full_name );
			} else if ( ! empty( $address->customer ) ) {
				$initials = WPSP_Customer::get_customer_initials( $address->customer );
			}

			$random        = WPSP_Helper::str_random( 3, 'numeric' );
			$code          = "{$initials}-{$address->state}-{$random}";
			$address->code = $code;
		}

		$row = array(
			"address_name"   => $address->code . " - " . $address->full_name . " " . $address->street_1 . " " . $address->street_2 . ", " . $address->city . ", " . $address->state . ", " . $address->country . " " . $address->zip_code,
			"customer_id"    => $address->customer,
			"address_code"   => $address->code,
			"data"           => json_encode( $address ),
			"is_default"     => $is_first,
			"type"           => null,
			"is_verified"    => 1,
			"is_residential" => $address->is_residential,
		);

		$wpdb->insert( self::get_table_name(), $row );

		return $address;
	}

	public static function edit_address( $id, $data )
	{
		global $wpdb;

		$error       = false;
		$old_address = self::get_address( $id );
		$old_address = (object) $old_address;
		$address     = $data;

		$addresses = self::get_addresses_by_customer( $old_address->customer_id );

		$is_first = empty( $addresses );
		$row      = array(
			"address_name" => $address->code . " - " . $address->full_name . " " . $address->street_1 . " " . $address->street_2 . ", " . $address->city . ", " . $address->state . ", " . $address->country . " " . $address->zip_code,
			"address_code" => $address->code,
			"customer_id"  => $old_address->customer_id,
			"data"         => json_encode( $address ),
			"is_default"   => $is_first,
			"type"         => null,
			"is_verified"  => 0
		);
		$where    = array(
			"id" => $id
		);

		$result = $wpdb->update( self::get_table_name(), $row, $where );

		if ( false === $result ) {
			$error = true;
		}

		return $error;
	}
}