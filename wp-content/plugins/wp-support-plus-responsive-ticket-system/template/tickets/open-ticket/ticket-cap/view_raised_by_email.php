<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$flag = true;

$flag = apply_filters( 'wpsp_ticket_cap_view_raised_by_email', $flag, $ticket );
