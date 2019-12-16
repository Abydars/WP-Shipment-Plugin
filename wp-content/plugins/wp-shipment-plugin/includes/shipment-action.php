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
			$error          = false;
			$post_data      = (object) $_POST;
			$is_residential = true;
			$carriers       = apply_filters( 'wpsp_shipment_carriers', [] );

			foreach ( $carriers as $k_carrier => $v_carrier ) {
				$temp_is_residential = false;

				do_action_ref_array( "wpsp_verify_address_{$k_carrier}", [
					$post_data,
					&$error,
					&$temp_is_residential
				] );

				$is_residential &= $temp_is_residential;

				if ( $error !== false ) {
					break;
				}
			}

			if ( ! $error ) {
				$post_data->is_residential = $is_residential ? 1 : 0;

				WPSP_Address::store_address( $post_data );
				wp_redirect( admin_url( 'admin.php?page=list_addresses' ) );
			} else {
				wp_redirect( admin_url( 'admin.php?page=create_address&error=' . urlencode( $error ) ) );
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

		if ( isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'wpsp_save_label' ) ) {

			$post_data     = (object) $_REQUEST;
			$error         = false;
			$shipment_data = false;
			$rates         = 0;
			$pickup_rates  = 0;

			// rates
			do_action_ref_array( "wpsp_label_rates_{$post_data->carrier}", [
				$post_data,
				&$error,
				&$rates
			] );

			if ( ! $error && $rates > 0 ) {

				do_action_ref_array( "wpsp_service_pickup_rates_{$post_data->carrier}", [
					$post_data,
					&$error,
					&$pickup_rates
				] );
				// check for funds
				$user_funds  = WPSP_Customer::get_account_funds( $post_data->customer );
				$user_funds =  $user_funds + (150);
				$customer    = WPSP_Customer::get_customer( $post_data->customer );
				$fax_number  = WPSP_Customer::get_fax_number( $post_data->customer );
				$markup_rate = WPSP_Customer::get_markup_rate( $post_data->customer, $post_data->carrier );
				$label_rates = $rates;
				$markup      = $pickup_rates;

				if ( $markup_rate > 0 ) {
					$markup += ( $rates * ( $markup_rate / 100 ) );
					$rates  += $markup;
				}

						$funds_available = apply_filters( 'funds_available', ( $user_funds > $rates ), $user_funds, $rates );
				if ( $funds_available ) {


					// create shipment
					do_action_ref_array( "wpsp_create_shipment_{$post_data->carrier}", [
						$post_data,
						&$error,
						&$shipment_data
					] );

					if ( ! $error && ! empty( $shipment_data ) ) {

						$creator_id = ( ! empty( $post_data->creator_id ) ? $post_data->creator_id : null );

						if ( is_user_logged_in() && empty( $creator_id ) ) {
							$creator_id = get_current_user_id();
						}

						// db entry
						$row = array(
							"shipKey"        => "",
							"ticket_id"      => $post_data->ticket_id,
							"customer_id"    => $post_data->customer,
							"creator_id"     => $creator_id,
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
							"markupRate"     => $markup,
							"labelRate"      => $label_rates,
							"packages"       => json_encode( $post_data->packages ),
							"tracking"       => ""
						);

						$columns = array_keys( $row );
						$row     = array_merge( $row, $shipment_data );
						$row     = array_filter( $row, function ( $key ) use ( $columns ) {
							return in_array( $key, $columns );
						}, ARRAY_FILTER_USE_KEY );

						if ( ! empty( $row['tracking'] ) ) {
							$row['tracking'] = json_encode( $row['tracking'] );
						}

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
								$to_address  = WPSP_Address::get_address( $post_data->to );
								$extra_files = [];
								$pages       = [];

								$filename = apply_filters( 'wpsp_file_dir', "{$post_data->carrier}-{$shipment_id}-summary.pdf" );
								$subject  = __( 'Label', WPSP_LANG );
								$subtitle = __( '', WPSP_LANG );

								$text = "";

								if ( ! empty( $post_data->ticket_id ) ) {
									$text .= "Ticket #ID: {$post_data->ticket_id}";
								}

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

								$fname          = "{$post_data->carrier}-{$shipment_id}.pdf";
								$final_filename = apply_filters( 'wpsp_file_dir', $fname );
								$final_fileurl  = apply_filters( 'wpsp_file_url', $fname );

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
								$blogname      = get_bloginfo( 'name' );

								$inserted = $wpdb->insert( $wpdb->prefix . 'labels', [
									'shipment_id' => $shipment_id,
									'labels'      => json_encode( [ $fname ] ),
									'label_type'  => 'PDF'
								] );

								wp_mail( $email, "{$blogname} - Shipment #{$shipment_id}", __( "Label Summary: {$text}", WPSP_LANG ), $headers, $attachments );

								// send label via fax
								if ( class_exists( 'WPTM_FaxManager' ) && ! empty( $fax_number ) ) {
									$wptm_manager = new WPTM_FaxManager();
									$wptm_manager->sendFax( '+' . $fax_number, $final_fileurl );
								}

								// funds deduct
								WPSP_Customer::deduct_funds( $post_data->customer, $rates );

								$response['status']  = true;
								$response['data']    = [
									'shipment_id' => $shipment_id
								];
								$response['message'] = __( 'Shipment created successfully', WPSP_LANG );
								$response['nonce']   = wp_create_nonce( 'wpsp_save_label' );

								do_action( 'wpsp_after_shipment_creation', $shipment_id );
							}

						} else {
							$error = __( 'Failed to add shipment', WPSP_LANG );
						}
					}
				} else {
					$error = __( 'No funds available', WPSP_LANG );
				}
			} else if ( ! $error ) {
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

			$error          = false;
			$post_data      = (object) $_POST;
			$is_residential = true;
			$carriers       = apply_filters( 'wpsp_shipment_carriers', [] );

			foreach ( $carriers as $k_carrier => $v_carrier ) {
				$temp_is_residential = false;

				do_action_ref_array( "wpsp_verify_address_{$k_carrier}", [
					$post_data,
					&$error,
					&$temp_is_residential
				] );

				$is_residential &= $temp_is_residential;

				if ( $error !== false ) {
					break;
				}
			}

			if ( ! $error ) {
				$post_data->is_residential = $is_residential ? 1 : 0;
				$address                   = WPSP_Address::store_address( $post_data );
				$response['status']        = true;
				$response['message']       = __( 'Address created successfully', WPSP_LANG );
				$response['data']          = $address;
			} else {
				$response['status']  = false;
				$response['message'] = $error;
			}
		} else {
			$response['status']  = false;
			$response['none']    = wp_create_nonce( 'wpsp_add_address' );
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
		$has_rates    = false;

		if ( empty( $post_data->carrier ) ) {
			$carrier_keys = array_keys( $carriers );
		}

		foreach ( $carrier_keys as $k => $carrier ) {
			$rates        = [];
			$pickup_rates = 0;
			$markup_rate  = WPSP_Customer::get_markup_rate( $post_data->customer, $carrier );

			if ( $markup_rate > 0 ) {
				$markup_rate = ( $markup_rate / 100 );
			}

			do_action_ref_array( "wpsp_service_rates_{$carrier}", [
				$post_data,
				&$error,
				&$rates
			] );

			if ( $error === false ) {

				do_action_ref_array( "wpsp_service_pickup_rates_{$carrier}", [
					$post_data,
					&$error,
					&$pickup_rates
				] );

				if ( $error === false ) {

					$rates = array_filter( $rates, function ( $rate ) {
						return ( $rate['rate'] > 0 );
					} );
					$rates = array_values( $rates );

					foreach ( $rates as $j => $rate ) {
						$markup = floatval( number_format( $rate['rate'] * $markup_rate, 2 ) );
						$markup += $pickup_rates;
						$total  = floatval( number_format( $rate['rate'] + $markup, 2 ) );

						$rates[ $j ]['markup'] = $markup;
						$rates[ $j ]['total']  = $total;
					}

					$all_rates[ $carrier ] = [
						'name'         => $carriers[ $carrier ],
						'pickup_rates' => $pickup_rates,
						'rates'        => $rates
					];

					if ( ! $has_rates && count( $rates ) > 0 ) {
						$has_rates = true;
					}

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
			} else {
				$all_rates[ $carrier ] = [
					'name'  => $carriers[ $carrier ],
					'error' => $error
				];
			}
		}

		$response['status'] = true;
		$response['data']   = [
			'rates'  => $all_rates,
			'lowest' => $lowest_rate
		];

		if ( ! $has_rates && $error === false ) {
			$error = __( "Rates not found", WPSP_LANG );
		}

		if ( $error ) {
			$response['status']  = false;
			$response['message'] = $error;
		}

		header( 'Content-Type: application/json' );
		echo json_encode( $response );
		die;
	}

	function action_get_addresses()
	{
		$customer_id = $_REQUEST['customer_id'];
		$addresses   = [];
		$addresses   = array_merge( $addresses, WPSP_Address::get_addresses_no_customer() );

		if ( ! empty( $customer_id ) ) {
			$addresses = array_merge( $addresses, WPSP_Address::get_addresses_by_customer( $customer_id ) );
		}

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

			$error          = false;
			$post_data      = (object) $_POST;
			$is_residential = true;
			$carriers       = apply_filters( 'wpsp_shipment_carriers', [] );

			foreach ( $carriers as $k_carrier => $v_carrier ) {
				$temp_is_residential = false;

				do_action_ref_array( "wpsp_verify_address_{$k_carrier}", [
					$post_data,
					&$error,
					&$temp_is_residential
				] );

				$is_residential &= $temp_is_residential;

				if ( $error !== false ) {
					break;
				}
			}

			if ( ! $error ) {
				$id                        = $post_data->id;
				$post_data->is_residential = $is_residential ? 1 : 0;
				$address                   = WPSP_Address::edit_address( $id, $post_data );
				$response['status']        = true;
				$response['message']       = __( 'Address edited successfully', WPSP_LANG );
				$response['data']          = $address;
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

	function action_get_states()
	{
		$country = $_REQUEST['country'];

		$states = WPSP_Helper::get_states( $country );

		header( 'Content-Type: application/json' );
		echo json_encode( $states );
		die;
	}

	function action_save_settings()
	{
		$not = [ '_wpnonce' ];

		foreach ( $_POST as $k => $v ) {
			if ( ! in_array( $k, $not ) ) {
				update_option( $k, $v );
			}
		}
	}
}