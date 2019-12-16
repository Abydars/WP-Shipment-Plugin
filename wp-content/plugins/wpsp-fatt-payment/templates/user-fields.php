<h3><?php _e( "FATT Merchant Details", "blank" ); ?></h3>
<table class="form-table">
    <tr>
        <th><label>Reload Amount<br/>
                <small>Amount to be reloaded</small>
            </label></th>
        <td>
            <input type="number" step="any" class="regular-text" name="fatt_user_reload_amount"
                   value="<?= WPCC_Customer::get_reload( $user->ID ) ?>"/><br>
        </td>
    </tr>
</table>