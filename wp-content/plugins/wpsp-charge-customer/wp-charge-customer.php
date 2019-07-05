<?php
/*
Plugin Name: WPSP Charge Customer
Plugin URI: http://www.hztech.biz
Description: Charge customer when an accounts funds are less
Version: 0.0.1
Author: Hztech
Author URI: http://www.hztech.biz
*/

define( 'ShipmentForteOrgId', '351125' );
define( 'ShipmentForteLocId', '207639' );
define( 'ShipmentForteAccessId', 'db6a89acef6936ced77a61d0b7a0d488' );
define( 'ShipmentForteAccessIdProduction', '549e50363908d4dc9e2ffb7012861f3d' );
define( 'ShipmentForteSecureKey', '44e2be27712d277109fd2cff014cc1d9' );
define( 'ShipmentForteSecureKeyProduction', 'cc79046990514bea6584081621c4b593' );
define( 'ShipmentForteAuthorization', 'Basic ZGI2YTg5YWNlZjY5MzZjZWQ3N2E2MWQwYjdhMGQ0ODg6NDRlMmJlMjc3MTJkMjc3MTA5ZmQyY2ZmMDE0Y2MxZDk=' );
define( 'ShipmentForteAuthorizationProduction', 'Basic NTQ5ZTUwMzYzOTA4ZDRkYzllMmZmYjcwMTI4NjFmM2Q6Y2M3OTA0Njk5MDUxNGJlYTY1ODQwODE2MjFjNGI1OTM=' );
define( 'ShipmentForteSandbox', true );

require_once dirname( __FILE__ ) . '/inc/forte.php';
require_once dirname( __FILE__ ) . '/inc/customer.php';

