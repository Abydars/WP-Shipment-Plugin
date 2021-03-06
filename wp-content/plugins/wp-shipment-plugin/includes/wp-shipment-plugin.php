<?php

class WPSP
{
	public function __construct()
	{
		//Functions
		add_action( 'admin_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'admin_menu', array( $this, 'setup_shipment_menu' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_toolbar_items' ), 100 );
		add_action( 'admin_footer', array( $this, 'create_label' ) );
		add_action( 'admin_head', array( $this, 'ajax_form_request' ) );
		add_action( 'wp_ajax_wsp_render_shipment_form', array( $this, 'wsp_render_shipment_form' ) );

		//Actions
		$wpsp_actions = new WPSP_ShipmentActions();

		add_action( 'wp_ajax_save_label', array( $wpsp_actions, 'action_save_label' ) );
		add_action( 'wp_ajax_nopriv_save_label', array( $wpsp_actions, 'action_save_label' ) );
		add_action( 'wp_ajax_add_address', array( $wpsp_actions, 'action_add_address' ), 20 );
		add_action( 'wp_ajax_wpsp_shipment_carrier_levels', array( $wpsp_actions, 'action_carrier_levels' ) );
		add_action( 'wp_ajax_wpsp_shipment_package_types', array( $wpsp_actions, 'action_package_types' ) );
		add_action( 'wp_ajax_wpsp_get_rates', array( $wpsp_actions, 'action_get_rates' ) );
		add_action( 'wp_ajax_wpsp_get_addresses', array( $wpsp_actions, 'action_get_addresses' ) );
		add_action( 'wp_ajax_wpsp_void_label', array( $wpsp_actions, 'action_void_label' ) );
		add_action( 'wp_ajax_wpsp_edit_address', array( $wpsp_actions, 'action_edit_address' ) );
		add_action( 'wp_ajax_wpsp_delete_address', array( $wpsp_actions, 'action_delete_address' ) );
		add_action( 'wp_ajax_wpsp_get_states', array( $wpsp_actions, 'action_get_states' ) );
		add_action( 'admin_init', array( $wpsp_actions, 'action_create_new_address' ) );
		add_action( 'admin_init', array( $wpsp_actions, 'action_save_settings' ) );
		add_filter( 'wpsp_error', array( $wpsp_actions, 'filter_wpsp_error' ) );
		add_filter( 'wpsp_success', array( $wpsp_actions, 'filter_wpsp_success' ) );
		add_filter( 'wpsp_file_dir', array( $wpsp_actions, 'filter_wpsp_file_dir' ) );
		add_filter( 'wpsp_file_url', array( $wpsp_actions, 'filter_wpsp_file_url' ) );

		//User Extras
		$wpsp_user_meta = new WPSP_UserMeta();

