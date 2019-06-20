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
    <tr>
        <th><label><?php _e( "UPS Markup rate (%)" ); ?></label></th>
        <td>
            <input type="number" name="ups_rate" id="ups_rate"
                   value="<?php echo esc_attr( WPSP_Customer::get_ups_markup_rate( $user->ID ) ); ?>"
                   class="regular-text"/><br/>
        </td>
    </tr>
    <tr>
        <th><label><?php _e( "USPS Markup rate (%)" ); ?></label></th>
        <td>
            <input type="number" name="usps_rate" id="usps_rate"
                   value="<?php echo esc_attr( WPSP_Customer::get_usps_markup_rate( $user->ID ) ); ?>"
                   class="regular-text"/><br/>
        </td>
    </tr>
    <tr>
        <th><label><?php _e( "Fedex Markup rate (%)" ); ?></label></th>
        <td>
            <input type="number" name="fedex_rate" id="fedex_rate"
                   value="<?php echo esc_attr( WPSP_Customer::get_fedex_markup_rate( $user->ID ) ); ?>"
                   class="regular-text"/><br/>
        </td>
    </tr>
</table>