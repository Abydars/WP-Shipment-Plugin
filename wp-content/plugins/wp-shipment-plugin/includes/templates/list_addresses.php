<?php

global $wpdb;
$table_name = 'wp_addresses';
$addresses  = $wpdb->get_results( "SELECT * FROM $table_name" );
//var_dump($addresses);
?>
<div id="list-addresses">
    <div class="container">
        <div class="row">
            <h1>Addresses</h1>
        </div>
        <div class="row">
            <table id="listShipments">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer Name</th>
                    <th>Full Name</th>
                    <th>Company</th>
                    <th>Country</th>
                    <th>City</th>
                    <th>Street 1</th>
                    <th>Street 2</th>
                    <th>State</th>
                    <th>Zip Code</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
				<?php
				foreach ( $addresses as $address ) {
					$address_data = json_decode( $address->data );
					$address_data = (array) $address_data;
					$user         = get_user_meta( $address->customer_id );
					$user         = (object) $user;
					?>
                    <tr style="text-align: left">
                        <td><?= $address->id ?></td>
                        <td><?= $user->first_name[0] . " " . $user->last_name[0] ?></td>
                        <td><?= $address_data['full_name'] ?></td>
                        <td><?= $address_data['company'] ?></td>
                        <td><?= $address_data['country'] ?></td>
                        <td><?= $address_data['city'] ?></td>
                        <td><?= $address_data['street_1'] ?></td>
                        <td><?= $address_data['street_2'] ?></td>
                        <td><?= $address_data['state'] ?></td>
                        <td><?= $address_data['zip_code'] ?></td>
                        <td><?= $address_data['phone'] ?></td>
                        <td><?= $address_data['email'] ?></td>
                        <td class="view_shipment">
                            <a id="btn-edit-from-address" href="#" class="from-address" data-type="from"
                               data-id="<?= $address->id ?>"><i class="fa fa-eye"></i></a>
                            <a>
                                <i class="fa fa-trash"></i>
                            </a>
                        </td>
                    </tr>
				<?php } ?>
                </tbody>
            </table>
        </div>
        <div id="editAddressModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Edit Address</h2>

                <form method="POST" action="">
                    <div class="wpsp-row" style="display: none;">
                        <div class="wpsp-form-group">
                            <label>Paste US Address</label>
                            <br/>
                            <span>Format: 1005 N Gravenstein Highway Sebastopol, CA 95472</span>
                            <textarea name="address" placeholder="1005 N Gravenstein Highway Sebastopol, CA 95472"
                                      style="width: 100%;"></textarea>
                        </div>
                    </div>
                    <div class="wpsp-row">
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Full Name</label>
                                <input type="text" name="full_name" id="" required/>
                            </div>
                        </div>
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Company</label>
                                <input type="text" name="company" id="" required/>
                            </div>
                        </div>
                    </div>
                    <div class="wpsp-row">
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Country</label>
                                <input type="text" name="country" id="" required/>
                            </div>
                        </div>
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>City</label>
                                <input type="text" name="city" id="" required/>
                            </div>
                        </div>
                    </div>
                    <div class="wpsp-row">
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Street 1</label>
                                <input type="text" name="street_1" id="" required/>
                            </div>
                        </div>
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Street 2</label>
                                <input type="text" name="street_2" id=""/>
                            </div>
                        </div>
                    </div>
                    <div class="wpsp-row">
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>State</label>
                                <input type="text" name="state" id="" required/>
                            </div>
                        </div>
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Zip Code</label>
                                <input type="text" name="zip_code" id="" required/>
                            </div>
                        </div>
                    </div>
                    <div class="wpsp-row">
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Phone Number</label>
                                <input type="number" name="phone" id="" required/>
                            </div>
                        </div>
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Email</label>
                                <input type="email" name="email" id="" required/>
                            </div>
                        </div>
                    </div>

                    <div class="wpsp-clearfix"></div>

                    <input type="hidden" name="action" value="wpsp_edit_address"/>
                    <input type="hidden" name="id" value=""/>
					<?php wp_nonce_field( 'wpsp_edit_address' ); ?>

                    <div class="wpsp-row">
                        <div class="action">
                            <button class="cancel">Cancel</button>
                            <button type="submit">Edit</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

</div>