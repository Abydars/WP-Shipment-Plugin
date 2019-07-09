<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpdb, $wpsupportplus, $current_user;

$action = isset( $_REQUEST['action'] ) ? sanitize_text_field($_REQUEST['action']) : 'ticket-list';
$default_filters = $wpsupportplus->functions->get_default_filters();

$language = array("azb","ar","he","fa-IR");

$current_site_language = get_bloginfo("language");

$rtl_css = '';

if (in_array($current_site_language, $language) &&  is_rtl()){
	
		$rtl_css = "direction: rtl;";
		
}

if( $action == 'ticket-list' ):
    ?>
    <div class="container-fluid" style="<?php echo $rtl_css; ?>">

        <div class="row flex-xl-nowrap">
						<div id="ticket_filter_container" class="col-md-3 hidden-xs hidden-sm sidebar" style="<?php echo ($default_filters['ticket_list_filter']==1) ?'display:inline;':'display:none;'; ?>">
                <?php include WPSP_ABSPATH . 'template/tickets/ticket_list/filter.php'?>
            </div>
					
            <div id="ticket_list_container" class="<?php echo ($default_filters['ticket_list_filter']==1) ?'col-md-9':'col-md-12' ?>">
                <?php include WPSP_ABSPATH . 'template/tickets/ticket_list/list.php'?>
            </div>

        </div>

    </div>
    <?php
endif;


if( $action == 'open-ticket' && isset($_REQUEST['id']) ):

    $ticket_id  = isset($_REQUEST['id']) ? intval(sanitize_text_field($_REQUEST['id'])) : 0 ;
    $ticket = $wpdb->get_row( "select * from {$wpdb->prefix}wpsp_ticket where id=".$ticket_id );
    
    $ticket_list_url = $wpsupportplus->functions->get_support_page_url();
    
    if($wpsupportplus->functions->cu_has_cap_ticket( $ticket, 'read' )){
        
        ?>
        <div class="container-fluid" style="<?php echo $rtl_css; ?>">

            <div class="row flex-xl-nowrap">

                <div class="col-md-9">
                    <?php include WPSP_ABSPATH . 'template/tickets/open-ticket/body.php'?>
                </div>

                <div id="right-sidebar-container" class="col-md-3">
                    <?php include WPSP_ABSPATH . 'template/tickets/open-ticket/sidebar.php'?>
                </div>

            </div>

        </div>
        <?php
        
    } else {
        
        ?>
        <div class="container">

            <div class="col-md-12">
                
                <div class="alert alert-danger" role="alert"> 
                    
                    <h4><?php _e('Unauthorized access!','wp-support-plus-responsive-ticket-system')?></h4>
                    <?php printf(__('Sorry, you do not have permission to view this ticket. <a href="%s" class="alert-link">Click here</a> for ticket list.','wp-support-plus-responsive-ticket-system'), $ticket_list_url)?>
                    
                </div>
                
            </div>

        </div>
        <?php
        
    }
    
endif;

