<?php

class WPSP_ShipmentActions
{
	function filter_wpsp_file_dir( $filename )
	{
		$filepath = WPSP_FILES_DIR . $filename;

		return $filepath;
	}

	function filter_wpsp_file_url( $filename )
	{
		$fileurl = WPSP_FILES_URL . $filename;

		return $fileurl;
	}

	function filter_wpsp_error( $text )
	{
		return '<div class="wpsp-error"' . ( empty( $text ) ? 'style="display: none;"' : '' ) . '><h3><i class="fa fa-frown"></i><p>' . $text . '</p></h3></div>';
	}

	function filter_wpsp_success( $text )
	{
		return '<div class="wpsp-success"' . ( empty( $text ) ? 'style="display: none;"' : '' ) . '><h3><i class="fa fa-smile"></i><p>' . $text . '</p></h3></div>';
	}

	function action_create_new_address()
	{
		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'wpsp_create_address' ) ) {
			$error     = false;
			$post_data = (object) $_POST;

			if ( ! empty( $post_data->carrier ) ) {
				do_action_ref_array( "wpsp_verify_address_{$post_data->carrier}", [
					$post_data,
					&$error
				] );
			}

			if ( ! $error ) {
				WPSP_Address::store_address( $post_data );
				wp_redirect( admin_url( 'admin.php?page=list_addresses' ) );
			} else {
				wp_redirect( admin_url( 'admin.php?page=create_address&error=' . $error ) );
			}
			die;
		}
	}

	function action_void_label()
	{
		$response    = [];
		$shipment_id = $_REQUEST['id'];
		$refund      = isset( $_REQUEST['refund'] ) && $_REQUEST['refund'] == '1';
		$shipment    = WPSP_Shipment::get_shipment( $shipment_id );
		$carrier     = $shipment->server;
		$error       = false;

		$response['status']  = true;
		$response['message'] = __( 'Label void successfully', WPSP_LANG );

		do_action_ref_array( "wpsp_void_label_{$carrier}", [
			&$error,
			$shipment_id
		] );

		if ( ! $error ) {
			$shipment->status = 'Cancelled';

			WPSP_Shipment::update_shipment( $shipment_id, (array) $shipment );

			if ( $refund ) {
				WPSP_Customer::add_funds( $shipment->customer_id, floatval( $shipment->rates ) );
			}
		}

		if ( $error ) {
			$response['status']  = false;
			$response['message'] = $error;
		}

		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die;
	}

	function action_save_label()
	{
		global $wpdb;

		$response = [];

		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'wpsp_save_label' ) ) {

			$post_data     = (object) $_POST;
			$error         = false;
			$shipment_data = false;
			$rates         = 0;

			// rates
			do_action_ref_array( "wpsp_label_rates_{$post_data->carrier}", [
				$post_data,
				&$error,
				&$rates
			] );

			if ( ! $error && $rates ) {

				// check for funds
				$user_funds  = WPSP_Customer::get_account_funds( $post_data->customer );
				$customer    = WPSP_Customer::get_customer( $post_data->customer );
				$fax_number  = WPSP_Customer::get_fax_number( $post_data->customer );
				$markup_rate = apply_filters( "wpsp_get_markup_rate_{$post_data->carrier}", 0, $post_data->customer );
				$label_rates = $rates;

				if ( $markup_rate > 0 ) {
					$markup_rate = ( $rates * ( $markup_rate / 100 ) );
					$rates       += $markup_rate;
				}

				if ( $user_funds > $rates ) {

					// create shipment
					do_action_ref_array( "wpsp_create_shipment_{$post_data->carrier}", [
						$post_data,
						&$error,
						&$shipment_data
					] );

					if ( ! $error && ! empty( $shipment_data ) ) {

						// db entry
						$row = array(
							"shipKey"        => "",
							"ticket_id"      => $post_data->ticket_id,
							"customer_id"    => $post_data->customer,
							"creator_id"     => get_current_user_id(),
							"server"         => $post_data->carrier,
							"status"         => "Pending",
							"serverLevel"    => $post_data->shipping_method,
							"packageType"    => $post_data->package_type,
							"creation_date"  => date( "Y/m/d" ),
							"shipDate"       => date( 'Y-m-d', strtotime( $post_data->shipping_date ) ),
							"toAddress_id"   => intval( $post_data->to ),
							"fromAddress_id" => intval( $post_data->from ),
							"pickup_date"    => ! empty( $post_data->pickup_date ) ? date( 'Y-m-d H:i:s', strtotime( $post_data->pickup_date ) ) : null,
							"dropOffType"    => "",
							"confirmation"   => "",
							"reference"      => "",
							"shipmentNo"     => "",
							"rates"          => $rates,
							"markupRate"     => $markup_rate,
							"labelRate"      => $label_rates
						);

						$columns = array_keys( $row );
						$row     = array_merge( $row, $shipment_data );
						$row     = array_filter( $row, function ( $key ) use ( $columns ) {
							return in_array( $key, $columns );
						}, ARRAY_FILTER_USE_KEY );

						$inserted = $wpdb->insert( $wpdb->prefix . 'shipments', $row );

						if ( $inserted ) {
							$shipment_id    = $wpdb->insert_id;
							$encoded_images = [];

							// create label
							do_action_ref_array( "wpsp_create_label_{$post_data->carrier}", [
								&$error,
								&$encoded_images,
								$shipment_data,
								$shipment_id,
								$post_data,
							] );

							if ( ! $error ) {

								// TODO: generate label
								$shipment    = WPSP_Shipment::get_shipment( $shipment_id );
								$to_address  = WPSP_Address::getAddress( $post_data->to );
								$extra_files = [];
								$pages       = [];

								$filename = apply_filters( 'wpsp_file_dir', "{$post_data->carrier}-{$shipment_id}-summary.pdf" );
								$subject  = __( 'Label', WPSP_LANG );
								$subtitle = __( '', WPSP_LANG );

								$text = "Ticket #ID: {$post_data->ticket_id}";

								if ( ! empty( $to_address['full_name'] ) ) {
									$text .= "<br /><br />Shipped To: {$to_address['full_name']}";
								}

								$text .= "<br />Shipping Cost: $" . number_format( $shipment->rates, 2 );

								if ( ! empty( $to_address['is_verified'] ) && $to_address['is_verified'] == 0 ) {
									$text .= "<br /><br /><span style='color: red;'>Unverified Address</span>";
								}

								$text = apply_filters( "wpsp_label_summary", $text, $shipment_id );
								$text = apply_filters( "wpsp_label_summary_{$post_data->carrier}", $text, $shipment_id );

								WPSP_PdfHelper::generate( $text, $filename, $subject, $subtitle );

								$pages[]       = $filename;
								$extra_files[] = $filename;

								foreach ( $encoded_images as $k => $encoded_image ) {
									$filename = apply_filters( 'wpsp_file_dir', "{$post_data->carrier}-{$shipment_id}-{$k}.pdf" );

									if ( strpos( basename( $encoded_image ), '.pdf' ) > 0 ) {
										$filename = $encoded_image;
									} else {
										WPSP_PdfHelper::generate( $encoded_image, $filename, '', '', 'image' );
									}

									$pages[]       = $filename;
									$extra_files[] = $filename;
									$extra_files[] = $encoded_image;
								}

								$final_filename = apply_filters( 'wpsp_file_dir', "{$post_data->carrier}-{$shipment_id}.pdf" );
								$final_fileurl  = apply_filters( 'wpsp_file_url', "{$post_data->carrier}-{$shipment_id}.pdf" );

								WPSP_PdfHelper::merge( $pages, 'F', $final_filename );

								foreach ( $extra_files as $page ) {
									unlink( $page );
								}

								// send label via email
								$email         = $customer->user_email;
								$headers       = array(
									'Content-Type: text/html; charset=UTF-8'
								);
								$attachments[] = $final_filename;

								wp_mail( $email, "Ship4LessLabels - Shipment #{$shipment_id}", __( "Label Summary: {$text}", WPSP_LANG ), $headers, $attachments );

								// send label via fax
								if ( class_exists( 'WPTM_FaxManager' ) && ! empty( $fax_number ) ) {
									$wptm_manager = new WPTM_FaxManager();
									$wptm_manager->sendFax( $fax_number, $final_fileurl );
								}

								// funds deduct
								WPSP_Customer::deduct_funds( $post_data->customer, $rates );

								$response['status']  = true;
								$response['data']    = [
									'shipment_id' => $shipment_id
								];
								$response['message'] = __( 'Shipment created successfully', WPSP_LANG );
								$response['nonce']   = wp_create_nonce( 'wpsp_save_label' );
							}

						} else {
							$error = __( 'Failed to add shipment', WPSP_LANG );
						}
					}
				} else {
					$error = __( 'No funds available', WPSP_LANG );
				}
			} else {
				$error = __( 'Rates not found', WPSP_LANG );
			}

			if ( $error !== false ) {
				$response['status']  = false;
				$response['message'] = $error;
				$response['nonce']   = wp_create_nonce( 'wpsp_save_label' );
			}

		} else {
			$response['status']  = false;
			$response['message'] = __( 'Please try again', WPSP_LANG );
		}

		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die;
	}

	function action_add_address()
	{
		$response = [];

		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'wpsp_add_address' ) ) {

			$error     = false;
			$post_data = (object) $_POST;

			do_action_ref_array( "wpsp_verify_address_{$post_data->carrier}", [
				$post_data,
				&$error
			] );

			if ( ! $error ) {
				$address             = WPSP_Address::store_address( $post_data );
				$response['status']  = true;
				$response['message'] = __( 'Address created successfully', WPSP_LANG );
				$response['data']    = $address;
			} else {
				$response['status']  = false;
				$response['message'] = $error;
			}
		} else {
			$response['status']  = false;
			$response['message'] = __( 'Please try again', WPSP_LANG );
		}

		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die;
	}

	function action_carrier_levels()
	{
		$carrier = $_REQUEST['carrier'];
		$levels  = [];
		$levels  = apply_filters( "wpsp_shipment_{$carrier}_levels", $levels );

		header( 'Content-Type: application/json' );
		echo json_encode( $levels );
		die;
	}

	function action_package_types()
	{
		$carrier = $_REQUEST['carrier'];
		$types   = [];
		$types   = apply_filters( "wpsp_shipment_{$carrier}_package_types", $types );

		header( 'Content-Type: application/json' );
		echo json_encode( $types );
		die;
	}

	function action_get_rates()
	{
		$all_rates    = [];
		$response     = [];
		$error        = false;
		$post_data    = (object) $_POST;
		$carrier_keys = [ $post_data->carrier ];
		$carriers     = apply_filters( 'wpsp_shipment_carriers', [] );
		$lowest_rate  = false;

		if ( empty( $post_data->carrier ) ) {
			$carrier_keys = array_keys( $carriers );
		}

		foreach ( $carrier_keys as $k => $carrier ) {
			$rates = [];

			do_action_ref_array( "wpsp_service_rates_{$carrier}", [
				$post_data,
				&$error,
				&$rates
			] );

			$all_rates[ $carrier ] = [
				'name'  => $carriers[ $carrier ],
				'rates' => $rates
			];

			foreach ( $rates as $rate ) {
				if ( $lowest_rate === false || $rate['rate'] < $lowest_rate['rate'] ) {
					$lowest_rate = [
						'carrier'      => $carrier,
						'level'        => $rate['level'],
						'package_type' => $rate['package_type'],
						'rate'         => $rate['rate']
					];
				}
			}
		}

		$response['status'] = true;
		$response['data']   = [
			'rates'  => $all_rates,
			'lowest' => $lowest_rate
		];

		if ( $error ) {
			$response['status']  = true;
			$response['message'] = $error;
		}

		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die;
	}

	function action_get_addresses()
	{
		$customer_id = $_REQUEST['customer_id'];
		$addresses   = WPSP_Address::get_addresses_by_customer( $customer_id );
		$addresses   = array_merge( $addresses, WPSP_Address::get_addresses_no_customer() );

		header( 'Content-Type: application/json' );
		echo json_encode( $addresses );
		die;
	}

	function shipment_details()
	{
		include( 'templates/shipment-details.php' );
	}

	function action_edit_address()
	{
		$response = [];

		if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'wpsp_edit_address' ) ) {

			$error     = false;
			$post_data = (object) $_POST;

			if ( ! $error ) {
				$id                  = $post_data->id;
				$address             = WPSP_Address::edit_address( $id, $post_data );
				$response['status']  = true;
				$response['message'] = __( 'Address edited successfully', WPSP_LANG );
				$response['data']    = $address;
			} else {
				$response['status']  = false;
				$response['message'] = $error;
			}
		} else {
			$response['status']  = false;
			$response['message'] = __( 'Please try again', WPSP_LANG );
		}

		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die;
	}

	function action_delete_address()
	{
		$response = [
			'status'  => true,
			'message' => __( 'Address deleted successfully', WPSP_LANG )
		];

		$deleted = WPSP_Address::delete_address( $_REQUEST['id'] );

		if ( ! $deleted ) {
			$response = [
				'status'  => false,
				'message' => __( 'Address failed to delete', WPSP_LANG )
			];
		}

		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die;
	}
}