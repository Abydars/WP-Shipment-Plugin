<?php
/*
Plugin Name: Load Account Funds
Plugin URI: http://www.hztech.biz
Description: Load customers account funds and record it
Version: 0.0.1
Author: Hztech
Author URI: http://www.hztech.biz
Text Domain: load-account-funds
*/

define('LOAD_ACC_FUNDS_VER', '0.0.1');

if( !class_exists('AccountFundsLoader') ) {
	
	class AccountFundsLoader {
		
		private $notices;
		
		public function __construct() {
			$this->notices = array();
			
			add_action('admin_menu', array($this, 'render_admin_menu'), 1000);
			add_action('admin_init', array($this, 'render_admin_init'));
			add_filter('load_funds_error_message', array($this, 'render_as_error_div'));
			add_filter('load_funds_success_message', array($this, 'render_as_success_div'));
		}
		
		public function render_as_error_div($text) {
			return '<div class="alert alert-danger">' . $text . '</div>';
		}
		
		public function render_as_success_div($text) {
			return '<div class="alert alert-success">' . $text . '</div>';
		}
		
		public function render_admin_menu() {
			add_menu_page( 
				__('Load Funds', 'load-account-funds'),
				'Load Funds',
				'manage_options',
				'load-account-funds',
				array($this, 'render_load_funds_form'),
				'',
				30
			);
			
			add_submenu_page(
				'load-account-funds',
				'View History',
				'View History',
				'manage_options',
				'view-account-fund-loads-history',
				array($this, 'render_view')
			);
		}
		
		public function render_view() {
			global $wpdb;
			
			$table_name = $wpdb->prefix . 'funds_history';
			$results = $wpdb->get_results("SELECT * FROM {$table_name} ORDER BY id DESC");
			?>
			<div id="wrap">
				<h2>Account Funds History</h2>
				<table class="table table-bordered table-striped table-hover">
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
						<?php foreach($results as $result) { $customer = get_user_by('id', $result->customer_id); ?>
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
		
		public function render_admin_init() {
			global $wpdb;
			
			if(isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], 'load-account-funds')) {
				$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : false;
				$date = isset($_POST['date']) ? $_POST['date'] : false;
				$check_number = isset($_POST['check_number']) ? $_POST['check_number'] : false;
				$customer_id = isset($_POST['customer_id']) ? $_POST['customer_id'] : false;
				
				if(! $amount || ! $date || ! $check_number || ! $customer_id) {
					add_action('load_funds_errors', function() {
						echo apply_filters('load_funds_error_message', 'Please fill the required fields');
					});
				} else {
					$customer = get_user_by('id', $customer_id);
					$loaded = $this->addUserFunds($customer_id, $amount);
					
					if($loaded) {
						
						$table_name = $wpdb->prefix . 'funds_history';
						$wpdb->insert($table_name, array(
							'customer_id' => $customer_id,
							'amount' => $amount,
							'date' => $date,
							'check_number' => $check_number
						));
						
						if(class_exists('Shipment')) {
							$order_id = Shipment::createShipmentOrder(array(
								"amount" => $amount,
								"order_date" => date("Y-m-d h:i:s"),
								"user_id" => $customer_id,
								"description" => "Reload Account Funds via Check #{$check_number}",
								"status" => true
							));
							
							$newbalance = $this->getUserFunds($customer_id);
							
							if(class_exists('ShipmentPdf')) {
								$pdf = ShipmentPdf::generate("Hello {$customer->display_name},<br/>You Ship 4 Less account has reloaded with $".$amount.".<br/>Your balance is: $".$newbalance."<br/><br/>Thank you for your payment.", "Payment via Check #{$check_number}", "Amount Reload");
								$url = $pdf["url"];
								
								if($url && class_exists('TwilioManager')) {
									$fax_to = get_field("fax_number", "user_" . $customer->ID);
									try {
										
										$twilio = new TwilioManager;
										$fax = $twilio->sendFax('+' . $fax_to, $url);
										
										ShipmentPdf::deletePdf(realpath($pdf["path"]));
										
									} catch(Exception $e) {							
										ShipmentPdf::deletePdf(realpath($pdf["path"]));
									}
								}
							}
						}
						
						add_action('load_funds_success', function() {
							echo apply_filters('load_funds_success_message', 'Funds loaded successfully');
						});
					} else {
						add_action('load_funds_errors', function() {
							echo apply_filters('load_funds_error_message', 'Failed to load funds');
						});
					}
				}
			}
		}
		
		private function addUserFunds($user_id, $amount) {
			$funds = $this->getUserFunds($user_id);
			$funds += $amount;
			
			return update_user_meta($user_id, 'account_funds', $funds);
		}
		
		private function getUserFunds($user_id) {
			$funds = 0;
			$account_funds = get_user_meta($user_id, 'account_funds', true);
			
			if($account_funds != false)
				$funds = floatval($account_funds);
			
			return $funds;
		}
		
		public function render_load_funds_form() {
			$customers = get_users(array(
				"role" => "customer"
			));
			?>
			<div id="wrap">
				<h2>Load Account Funds</h2>
				<form method="POST" style="max-width: 800px;" enctype="multipart/form-data">
					<?php do_action('load_funds_errors'); ?>
					<?php do_action('load_funds_success'); ?>
					<div class="form-group">
						<label>Customer</label>
						<select name="customer_id" class="form-control" required>
							<?php foreach($customers as $customer) { ?>
							<option value="<?php echo $customer->ID; ?>"><?php echo $customer->display_name; ?></option>
							<?php } ?>
						</select>
					</div>
					<div class="form-group">
						<label>Check Number</label>
						<input type="text" name="check_number" class="form-control" required />
					</div>
					<div class="form-group">
						<label>Amount</label>
						<input type="number" name="amount" class="form-control" placeholder="$" step="any" required />
					</div>
					<div class="form-group">
						<label>Date</label>
						<input id="funds-datepicker" type="date" name="date" class="form-control datepicker" required />
					</div>
					<div class="form-group">
						<?php wp_nonce_field('load-account-funds'); ?>
						<input type="submit" value="Load" class="btn btn-success" required />
					</div>
				</form>
			</div>
			<script>
				document.getElementById("funds-datepicker").value = "<?= date("Y-m-d") ?>";
			</script>
			<?php
		}
		
		public function add_notice($type, $message) {
			$this->notices[] = array(
				"type" => $type,
				"message" => $message
			);
		}
		
		public function activation() {
			$installed_ver = get_option( "load-account-funds" );
			
			if ( $installed_ver != LOAD_ACC_FUNDS_VER ) {
				$this->createTable('funds_history', "id mediumint(9) NOT NULL AUTO_INCREMENT,
					customer_id int (9) NOT NULL,
					check_number varchar(100) DEFAULT '',
					amount float DEFAULT 0,
					`date` varchar(255) DEFAULT '',
					PRIMARY KEY  (id)");
			}
		}
		
		private function createTable($name, $columns) {
			global $wpdb;
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			
			$table_name = $wpdb->prefix . $name;
			dbDelta( 'CREATE TABLE ' . $table_name . ' (' . $columns . ');' );
		}
		
	}
	
}

$loader = new AccountFundsLoader();

register_activation_hook( __FILE__, array($loader, 'activation') );