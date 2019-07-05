<div id="create-address" class="wpsp-container">
    <div class="container">
        <div class="row">
            <h1 class="wpsp-page-title">Create Customer <a href="<?= admin_url( 'admin.php?page=wpcc-customers' ) ?>">View
                    Customers</a></h1>

			<?php if ( ! empty( $error ) ) { ?>
				<?= apply_filters( 'wpsp_error', $error ) ?>
			<?php } ?>

            <form method="POST">
                <div class="wpsp-row">
                    <div class="wpsp-form-group customers-list">
                        <label>Customer</label>
                        <select class="wpsp-chosen" name="customer" required>
                            <option value="">Select Customer</option>
							<?php foreach ( $customers as $customer ) : ?>
                                <option value="<?= $customer->ID ?>"<?= ( ( isset( $data['customer'] ) && $customer->ID == $data['customer'] ) ? ' selected' : '' ) ?>><?= $customer->display_name ?></option>
							<?php endforeach; ?>
                        </select>
                    </div>
                    <h3>Address</h3>
                    <div class="wpsp-row">
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>First Name</label>
                                <input type="text" name="addresses[0][first_name]"
                                       value="<?= ( ( isset( $data['addresses'][0]['first_name'] ) ? $data['addresses'][0]['first_name'] : '' ) ) ?>"
                                       required/>
                            </div>
                        </div>
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Last Name</label>
                                <input type="text" name="addresses[0][last_name]"
                                       value="<?= ( ( isset( $data['addresses'][0]['last_name'] ) ? $data['addresses'][0]['last_name'] : '' ) ) ?>"
                                       required/>
                            </div>
                        </div>
                    </div>
                    <div class="wpsp-row">
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Company Name</label>
                                <input type="text" name="addresses[0][company_name]"
                                       value="<?= ( ( isset( $data['addresses'][0]['company_name'] ) ? $data['addresses'][0]['company_name'] : '' ) ) ?>"
                                       required/>
                            </div>
                        </div>
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Phone</label>
                                <input type="text" name="addresses[0][phone]"
                                       value="<?= ( ( isset( $data['addresses'][0]['phone'] ) ? $data['addresses'][0]['phone'] : '' ) ) ?>"
                                       required/>
                            </div>
                        </div>
                    </div>
                    <div class="wpsp-form-group">
                        <label>Email</label>
                        <input type="text" name="addresses[0][email]"
                               value="<?= ( ( isset( $data['addresses'][0]['email'] ) ? $data['addresses'][0]['email'] : '' ) ) ?>"
                               required/>
                    </div>
                    <div class="wpsp-row">
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Street 1</label>
                                <input type="text" name="addresses[0][physical_address][street_line1]"
                                       value="<?= ( ( isset( $data['addresses'][0]['physical_address']['street_line1'] ) ? $data['addresses'][0]['physical_address']['street_line1'] : '' ) ) ?>"
                                       required/>
                            </div>
                        </div>
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>Street 2</label>
                                <input type="text" name="addresses[0][physical_address][street_line2]"
                                       value="<?= ( ( isset( $data['addresses'][0]['physical_address']['street_line2'] ) ? $data['addresses'][0]['physical_address']['street_line2'] : '' ) ) ?>"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="wpsp-row">
                        <div class="wpsp-one-half">
                            <div class="wpsp-form-group">
                                <label>City</label>
                                <input type="text" name="addresses[0][physical_address][locality]"
                                       value="<?= ( ( isset( $data['addresses'][0]['physical_address']['locality'] ) ? $data['addresses'][0]['physical_address']['locality'] : '' ) ) ?>"
                                       required/>
                            </div>
                        </div>
                        <div class="wpsp-one-fourth">
                            <div class="wpsp-form-group">
                                <label>State</label>
                                <input type="text" name="addresses[0][physical_address][region]"
                                       value="<?= ( ( isset( $data['addresses'][0]['physical_address']['region'] ) ? $data['addresses'][0]['physical_address']['region'] : '' ) ) ?>"
                                       required/>
                            </div>
                        </div>
                        <div class="wpsp-one-fourth">
                            <div class="wpsp-form-group">
                                <label>Zip Code</label>
                                <input type="text" name="addresses[0][physical_address][postal_code]"
                                       value="<?= ( ( isset( $data['addresses'][0]['physical_address']['postal_code'] ) ? $data['addresses'][0]['physical_address']['postal_code'] : '' ) ) ?>"
                                       required/>
                            </div>
                        </div>
                    </div>
                    <div class="wpsp-clearfix"></div>
                    <h3>Payment Method</h3>
                    <div class="wpsp-form-group select-payment-type">
                        <label>Payment Type</label>
                        <select name="payment_type" required>
                            <option value="">Select Payment Type</option>
                            <option<?= ( ( isset( $data['payment_type'] ) && 'eCheck' === $data['payment_type'] ) ? ' selected' : '' ) ?>>
                                eCheck
                            </option>
                            <option<?= ( ( isset( $data['payment_type'] ) && 'Card' === $data['payment_type'] ) ? ' selected' : '' ) ?>>
                                Card
                            </option>
                        </select>
                    </div>
                    <div class="echeck-fields" style="display: none;">
                        <div class="wpsp-form-group">
                            <label>Account Type</label>
                            <select name="paymethod[echeck][account_type]">
                                <option value="checking"<?= ( ( isset( $data['paymethod']['echeck']['account_type'] ) && 'checking' === $data['paymethod']['echeck']['account_type'] ) ? ' selected' : '' ) ?>>
                                    Checking
                                </option>
                                <option value="saving"<?= ( ( isset( $data['paymethod']['echeck']['account_type'] ) && 'saving' === $data['paymethod']['echeck']['account_type'] ) ? ' selected' : '' ) ?>>
                                    Saving
                                </option>
                            </select>
                        </div>
                        <div class="wpsp-form-group">
                            <label>Account Holder Name</label>
                            <input type="text" name="paymethod[echeck][account_holder]"
                                   value="<?= ( ( isset( $data['paymethod']['echeck']['account_holder'] ) ? $data['paymethod']['echeck']['account_holder'] : '' ) ) ?>"
                            />
                        </div>
                        <div class="wpsp-form-group">
                            <label>Account Number</label>
                            <input type="text" name="paymethod[echeck][account_number]"
                                   value="<?= ( ( isset( $data['paymethod']['echeck']['account_number'] ) ? $data['paymethod']['echeck']['account_number'] : '' ) ) ?>"
                            />
                        </div>
                        <div class="wpsp-form-group">
                            <label>Routing Number</label>
                            <input type="text" name="paymethod[echeck][routing_number]"
                                   value="<?= ( ( isset( $data['paymethod']['echeck']['routing_number'] ) ? $data['paymethod']['echeck']['routing_number'] : '' ) ) ?>"
                            />
                        </div>
                    </div>
                    <div class="card-fields" style="display: none;">
                        <div class="wpsp-form-group">
                            <label>Card Type</label>
                            <select name="paymethod[card][card_type]">
                                <option value="visa"<?= ( ( isset( $data['paymethod']['card']['card_type'] ) && 'visa' === $data['paymethod']['card']['card_type'] ) ? ' selected' : '' ) ?>>
                                    visa
                                </option>
                                <option value="mast"<?= ( ( isset( $data['paymethod']['card']['card_type'] ) && 'mast' === $data['paymethod']['card']['card_type'] ) ? ' selected' : '' ) ?>>
                                    mast
                                </option>
                                <option value="amex"<?= ( ( isset( $data['paymethod']['card']['card_type'] ) && 'amex' === $data['paymethod']['card']['card_type'] ) ? ' selected' : '' ) ?>>
                                    amex
                                </option>
                                <option value="disc"<?= ( ( isset( $data['paymethod']['card']['card_type'] ) && 'disc' === $data['paymethod']['card']['card_type'] ) ? ' selected' : '' ) ?>>
                                    disc
                                </option>
                                <option value="dine"<?= ( ( isset( $data['paymethod']['card']['card_type'] ) && 'dine' === $data['paymethod']['card']['card_type'] ) ? ' selected' : '' ) ?>>
                                    dine
                                </option>
                                <option value="jcb"<?= ( ( isset( $data['paymethod']['card']['card_type'] ) && 'jcb' === $data['paymethod']['card']['card_type'] ) ? ' selected' : '' ) ?>>
                                    jcb
                                </option>
                            </select>
                        </div>
                        <div class="wpsp-row">
                            <div class="wpsp-one-half">
                                <div class="wpsp-form-group">
                                    <label>Name on Card</label>
                                    <input type="text" name="paymethod[card][name_on_card]"
                                           value="<?= ( ( isset( $data['paymethod']['card']['name_on_card'] ) ? $data['paymethod']['card']['name_on_card'] : '' ) ) ?>"
                                    />
                                </div>
                            </div>
                            <div class="wpsp-one-half">
                                <div class="wpsp-form-group">
                                    <label>Card Number</label>
                                    <input type="text" name="paymethod[card][account_number]"
                                           value="<?= ( ( isset( $data['paymethod']['card']['account_number'] ) ? $data['paymethod']['card']['account_number'] : '' ) ) ?>"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="wpsp-row">
                            <div class="wpsp-one-half">
                                <div class="wpsp-form-group">
                                    <label>Expire Month</label>
                                    <input type="number" name="paymethod[card][expire_month]"
                                           value="<?= ( ( isset( $data['paymethod']['card']['expire_month'] ) ? $data['paymethod']['card']['expire_month'] : '' ) ) ?>"
                                    />
                                </div>
                            </div>
                            <div class="wpsp-one-half">
                                <div class="wpsp-form-group">
                                    <label>Expire Year</label>
                                    <input type="number" name="paymethod[card][expire_year]"
                                           value="<?= ( ( isset( $data['paymethod']['card']['expire_year'] ) ? $data['paymethod']['card']['expire_year'] : '' ) ) ?>"
                                    />
                                </div>
                            </div>
                        </div>
                        <div class="wpsp-form-group">
                            <label>CVV</label>
                            <input type="text" name="paymethod[card][card_verification_value]"
                                   value="<?= ( ( isset( $data['paymethod']['card']['card_verification_value'] ) ? $data['paymethod']['card']['card_verification_value'] : '' ) ) ?>"
                            />
                        </div>
                    </div>
					<?php wp_nonce_field( 'wpcc_add_customer' ) ?>
                    <button class="wpsp-btn-green" type="submit">Authorize & Save</button>
                </div>
            </form>
        </div>
    </div>
</div>