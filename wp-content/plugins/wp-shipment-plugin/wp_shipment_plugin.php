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
define( 'WPSP_FILES_DIR', WP_CONTENT_DIR . '/uploads/wpsp/' );
define( 'WPSP_FILES_URL', get_bloginfo( 'url' ) . '/wp-content/uploads/wpsp/' );

include( 'includes/wp-shipment-plugin.php' );
include( 'includes/shipment-action.php' );
include( 'includes/customer.php' );
include( 'includes/shipment.php' );
include( 'includes/address.php' );
include( 'includes/user-meta.php' );
include( 'includes/tcpdf/tcpdf.php' );
include( 'includes/pdf-helper.php' );

$wp_shipment = new WPSP();
register_activation_hook( __FILE__, array( $wp_shipment, 'wpsp_activation' ) );