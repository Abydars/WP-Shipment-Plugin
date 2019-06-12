<?php

/**
 * Storefront engine room
 *
 * @package storefront
 */


/**
 * Assign the Storefront version to a var
 */

$theme = wp_get_theme( 'storefront' );

$storefront_version = $theme['Version'];


/**
 * Set the content width based on the theme's design and stylesheet.
 */

if ( ! isset( $content_width ) ) {

	$content_width = 980; /* pixels */

}


$storefront = (object) array(

	'version' => $storefront_version,


	/**
	 * Initialize all the things.
	 */

	'main' => require 'inc/class-storefront.php',

	'customizer' => require 'inc/customizer/class-storefront-customizer.php',

);


require 'inc/storefront-functions.php';

require 'inc/storefront-template-hooks.php';

require 'inc/storefront-template-functions.php';


if ( class_exists( 'Jetpack' ) ) {

	$storefront->jetpack = require 'inc/jetpack/class-storefront-jetpack.php';

}


if ( storefront_is_woocommerce_activated() ) {

	$storefront->woocommerce = require 'inc/woocommerce/class-storefront-woocommerce.php';


	require 'inc/woocommerce/storefront-woocommerce-template-hooks.php';

	require 'inc/woocommerce/storefront-woocommerce-template-functions.php';

}


if ( is_admin() ) {

	$storefront->admin = require 'inc/admin/class-storefront-admin.php';


	require 'inc/admin/class-storefront-plugin-install.php';

}


/**
 * NUX
 * Only load if wp version is 4.7.3 or above because of this issue;
 * https://core.trac.wordpress.org/ticket/39610?cversion=1&cnum_hist=2
 */

if ( version_compare( get_bloginfo( 'version' ), '4.7.3', '>=' ) && ( is_admin() || is_customize_preview() ) ) {

	require 'inc/nux/class-storefront-nux-admin.php';

	require 'inc/nux/class-storefront-nux-guided-tour.php';


	if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '3.0.0', '>=' ) ) {

		require 'inc/nux/class-storefront-nux-starter-content.php';

	}

}


/**
 * Note: Do not add any custom code here. Please use a custom plugin so that your customizations aren't lost during updates.
 * https://github.com/woocommerce/theme-customisations
 */

add_filter( 'woocommerce_countries_base_postcode', 'set_woocommerce_countries_base_postcode' );

function set_woocommerce_countries_base_postcode()
{

	// Replace with your store postcode / zipcode

	return '17562';

}

function render_profile_fields( WP_User $user )
{
	?>
    <h3>Extra User information</h3>
    <table class="form-table">
        <tr>
            <th><label for="carrier">Shipping Carriers</label></th>
            <td>
                <input type="checkbox" name="carrier" value="FeDex"> FeDex<br>
                <input type="checkbox" name="carrier" value="UPS"> UPS<br>
                <input type="checkbox" name="carrier" value="USPS"> USPS<br>
            </td>
        </tr>

    </table>
	<?php
}

add_action( 'show_user_profile', 'render_profile_fields' );
add_action( 'edit_user_profile', 'render_profile_fields' );

function save_profile_fields( $id )
{
	if ( ! current_user_can( 'edit_user', $id ) ) {
		return false;
	}
	if ( isset( $_POST['carrier'] ) ) {
		update_user_meta( $id, 'carrier', $_POST['carrier'] );
	}
}

add_action( 'edit_user_profile_update', 'save_profile_fields' );
add_action( 'personal_options_update', 'save_profile_fields' );