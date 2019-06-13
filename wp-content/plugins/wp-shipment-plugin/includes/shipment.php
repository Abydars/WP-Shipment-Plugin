<?php

class WPSP_Shipment
{
	static function get_shipment( $id )
	{
		global $wpdb;

		return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}shipments WHERE id = {$id};" );
	}
}