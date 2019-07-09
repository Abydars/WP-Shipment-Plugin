<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $current_user, $wpdb;

$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();

$dashboard_customer_view      = $wpsupportplus->functions->is_allow_to_view_customer_dashboard_page();
$dashboard_agent_view         = $wpsupportplus->functions->is_allow_to_view_agent_dashboard_page();
$dashboard_supervisor_view    = $wpsupportplus->functions->is_allow_to_view_supervisor_dashboard_page();
$dashboard_admininstraor_view = $wpsupportplus->functions->is_allow_to_view_administrator_dashboard_page();

$ticket_sections = apply_filters( 'wpsp_tickets_sections', array(
    'ticket-list'     => array(
        'label' => __('Ticket List', 'wp-support-plus-responsive-ticket-system'),
        'path'  => WPSP_ABSPATH . 'template/tickets/ticket-list.php'
    ),
    'create-ticket'     => array(
        'label' => __('Create New Ticket', 'wp-support-plus-responsive-ticket-system'),
        'path'  => WPSP_ABSPATH . 'template/tickets/create-ticket.php'
    ),
		'dashboard'     => array(
        'label' => __('Dashboard', 'wp-support-plus-responsive-ticket-system'),
        'path'  => WPSP_ABSPATH . 'template/tickets/dashboard.php'
    )
));

$ticket_sections = apply_filters( 'wpsp_tickets_sections_setting', $ticket_sections );

if( !$wpsupportplus->functions->is_staff($current_user) && $dashboard_customer_view ) {
		unset($ticket_sections['dashboard']);
}

if($wpsupportplus->functions->is_agent($current_user) && $dashboard_agent_view ){
	unset($ticket_sections['dashboard']);
}

if($wpsupportplus->functions->is_supervisor($current_user) && $dashboard_supervisor_view ){
	unset($ticket_sections['dashboard']);
}

if($wpsupportplus->functions->is_administrator($current_user) && $dashboard_admininstraor_view ){
	unset($ticket_sections['dashboard']);
}

if( $wpsupportplus->functions->is_staff($current_user) ) {
		$ticket_sections['agent-setting']=array(
			'label'=> __('Agent Setting', 'wp-support-plus-responsive-ticket-system'),
			'path'  => WPSP_ABSPATH . 'template/tickets/agent_setting.php'
		);
}

$default_tab = apply_filters( 'wpsp_tickets_default_section', 'ticket-list' );

$current_tab = isset($_REQUEST['section']) ? sanitize_text_field($_REQUEST['section']) : $default_tab;

?>

<nav class="navbar navbar-default secondery-menu hidden-xs wpsp_secondery_menu">
    <div class="container-fluid support-ticket-sub-header">
        <ul class="nav navbar-nav">
            <?php
            foreach($ticket_sections as $key=>$val):
                $tab_class = $key==$current_tab ? 'active' : '' ;
                ?>
                <li role="presentation" class="<?php echo $tab_class?>" id="<?php echo $key;?>">
                    <a href="<?php echo $wpsupportplus->functions->get_support_page_url(array('page'=>'tickets','section'=>$key,'dc'=>time()));?>"><?php echo $val['label']?></a>
                </li>
                <?php
            endforeach;
            if ( $wpsp_user_session ) {
								?>
								<li role="presentation" id="sign-out">
										<a href="<?php echo $wpsupportplus->functions->get_support_page_url(array('page'=>'sign-out','dc'=>time()));?>"><?php _e('Sign Out', 'wp-support-plus-responsive-ticket-system')?></a>
								</li>
								<?php
						}
						?>
        </ul>
    </div>
</nav>

<nav class="nav nav-tabs visible-xs support-ticket-sub-header">
    <?php
    foreach($ticket_sections as $key=>$val):
        $tab_class = $key==$current_tab ? 'active' : '' ;
        ?>
        <li class="nav-item <?php echo $tab_class?>">
            <a class="nav-link" href="<?php echo $wpsupportplus->functions->get_support_page_url(array('page'=>'tickets','section'=>$key));?>"><?php echo $val['label']?></a>
        </li>        
        <?php
    endforeach;
		if ( $wpsp_user_session ) {
				?>
				<li role="presentation">
						<a href="<?php echo $wpsupportplus->functions->get_support_page_url(array('page'=>'sign-out'));?>"><?php _e('Sign Out', 'wp-support-plus-responsive-ticket-system')?></a>
				</li>
				<?php
		}
    ?>
</nav>

<?php
if(isset($ticket_sections[$current_tab]['path'])){
	include $ticket_sections[$current_tab]['path'];
}

