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
                            <input type="number" step="any" name="reload_amount" value="<?= $reload_amount ?>"/>
                        </div>
                    </div>
                    <div class="wpsp-one-fourth">
                        <div class="wpsp-form-group">
                            <label>Processing Fee<br/>
                                <small>Payment processing fees in percentage</small>
                            </label>
                            <input type="number" step="any" name="processing_fee" value="<?= $processing_fee ?>"/>
                        </div>
                    </div>
                    <div class="wpsp-one-fourth">
                        <div class="wpsp-form-group">
                            <label>Funds Limit<br/>
                                <small>Charge customer when account funds are less than</small>
                            </label>
                            <input type="number" step="any" name="funds_limit" value="<?= $funds_limit ?>"/>
                        </div>
                    </div>
                </div>
                <div class="wpsp-clearfix"></div>
				<?php wp_nonce_field( 'wpcc_save_settings' ) ?>
                <button type="submit" class="wpsp-btn-green">Save Settings</button>
            </form>
        </div>
    </div>
</div>