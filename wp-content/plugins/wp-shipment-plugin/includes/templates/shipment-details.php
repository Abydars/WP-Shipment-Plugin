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
				<?php
				$can_void = true;
				$twelvepm = strtotime( $details->creation_date . ' 12:00:00' );
				$time_now = strtotime( date( 'Y-m-d H:i:s' ) );

				if ( strtolower( $details->status ) === 'pending' ) { ?>
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
                    <td>
						<?php foreach ( $labels as $k => $label ) { ?>
                            <a target="_blank" href="<?= $label ?>"><?= basename( $label ) ?></a>
						<?php } ?>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="wpsp-one-half" style="display: none;">
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
            <div class="wpsp-one-half1">
                <h1>Rates</h1>
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
			<?php if ( ! empty( $details->tracking ) ) :
				$trackings = json_decode( $details->tracking, true );
				if ( ! empty( $trackings ) ) :
					?>
                    <div>
                        <h1>Tracking Numbers</h1>
                        <table>
                            <tbody>
							<?php foreach ( $trackings as $k => $tracking ) : ?>
                                <tr>
                                    <td>
                                        <a target="_blank"
                                           href="<?= empty( $tracking['url'] ) ? '#' : $tracking['url'] ?>"><?= $tracking['id'] ?></a>
                                    </td>
                                </tr>
							<?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
				<?php endif; ?>
			<?php endif; ?>
        </div>
    </div>
</div>