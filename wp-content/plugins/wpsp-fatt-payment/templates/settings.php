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
                            <input type="number" step="any" name="fatt_reload_amount"
                                   value="<?= WPSP_FattCustomer::wpcc_get_option( 'fatt_reload_amount' ) ?>"/>
                        </div>
                    </div>
                    <div class="wpsp-one-fourth">
                        <div class="wpsp-form-group">
                            <label>Processing Fee<br/>
                                <small>Payment processing fees in percentage</small>
                            </label>
                            <input type="number" step="any" name="fatt_processing_fee"
                                   value="<?= WPSP_FattCustomer::wpcc_get_option( 'fatt_processing_fee' ) ?>"/>
                        </div>
                    </div>
                    <div class="wpsp-one-fourth">
                        <div class="wpsp-form-group">
                            <label>Funds Limit<br/>
                                <small>Charge customer when account funds are less than</small>
                            </label>
                            <input type="number" step="any" name="fatt_funds_limit"
                                   value="<?= WPSP_FattCustomer::wpcc_get_option( 'fatt_funds_limit' ) ?>"/>
                        </div>
                    </div>
                </div>
                <div class="wpsp-clearfix"></div>
                <h3>Fattmerchant API Settings</h3>
                <div class="wpsp-form-group" style="display: none;">
                    <label>Enable Test Mode</label>
                    <select name="wpcc_fatt_test_mode">
                        <option value="yes"<?= ( WPSP_FattCustomer::wpcc_get_option( 'wpcc_fatt_test_mode' ) === 'yes' ? ' selected' : '' ) ?>>
                            Yes
                        </option>
                        <option value="no"<?= ( WPSP_FattCustomer::wpcc_get_option( 'wpcc_fatt_test_mode' ) === 'no' ? ' selected' : '' ) ?>>
                            No
                        </option>
                    </select>
                </div>
                <div class="wpsp-form-group">
                    <label>Web Payment Token<br/>
                        <small>This token is your public api key and can be used in FattJs and for API-driven
                            web payments.
                        </small>
                    </label>
                    <input type="text" name="fatt_web_payment_token"
                           value="<?= WPSP_FattCustomer::wpcc_get_option( 'fatt_web_payment_token' ) ?>"/>
                </div>
                <div class="wpsp-form-group" style="display: none;">
                    <label>Authorization<br/>
                        <small>Staging environment (API)</small>
                    </label>
                    <input type="text" name="fatt_authorization"
                           value="<?= WPSP_FattCustomer::wpcc_get_option( 'fatt_authorization' ) ?>"/>
                </div>
                <div class="wpsp-form-group">
                    <label>Authorization<br/>
                        <small>Production environment</small>
                    </label>
                    <input type="text" name="fatt_live_authorization"
                           value="<?= WPSP_FattCustomer::wpcc_get_option( 'fatt_live_authorization' ) ?>"/>
                </div>
                <div class="wpsp-clearfix"></div>
				<?php wp_nonce_field( 'wpcc_fatt_save_settings' ) ?>
                <button type="submit" class="wpsp-btn-green">Save Settings</button>
            </form>
        </div>
    </div>
</div>