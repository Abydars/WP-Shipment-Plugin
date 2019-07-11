<?php

class WPSP_Shipment
{
	static function get_shipment( $id )
	{
		global $wpdb;

		return $wpdb->get_row( "SELECT * FROM {$wpdb->prefix}shipments WHERE id = {$id};" );
	}

	static function get_labels( $id )
	{
		global $wpdb;

		$labels = $wpdb->get_var( "SELECT labels FROM {$wpdb->prefix}labels WHERE shipment_id = {$id};" );
		$labels = json_decode( $labels, true );

		if ( empty( $labels ) ) {
			$labels = [];
		}

		return $labels;
	}

	static function get_shipments( $order_by = 'desc' )
	{
		global $wpdb;

		return $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}shipments ORDER BY id {$order_by}" );
	}

	static function get_shipments_where( $where = [] )
	{
		global $wpdb;

		$query = "SELECT * FROM {$wpdb->prefix}shipments WHERE 1=1";

		foreach ( $where as $v ) {
			$query .= ' AND ' . $v;
		}

		return $wpdb->get_results( $query );
	}

	static function update_shipment( $id, $data )
	{
		global $wpdb;

		$shipment = (array) self::get_shipment( $id );
		$shipment = array_merge( $shipment, $data );

		return $wpdb->update( $wpdb->prefix . 'shipments', $shipment, [
			'id' => $id
		] );
	}
}