<?php

global $wpdb;
$table_name = 'wp_shipments';
$shipments = $wpdb->get_results("SELECT * FROM $table_name");
$table_address = 'wp_addresses';
?>
<div id="list-shipments">
    <div class="container">
        <div class="row">
            <h1>Shipments</h1>
        </div>
        <div class="row">
            <table id="listShipments">
                <thead>
                <tr>
                    <th>ID: Key</th>
                    <th>Ticket #</th>
                    <th>Customer</th>
                    <th>Creator</th>
                    <th>Carrier</th>
                    <th>From Address</th>
                    <th>To Address</th>
                    <th>Shipped To</th>
                    <th>Creation Date</th>
                    <th>Shipment Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($shipments as $shipment) {
                $customer_id = get_userdata($shipment->customer_id);
                $creator_id = get_userdata($shipment->creator_id);
                $from_address = $wpdb->get_row("SELECT * FROM $table_address WHERE (id = '". $shipment->fromAddress_id ."')");
                $to_address = $wpdb->get_row("SELECT * FROM $table_address WHERE (id = '". $shipment->toAddress_id ."')");
                ?>
                    <tr style="text-align: left">
                        <td></td>
                        <td><?= $shipment->ticket_id ?></td>
                        <td><?= $customer_id->data->display_name ?></td>
                        <td><?= $creator_id->data->display_name ?></td>
                        <td><?= $shipment->server ?></td>
                        <td><?= $from_address->address_name ?></td>
                        <td><?= $to_address->address_name ?></td>
                        <td></td>
                        <td><?= $shipment->creation_date ?></td>
                        <td><?= $shipment->shipDate ?></td>
                        <td><?= $shipment->status ?></td>
                        <td class="view_shipment">
                            <a href="admin.php?page=shipments&id=<?= $shipment->id ?>">
                                <i class="fa fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>