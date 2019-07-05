<div id="wpsp" class="wpsp-container">
    <div class="container">
        <div class="row">
            <h1 class="wpsp-page-title">Twilio Settings</h1>
            <form method="POST">
                <div class="wpsp-form-group">
                    <label>Account SID</label>
                    <input type="text" name="twilio_sid" value="<?= WPTM_Twilio::get_option( 'twilio_sid' ) ?>"
                           required/>
                </div>
                <div class="wpsp-form-group">
                    <label>Account Token</label>
                    <input type="text" name="twilio_token" value="<?= WPTM_Twilio::get_option( 'twilio_token' ) ?>"
                           required/>
                </div>
				<?php wp_create_nonce( 'wptm_save_settings' ) ?>
                <button type="submit" class="wpsp-btn-green">Save Settings</button>
            </form>
        </div>
    </div>
</div>