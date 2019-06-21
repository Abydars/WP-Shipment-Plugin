<?php

if ( ! class_exists( 'WPSP_Helper' ) ) {

	class WPSP_Helper
	{
		static function str_random( $length = 10 )
		{
			$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen( $characters );
			$randomString     = '';
			for ( $i = 0; $i < $length; $i ++ ) {
				$randomString .= $characters[ rand( 0, $charactersLength - 1 ) ];
			}

			return $randomString;
		}

		static function get_initials( $name )
		{
			$names    = explode( ' ', preg_replace( '/[^A-Za-z0-9\- ]/', '', $name ) );
			$initials = array_map( function ( $name ) {
				return $name[0];
			}, $names );

			return preg_replace( '/[^A-Za-z0-9\-]/', '', strtoupper( implode( '', $initials ) ) );
		}

		static function get_constants()
		{
			$constants = file_get_contents( dirname( __FILE__ ) . '/assets/constants.json' );

			return json_decode( $constants, true );
		}

		static function get_countries()
		{
			$countries = self::get_constants()['countries'];

			return $countries;
		}

		static function get_states( $country )
		{
			$countries = self::get_countries();

			foreach ( $countries as $country ) {
				if ( $country['code2'] === $country ) {
					return $country['states'];
				}
			}

			return false;
		}
	}
}