<div id="create-address" class="wpsp-container">
    <div class="container">
        <div class="row">
            <h1 class="wpsp-page-title">Create Address <a href="<?= admin_url( 'admin.php?page=list_addresses' ) ?>">View
                    Addresses</a></h1>

			<?php if ( isset( $_GET['error'] ) ) { ?>
				<?= apply_filters( 'wpsp_error', $_GET['error'] ) ?>
			<?php } ?>

            <form method="POST">
                <div class="wpsp-row">
                    <div class="wpsp-form-group customers-list">
                        <label>Customer</label>
                        <select name="customer">
                            <option value="">All Customers</option>
							<?php foreach ( $customers as $customer ) : ?>
                                <option value="<?= $customer->ID ?>"
                                        data-initials="<?= WPSP_Customer::get_customer_initials( $customer->ID ) ?>"><?= $customer->display_name ?></option>
							<?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="wpsp-row" style="display: none;">
                    <div class="wpsp-form-group">
                        <label>Paste US Address</label>
                        <br/>
                        <br/>
                        <span>Format: 1005 N Gravenstein Highway Sebastopol, CA 95472</span>
                        <textarea name="address" placeholder="1005 N Gravenstein Highway Sebastopol, CA 95472"
                                  style="width: 100%;"></textarea>
                    </div>
                </div>
                <div class="wpsp-row">
                    <div class="wpsp-form-group address-code">
                        <label>Address Code</label>
                        <input type="text" name="code" id="" placeholder="(optional)"/>
                    </div>
                </div>
                <div class="wpsp-row">
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" id=""/>
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
                        <div class="wpsp-form-group select-country">
                            <label>Country</label>
                            <select class="wpsp-chosen" name="country" required>
                                <option value="">Select Country</option>
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
                        <div class="wpsp-form-group select-state">
                            <label>State</label>
                            <select class="wpsp-chosen" name="state" required>
                            </select>
                        </div>
                    </div>
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Zip Code</label>
                            <input type="text" name="zip_code" id="" required/>
                        </div>
                    </div>
                </div>
                <div class="wpsp-row" style="display: none;">
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Phone Number</label>
                            <input type="number" name="phone" id=""/>
                        </div>
                    </div>
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Email</label>
                            <input type="email" name="email" id=""/>
                        </div>
                    </div>
                </div>

                <div class="wpsp-clearfix"></div>

                <input type="hidden" name="action" value="add_address"/>
				<?php wp_nonce_field( 'wpsp_create_address' ); ?>

                <div class="wpsp-row">
                    <div class="action">
                        <button type="submit">Create Address</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>