<?php
$user_id = get_current_user_id();
$maxwidth = !empty( $_POST['maxwidth'] ) && $_POST['maxwidth'] != 'auto' ? (int)$_POST['maxwidth'] : 700;
$export_data = !empty( $_POST['data'] ) ? GW_GoPricing_Data::export( $_POST['data'] ) : '';
?>
<div class="gwa-popup"<?php echo !empty( $maxwidth ) ? sprintf( ' style="max-width:%dpx;"', $maxwidth ) : ''; ?>>
	<div class="gwa-popup-inner"<?php echo !empty( $_POST['maxwidth'] ) && $_POST['maxwidth'] != 'auto' ? sprintf( ' style="width:%dpx;"', (int)$_POST['maxwidth'] ) : ''; ?>>
		<div class="gwa-popup-header">
			<div class="gwa-popup-header-icon-preview"></div>
			<div class="gwa-popup-title"><?php _e( 'Live Preview', 'go_pricing_textdomain' ); ?><small><?php echo !empty( $_POST['subtitle'] ) ? $_POST['subtitle'] : ''; ?></small></div>
			<a href="#" title="<?php _e( 'Close', 'go_pricing_textdomain' ); ?>" class="gwa-popup-close"></a>
		</div>
		<div class="gwa-popup-content-wrap">
			<div class="gwa-popup-content">			
				<iframe class="gwa-popup-iframe" src="<?php echo esc_attr_e( add_query_arg( array( 'id' => (int)$_POST['data'],  'nonce' => wp_create_nonce( $this->plugin_base .'-preview' ), 'rnd' => uniqid() ), $this->plugin_url . 'includes/preview.php' ) ) ; ?>"></iframe>
				<div id="go-pricing-export-popup" class="gwa-abox">
					<div class="gwa-abox-content-wrap">
						<div class="gwa-abox-content">
							<table class="gwa-table">
								<tr class="gwa-row-fullwidth">
									<th><label><?php _e( 'Export Data', 'go_pricing_textdomain' ); ?></label><a href="#" title="Close" class="gwa-export-close"></a></th>
									<td><textarea rows="10"><?php echo esc_textarea( $export_data ); ?></textarea></td>
									<td><p class="gwa-info"><i class="fa fa-info-circle"></i><?php _e( 'Copy the content of the textarea and save into a file on your hard drive.', 'go_pricing_textdomain' ); ?></p></td>									
								</tr>
							</table>			
						</div>
					 </div>
				</div>				
			</div>
		</div>
		<div class="gwa-popup-footer">
			<div class="gwa-popup-views">
				<a href="#" data-action="view" data-view="desktop" title="<?php esc_attr_e( 'Desktop View', 'go_pricing_textdomain' ); ?>" class="gwa-popup-view-icon-desktop gwa-current"></a><a href="#" data-action="view" data-view="tablet" title="<?php _e( 'Tablet View', 'go_pricing_textdomain' ); ?>" class="gwa-popup-view-icon-tablet"></a><a href="#" data-action="view" data-view="mobile" title="<?php _e( 'Mobile View', 'go_pricing_textdomain' ); ?>" class="gwa-popup-view-icon-mobile"></a>			
			</div>
			<div class="gwa-popup-assets gwa-fr">
				<?php if ( empty( $noedit ) ) : ?><a href="#" data-action="popup-edit" data-id="<?php echo (int)$_POST['data']; ?>" title="<?php esc_attr_e( 'Edit', 'go_pricing_textdomain' ); ?>" class="gwa-btn-style1"><?php _e( 'Edit', 'go_pricing_textdomain' ); ?></a><?php endif; ?><a href="#" data-action="popup-export" title="<?php esc_attr_e( 'Export', 'go_pricing_textdomain' ); ?>" class="gwa-btn-style2 gwa-ml10"<?php echo empty( $_COOKIE['go_pricing']['settings']['popup'][$user_id] ) || ( !empty( $_COOKIE['go_pricing']['settings']['popup'][$user_id] ) && $_COOKIE['go_pricing']['settings']['popup'][$user_id] != 'mobile' ) ? '' : ' style="display:none;"'; ?>><?php _e( 'Export', 'go_pricing_textdomain' ); ?></a>
			</div>
		</div>
	</div>	
</div>