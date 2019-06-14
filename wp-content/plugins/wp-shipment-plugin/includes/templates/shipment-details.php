<div id="shipmentDetails">
    <div class="container">
        <div class="row">
            <h1>Shipment #<?= $shipment_id ?></h1>
            <p>Key: <?= $details->shipKey ?></p>
			<?php if ( $details->pickup_date ) { ?>
                <p>Pickup Scheduled at <?= date( 'Y-m-d H:i:s', strtotime( $details->pickup_date ) ) ?></p>
			<?php } ?>

			<?= apply_filters( 'wpsp_error', '' ) ?>
			<?= apply_filters( 'wpsp_success', '' ) ?>

            <div class="shipment_detail_actions">
				<?php if ( strtolower( $details->status ) === 'pending' ) { ?>
                    <button data-shipment-id="<?= $shipment_id ?>" data-refund="0" class="void-label">Void Label
                    </button>
                    <button data-shipment-id="<?= $shipment_id ?>" data-refund="1" class="void-label">Void Label and
                        Refund
                    </button>
				<?php } ?>
            </div>
            <table>
                <tbody>
                <tr>
                    <th>Status</th>
                    <td><?= $details->status ?></td>
                </tr>
                <tr style="display: none;">
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
                        From: <?= $from_address['address_name'] ?>
                        <br/>
                        <br/>
                        To: <?= $to_address['address_name'] ?>
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
                        <th>Label Rate</th>
                        <td><?= ( ! empty( $details->labelRate ) ? '$' . number_format( $details->labelRate, 2 ) : 'Not Applied' ) ?></td>
                    </tr>
                    <tr>
                        <th>Markup Rate</th>
                        <td><?= ( ! empty( $details->markupRate ) ? '$' . number_format( $details->markupRate, 2 ) : 'Not Applied' ) ?></td>
                    </tr>
                    <tr>
                        <th>Total Rates</th>
                        <td>$<?= number_format( $details->rates, 2 ) ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>