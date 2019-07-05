<?php

if ( ! class_exists( 'WPSP_Forte' ) ) {

	class WPSP_Forte
	{

		private $endpoint;
		private $endpoints;
		private $headers;
		private $key;

		public function __construct()
		{
			$this->endpoint = 'https://api.forte.net/v3/';
			$this->key      = ShipmentForteAuthorizationProduction;

			$this->endpoints = array(
				'getCustomers'        => 'organizations/{organization_id}/locations/{location_id}/customers',
				'createCustomer'      => 'organizations/{organization_id}/locations/{location_id}/customers',
				'createTransaction'   => 'organizations/{organization_id}/locations/{location_id}/transactions',
				'createPaymentMethod' => 'organizations/{organization_id}/locations/{location_id}/customers/{customer_token}/paymethods',
				'getPaymentMethod'    => 'organizations/{organization_id}/locations/{location_id}/customers/{customer_token}/paymethods/mth_{paymethod_token}',
			);

			if ( ShipmentForteSandbox ) {
				$this->endpoint = 'https://sandbox.forte.net/api/v3/';
				$this->key      = ShipmentForteAuthorization;
			}

			$this->headers = array(
				"Content-Type: application/json",
				"Accept: application/json",
				"X-Forte-Auth-Organization-Id : org_" . ShipmentForteOrgId,
				"Authorization: " . $this->key
			);
		}

		public function getCustomerById( $customer_id )
		{
			$response = $this->request( add_query_arg( array(
				                                           'filter' => "customer_id+eq+$customer_id"
			                                           ), $this->getEndpoint( 'getCustomers' ) ) );

			return $response;
		}

		public function createCustomer( $data )
		{
			$response = $this->request( $this->getEndpoint( 'createCustomer' ), $data );

			return $response;
		}

		public function createTransaction( $data )
		{
			$response = $this->request( $this->getEndpoint( 'createTransaction' ), $data );

			return $response;
		}

		public function createPaymentMethod( $data, $token = false )
		{
			$response = $this->request( $this->getEndpoint( 'createPaymentMethod', array(
				'customer_token' => $token
			) ), $data );

			return $response;
		}

		public function getPaymentMethod( $token )
		{
			$response = $this->request( $this->getEndpoint( 'createPaymentMethod', array(
				'paymethod_token' => $token
			) ) );

			return $response;
		}

		private function getEndpoint( $key, $extras = array() )
		{
			$request = $this->endpoints[ $key ];

			$request = str_replace( '{organization_id}', "org_" . ShipmentForteOrgId, $request );
			$request = str_replace( '{location_id}', "loc_" . ShipmentForteLocId, $request );

			if ( $extras ) {
				foreach ( $extras as $key => $val ) {
					$request = str_replace( '{' . $key . '}', $val, $request );
				}
			}

			return $this->endpoint . $request;
		}

		private function request( $endpoint, $data = false, $custom_request = false )
		{
			$ch = curl_init();

			curl_setopt( $ch, CURLOPT_URL, $endpoint );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $this->headers );

			if ( $data ) {

				if ( ! $custom_request ) {
					curl_setopt( $ch, CURLOPT_POST, 1 );
				} else {
					curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, $custom_request );
				}

				curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
			}

			$response = curl_exec( $ch );

			if ( curl_errno( $ch ) !== 0 || ! json_decode( $response, true ) ) {
				$error_message = ! json_decode( $response, true ) ? "Failed to parse: {$response}\nData: {$data}" : curl_error( $ch );

				return array(
					"status" => false,
					"error"  => $error_message
				);
			}

			if ( is_resource( $ch ) ) {
				curl_close( $ch );
			}

			return json_decode( $response, true );
		}

	}

}