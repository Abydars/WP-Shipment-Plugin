<div class="right-sidebar">
    <h1>Create Label</h1>
    <form method="POST" name="shipmentForm" id="shipment_form">
        <div class="basic-details">
            <div class="form-group">
                <label>Ticket ID</label>
                <input type="text" name="id">
            </div>
            <div class="form-group">
                <label>Customer</label>
                <select name="customer">
                    <option value=""></option>
                </select>
            </div>
            <div class="form-group">
                <label>From <a href="#" class="from-address" data-type="from">(New Address)</a></label>
                <select name="from">
                    <option value=""></option>
                </select>
            </div>
            <div class="form-group">
                <label>To <a href="#" class="to-address" data-type="to">(New Address)</a></label>
                <select name="to">
                    <option value=""></option>
                </select>
            </div>
            <div class="form-group">
                <label>Shipping Carrier</label>
                <select name="shipping_carrier">
                    <option value=""></option>
                </select>
            </div>
            <div class="form-group">
                <label>Shipping Method</label>
                <select name="shipping_method">
                    <option value=""></option>
                </select>
            </div>
            <div class="form-group">
                <label>Shipping Date</label>
                <input type="date" name="shipping_date" id="">
            </div>
        </div>
        <div class="packages">
            <h2>Packages</h2>
            <div class="package">
                <h4>Package #1</h4>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Weight</label>
                            <input type="text" name="weight">
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>Weight Unit</label>
                            <select name="unit">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Length</label>
                            <input type="text" name="length">
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>Width</label>
                            <input type="text" name="width">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Height</label>
                            <input type="text" name="height">
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>SKU</label>
                            <input type="text" name="sku">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Declared Currency</label>
                            <select name="dec-currency">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>Declared Customs Value</label>
                            <input type="text" name="dec_customs_val">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="pickup">
                <div class="row">
                    <input type="checkbox" name="schedule" value="yes"> Schedule Pickup
                </div>
                <div class="pickup-schedule" style="display: none">
                    <div class="form-group">
                        <label>Pickup Date</label>
                        <input type="date" name="pickup_date">
                    </div>
                    <div class="row">
                        <div class="one-third">
                            <div class="form-group">
                                <label>Pickup Time</label>
                                <select name="pickup_time">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="one-fourth">
                            <div class="form-group">
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
        <div class="new-package modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2>Add new Package</h2>
                <div class="package">
                    <h4>Package #</h4>
                    <div class="row">
                        <div class="one-half">
                            <div class="form-group">
                                <label>Weight</label>
                                <input type="text" name="weight">
                            </div>
                        </div>
                        <div class="one-half">
                            <div class="form-group">
                                <label>Weight Unit</label>
                                <select name="unit">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="one-half">
                            <div class="form-group">
                                <label>Length</label>
                                <input type="text" name="length">
                            </div>
                        </div>
                        <div class="one-half">
                            <div class="form-group">
                                <label>Width</label>
                                <input type="text" name="width">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="one-half">
                            <div class="form-group">
                                <label>Height</label>
                                <input type="text" name="height">
                            </div>
                        </div>
                        <div class="one-half">
                            <div class="form-group">
                                <label>SKU</label>
                                <input type="text" name="sku">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="one-half">
                            <div class="form-group">
                                <label>Declared Currency</label>
                                <select name="dec-currency">
                                    <option value=""></option>
                                </select>
                            </div>
                        </div>
                        <div class="one-half">
                            <div class="form-group">
                                <label>Declared Customs Value</label>
                                <input type="text" name="dec_customs_val">
                            </div>
                        </div>
                    </div>
                    <div class="actions">
                        <div class="row">
                            <button class="cancel">Cancel</button>
                            <button>Add</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php wp_nonce_field( 'create_label', 'create_label_form' ); ?>
        <div class="row">
            <button>Rate Shop & Send</button>
            <button class="add_new_package">+ Add New Package</button>
        </div>
    </form>

    <div id="fromAddress" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add (From) New Address</h2>
            <form method="POST" action="">
                <div class="row">
                    <div class="form-group">
                        <label>Paste US Address</label>
                        <br/>
                        <span>Format: 1005 N Gravenstein Highway Sebastopol, CA 95472</span>
                        <textarea name="address" placeholder="1005 N Gravenstein Highway Sebastopol, CA 95472"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" id="">
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>Company</label>
                            <input type="text" name="company" id="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Country</label>
                            <select name="country">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" id="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Street 1</label>
                            <input type="text" name="street_one" id="">
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>Street 2</label>
                            <input type="text" name="street_two" id="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" id="">
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>Zip Code</label>
                            <input type="text" name="zip_code" id="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="number" name="phone" id="">
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <input type="checkbox" name="verify" id=""> <label>Circle Verification</label>
                    </div>
                </div>
                <?php wp_nonce_field( 'from_address', 'create_from_address' ); ?>
                <div class="row">
                    <div class="action">
                        <button>Cancel</button>
                        <button>Add</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <div id="toAddress" class="modal">

        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add (To) New Address</h2>
            <form method="POST" action="">
                <div class="row">
                    <div class="form-group">
                        <label>Paste US Address</label>
                        <br/>
                        <span>Format: 1005 N Gravenstein Highway Sebastopol, CA 95472</span>
                        <textarea name="address" placeholder="1005 N Gravenstein Highway Sebastopol, CA 95472"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" id="">
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>Company</label>
                            <input type="text" name="company" id="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Country</label>
                            <select name="country">
                                <option value=""></option>
                            </select>
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" id="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Street 1</label>
                            <input type="text" name="street_one" id="">
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>Street 2</label>
                            <input type="text" name="street_two" id="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" id="">
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>Zip Code</label>
                            <input type="text" name="zip_code" id="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="one-half">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="number" name="phone" id="">
                        </div>
                    </div>
                    <div class="one-half">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <input type="checkbox" name="verify" id=""> <label>Circle Verification</label>
                    </div>
                </div>
                <?php wp_nonce_field( 'to_address', 'create_to_address' ); ?>
                <div class="row">
                    <div class="action">
                        <button>Cancel</button>
                        <button>Add</button>
                    </div>
                </div>
            </form>
        </div>

    </div>

</div>
