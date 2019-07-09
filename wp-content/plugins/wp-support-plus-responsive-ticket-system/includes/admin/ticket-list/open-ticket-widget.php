<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $wpsupportplus, $wpdb, $value;

add_thickbox();
$default_widget= $wpsupportplus->functions->get_default_ticket_widget();
$results = get_option('wpsp_ticket_widget_order');

$nonce = wp_create_nonce();
?>

<div id="tab_container" style="margin-top: 50px;">
    
    <h1 class="wp-heading-inline"><?php _e('Reorder Ticket Widgets','wp-support-plus-responsive-ticket-system')?></h1>
    
    <form action="" onsubmit="return wpsp_save_ticket_widget(this);">
        
        <table class="wp-list-table widefat fixed striped pages">

            <thead>
                <tr>
                    <th class="column-primary" style="width: 50px;"><?php _e('Order','wp-support-plus-responsive-ticket-system')?></th>
                    <th class="column-primary"><?php _e('Widgets Name','wp-support-plus-responsive-ticket-system')?></th>
                </tr>
            </thead>

            <tbody id="wpsp_sortable">
              <?php
							if (is_array($results) || is_object($results))
              {
								$counter=0;
               foreach ( $results as $field => $value ) {
 		 		       ?>
 		 		            <tr class="sectionsid" id="<?php echo $field?>">
 		 		                <td style="width: 50px;">
 		 		                    <span class="dashicons dashicons-move wpsp_pointer"></span>
 		 		                </td>
 		 										<td>
 		 		                  <?php	echo "{$value}"; ?>
												</td>
 		 		          </tr>
 		 		       <?php
							 $counter++;
 		 		        }
							}
 		 		        ?>
 		 		    </tbody>
 		 		        
 	    </table><br>                        
            <input type="hidden" name="action" value="wpsp_save_ticket_widget" />
            <input type="hidden" name="nonce" value="<?php echo wp_create_nonce('ticket_widget')?>" />

            <button id="wpsp_setting_submit_btn" class="button button-primary"><?php _e('Save Changes','wp-support-plus-responsive-ticket-system')?></button><br><br>

					</form>
					
</div>
<script type="text/javascript">
 jQuery(document).ready(function(){
			wpspjq("#wpsp_sortable").sortable({
		 					items: "tr",
						  cursor: 'move',
						  opacity: 0.6,
						  update: function() {
						                
						  }
			  });
						        
  });
						    
 function validate_wpsp_admin_popup_form(obj){

		   wpsp_save_ticket_widget(obj);
						  return false;
  }
						    
function wpsp_save_ticket_widget(obj){

			wpspjq('#wpsp_setting_submit_btn').text('<?php _e('Please Wait ...','wp-support-plus-responsive-ticket-system')?>');
			var order = wpspjq("#wpsp_sortable").sortable("toArray", { attribute: 'id' });
		 	var dataform = new FormData(obj);
		  dataform.append('load_order', order);
			wpspjq.ajax({
					url: wpsp_admin.ajax_url,
					type: 'POST',
					data: dataform,
					processData: false,
					contentType: false
			})
			.done(function (response) {
					window.location.reload();
			});
			return false;
}
	</script>