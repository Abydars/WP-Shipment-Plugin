<?php

if ( ! class_exists( 'WPSP_Fatt' ) ) {

	class WPSP_Fatt
	{

		private $endpoint;
		private $endpoints;
		private $headers;
		private $key;
		private $authorization;
		private $is_sandbox;

		public function __construct()
		{
			$this->authorization = WPSP_FattCustomer::wpcc_get_option( 'fatt_live_authorization' );
			$this->is_sandbox    = WPSP_FattCustomer::wpcc_get_option( 'wpcc_test_mode' ) == 'yes';
			$this->endpoint      = 'https://apiprod.fattlabs.com/';
			$this->key           = $this->authorization;

			$this->headers   = array(
				"Content-Type: application/json",
				"Accept: application/json"
			);
			$this->endpoints = array(
				'createToken'       => '/ephemeral',
				//'getCustomers'      => '',
				'getCustomer'       => 'customer/{id}',
				'createCustomer'    => 'customer',
				'createTransaction' => 'charge',
				//'createPaymentMethod' => 'organizations/{organization_id}/locations/{location_id}/customers/{customer_token}/paymethods',
				'getPaymentMethod'  => 'customer/{customerId}/payment-method',
			);

			if ( $this->is_sandbox ) {
				//$this->endpoint      = 'https://apiprod.fattlabs.com/';
				//$this->authorization = WPSP_FattCustomer::wpcc_get_option( 'authorization' );
				//$this->key           = $this->authorization;
			}

			$generated_token = $this->createToken( $this->key );
			$this->headers[] = "Authorization: bearer {$generated_token}";
		}

		private function createToken( $key )
		{
			$response = $this->request( $this->getEndpoint( 'createToken' ), false, false, [
				"Content-Type: application/json",
				"Accept: application/json",
				"Authorization: {$key}"
			] );

			if ( ! empty( $response['token'] ) ) {
				return $response['token'];
			}

			return false;
		}

		public function getCustomerById( $customer_id )
		{
			$response = $this->request( add_query_arg( array(
				                                           'id' => $customer_id
			                                           ), $this->getEndpoint( 'getCustomer' ) ) );

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

		public function getPaymentMethod( $customer_id )
		{
			$response = $this->request( $this->getEndpoint( 'getPaymentMethod', array(
				'customerId' => $customer_id
			) ) );

			return $response;
		}

		private function getEndpoint( $key, $extras = array() )
		{
			$request = $this->endpoints[ $key ];

			if ( $extras ) {
				foreach ( $extras as $key => $val ) {
					$request = str_replace( '{' . $key . '}', $val, $request );
				}
			}

			return $this->endpoint . $request;
		}

		private function request( $endpoint, $data = false, $custom_request = false, $headers = [] )
		{
			$ch = curl_init();

			if ( ! empty( $headers ) ) {
				$headers = array_merge( $this->headers, $headers );
			} else {
				$headers = $this->headers;
			}

			curl_setopt( $ch, CURLOPT_URL, $endpoint );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

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