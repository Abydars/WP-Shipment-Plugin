<h3><?php _e( "Account", "blank" ); ?></h3>
<table class="form-table">
    <tr>
        <th><label><?php _e( "Account Funds" ); ?></label></th>
        <td>
            <input type="number" name="account_funds" id="account_funds"
                   value="<?php echo esc_attr( WPSP_Customer::get_account_funds( $user->ID ) ); ?>"
                   class="regular-text"/><br/>
        </td>
    </tr>
</table>

<h3><?php _e( "Additional Information", "blank" ); ?></h3>

<table class="form-table">
    <tr>
        <th><label><?php _e( "Default Address" ); ?></label></th>
        <td>
            <select name="default_address" id="default_address">
				<?php foreach ( $addresses as $address ) { ?>
                    <option value="<?= $address->id ?>"<?= ( ( WPSP_Customer::get_default_address( $user->ID ) == $address->id ) ? ' selected="selected"' : '' ) ?>><?= $address->address_name ?></option>
				<?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <th><label><?php _e( "Alternate Email Address" ); ?></label></th>
        <td>
            <input type="email" name="alternate_address" id="alternate_address"
                   value="<?php echo esc_attr( WPSP_Customer::get_alt_email_address( $user->ID ) ); ?>"
                   class="regular-text"/><br/>
        </td>
    </tr>
    <tr>
        <th><label><?php _e( "Fax Number" ); ?></label></th>
        <td>
            <input type="text" name="fax_number" id="fax_number"
                   value="<?php echo esc_attr( WPSP_Customer::get_fax_number( $user->ID ) ); ?>"
                   class="regular-text"/><br/>
        </td>
    </tr>
	<?php foreach ( $carriers as $k => $carrier ) : ?>
        <tr>
            <th><label><?php _e( "{$carrier} Markup rate (%)" ); ?></label></th>
            <td>
                <input type="number" name="<?= $k ?>_rate" id="<?= $k ?>_rate"
                       value="<?php echo esc_attr( WPSP_Customer::get_markup_rate( $user->ID, $k ) ); ?>"
                       class="regular-text"/><br/>
            </td>
        </tr>
	<?php endforeach; ?>
</table>