		add_action( 'show_user_profile', array( $wpsp_user_meta, 'extra_user_profile_fields' ) );
		add_action( 'edit_user_profile', array( $wpsp_user_meta, 'extra_user_profile_fields' ) );
		add_action( 'edit_user_profile_update', array( $wpsp_user_meta, 'save_extra_user_profile_fields' ) );
		add_action( 'personal_options_update', array( $wpsp_user_meta, 'save_extra_user_profile_fields' ) );
	}

	public function register_plugin_styles()
	{
		wp_enqueue_style( 'wpsp_styles', WPSP_PLUGINS_URL . '/includes/assets/css/custom.css' );
		wp_enqueue_style( 'wpsp_font_awesome', WPSP_PLUGINS_URL . '/includes/assets/fontawesome/css/all.css' );
		wp_enqueue_style( 'wpsp_data-table-styles', 'https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css' );
		wp_enqueue_style( 'wpsp_data-table-button-styles', 'https://cdn.datatables.net/buttons/1.4.0/css/buttons.dataTables.min.css' );
		wp_enqueue_style( 'wpsp_chosen', WPSP_PLUGINS_URL . '/includes/assets/chosen/chosen.min.css' );
		wp_enqueue_script( 'wpsp_data-table-script', 'https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js', [ 'jquery' ], rand( 0, 100 ) );
		wp_enqueue_script( 'wpsp_data-table-button-script', 'https://cdn.datatables.net/buttons/1.4.0/js/dataTables.buttons.min.js', [ 'jquery' ], rand( 0, 100 ) );
		wp_enqueue_script( 'wpsp_data-table-html5-script', 'https://cdn.datatables.net/buttons/1.4.0/js/buttons.html5.min.js', [ 'jquery' ], rand( 0, 100 ) );
		wp_enqueue_script( 'wpsp_data-table-jszip-script', 'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js', [ 'jquery' ], rand( 0, 100 ) );
		wp_enqueue_script( 'wpsp_data-table-button-flash-script', 'https://cdn.datatables.net/buttons/1.4.0/js/buttons.flash.min.js', [ 'jquery' ], rand( 0, 100 ) );
		wp_enqueue_script( 'wpsp_data-table-button-print-script', 'https://cdn.datatables.net/buttons/1.4.0/js/buttons.print.min.js', [ 'jquery' ], rand( 0, 100 ) );
		wp_enqueue_script( 'wpsp_data-table-pdf-maker-script', 'https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js', [ 'jquery' ], rand( 0, 100 ) );
		wp_enqueue_script( 'wpsp_data-table-pdf-maker-fonts-script', 'https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js', [ 'jquery' ], rand( 0, 100 ) );
		wp_enqueue_script( 'wpsp_scripts', WPSP_PLUGINS_URL . '/includes/assets/js/custom.js', [ 'jquery' ], rand( 0, 100 ) );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'wpsp_chosen', WPSP_PLUGINS_URL . '/includes/assets/chosen/chosen.jquery.min.js', [ 'jquery' ], rand( 0, 100 ) );
	}

	function setup_shipment_menu()
	{
		add_menu_page( __( 'Shipments', WPSP_LANG ), __( 'Shipments', WPSP_LANG ), 'edit_shipments', 'wpsp-shipments', array(
			$this,
			'list_shipments'
		) );
		add_submenu_page( 'wpsp-shipments', __( 'Create Label', WPSP_LANG ), __( 'Create Label', WPSP_LANG ), 'edit_shipments', '#wpsp_create_label' );
		add_submenu_page( 'wpsp-shipments', __( 'Addresses', WPSP_LANG ), __( 'Addresses', WPSP_LANG ), 'edit_shipments', 'list_addresses', array(
			$this,
			'list_addresses'
		) );
		add_submenu_page( 'wpsp-shipments', __( 'Create Address', WPSP_LANG ), __( 'Create Address', WPSP_LANG ), 'edit_shipments', 'create_address', array(
			$this,
			'create_address'
		) );
		add_submenu_page( 'wpsp-shipments', __( 'Settings', WPSP_LANG ), __( 'Settings', WPSP_LANG ), 'manage_options', 'wpsp_settings', array(
			$this,
			'settings'
		) );
	}

	function settings()
	{
		$carriers = apply_filters( 'wpsp_shipment_carriers', [] );

		include( 'templates/settings.php' );
	}

	function list_shipments()
	{
		if ( isset( $_GET['id'] ) ) {
			$shipment_id  = $_GET['id'];
			$details      = WPSP_Shipment::get_shipment( $shipment_id );
			$customer_id  = get_userdata( $details->customer_id );
			$creator_id   = get_userdata( $details->creator_id );
			$from_address = WPSP_Address::get_address( $details->fromAddress_id );
			$to_address   = WPSP_Address::get_address( $details->toAddress_id );
			$labels       = array_map( function ( $label ) {
				return apply_filters( 'wpsp_file_url', $label );
			}, WPSP_Shipment::get_labels( $shipment_id ) );

			include( 'templates/shipment-details.php' );
		} else {
			$where = [];

			if ( ! empty( $_POST['ticket_id'] ) ) {
				$where[] = "ticket_id = '{$_POST['ticket_id']}'";
			}

			if ( ! empty( $_POST['date_from'] ) && ! empty( $_POST['date_to'] ) ) {
				$date_from = date( 'Y-m-d', strtotime( $_POST['date_from'] ) );
				$date_to   = date( 'Y-m-d', strtotime( $_POST['date_to'] ) );
				$where[]   = "DATE(creation_date) between '{$date_from}' AND '{$date_to}'";
			} else if ( ! empty( $_POST['date_from'] ) ) {
				$date_from = date( 'Y-m-d', strtotime( $_POST['date_from'] ) );
				$where[]   = "DATE(creation_date) = '{$date_from}'";
			} else if ( ! empty( $_POST['date_to'] ) ) {
				$date_to = date( 'Y-m-d', strtotime( $_POST['date_to'] ) );
				$where[] = "DATE(creation_date) = '{$date_to}'";
			}

			if ( empty( $where ) ) {
				$shipments = WPSP_Shipment::get_shipments( 'desc' );
			} else {
				$shipments = WPSP_Shipment::get_shipments_where( $where );
			}

			include( 'templates/list_shipment.php' );
		}
	}

	function list_addresses()
	{
		$addresses = WPSP_Address::get_addresses();
		$countries = apply_filters( 'wpsp_countries', WPSP_Helper::get_countries() );

		include( 'templates/list_addresses.php' );
	}

	function create_address()
	{
		$customers = WPSP_Customer::get_customers();
		$countries = apply_filters( 'wpsp_countries', WPSP_Helper::get_countries() );

		include( 'templates/create_address.php' );
	}

	function add_toolbar_items( $admin_bar )
	{
		$admin_bar->add_menu( array(
			                      'id'    => 'create-label',
			                      'title' => 'Create Label',
			                      'href'  => '#',
			                      'meta'  => array(
				                      'title' => __( 'Create Label' ),
			                      ),
		                      ) );
	}

	function create_label()
	{
		?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('body').append('<div id="shipment-form"></div>');

                $.ajax({
                    url: wsp_ajax_url,
                    data: {
                        action: 'wsp_render_shipment_form'
                    },
                    success: function (response) {
                        $('#shipment-form').html(response);
                        $('body').trigger('label.form.loaded');
                    }
                });
            })
        </script>
		<?php
	}

	function ajax_form_request()
	{
		?>
        <script>
            var wsp_ajax_url = '<?= admin_url( 'admin-ajax.php' ) ?>';
        </script>
		<?php
	}

	function wsp_render_shipment_form()
	{
		$carriers     = apply_filters( 'wpsp_shipment_carriers', [] );
		$carriers[''] = 'All';
		$customers    = WPSP_Customer::get_customers();
		$countries    = WPSP_Helper::get_countries();

		include( 'templates/create_label.php' );
		die;
	}

	function wpsp_activation()
	{
		$this->create_table( 'addresses', "id mediumint(9) NOT NULL AUTO_INCREMENT,
					customer_id int (9) NOT NULL,
					address_id varchar(100) DEFAULT '' NULL,
					address_name varchar(100) DEFAULT '' NOT NULL,
					address_code varchar(100) DEFAULT NULL,
					data longtext DEFAULT '' NOT NULL,
					is_default tinyint DEFAULT 0,
					type varchar(20) DEFAULT 'to',
					is_verified tinyint DEFAULT 1,
					is_residential tinyint DEFAULT 0,
					PRIMARY KEY  (id)" );
		$this->create_table( 'shipments', "id mediumint(9) NOT NULL AUTO_INCREMENT,
					customer_id int (9) NOT NULL,
					creator_id int (9) NOT NULL,
					order_id int (9) DEFAULT 0,
					ticket_id varchar(255) DEFAULT '',
					creation_date varchar(25) NOT NULL,
					shipKey varchar(255) DEFAULT '' NOT NULL,
					status varchar(100) DEFAULT '' NOT NULL,
					toAddress_id int (9) NOT NULL,
					fromAddress_id int (9) NOT NULL,
					server varchar(100) DEFAULT '' NOT NULL,
					serverLevel varchar(100) DEFAULT '' NOT NULL,
					packageType varchar(100) DEFAULT '' NOT NULL,
					dropOffType varchar(100) DEFAULT '' NOT NULL,
					confirmation varchar(100) DEFAULT '' NOT NULL,
					reference varchar(100) DEFAULT '',
					shipmentNo varchar(100) DEFAULT '',
					shipDate varchar(100) DEFAULT '' NOT NULL,
					pickup_date varchar(100),
					rates longtext DEFAULT '',
					markupRate float(25) DEFAULT 0,
					labelRate float(25) DEFAULT 0,
					packages longtext DEFAULT NULL,
					tracking longtext DEFAULT NULL,
					PRIMARY KEY  (id)" );
		$this->create_table( 'packages', "id mediumint(9) NOT NULL AUTO_INCREMENT,
					shipment_id mediumint(9) NOT NULL,
					weight varchar(25),
					weightUnit varchar(10),
					length varchar(25),
					width varchar(25),
					height varchar(25),
					sizeUnit varchar(25),
					insurancedeclvalue varchar(25),
					trackingNumber varchar(255),
					toAddress_id int (9) NOT NULL,
					fromAddress_id int (9) NOT NULL,
					PRIMARY KEY  (id)" );
		$this->create_table( 'labels', "id mediumint(9) NOT NULL AUTO_INCREMENT,
					shipment_id mediumint(9) NOT NULL,
					labels longtext DEFAULT '',
					label_type varchar(25) DEFAULT 'PDF',
					PRIMARY KEY  (id)" );
		$this->create_table( 'shipment_orders', "id mediumint(9) NOT NULL AUTO_INCREMENT,
					shipment_id mediumint(9) DEFAULT 0,
					amount float(25) DEFAULT 0,
					order_date varchar(25) DEFAULT '',
					user_id mediumint(9),
					meta_data longtext DEFAULT '',
					description varchar(255) DEFAULT '',
					status tinyint(1) DEFAULT 1,
					PRIMARY KEY  (id)" );
		$this->create_table( 'labels_trackings', "id mediumint(9) NOT NULL AUTO_INCREMENT,
					shipment_id mediumint(9) DEFAULT 0,
					faxid varchar(255) DEFAULT '',
					status tinyint(1) DEFAULT 0,
					PRIMARY KEY  (id)" );

		$filepath = apply_filters( 'wpsp_file_dir', '/' );

		if ( ! file_exists( $filepath ) ) {
			mkdir( $filepath, 775 );
		}

		add_option( 'wpsp_db_version', WPSP_DB_VERSION );
	}

	private function create_table( $table_name, $structure )
	{
		global $wpdb;

		$table_name      = $wpdb->prefix . $table_name;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name ({$structure}) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
	}

	public static function get_option( $key, $default = false )
	{
		$defaults = [];
		$value    = get_option( $key );

		if ( empty( $value ) && ! empty( $defaults[ $key ] ) ) {
			$value = $defaults[ $key ];
		}

		return empty( $value ) ? $default : $value;
	}

	public static function is_test()
	{
		return self::get_option( 'wpsp_test_mode' ) === 'yes';
	}

	public static function get_settings( $carrier )
	{
		$settings = apply_filters( "wpsp_{$carrier}_settings", [] );

		return $settings;
	}
}