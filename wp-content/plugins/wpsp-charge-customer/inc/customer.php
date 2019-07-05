<?php


class WPCC_Customer extends WPSP_Customer
{
	public static function log( $id, $text )
	{
		$date_now = date( 'Y-m-d H:i:s' );
		$logs     = self::get_logs( $id );
		$logs[]   = [ $date_now, $text ];

		return update_user_meta( $id, 'forte_logs', $logs );
	}

	public static function get_logs( $id )
	{
		$logs = get_user_meta( $id, 'forte_logs', true );

		if ( empty( $logs ) ) {
			$logs = [];
		}

		return $logs;
	}
}