<div id="wpsp" class="wpsp-container">
    <div class="container">
        <div class="row">
            <h1 class="wpsp-page-title">WPSP Settings</h1>

			<?php if ( isset( $_GET['error'] ) ) { ?>
				<?= apply_filters( 'wpsp_error', $_GET['error'] ) ?>
			<?php } ?>

            <form method="POST">
                <h3>Default Markup Rates (%)</h3>
                <div class="wpsp-row">
					<?php foreach ( $carriers as $k => $carrier ) : ?>
                        <div class="wpsp-form-group">
                            <label><?= $carrier ?></label>
                            <input type="number" step="any" name="wpsp_<?= $k ?>_rate"
                                   value="<?= WPSP::get_option( "wpsp_{$k}_rate", 0 ) ?>"/>
                        </div>
					<?php endforeach; ?>
                </div>
                <h3>Default Pickup Rates ($)</h3>
                <div class="wpsp-row">
					<?php foreach ( $carriers as $k => $carrier ) : ?>
                        <div class="wpsp-form-group">
                            <label><?= $carrier ?></label>
                            <input type="number" step="any" name="wpsp_<?= $k ?>_pickup_rates"
                                   value="<?= WPSP::get_option( "wpsp_{$k}_pickup_rates", 0 ) ?>"/>
                        </div>
					<?php endforeach; ?>
                </div>
				<?php wp_nonce_field( 'wpsp_save_settings' ) ?>
                <button type="submit" class="wpsp-btn-green">Save Settings</button>
            </form>
        </div>
    </div>
</div>