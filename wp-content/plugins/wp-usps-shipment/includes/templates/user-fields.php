<h3><?php _e( "USPS Details", "blank" ); ?></h3>
<table class="form-table">
	<tr>
		<th><label><?php _e( "Customer ID" ); ?></label></th>
		<td>
			<input type="text" name="usps_customer_id" id="usps_customer_id"
			       value="<?php echo esc_attr( WPSP_USPS_UserMeta::get_field( $user->ID, 'usps_customer_id' ) ); ?>"
			       class="regular-text"/><br/>
		</td>
	</tr>
</table>