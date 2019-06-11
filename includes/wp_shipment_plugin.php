<?php

class wpShipment
{
    public function __construct()
    {
        //Functions
        add_action('admin_enqueue_scripts', array($this, 'register_plugin_styles'));
        add_action('admin_menu', array($this, 'setup_shipment_menu'));
        add_action('admin_bar_menu', array($this, 'add_toolbar_items'), 100);
        add_action('admin_footer', array($this, 'create_label'));
        add_action('admin_head', array($this, 'header'));
        add_action( 'wp_ajax_wsp_render_shipment_form', array($this, 'wsp_render_shipment_form') );
        add_action('wp', array($this, 'create_shipment'));
    }

    public function register_plugin_styles()
    {
        wp_register_style('wsp_styles', plugins_url('wp_shipment_plugin/includes/assets/css/custom.css'));
        wp_enqueue_style('wsp_styles');
    }

    function setup_shipment_menu()
    {
        add_menu_page('Shipments', 'Shipments', 'manage_options', 'shipments', array($this, 'list_shipments'));
        add_submenu_page('shipments', 'Create Shipment', 'Create Shipment', 'manage_options', 'create-shipment', '');
    }

    function list_shipments()
    {
        ?>
        <h1>Shipments</h1>
        <?php
    }

    function add_toolbar_items($admin_bar)
    {
        $admin_bar->add_menu(array(
            'id' => 'create-label',
            'title' => 'Create Label',
            'href' => '#',
            'meta' => array(
                'title' => __('Create Label'),
            ),
        ));
    }

    function create_label()
    {
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                $('body').append('<div id="shipment-form"></div>');
                $.ajax({
                    url : wsp_ajax_url,
                    data : {
                        action : 'wsp_render_shipment_form'
                    },
                    success: function (response) {
                        $('#shipment-form').html(response);
                    }
                });
            })
        </script>
        <?php
    }

    function header()
    {
        ?>
        <script>
            var wsp_ajax_url = '<?= admin_url('admin-ajax.php') ?>';
        </script>
        <?php
    }

    function wsp_render_shipment_form(){
        include ('templates/create_label.php');
    }
}