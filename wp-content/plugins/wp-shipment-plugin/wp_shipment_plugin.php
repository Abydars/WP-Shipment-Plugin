<?php
/*
Plugin Name: WPSP Shipment Plugin
Plugin URI:
description:
Version: 1.0
Author:
Author URI:
License:
*/

define( 'WPSP_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'WPSP_LANG', 'WPSP-SHIPMENT-PLUGIN' );
define( 'WPSP_DB_VERSION', '0.0.1' );

include( 'includes/wp-shipment-plugin.php' );
include( 'includes/shipment-action.php' );
include( 'includes/customer.php' );
include( 'includes/shipment.php' );
include( 'includes/address.php' );
include( 'includes/user-meta.php' );

$wp_shipment = new WPSP();