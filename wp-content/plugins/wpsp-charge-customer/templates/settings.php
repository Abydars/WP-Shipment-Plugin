<div id="wpsp" class="wpsp-container">
    <div class="container">
        <div class="row">
            <h1 class="wpsp-page-title">Charge Customer Settings</h1>
            <form method="POST">
                <div class="wpsp-row">
                    <div class="wpsp-one-fourth">
                        <div class="wpsp-form-group">
                            <label>Reload Amount<br/>
                                <small>Amount to be reloaded</small>
                            </label>
                            <input type="number" step="any" name="reload_amount"
                                   value="<?= WPSP_ChargeCustomer::wpcc_get_option( 'reload_amount' ) ?>"/>
                        </div>
                    </div>
                    <div class="wpsp-one-fourth">
                        <div class="wpsp-form-group">
                            <label>Processing Fee<br/>
                                <small>Payment processing fees in percentage</small>
                            </label>
                            <input type="number" step="any" name="processing_fee"
                                   value="<?= WPSP_ChargeCustomer::wpcc_get_option( 'processing_fee' ) ?>"/>
                        </div>
                    </div>
                    <div class="wpsp-one-fourth">
                        <div class="wpsp-form-group">
                            <label>Funds Limit<br/>
                                <small>Charge customer when account funds are less than</small>
                            </label>
                            <input type="number" step="any" name="funds_limit"
                                   value="<?= WPSP_ChargeCustomer::wpcc_get_option( 'funds_limit' ) ?>"/>
                        </div>
                    </div>
                </div>
                <div class="wpsp-clearfix"></div>
                <h3>Forte Settings</h3>
                <div class="wpsp-form-group">
                    <label>Enable Test Mode</label>
                    <select name="test_mode">
                        <option value="yes"<?= ( WPSP_ChargeCustomer::wpcc_get_option( 'test_mode' ) === 'yes' ? ' selected' : '' ) ?>>
                            Yes
                        </option>
                        <option value="no"<?= ( WPSP_ChargeCustomer::wpcc_get_option( 'test_mode' ) === 'no' ? ' selected' : '' ) ?>>
                            No
                        </option>
                    </select>
                </div>
                <div class="wpsp-row">
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Organization ID</label>
                            <input type="text" name="org_id"
                                   value="<?= WPSP_ChargeCustomer::wpcc_get_option( 'org_id' ) ?>"/>
                        </div>
                    </div>
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Location ID</label>
                            <input type="text" name="loc_id"
                                   value="<?= WPSP_ChargeCustomer::wpcc_get_option( 'loc_id' ) ?>"/>
                        </div>
                    </div>
                </div>
                <div class="wpsp-row">
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Access ID<br/>
                                <small>Staging environment</small>
                            </label>
                            <input type="text" name="access_id"
                                   value="<?= WPSP_ChargeCustomer::wpcc_get_option( 'access_id' ) ?>"/>
                        </div>
                    </div>
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Secure Key<br/>
                                <small>Staging environment</small>
                            </label>
                            <input type="text" name="secure_key"
                                   value="<?= WPSP_ChargeCustomer::wpcc_get_option( 'secure_key' ) ?>"/>
                        </div>
                    </div>
                </div>
                <div class="wpsp-form-group">
                    <label>Authorization<br/>
                        <small>Staging environment</small>
                    </label>
                    <input type="text" name="authorization"
                           value="<?= WPSP_ChargeCustomer::wpcc_get_option( 'authorization' ) ?>"/>
                </div>
                <div class="wpsp-row">
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Access ID<br/>
                                <small>Production environment</small>
                            </label>
                            <input type="text" name="live_access_id"
                                   value="<?= WPSP_ChargeCustomer::wpcc_get_option( 'live_access_id' ) ?>"/>
                        </div>
                    </div>
                    <div class="wpsp-one-half">
                        <div class="wpsp-form-group">
                            <label>Secure Key<br/>
                                <small>Production environment</small>
                            </label>
                            <input type="text" name="live_secure_key"
                                   value="<?= WPSP_ChargeCustomer::wpcc_get_option( 'live_secure_key' ) ?>"/>
                        </div>
                    </div>
                </div>
                <div class="wpsp-form-group">
                    <label>Authorization<br/>
                        <small>Production environment</small>
                    </label>
                    <input type="text" name="live_authorization"
                           value="<?= WPSP_ChargeCustomer::wpcc_get_option( 'live_authorization' ) ?>"/>
                </div>
                <div class="wpsp-clearfix"></div>
				<?php wp_nonce_field( 'wpcc_save_settings' ) ?>
                <button type="submit" class="wpsp-btn-green">Save Settings</button>
            </form>
        </div>
    </div>
</div>