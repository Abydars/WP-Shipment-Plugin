<?php
if ( ! defined( 'ABSPATH' ) ) {
exit; // Exit if accessed directly
}

global $wpsupportplus, $current_user, $wpdb;
?>

<div class="container">

	  <?php do_action('wpsp_dashboard_before_ticket_status_counts')?>

		<?php

		$statusses = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpsp_custom_status" );
		
		$wpsp_user_session = $wpsupportplus->functions->get_current_user_session();
		$guest_email    = $wpsp_user_session['email'];
		
		$dashbord_general = $wpsupportplus->functions->get_dashbord_general();
		$dash_active_status=$dashbord_general['statuses'];
	  $total_ticket=0;
		
		$staff_to_read_all_ticket=$wpsupportplus->functions->is_allow_staff_read_all_ticket();

	  foreach($statusses as $status){
						if(!in_array($status->id, $dash_active_status)){
						 continue;
						}
						if($wpsupportplus->functions->is_staff($current_user)){
									if(!$staff_to_read_all_ticket){
											//Not Allow Support Staff to read all ticket
										if($wpsupportplus->functions->is_agent($current_user)){
											//agent
											$sql="select count(id) from {$wpdb->prefix}wpsp_ticket where status_id='".$status->id."' AND active=1 AND (assigned_to RLIKE '(^|,)".$current_user->ID."(,|$)' OR created_by=".$current_user->ID.")";

								    }else if($wpsupportplus->functions->is_supervisor($current_user)){
						             //supervisor
												$supervisor_categories = $wpsupportplus->functions->get_supervisor_categories($current_user->ID);

								        $sql="select count(id) from {$wpdb->prefix}wpsp_ticket where
								        status_id='".$status->id."' AND active=1 AND (assigned_to RLIKE '(^|,)".$current_user->ID."(,|$)' OR cat_id IN('". implode(',', $supervisor_categories) ."') OR created_by=".$current_user->ID.")";

								    }else if($wpsupportplus->functions->is_administrator($current_user)){

											  //adminstor
								        $sql="select count(id) from {$wpdb->prefix}wpsp_ticket where status_id='".$status->id."' AND active=1";
									  }
								}else{
										//Allow Support Staff to read all ticket
										$sql="select count(id) from {$wpdb->prefix}wpsp_ticket where status_id='".$status->id."' AND active=1";
								}
						}else{
								//user or guest
								$sql="select count(id) from {$wpdb->prefix}wpsp_ticket where status_id='".$status->id."' AND active=1 AND guest_email='".$guest_email."'";
						}
			      
						$sql=apply_filters('wpsp_dashboard_ticket_count_sql',$sql,$status);
			    	$total_no_tickets = $wpdb->get_var( $sql );
				?>

				<div class="col-md-3 col-sm-3 wpsp_dashboard_status_container">

	          <div class="row">

	              <div class="wpsp_dashboard_status" style="background-color: <?php echo $status->color?>"><?php echo   $total_no_tickets;?></div>

	                  <div class="text-muted">
											<?php $custom_status_localize = get_option('wpsp_custom_status_localize'); ?>
	                    <?php echo '<h4>'.$custom_status_localize['label_'.$status->id].'</h4>'?>
											<span class="text-muted"><?php _e('Tickets','wp-support-plus-responsive-ticket-system');?></span>
	                  </div>
	          </div>

			 </div>

	 <?php
	}
?>
<br><br>
</div>

<?php do_action('wpsp_dashboard_after_ticket_status_counts')?>

