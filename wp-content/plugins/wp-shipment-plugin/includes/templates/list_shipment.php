<div id="wpsp">
    <div id="list-shipments" class="wpsp-container">
        <div class="container">
            <div class="row">
                <h1 class="wpsp-page-title">Shipments <a href="#" data-wpsp-create-label>Create Label</a></h1>
            </div>
            <div class="filters">
                <h3>Filters</h3>
                <form method="post">
                    <div class="wpsp-row">
                        <div class="wpsp-form-group">
                            <label>Ticket ID</label>
                            <input type="text" placeholder="Search by Ticket ID" name="ticket_id"
                                   value="<?= isset( $_POST['ticket_id'] ) ? $_POST['ticket_id'] : "" ?>"/>
                        </div>
                    </div>
                    <div class="wpsp-row">
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Date From</label>
                                <input type="date" name="date_from"
                                       value="<?= isset( $_POST['date_from'] ) ? $_POST['date_from'] : "" ?>"/>
                            </div>
                        </div>
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Date To</label>
                                <input type="date" name="date_to"
                                       value="<?= isset( $_POST['date_to'] ) ? $_POST['date_to'] : "" ?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="wpsp-row">
                        <div class="action" style="text-align: right;">
                            <button type="submit">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="row">
                <table id="listShipments">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ticket ID</th>
                        <th>Tracking Number</th>
                        <th>Customer</th>
                        <th>Carrier</th>
                        <th>From Address</th>
                        <th>To Address</th>
                        <th>Shipped To</th>
                        <th>Creation Date</th>
                        <th>Shipment Date</th>
                        <th>Cost</th>
                        <th>Status</th>
                        <th class="js-not-exportable">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
					<?php
					foreach ( $shipments as $shipment ) {
						$customer     = get_userdata( $shipment->customer_id );
						$creator      = get_userdata( $shipment->creator_id );
						$from_address = WPSP_Address::get_address( $shipment->fromAddress_id );
						$to_address   = WPSP_Address::get_address( $shipment->toAddress_id );
						$trackings    = $shipment->tracking;

						if ( ! empty( $trackings ) ) {
							$trackings = json_decode( $trackings, true );
						} else {
							$trackings = [];
						}
						?>
                        <tr style="text-align: left">
                            <td><?= $shipment->id ?></td>
                            <td><?= $shipment->ticket_id ?></td>
                            <td><?= implode( ', ', array_map( function ( $tracking ) {
									return '<a href="' . $tracking['url'] . '">' . $tracking['id'] . '</a>';
								}, $trackings ) ) ?></td>
                            <td><?= $customer->data->display_name ?></td>
                            <td><?= $shipment->server ?></td>
                            <td><?= $from_address['address_name'] ?></td>
                            <td><?= $to_address['address_name'] ?></td>
                            <td></td>
                            <td><?= $shipment->creation_date ?></td>
                            <td><?= $shipment->shipDate ?></td>
                            <td>$<?= number_format($shipment->rates, 2) ?></td>
                            <td><?= $shipment->status ?></td>
                            <td class="view_shipment">
                                <a href="admin.php?page=wpsp-shipments&id=<?= $shipment->id ?>">
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
</div>