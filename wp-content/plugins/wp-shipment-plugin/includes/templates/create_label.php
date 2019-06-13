<!-- TASK FOR ARSALAN

Package add par click se form men show nae karaya package?
Package box delete karne ka option dena

Form par validation lagani hai jese old plugin men hai
1. Customer select karne ke baad ee New Address ka popup allow karey, warna alert box

!-->

<div id="wpsp-shipment-form-container" class="right-sidebar">
    <h1>Create Label</h1>
    <form method="POST" name="shipmentForm" id="shipment_form">
        <div class="basic-details">
            <div class="wpsp-form-group">
                <label>Ticket ID</label>
                <input type="text" name="id">
            </div>
            <div class="wpsp-form-group customers-list">
                <label>Customer</label>
                <select name="customer">
                    <option value="">Select Customer</option>
					<?php foreach ( $customers as $customer ) : ?>
                        <option value="<?= $customer->ID ?>"><?= $customer->display_name ?></option>
					<?php endforeach; ?>
                </select>
            </div>
            <div class="wpsp-form-group">
                <label>From <a id="btn-new-from-address" href="#" class="from-address" data-type="from">(New
                        Address)</a></label>
                <select name="from">
                    <option value=""></option>
                </select>
            </div>
            <div class="wpsp-form-group">
                <label>To <a id="btn-new-to-address" href="#" class="to-address" data-type="to">(New
                        Address)</a></label>
                <select name="to">
                    <option value=""></option>
                </select>
            </div>
            <div class="wpsp-form-group">
                <label>Shipping Carrier</label>
                <select name="carrier">
					<?php foreach ( $carriers as $k => $carrier ) { ?>
                        <option value="<?= $k ?>"><?= $carrier ?></option>
					<?php } ?>
                </select>
            </div>
            <div class="wpsp-form-group">
                <label>Shipping Method</label>
                <select name="shipping_method">
                    <option value=""></option>
                </select>
            </div>
            <div class="wpsp-form-group">
                <label>Shipping Date</label>
                <input type="date" name="shipping_date" id="">
            </div>
        </div>
        <div class="packages">
            <h2>Packages</h2>
            <div class="package">
                <h4>Package #1</h4>
                <div class="wpsp-row">
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Weight</label>
                            <input type="text" name="packages[0][weight]">
                        </div>
                    </div>
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Weight Unit</label>
                            <select name="packages[0][unit]">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="wpsp-row">
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Length</label>
                            <input type="text" name="packages[0][length]">
                        </div>
                    </div>
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Width</label>
                            <input type="text" name="packages[0][width]">
                        </div>
                    </div>
                </div>
                <div class="wpsp-row">
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Height</label>
                            <input type="text" name="packages[0][height]">
                        </div>
                    </div>
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>SKU</label>
                            <input type="text" name="packages[0][sku]">
                        </div>
                    </div>
                </div>
                <div class="wpsp-row">
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Declared Currency</label>
                            <select name="packages[0][declared_currency]">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Declared Customs Value</label>
                            <input type="text" name="packages[0][declared_customs_value]">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="wpsp-row">
            <div class="pickup">
                <div class="wpsp-row">
                    <input type="checkbox" name="schedule" value="yes"> Schedule Pickup
                </div>
                <div class="pickup-schedule" style="display: none">
                    <div class="wpsp-form-group">
                        <label>Pickup Date</label>
                        <input type="date" name="pickup_date">
                    </div>
                    <div class="wpsp-row">
                        <div class="one-third">
                            <div class="wpsp-form-group">
                                <label>Pickup Time</label>
                                <select name="pickup_time">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="one-fourth">
                            <div class="wpsp-form-group">
                                <label></label>
                                <select name="">
                                    <option value="am">AM</option>
                                    <option value="pm">PM</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <input type="hidden" name="action" value="save_label"/>
		<?php wp_nonce_field( 'wpsp_save_label' ); ?>

        <div class="wpsp-row">
            <button>Rate Shop & Send</button>
            <button id="btn-new-package">+ Add New Package</button>
            <button type="submit">Create Shipment</button>
        </div>
    </form>

    <div id="addNewPackage" class="new-package modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add new Package</h2>
            <div class="package">
                <h4>Package #</h4>
                <div class="wpsp-row">
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Weight</label>
                            <input type="text" name="weight">
                        </div>
                    </div>
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Weight Unit</label>
                            <select name="unit">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="wpsp-row">
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Length</label>
                            <input type="text" name="length">
                        </div>
                    </div>
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Width</label>
                            <input type="text" name="width">
                        </div>
                    </div>
                </div>
                <div class="wpsp-row">
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Height</label>
                            <input type="text" name="height">
                        </div>
                    </div>
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>SKU</label>
                            <input type="text" name="sku">
                        </div>
                    </div>
                </div>
                <div class="wpsp-row">
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Declared Currency</label>
                            <select name="dec-currency">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Declared Customs Value</label>
                            <input type="text" name="dec_customs_val">
                        </div>
                    </div>
                </div>
                <div class="wpsp-clearfix"></div>
                <div class="action">
                    <div class="wpsp-row">
                        <button class="cancel">Cancel</button>
                        <button>Add</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="addAddressModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add New Address</h2>

            <form method="POST" action="">
                <div class="wpsp-row">
                    <div class="wpsp-form-group">
                        <label>Paste US Address</label>
                        <br/>
                        <span>Format: 1005 N Gravenstein Highway Sebastopol, CA 95472</span>
                        <textarea name="address"
                                  placeholder="1005 N Gravenstein Highway Sebastopol, CA 95472"
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
                            <input type="text" name="street_2" id="" required/>
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

                <input type="hidden" name="action" value="add_address"/>
				<?php wp_nonce_field( 'wpsp_add_address' ); ?>

                <div class="wpsp-row">
                    <div class="action">
                        <button class="cancel">Cancel</button>
                        <button type="submit">Add</button>
                    </div>
                </div>
            </form>
        </div>

    </div>

</div>
