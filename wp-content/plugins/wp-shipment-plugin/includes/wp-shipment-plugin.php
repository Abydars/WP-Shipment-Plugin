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
		add_action( 'wp_ajax_save_from_address', array( $wpsp_actions, 'action_add_address' ) );

		//User Extras
		$wpsp_user_meta = new WPSP_UserMeta();

		add_action( 'show_user_profile', array( $wpsp_user_meta, 'extra_user_profile_fields' ) );
		add_action( 'edit_user_profile', array( $wpsp_user_meta, 'extra_user_profile_fields' ) );
        add_action( 'edit_user_profile_update', array($wpsp_user_meta, 'save_extra_user_profile_fields'));
	}

	public function register_plugin_styles()
	{
		wp_enqueue_style( 'wpsp_styles', WPSP_PLUGIN_URL . '/includes/assets/css/custom.css' );
		wp_enqueue_style( 'wpsp_data-table-styles', WPSP_PLUGIN_URL . '/includes/assets/css/jquery.dataTables.min.css' );
		wp_enqueue_script( 'wpsp_data-table-script', WPSP_PLUGIN_URL . '/includes/assets/js/jquery.dataTables.min.js' );
		wp_enqueue_script( 'wpsp_scripts', WPSP_PLUGIN_URL . '/includes/assets/js/custom.js' );
	}

	function setup_shipment_menu()
	{
		add_menu_page( 'Shipments', 'Shipments', 'manage_options', 'shipments', array( $this, 'list_shipments' ) );
		add_submenu_page( 'shipments', 'Create Shipment', 'Create Shipment', 'manage_options', 'create-shipment', '' );
	}

	function list_shipments()
	{
		include( 'templates/list_shipment.php' );
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
		$carriers  = [];
		$carriers  = apply_filters( 'wpsp_shipment_carriers', $carriers );
		$customers = WPSP_Customer::get_customers();

		include( 'templates/create_label.php' );
		die;
	}

	function wpsp_activation()
	{
		$this->create_table( 'table_name_here', "
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name tinytext NOT NULL,
		text text NOT NULL,
		url varchar(55) DEFAULT '' NOT NULL,
		PRIMARY KEY  (id)
		" );

		/*
		 * TASK FOR ASAD
		 *
		 * Yahan 2 tables create kardo, jese upar method use kia hai wese use karke.
		 * Tables:
		 * 1. shipments
		 * 2. addresses
		 * 3. labels
		 *
		 * dono ki fields purane plugin se lelo
		 *
		 */

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
}