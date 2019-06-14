<?php

global $wpdb;
$shipment_id = $_GET['id'];
$table_name = 'wp_shipments';
$details = $wpdb->get_row("SELECT * FROM $table_name WHERE (id = '". $shipment_id ."')");

$table_address = 'wp_addresses';
$customer_id = get_userdata($details->customer_id);
$creator_id = get_userdata($details->creator_id);
$from_address = $wpdb->get_row("SELECT * FROM $table_address WHERE (id = '". $details->fromAddress_id ."')");
$to_address = $wpdb->get_row("SELECT * FROM $table_address WHERE (id = '". $details->toAddress_id ."')");

$rate = $details->rates;
$markupRates = $details->markupRates;
$labelRates = $details->labelRate;
$total = $details->markupRates + $details->labelRate;
?>
<div id="shipmentDetails">
    <div class="container">
        <div class="row">
            <h1>Shipment #<?= $shipment_id ?></h1>
            <br/>
            <br/>
            <p>Key </p>
            <br/>
            <p>Pickup Schedule at</p>
            <br/>
            <div class="shipment_detail_actions">
                <button data-shipment-id="<?= $shipment_id ?>" class="void-label">Void Label</button>
                <button class="void-label-refund">Void Label and Refund</button>
            </div>
            <table>
                <tbody>
                <tr>
                    <th>Status</th>
                    <td><?= $details->status ?></td>
                </tr>
                <tr>
                    <th>Ticket ID</th>
                    <td><?= $details->ticket_id ?></td>
                </tr>
                <tr>
                    <th>Creation Date</th>
                    <td><?= $details->creation_date ?></td>
                </tr>
                <tr>
                    <th>Creator</th>
                    <td><?= $creator_id->data->display_name ?></td>
                </tr>
                <tr>
                    <th>Customer</th>
                    <td><?= $customer_id->data->display_name ?></td>
                </tr>
                <tr>
                    <th>Shipment Date</th>
                    <td><?= $details->shipDate ?></td>
                </tr>
                <tr>
                    <th>Address</th>
                    <td>
                        From: <?= $from_address->address_name ?>
                        <br/>
                        <br/>
                        To: <?= $to_address->address_name ?>
                    </td>
                </tr>
                <tr>
                    <th>Server</th>
                    <td style="text-transform: uppercase"><?= $details->server ?></td>
                </tr>
                <tr>
                    <th>Server Level</th>
                    <td><?= $details->serverLevel ?></td>
                </tr>
                <tr>
                    <th>Package Type</th>
                    <td><?= $details->packageType ?></td>
                </tr>
                <tr>
                    <th>Packages</th>
                    <td></td>
                </tr>
                <tr>
                    <th>Labels</th>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="wpsp-one-half">
                <h1>Tracking</h1>
                <table>
                    <tbody>
                    <tr>
                        <th>Packages #</th>
                        <th>Tracking No.</th>
                        <th>Status</th>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="wpsp-one-half">
                <h1>Rate</h1>
                <table>
                    <tbody>
                    <tr>
                        <th>Rate</th>
                        <td><?= (!empty($rate) ? '$'.number_format($rate, 2): 'Not Applied') ?></td>
                    </tr>
                    <tr>
                        <th>Markup Rate</th>
                        <td><?= (!empty($markupRates) ? '$'.number_format($markupRates, 2) : 'Not Applied') ?></td>
                    </tr>
                    <tr>
                        <th>Label Rate</th>
                        <td><?= (!empty($labelRates) ? '$'.number_format($labelRates, 2) : 'Not Applied') ?></td>
                    </tr>
                    <tr>
                        <th>Total Rates</th>
                        <td>$<?= number_format($total, 2) ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>