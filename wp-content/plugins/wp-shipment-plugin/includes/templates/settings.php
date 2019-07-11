<div id="wpsp" class="wpsp-container">
    <div class="container">
        <div class="row">
            <h1 class="wpsp-page-title">WPSP Settings</h1>

			<?php if ( isset( $_GET['error'] ) ) { ?>
				<?= apply_filters( 'wpsp_error', $_GET['error'] ) ?>
			<?php } ?>

            <form method="POST">
                <h3>General</h3>
                <div class="wpsp-form-group">
                    <label>Enable Test Mode</label>
                    <select name="wpsp_test_mode">
                        <option value="yes"<?= ( WPSP::get_option( 'wpsp_test_mode' ) === 'yes' ? ' selected' : '' ) ?>>
                            Yes
                        </option>
                        <option value="no"<?= ( WPSP::get_option( 'wpsp_test_mode' ) === 'no' ? ' selected' : '' ) ?>>
                            No
                        </option>
                    </select>
                </div>
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
				<?php foreach ( $carriers as $k => $carrier ) : $settings = WPSP::get_settings( $k ); ?>
					<?php if ( ! empty( $settings ) ) : ?>
                        <h3><?= $carrier ?> Additional Settings</h3>
						<?php foreach ( $settings as $setting ) : ?>
                            <div class="wpsp-row">
                                <div class="wpsp-form-group">
                                    <label><?= $setting['label'] ?></label>
									<?php if ( $setting['type'] == 'text' || $setting['type'] == 'number' ) : ?>
                                        <input <?= ( $setting['type'] == 'number' ) ? 'step="any"' : '' ?>
                                                type="<?= $setting['type'] ?>"
                                                name="<?= "wpsp_{$k}_{$setting['id']}" ?>"
                                                value="<?= WPSP::get_option( "wpsp_{$k}_{$setting['id']}", $setting['default'] ) ?>"/>
									<?php elseif ( $setting['type'] == 'select' ): ?>

									<?php endif; ?>
                                </div>
                            </div>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endforeach; ?>
				<?php wp_nonce_field( 'wpsp_save_settings' ) ?>
                <button type="submit" class="wpsp-btn-green">Save Settings</button>
            </form>
        </div>
    </div>
</div>