if ( ! class_exists( 'WPSP_ChargeCustomer' ) ) {
	class WPSP_ChargeCustomer
	{
		private $forte;

		public function __construct()
		{
			$this->forte = new WPSP_Forte();

			add_action( 'wp_ajax_nopriv_charge_customer', array( $this, 'charge_customer' ) );
			add_action( 'wp_ajax_charge_customer', array( $this, 'charge_customer' ) );
			add_action( 'wp_ajax_wpcc_get_customer_logs', array( $this, 'wpcc_get_customer_logs' ) );

			add_action( 'admin_menu', array( $this, 'add_charge_customer_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
			add_action( 'admin_init', array( $this, 'actions' ) );
			add_action( 'admin_head', array( $this, 'ajax_form_request' ) );
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
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'wpcc-customers' && isset( $_GET['action'] ) ) {
				$action = $_GET['action'];

				switch ( $action ) {
					case 'delete':
						$id = $_GET['id'];

						update_user_meta( $id, 'forte_token', false );

						wp_redirect( admin_url( 'admin.php?page=wpcc-customers' ) );
						die;

						break;
				}
			}

			if ( isset( $_POST['_wpnonce'] ) ) {
				if ( wp_verify_nonce( $_POST['_wpnonce'], 'wpcc_add_customer' ) ) {
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
						'first_name'   => $addresses[0]['first_name'],
						'last_name'    => $addresses[0]['last_name'],
						'company_name' => $addresses[0]['company_name'],
						'addresses'    => $addresses,
						'paymethod'    => $payment_info
					];

					$res = $this->forte->createCustomer( $data );

					if ( ! empty( $res['customer_token'] ) ) {
						$customer_token = $res['customer_token'];
						$url            = admin_url( 'admin.php?page=wpcc-customers' );

						update_user_meta( $post_data['customer'], 'forte_token', $customer_token );
						update_user_meta( $post_data['customer'], 'forte_token_last_updated', date( 'Y-m-d H:i:s' ) );

						wp_redirect( $url );
						die;

					} else {
						$error = $res['response']['response_desc'];
						add_filter( 'wpcc_error', function ( $err ) use ( &$error ) {
							return $error;
						} );
					}
				} else if ( wp_verify_nonce( $_POST['_wpnonce'], 'wpcc_save_settings' ) ) {
					$keys = [
						'reload_amount',
						'processing_fee',
						'funds_limit'
					];

					foreach ( $keys as $key ) {
						update_option( $key, $_POST[ $key ] );
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
			add_menu_page( __( 'Forte', WPSP_LANG ), __( 'Forte Customers', WPSP_LANG ), 'manage_options', 'wpcc-customers', array(
				$this,
				'list_customers'
			) );
			add_submenu_page( 'wpcc-customers', __( 'Create Forte Customer', WPSP_LANG ), __( 'Create Customer', WPSP_LANG ), 'manage_options', 'create_forte_customer', array(
				$this,
				'create_customer'
			) );
			add_submenu_page( 'wpcc-customers', __( 'Charge Customer Settings', WPSP_LANG ), __( 'Settings', WPSP_LANG ), 'manage_options', 'forte_settings', array(
				$this,
				'settings_page'
			) );
		}

		public function settings_page()
		{
			$reload_amount  = $this->wpcc_get_option( 'reload_amount' );
			$processing_fee = $this->wpcc_get_option( 'processing_fee' );
			$funds_limit    = $this->wpcc_get_option( 'funds_limit' );

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
			$customers = $this->get_forte_customers();

			include( 'templates/list_customers.php' );
		}

		public function charge_customer()
		{
			global $wpdb;

			$customers           = $this->get_forte_customers();
			$card_processing_fee = $this->wpcc_get_option( 'processing_fee' ) / 100;
			$funds_limit         = $this->wpcc_get_option( 'funds_limit' );
			$reload_amount       = $this->wpcc_get_option( 'reload_amount' );

			foreach ( $customers as $customer ) {
				$token      = get_user_meta( $customer->ID, 'forte_token', true );
				$funds      = WPCC_Customer::get_account_funds( $customer->ID );
				$fax_number = WPSP_Customer::get_fax_number( $customer->ID );

				if ( $funds >= $funds_limit ) {
					echo "{$customer->display_name}: $ {$funds}<br/>";
					continue;
				}

				$amount = $reload_amount + ( $reload_amount * $card_processing_fee );
				$data   = [
					'action'               => 'sale',
					'authorization_amount' => $amount,
					'customer_token'       => $token
				];
				$res    = $this->forte->createTransaction( $data );

				if ( ! empty( $res['response']['response_type'] ) && $res['response']['response_type'] === 'A' ) {
					$log_text       = "$ {$reload_amount} added successfully";
					$transaction_id = $res['transaction_id'];

					WPCC_Customer::add_funds( $customer->ID, $reload_amount );
					WPCC_Customer::log( $customer->ID, $log_text );

					// TODO: Maintain Order History

					$filename     = apply_filters( 'wpsp_file_dir', "charge-customer-{$customer->ID}.pdf" );
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
							$wptm_manager->sendFax( '+' . $fax_number, $filename );

							if ( file_exists( $filename ) ) {
								unlink( $filename );
							}

						} catch ( Exception $e ) {
							if ( file_exists( $filename ) ) {
								unlink( $filename );
							}
						}
					}

				} else {
					$log_text = 'Failed to charge';

					if ( ! empty( $res['response']['response_desc'] ) ) {
						$log_text = $res['response']['response_desc'];
					}

					WPCC_Customer::log( $customer->ID, $log_text );
				}
			}
			die;
		}

		public function get_forte_customers()
		{
			return get_users( array(
				                  'meta_key'     => 'forte_token',
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

		function wpcc_get_option( $key )
		{
			$defaults = [
				'reload_amount'  => 100,
				'processing_fee' => 3,
				'funds_limit'    => - 150
			];
			$value    = get_option( $key );

			if ( empty( $value ) && ! empty( $defaults[ $key ] ) ) {
				$value = $defaults[ $key ];
			}

			return $value;
		}
	}
}

new WPSP_ChargeCustomer();