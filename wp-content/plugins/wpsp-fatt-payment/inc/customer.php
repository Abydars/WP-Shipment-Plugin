<?php

if ( ! class_exists( 'WPCC_Customer' ) && class_exists( 'WPSP_Customer' ) ) {
	class WPCC_Customer extends WPSP_Customer
	{
		public static function log( $id, $text )
		{
			echo $text . '<br/>';
			$date_now = date( 'Y-m-d H:i:s' );
			$logs     = self::get_logs( $id );
			$logs[]   = [ $date_now, $text ];

			return update_user_meta( $id, 'fatt_logs', $logs );
		}

		public static function get_logs( $id )
		{
			$logs = get_user_meta( $id, 'fatt_logs', true );

			if ( empty( $logs ) ) {
				$logs = [];
			}

			return $logs;
		}

		public static function set_reload( $id, $value )
		{
			update_user_meta( $id, 'fatt_user_reload_amount', $value );
		}

		public static function get_reload( $id )
		{
			$value = get_user_meta( $id, 'fatt_user_reload_amount', true );

			return $value;
		}
	}
}