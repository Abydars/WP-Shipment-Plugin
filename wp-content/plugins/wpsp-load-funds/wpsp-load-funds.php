<?php
/*
Plugin Name: WPSP Load Funds
Plugin URI: http://www.hztech.biz
Description: Load customers account funds and record it
Version: 0.0.1
Author: Hztech
Author URI: http://www.hztech.biz
*/

define( 'WPSP_LOAD_FUNDS_VER', '0.0.1' );

if ( ! class_exists( 'WPSP_LoadFunds' ) ) {

	class WPSP_LoadFunds
	{

		private $notices;

		public function __construct()
		{
			$this->notices = array();

			add_action( 'admin_menu', array( $this, 'render_admin_menu' ), 1000 );
			add_action( 'admin_init', array( $this, 'render_admin_init' ) );
		}

		public function render_admin_menu()
		{
			add_menu_page(
				__( 'Account Funds', WPSP_LANG ),
				'Account Funds',
				'manage_options',
				'wpsp-load-funds',
				array( $this, 'render_load_funds_list' ),
				'',
				30
			);

			add_submenu_page(
				'wpsp-load-funds',
				'Load Funds',
				'Load Funds',
				'manage_options',
				'wpsp-load-funds-load',
				array( $this, 'render_load_funds_form' )
			);

			add_submenu_page(
				'wpsp-load-funds',
				'View History',
				'View History',
				'manage_options',
				'wpsp-load-funds-history',
				array( $this, 'render_view' )
			);
		}

		public function render_view()
		{
			global $wpdb;

			$table_name = $wpdb->prefix . 'funds_history';
			$results    = $wpdb->get_results( "SELECT * FROM {$table_name} ORDER BY id DESC" );
			?>
            <div id="wrap">
                <h1 class="wpsp-page-title">Account Funds History</h1>
                <table class="wpsp-datatable">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Check Number</th>
                        <th>Amount</th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ( $results as $result ) {
						$customer = get_user_by( 'id', $result->customer_id ); ?>
                        <tr>
                            <td><?php echo $result->id; ?></td>
                            <td><?php echo $customer->display_name; ?></td>
                            <td><?php echo $result->date; ?></td>
                            <td><?php echo $result->check_number; ?></td>
                            <td>$<?php echo $result->amount; ?></td>
                        </tr>
					<?php } ?>
                    </tbody>
                </table>
            </div>
			<?php
		}

		public function render_admin_init()
		{
			global $wpdb;

			if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'wpsp-load-funds' ) ) {
				$amount       = isset( $_POST['amount'] ) ? floatval( $_POST['amount'] ) : false;
				$date         = isset( $_POST['date'] ) ? $_POST['date'] : false;
				$check_number = isset( $_POST['check_number'] ) ? $_POST['check_number'] : false;
				$customer_id  = isset( $_POST['customer_id'] ) ? $_POST['customer_id'] : false;

				if ( ! $amount || ! $date || ! $check_number || ! $customer_id ) {
					add_action( 'load_funds_errors', function () {
						echo apply_filters( 'wpsp_error', 'Please fill the required fields' );
					} );
				} else {
					$customer = get_user_by( 'id', $customer_id );
					$loaded   = $this->addUserFunds( $customer_id, $amount );

					if ( $loaded ) {

						$table_name = $wpdb->prefix . 'funds_history';
						$wpdb->insert( $table_name, array(
							'customer_id'  => $customer_id,
							'amount'       => $amount,
							'date'         => $date,
							'check_number' => $check_number
						) );

						if ( class_exists( 'WPSP' ) ) {
							// TODO: Create shipment order in customer dashboard

							$newbalance = $this->getUserFunds( $customer_id );

							if ( class_exists( 'WPSP_PdfHelper' ) ) {
								$date_now = date( 'Y-m-d-H-i-s' );
								$path     = apply_filters( 'wpsp_file_dir', "{$customer_id}-{$date_now}.pdf" );
								$url      = apply_filters( 'wpsp_file_url', "{$customer_id}-{$date_now}.pdf" );

								$text    = "Hello {$customer->display_name},<br/>You Ship 4 Less account has reloaded with $" . $amount . ".<br/>Your balance is: $" . $newbalance . "<br/><br/>Thank you for your payment.";
								$subject = "Payment via Check #{$check_number}";

								WPSP_PdfHelper::generate( $text, $path, $subject, __( "Amount Reload", WPSP_LANG ) );

								if ( $url && class_exists( 'WPTM_FaxManager' ) ) {
									$fax_to = WPSP_Customer::get_fax_number( $customer_id );

									try {

										$twilio = new WPTM_FaxManager();
										$twilio->sendFax( '+' . $fax_to, $url );

										if ( file_exists( $path ) ) {
											unlink( $path );
										}

									} catch ( Exception $e ) {
										if ( file_exists( $path ) ) {
											unlink( $path );
										}
									}
								}
							}
						}

						add_action( 'load_funds_success', function () {
							echo apply_filters( 'wpsp_success', 'Funds loaded successfully' );
						} );
					} else {
						add_action( 'load_funds_errors', function () {
							echo apply_filters( 'wpsp_error', 'Failed to load funds' );
						} );
					}
				}
			}
		}

		private function addUserFunds( $user_id, $amount )
		{
			return WPSP_Customer::add_funds( $user_id, $amount );
		}

		private function getUserFunds( $user_id )
		{
			$funds = WPSP_Customer::get_account_funds( $user_id );

			return $funds;
		}

		public function render_load_funds_form()
		{
			$customers = get_users( array(
				                        "role" => "customer"
			                        ) );
			?>
            <div id="wpsp">
                <h1 class="wpsp-page-title">Load Account Funds</h1>
                <form method="POST" style="max-width: 800px;" enctype="multipart/form-data">
					<?php do_action( 'load_funds_errors' ); ?>
					<?php do_action( 'load_funds_success' ); ?>
                    <div class="wpsp-form-group">
                        <label>Customer</label>
                        <select name="customer_id" class="form-control" required>
							<?php foreach ( $customers as $customer ) { ?>
                                <option value="<?php echo $customer->ID; ?>"><?php echo $customer->display_name; ?></option>
							<?php } ?>
                        </select>
                    </div>
                    <div class="wpsp-form-group">
                        <label>Check Number</label>
                        <input type="text" name="check_number" class="form-control" required/>
                    </div>
                    <div class="wpsp-form-group">
                        <label>Amount</label>
                        <input type="number" name="amount" class="form-control" placeholder="$" step="any" required/>
                    </div>
                    <div class="wpsp-form-group">
                        <label>Date</label>
                        <input id="funds-datepicker" type="date" name="date" class="form-control datepicker" required/>
                    </div>
                    <div class="wpsp-form-group">
						<?php wp_nonce_field( 'wpsp-load-funds' ); ?>
                        <button type="submit">Load</button>
                    </div>
                </form>
            </div>
            <script>
                document.getElementById("funds-datepicker").value = "<?= date( "Y-m-d" ) ?>";
            </script>
			<?php
		}

		public function render_load_funds_list()
		{
			$customers = get_users( array(
				                        "role" => "customer"
			                        ) );
			?>
            <div id="wpsp">
                <h1 class="wpsp-page-title">Account Funds <a
                            href="<?= admin_url( 'admin.php?page=wpsp-load-funds-load' ) ?>">Load Funds</a></h1>
                <table class="wpsp-datatable">
                    <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Funds</th>
                    </tr>
                    </thead>
                    <tbody>
					<?php foreach ( $customers as $customer ) : ?>
                        <tr>
                            <td><?= $customer->display_name ?></td>
                            <td>$<?= number_format( WPSP_Customer::get_account_funds( $customer->ID ), 2 ) ?></td>
                        </tr>
					<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <script>
                document.getElementById("funds-datepicker").value = "<?= date( "Y-m-d" ) ?>";
            </script>
			<?php
		}

		public function add_notice( $type, $message )
		{
			$this->notices[] = array(
				"type"    => $type,
				"message" => $message
			);
		}

		public function activation()
		{
			$installed_ver = get_option( "load-account-funds" );

			if ( $installed_ver != WPSP_LOAD_FUNDS_VER ) {
				$this->createTable( 'funds_history', "id mediumint(9) NOT NULL AUTO_INCREMENT,
					customer_id int (9) NOT NULL,
					check_number varchar(100) DEFAULT '',
					amount float DEFAULT 0,
					`date` varchar(255) DEFAULT '',
					PRIMARY KEY  (id)" );
			}
		}

		private function createTable( $name, $columns )
		{
			global $wpdb;
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$table_name = $wpdb->prefix . $name;
			dbDelta( 'CREATE TABLE ' . $table_name . ' (' . $columns . ');' );
		}

	}

}

$loader = new WPSP_LoadFunds();

register_activation_hook( __FILE__, array( $loader, 'activation' ) );