<?php
if($wpsupportplus->functions->is_administrator($current_user) || $wpsupportplus->functions->is_supervisor($current_user)){
		
		$language = array("azb","ar","he","fa-IR");
		
		$current_site_language = get_bloginfo("language");
		
		$rtl_css = '';
			
		if (in_array($current_site_language, $language) &&  is_rtl()){
				
				$rtl_css = "direction: rtl;";
					
		}
?>

	<div class="container" style="<?php echo $rtl_css; ?>">

		<h3><?php _e('Ticket Statistics','wp-support-plus-responsive-ticket-system');?></h3>

		<div class="table-responsive">
			
		 <table id="tbl_ticket_stats" class="table table-striped table-bordered" cellspacing="0" width="100%">
		      <thead>

		        <tr>
		            <th><?php _e('Agent','wp-support-plus-responsive-ticket-system');?></th>
		                <?php
		                    foreach($statusses as $status){
		                        if(!in_array($status->id, $dash_active_status)){
		                            continue;
		                        }
		                       ?><th><?php echo $status->name;?></th><?php
		                    }
		                ?>

		            <th><?php _e('Total','wp-support-plus-responsive-ticket-system');?></th>
		       </tr>

		      </thead>

			    <tbody>
			        <tr>
			            <td><?php _e('None','wp-support-plus-responsive-ticket-system');?></td>
			                <?php
			                    foreach($statusses as $status){
			                        if(!in_array($status->id, $dash_active_status)){
			                        continue;
			                        }
														  $sql="select count(id) from {$wpdb->prefix}wpsp_ticket where status_id='".$status->id."'  and assigned_to=0 and active=1";
			                        $total_none_tickets = $wpdb->get_var( $sql );
			                        $total_ticket=$total_ticket+$total_none_tickets;
			                        ?><td><?php echo $total_none_tickets;?></td><?php
			                    }
			                 ?>

			            <td><?php echo $total_ticket?></td>

			        </tr>

			       <?php

		          $agents=array();
		          $agents=$wpdb->get_results("select SQL_CALC_FOUND_ROWS "
		              . "u.ID as id, "
		              . "u.user_login as username, "
		              . "u.display_name as name, "
		              . "u.user_email as email, "
		              . "w.role as role "
		              . "FROM {$wpdb->prefix}wpsp_users w "
		              . "INNER JOIN  {$wpdb->base_prefix}users u ON w.user_id = u.ID " );

			        foreach ($agents as $agent){

			           $total_no_of_ticket=0;
								 echo '<tr>
		                  <td>'.$agent->name.'</td>';

				             foreach($statusses as $status){
				                if(!in_array($status->id, $dash_active_status)){
				                    continue;
				                }

												//administrator/supervisor/agent
												if($wpsupportplus->functions->is_supervisor($current_user))
												{
													$supervisor_categories = $wpsupportplus->functions->get_supervisor_categories($current_user->ID);
                        	$sql="select count(id) from {$wpdb->prefix}wpsp_ticket where
													status_id='".$status->id."' AND active=1 AND (assigned_to RLIKE '(^|,)".$agent->id."(,|$)' AND cat_id IN('". implode(',', $supervisor_categories) ."'))";
                      	}
												else{
													$sql="select count(id) from {$wpdb->prefix}wpsp_ticket where status_id='".$status->id."' AND active=1 AND (assigned_to RLIKE '(^|,)".$agent->id."(,|$)')";
												}												
											
												
                      $sql=apply_filters('wpsp_backend_dashboard_ticket_statistics_sql',$sql,$status);
										  $total_agent_ticket = $wpdb->get_var( $sql );

										  echo '<td>'.$total_agent_ticket.'</td>';
			                $total_no_of_ticket=$total_no_of_ticket+$total_agent_ticket;
				            }

		              echo '<td>'.$total_no_of_ticket.'</td>';
		              echo '</tr>';
								}
			   ?>
			 </tbody>

		 </table>

		</div>
</div>
<?php
}
?>
<?php do_action('wpsp_dashboard_after_ticket_statistics')?>
<link rel="stylesheet" type="text/css" href="<?php echo WPSP_PLUGIN_URL.'asset/library/DataTables/datatables.min.css';?>"/>
<script type="text/javascript" src="<?php echo WPSP_PLUGIN_URL.'asset/library/DataTables/datatables.min.js';?>"></script>
<script>
jQuery(document).ready(function() {
	wpspjq('#tbl_ticket_stats').DataTable();
});
    
</script>
