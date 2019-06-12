<?php

class WPSP_UserMeta
{
    function extra_user_profile_fields($user)
    { ?>
        <h3><?php _e("Additional Information", "blank"); ?></h3>

        <table class="form-table">
            <tr>
                <th><label><?php _e("Alternate Email Address"); ?></label></th>
                <td>
                    <input type="email" name="alternate_address" id="alternate_address"
                           value="<?php echo esc_attr(get_the_author_meta('alternate_address', $user->ID)); ?>"
                           class="regular-text"/><br/>
                </td>
            </tr>
            <tr>
                <th><label><?php _e("Fax Number"); ?></label></th>
                <td>
                    <input type="text" name="fax_number" id="fax_number"
                           value="<?php echo esc_attr(get_the_author_meta('fax_number', $user->ID)); ?>"
                           class="regular-text"/><br/>
                </td>
            </tr>
            <tr>
                <th><label><?php _e("UPS Markup rate (%)"); ?></label></th>
                <td>
                    <input type="number" name="ups_rate" id="ups_rate"
                           value="<?php echo esc_attr(get_the_author_meta('ups_rate', $user->ID)); ?>"
                           class="regular-text"/><br/>
                </td>
            </tr>
            <tr>
                <th><label><?php _e("USPS Markup rate (%)"); ?></label></th>
                <td>
                    <input type="number" name="usps_rate" id="usps_rate"
                           value="<?php echo esc_attr(get_the_author_meta('usps_rate', $user->ID)); ?>"
                           class="regular-text"/><br/>
                </td>
            </tr>
            <tr>
                <th><label><?php _e("Fedex Markup rate (%)"); ?></label></th>
                <td>
                    <input type="number" name="fedex_rate" id="fedex_rate"
                           value="<?php echo esc_attr(get_the_author_meta('fedex_rate', $user->ID)); ?>"
                           class="regular-text"/><br/>
                </td>
            </tr>
        </table>
    <?php }
}