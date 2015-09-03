<?php
$img_url = !empty( $_POST['data'] ) ? $_POST['data'] : '';
$maxwidth = !empty( $_POST['maxwidth'] ) && $_POST['maxwidth'] != 'auto' ? (int)$_POST['maxwidth'] : 700;
$export_table_ids = !empty( $_POST['data'] ) ? explode( ',', $_POST['data'] ) : array();
$export_data = !empty( $export_table_ids ) ? GW_GoPricing_Data::export( $export_table_ids ) : '';
?>
<div class="gwa-popup">
	<div class="gwa-popup-inner"<?php echo !empty( $_POST['maxwidth'] ) && $_POST['maxwidth'] != 'auto' ? sprintf( ' style="width:%dpx;"', (int)$_POST['maxwidth'] ) : ''; ?>>
		<div class="gwa-popup-header">
			<div class="gwa-popup-header-icon-export"></div>
			<div class="gwa-popup-title"><?php _e( 'Export', 'go_pricing_textdomain' ); ?><small><?php echo sprintf( __( 'Selected pricing table data (%d)', 'go_pricing_textdomain'), count( $export_table_ids ) ); ?></small></div>
			<a href="#" title="<?php _e( 'Close', 'go_pricing_textdomain' ); ?>" class="gwa-popup-close"></a>
		</div>
		<div class="gwa-popup-content-wrap">
			<div class="gwa-popup-content">	
				<div class="gwa-abox">
					<div class="gwa-abox-content-wrap">
						<div class="gwa-abox-content">
							<table class="gwa-table">
								<tr class="gwa-row-fullwidth">
									<th><label><?php _e( 'Export Data', 'go_pricing_textdomain' ); ?></label></th>
									<td><textarea rows="10"><?php echo esc_textarea( $export_data ); ?></textarea></td>
									<td><p class="gwa-info"><i class="fa fa-info-circle"></i><?php _e( 'Copy the content of the textarea and save into a file on your hard drive.', 'go_pricing_textdomain' ); ?></p></td>									
								</tr>
							</table>			
						</div>
					 </div>
				</div>
			</div>
		</div>
	</div>	
</div>