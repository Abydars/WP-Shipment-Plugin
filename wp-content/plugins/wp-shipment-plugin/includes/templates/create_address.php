<div id="create-address">
    <div class="container">
        <div class="row">
            <h1>Create Address</h1>
            <form method="POST" action="">
                <div class="wpsp-row">
                    <div class="wpsp-form-group">
                        <label>Paste US Address</label>
                        <br/>
                        <br/>
                        <span>Format: 1005 N Gravenstein Highway Sebastopol, CA 95472</span>
                        <textarea name="address" placeholder="1005 N Gravenstein Highway Sebastopol, CA 95472" style="width: 100%;"></textarea>
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

                <input type="hidden" name="action" value="add_address"/>
<!--                --><?php //wp_nonce_field( 'wpsp_add_address' ); ?>

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