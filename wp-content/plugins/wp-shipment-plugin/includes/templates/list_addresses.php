<div id="list-addresses" class="wpsp-container">
    <div class="container">
        <div class="row">
            <h1 class="wpsp-page-title">Addresses <a href="<?= admin_url( 'admin.php?page=create_address' ) ?>">Create
                    Address</a></h1>
        </div>

		<?php if ( isset( $_GET['error'] ) ) { ?>
			<?= apply_filters( 'wpsp_error', $_GET['error'] ) ?>
		<?php } ?>

		<?php if ( isset( $_GET['success'] ) ) { ?>
			<?= apply_filters( 'wpsp_success', $_GET['success'] ) ?>
		<?php } ?>

        <div class="row">
            <table id="listShipments">
                <thead>
                <tr>
                    <th>Code</th>
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
                    <th>Residential</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
				<?php
				foreach ( $addresses as $k => $address ) {
					$address_data  = json_decode( $address->data );
					$address_data  = (array) $address_data;
					$customer_name = "All Customers";

					if ( ! empty( $address->customer_id ) ) {
						$user          = get_user_meta( $address->customer_id );
						$user          = (object) $user;
						$customer_name = $user->first_name[0] . " " . $user->last_name[0];
					}

					?>
                    <tr style="text-align: left">
                        <td><?= $address->address_code ?></td>
                        <td><?= $customer_name ?></td>
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
                        <td><?= ($address->is_residential == 1) ? 'Yes' : 'No' ?></td>
                        <td class="address_actions">
                            <a href="#" class="btn-edit-address" data-type="from"
                               data-key="<?= $k ?>" data-id="<?= $address->id ?>" style="display: none;"><i class="fa fa-eye"></i></a>
                            <a href="#" class="btn-delete-address"
                               data-key="<?= $k ?>" data-id="<?= $address->id ?>">
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
                        <div class="wpsp-form-group">
                            <label>Address Code</label>
                            <input type="text" name="code" id="" placeholder="(optional)"/>
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
                                <input type="text" name="company" id=""/>
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
                                <label>Country</label>
                                <select name="country" required>
                                    <?php foreach ( $countries as $country ) { ?>
                                        <option value="<?= $country['code2'] ?>"><?= $country['name'] ?></option>
                                    <?php } ?>
                                </select>
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
                            <button type="submit" class="wpsp-btn-green">Edit</button>
                            <button class="cancel">Cancel</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>

</div>