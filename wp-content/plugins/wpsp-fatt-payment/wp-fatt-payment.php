<?php
/*
Plugin Name: WPSP Fatt Payment Merchant
Plugin URI: http://www.hztech.biz
Description: Charge customer when an accounts funds are less via Fatt Merchant
Version: 0.0.1
Author: Hztech
Author URI: http://www.hztech.biz
*/

define( 'ShipmentFattSandbox', true );

require_once dirname( __FILE__ ) . '/inc/fatt.php';
require_once dirname( __FILE__ ) . '/inc/customer.php';

if ( ! class_exists( 'WPSP_FattCustomer' ) ) {
	class WPSP_FattCustomer
	{
		private $fatt;

		public function __construct()
		{
			$this->fatt = new WPSP_Fatt();

			add_action( 'wp_ajax_nopriv_charge_customer', array( $this, 'charge_customer' ) );
			add_action( 'wp_ajax_charge_customer', array( $this, 'charge_customer' ) );
			add_action( 'wp_ajax_wpcc_get_customer_logs', array( $this, 'wpcc_get_customer_logs' ) );

			add_action( 'admin_menu', array( $this, 'add_charge_customer_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
			add_action( 'admin_init', array( $this, 'actions' ) );
			add_action( 'admin_head', array( $this, 'ajax_form_request' ) );
			add_action( 'wp_ajax_save_payment_method', array( $this, 'save_payment_method' ) );
			add_action( 'wp_ajax_nopriv_save_payment_method', array( $this, 'save_payment_method' ) );
			add_filter( 'funds_available', array( $this, 'funds_available' ), 10, 3 );

			add_action( 'show_user_profile', array( $this, 'extra_user_profile_fields' ) );
			add_action( 'edit_user_profile', array( $this, 'extra_user_profile_fields' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_extra_user_profile_fields' ) );
			add_action( 'personal_options_update', array( $this, 'save_extra_user_profile_fields' ) );
		}

		function extra_user_profile_fields( $user )
		{
			include 'templates/user-fields.php';
		}

		function save_extra_user_profile_fields( $user_id )
		{
			if ( $_POST['fatt_user_reload_amount'] ) {
				WPCC_Customer::set_reload_amount( $user_id, $_POST['fatt_user_reload_amount'] );
				WPCC_Customer::set_processing_fees($user_id, $_POST['fatt_user_reload_amount'] );
			}
		}

		function funds_available( $available, $funds, $rates )
		{
			$threshold = $this->wpcc_get_option( 'fatt_balance_threshold' );

			if ( ! empty( $threshold ) ) {
				if ( ( $funds - $rates ) > $threshold ) {
					$available = true;
				}
			}

			return $available;
		}

		function save_payment_method()
		{
			if ( isset( $_POST['customer_id'] ) ) {

				$fatt_customer_id       = $_POST['customer_id'];
				$fatt_payment_method_id = $_POST['payment_id'];

				$users = get_users( array(
					                    'meta_key'   => 'fatt_token',
					                    'meta_value' => $fatt_customer_id
				                    ) );

				update_user_meta( $users[0]->ID, 'fatt_payment_id', $fatt_payment_method_id );
				update_user_meta( $users[0]->ID, 'fatt_payment_id_token_last_updated', date( 'Y-m-d H:i:s' ) );

				echo json_encode( [
					                  'token'      => $fatt_customer_id,
					                  'payment_id' => $fatt_payment_method_id
				                  ] );
			}
			die;
		}

		public function wpcc_get_customer_logs()
		{
			$customer_id = $_REQUEST['id'];
			$logs        = WPCC_Customer::get_logs( $customer_id );

			header( 'Content-Type: application/json' );
			echo json_encode( $logs );
			die;
		}

		public function actions()
		{
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'wpcc-fatt-customers' && isset( $_GET['action'] ) ) {
				$action = $_GET['action'];

				switch ( $action ) {
					case 'delete':
						$id = $_GET['id'];

						update_user_meta( $id, 'fatt_token', false );

						wp_redirect( admin_url( 'admin.php?page=wpcc-fatt-customers' ) );
						die;

						break;
				}
			}

			if ( isset( $_POST['_wpnonce'] ) ) {
				if ( wp_verify_nonce( $_POST['_wpnonce'], 'wpcc_fatt_add_customer' ) ) {
					$post_data    = $_POST;
					$addresses    = $_POST['addresses'];
					$card         = $_POST['paymethod']['card'];
					$echeck       = $_POST['paymethod']['echeck'];
					$pay_method   = $_POST['payment_type'];
					$payment_info = [];

					if ( strtolower( $pay_method ) === 'card' ) {
						$payment_info['card'] = $card;
					} else {
						$payment_info['echeck'] = $echeck;
					}

					$data = [
						'firstname'       => $addresses[0]['first_name'],
						'lastname'        => $addresses[0]['last_name'],
						'company'         => $addresses[0]['company_name'],
						'address_1'       => $addresses[0]['physical_address']['street_line1'],
						'address_2'       => $addresses[0]['physical_address']['street_line2'],
						'address_city'    => $addresses[0]['physical_address']['locality'],
						'address_state'   => $addresses[0]['physical_address']['region'],
						'address_zip'     => $addresses[0]['physical_address']['postal_code'],
						'address_country' => $addresses[0]['physical_address']['country'],
						'reference'       => get_bloginfo( 'name' )
					];

					$res = $this->fatt->createCustomer( $data );

					if ( ! empty( $res['id'] ) ) {
						$customer_token = $res['id'];
						$url            = admin_url( 'admin.php?page=create_fatt_customer&action=payment-method&id=' . $post_data['customer'] );

						update_user_meta( $post_data['customer'], 'fatt_token', $customer_token );
						update_user_meta( $post_data['customer'], 'fatt_token_last_updated', date( 'Y-m-d H:i:s' ) );
						update_user_meta( $post_data['customer'], 'fatt_customer_details', serialize( $data ) );

						wp_redirect( $url );
						die;

					} else {
						$error = $res['error'];
						add_filter( 'wpcc_error', function ( $err ) use ( &$error ) {
							return $error;
						} );
					}
				} else if ( wp_verify_nonce( $_POST['_wpnonce'], 'wpcc_fatt_save_settings' ) ) {
					$not = [ '_wpnonce' ];

					foreach ( $_POST as $k => $v ) {
						if ( ! in_array( $k, $not ) ) {
							update_option( $k, $v );
						}
					}
				}
			}
		}

		public function admin_enqueue()
		{
			wp_enqueue_script( 'wpcc_scripts', plugins_url( '/inc/assets/js/wpcc.js', __FILE__ ), [ 'jquery' ], rand( 0, 100 ) );
			wp_enqueue_style( 'wpcc_styles', plugins_url( '/inc/assets/css/wpcc.css', __FILE__ ), [], rand( 0, 100 ) );
		}

		public function add_charge_customer_menu()
		{
			add_menu_page( __( 'Fatt', WPSP_LANG ), __( 'Fatt Customers', WPSP_LANG ), 'manage_options', 'wpcc-fatt-customers', array(
				$this,
				'list_customers'
			) );
			add_submenu_page( 'wpcc-fatt-customers', __( 'Create Fatt Customer', WPSP_LANG ), __( 'Create Customer', WPSP_LANG ), 'manage_options', 'create_fatt_customer', array(
				$this,
				'create_customer'
			) );
			add_submenu_page( 'wpcc-fatt-customers', __( 'Charge Customer Settings', WPSP_LANG ), __( 'Settings', WPSP_LANG ), 'manage_options', 'fatt_settings', array(
				$this,
				'settings_page'
			) );
		}

		public function settings_page()
		{
			include( 'templates/settings.php' );
		}

		public function create_customer()
		{
			$error     = apply_filters( 'wpcc_error', '' );
			$customers = get_users( array(
				                        'number' => - 1
			                        ) );
			$data      = [];

			if ( ! empty( $_POST ) ) {
				$data = $_POST;
			}

			include( 'templates/create_customer.php' );
		}

		public function list_customers()
		{
			$customers = $this->get_fatt_customers();

			include( 'templates/list_customers.php' );
		}

		public function charge_customer()
		{
			global $wpdb;

			$customers                   = $this->get_fatt_customers();
			$default_card_processing_fee = $this->wpcc_get_option( 'fatt_processing_fee' ) / 100;
			$funds_limit                 = $this->wpcc_get_option( 'fatt_funds_limit' );
			$default_reload_amount       = $this->wpcc_get_option( 'fatt_reload_amount' );

			foreach ( $customers as $customer ) {
				$token          = get_user_meta( $customer->ID, 'fatt_token', true );
				$payment_token  = get_user_meta( $customer->ID, 'fatt_payment_id', true );
				$funds          = WPCC_Customer::get_account_funds( $customer->ID );
				$fax_number     = WPSP_Customer::get_fax_number( $customer->ID );
				$reload_amount  = WPCC_Customer::get_reload_amount( $customer->ID );
				$card_processing_fee = WPCC_Customer::get_processing_fees($customer->ID) / 100;

				if ( empty( $reload_amount ) ) {
					$reload_amount = $default_reload_amount;
				}

                if ( empty( $processing_fee ) ) {
                    $card_processing_fee = $default_card_processing_fee;
                }

				if ( $funds >= $funds_limit ) {
					echo "{$customer->display_name}: $ {$funds}<br/>";
					continue;
				}

				$amount = $reload_amount + ( $reload_amount * $card_processing_fee );
				$data   = [
					'total'             => $amount,
					'customer_token'    => $token,
					'payment_method_id' => $payment_token,
					'meta'              => [
						'pre_auth' => 1,
						'tax'      => 0,
						'subtotal' => $amount
					]
				];
				$res    = $this->fatt->createTransaction( $data );

				if ( ! empty( $res['success'] ) && $res['success'] ) {
					$log_text       = "$ {$reload_amount} added successfully";
					$transaction_id = $res['transaction_id'];

					WPCC_Customer::add_funds( $customer->ID, $reload_amount );
					WPCC_Customer::log( $customer->ID, $log_text );

					// TODO: Maintain Order History
					$wpdb->insert( "{$wpdb->prefix}funds_history", [
						'customer_id' => $customer->ID,
						'amount'      => $amount,
						'date'        => date( 'Y-m-d H:i:s' ),
						'notes'       => "Auto funds loaded"
					] );

					$filename     = apply_filters( 'wpsp_file_dir', "charge-customer-{$customer->ID}.pdf" );
					$file_url     = apply_filters( 'wpsp_file_url', "charge-customer-{$customer->ID}.pdf" );
					$subject      = __( 'Funds Loaded', WPSP_LANG );
					$subtitle     = __( '', WPSP_LANG );
					$datetime_now = date( 'Y-m-d H:i:s' );

					$text = "";

					$text .= "Transaction ID: {$transaction_id}<br/>";
					$text .= "Date Time: {$datetime_now}<br/><br/>";
					$text .= "$" . number_format( $amount, 2 ) . " has been charged.<br/>";

					WPSP_PdfHelper::generate( $text, $filename, $subject, $subtitle );

					$email         = $customer->user_email;
					$headers       = array(
						'Content-Type: text/html; charset=UTF-8'
					);
					$attachments[] = $filename;
					$blogname      = get_bloginfo( 'name' );

					wp_mail( $email, "{$blogname} - Transaction #{$transaction_id}", __( "Transaction Summary: {$text}", WPSP_LANG ), $headers, $attachments );

					// send transaction summary via fax
					if ( class_exists( 'WPTM_FaxManager' ) && ! empty( $fax_number ) ) {
						try {

							$wptm_manager = new WPTM_FaxManager();
							$wptm_manager->sendFax( '+' . $fax_number, $file_url );

						} catch ( Exception $e ) {
							if ( file_exists( $filename ) ) {
								unlink( $filename );
							}
						}
					}

				} else {
					$log_text = 'Failed to charge';

					if ( ! empty( $res['message'] ) ) {
						$log_text = $res['message'];
					}

					WPCC_Customer::log( $customer->ID, $log_text );
				}
			}
			die;
		}

		public function get_fatt_customers()
		{
			return get_users( array(
				                  'meta_key'     => 'fatt_token',
				                  'meta_compare' => '!=',
				                  'meta_value'   => false,
				                  'number'       => - 1
			                  ) );
		}

		function ajax_form_request()
		{
			?>
            <script>
                var wpcc_ajax_url = '<?= admin_url( 'admin-ajax.php' ) ?>';
            </script>
			<?php
		}

		public static function wpcc_get_option( $key )
		{
			$defaults = [
				'fatt_reload_amount'     => 100,
				'fatt_processing_fee'    => 3,
				'fatt_funds_limit'       => - 150,
				'fatt_balance_threshold' => - 150
			];
			$value    = get_option( $key );

			if ( empty( $value ) && ! empty( $defaults[ $key ] ) ) {
				$value = $defaults[ $key ];
			}

			return $value;
		}
	}
}

if ( defined( 'WPSP_LANG' ) ) {
	new WPSP_FattCustomer();
}