<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$no_of_steps = 5;

$installation_step = get_option( 'wpsp_installation' );
$installation_step = $installation_step ? $installation_step : 0;
$installation_step++;

switch ($installation_step){

    case 1:

        ?>
        <div class="wpsp_installation_container">

            <h3>Step <?php echo $installation_step?> of 2: Set Support Page</h3>

            <div class="wpsp_upgrade_terms_container" style="margin-bottom: 20px;">

                <strong>Support Page</strong><br>
                You must need to create new page for support functionality and select this in general setting. There is need to insert <b>[wp_support_plus]</b>
                shortcode on this page. If you do not set support page in general setting, you won't be able to see your tickets.
                From version 9.0.0, we have moved all ticketing functionality to front-end and that is why page is needed to set.<br><br>

                <table class="form-table">
                    <tr>
                        <th scope="row">Select Page :</th>
                        <td>
                            <select id="wpsp_support_page_id">

                                <option value=""></option>

                                <?php
                                foreach ( $wpsupportplus->functions->get_wp_page_list() as $page ) :

                                    $selected = $wpsupportplus->functions->get_support_page_id() == $page->ID ? 'selected="selected"' : '';
                                    echo '<option '.$selected.' value="'.$page->ID.'">'.$page->post_title.'</option>';

                                endforeach;
                                ?>

                            </select><br>

                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align: left;">
                            OR
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">Create Page :</th>
                        <td>
                            <input type="text" id="wpsp_support_page_title" placeholder="Title" value="Support">
                            <button onclick="wpsp_create_support_page('<?php echo wp_create_nonce($current_user->ID)?>');">Submit</button>
                        </td>
                        <td></td>
                    </tr>
                </table>

            </div>

            <button onclick="wpsp_select_support_page('<?php echo wp_create_nonce($current_user->ID)?>');" class="button button-primary" style="float: right;">Confirm</button>

        </div>
        <?php
        break;

    case 2:

        ?>
        <div class="wpsp_installation_container">

            <h3>Step <?php echo $installation_step?> of <?php echo $no_of_steps?>: Setup General Settings</h3>

            <div class="wpsp_upgrade_terms_container" style="margin-bottom: 20px;">
                We've already set default settings but to customize it as per your need, please go through General Settings.<br><br>

                <strong>Categories</strong><br>
                You can customize categories as per your need. You can set supervisor for category. We'll cover what supervisor role does in later part of this installation. For now, you can leave supervisor while creating category.<br><br>

                <strong>Statuses</strong><br>
                Status indicates current status label for ticket. We've set some default values, but you can customize it according to your need.<br><br>

                <strong>Priority</strong><br>
                As name suggest, it is label for priority of ticket. We've set some default values, but you can customize it according to your need.<br><br>

            </div>

            <button onclick="wpsp_installation_next(<?php echo $installation_step?>,'<?php echo wp_create_nonce($current_user->ID)?>');" class="button button-primary" style="float: right;"><?php _e('Next','wp-support-plus-responsive-ticket-system')?></button>

        </div>
        <?php
        break;

    case 3:

        ?>
        <div class="wpsp_installation_container">

            <h3>Step <?php echo $installation_step?> of <?php echo $no_of_steps?>: Setup Ticket Form</h3>

            <div class="wpsp_upgrade_terms_container" style="margin-bottom: 20px;">
                We've already set default settings but to customize it as per your need, please go through Ticket Form Settings.<br><br>
                You can create custom fields of available custom field types here.
            </div>

            <button onclick="wpsp_installation_next(<?php echo $installation_step?>,'<?php echo wp_create_nonce($current_user->ID)?>');" class="button button-primary" style="float: right;">Next</button>

        </div>
        <?php
        break;

    case 4:

        ?>
        <div class="wpsp_installation_container">

            <h3>Step <?php echo $installation_step?> of <?php echo $no_of_steps?>: Setup Ticket List</h3>

            <div class="wpsp_upgrade_terms_container" style="margin-bottom: 20px;">
                We've already set default settings but to customize it as per your need, please go through Ticket List Settings.<br><br>
            </div>

            <button onclick="wpsp_installation_next(<?php echo htmlentities($installation_step)?>,'<?php echo wp_create_nonce($current_user->ID)?>');" class="button button-primary" style="float: right;">Next</button>

        </div>
        <?php
        break;

    case 5:

        ?>
        <div class="wpsp_installation_container">

            <h3>Step 2 of 2: Setup Agents</h3>

            <div class="wpsp_upgrade_terms_container" style="margin-bottom: 20px;">

                Agents are your support staff persons who reply ticket for you. For better management we've divided them into roles:<br><br>

                <strong>Administrator</strong><br>
                This is not necessarily your WordPress administrator but you can set any user as your support plus administrator. He has access to all tickets and can perform any action on tickets.<br><br>

                <strong>Supervisor</strong><br>
                Supervisor is category supervisor ( you can edit category to set supervisors ) who can assign tickets of his category to agents. He has access to below tickets:<br>
                <ol>
                    <li>Tickets of categories where he is supervisor</li>
                    <li>Tickets assigned to him</li>
                    <li>Tickets created by him</li>
                </ol>

                <strong>Agent</strong><br>
                Agent is user to whom tickets can be assigned to reply. He has access to below tickets:<br>
                <ol>
                    <li>Tickets assigned to him</li>
                    <li>Tickets created by him</li>
                </ol>

            </div>

            <button onclick="wpsp_installation_next(<?php echo $installation_step?>,'<?php echo wp_create_nonce($current_user->ID)?>');" class="button button-primary" style="float: right;">Finish & View Support Page</button>

        </div>
        <?php
        break;

    default :

        ?>
        <script>
            jQuery(document).ready(function(){
                window.location.href = "<?php echo $wpsupportplus->functions->get_support_page_url();?>";
            });
        </script>
        <?php


}
?>

<div id="wpsp_wait_html" style="display: none;">
    <div class="wpsp_filter_loading_icon" style="margin-top: 190px;">
        <img src="<?php echo WPSP_PLUGIN_URL.'asset/images/ajax-loader@2x.gif'?>">
    </div>
</div>
