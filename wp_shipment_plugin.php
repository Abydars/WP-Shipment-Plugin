<?php
/*
Plugin Name: WP Shipment Plugin
Plugin URI:
description:
Version: 1.0
Author:
Author URI:
License:
*/

include ('includes/wp_shipment_plugin.php');
include ('includes/wp_shipment_actions.php');
include ('includes/shipment.php');
include('includes/address.php');

$wp_shipment = new wpShipment();
