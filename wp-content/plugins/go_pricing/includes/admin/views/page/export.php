<?php 
/**
 * Import & Export Page - Export View
 */


// Prevent direct call
if ( !defined( 'WPINC' ) ) die;
if ( !class_exists( 'GW_GoPricing' ) ) die;	

// Get current user id
$user_id = get_current_user_id();

// Get general settings
$general_settings = get_option( self::$plugin_prefix . '_table_settings' );

// Get temporary postdata
$data = $this->get_temp_postdata();
$this->delete_temp_postdata();

// Get tables data
if ( $data === false ) return;
$data = $data == 'all' ? array() : $data;
$db_data = GW_GoPricing_Data::export( $data );

?>
<!-- Top Bar -->
<div class="gwa-ptopbar">
	<div class="gwa-ptopbar-icon"></div>
	<div class="gwa-ptopbar-title">Go Pricing</div>
	<div class="gwa-ptopbar-content"><label><span class="gwa-label"><?php _e( 'Help', 'go_pricing_textdomain' ); ?></span><select data-action="help" class="gwa-w80"><option value="1"<?php echo isset( $_COOKIE['go_pricing']['settings']['help'][$user_id] ) && $_COOKIE['go_pricing']['settings']['help'][$user_id] == 1 ? ' selected="selected"' : ''; ?>><?php _e( 'Tooltip', 'go_pricing_textdomain' ); ?></option><option value="2"<?php echo isset( $_COOKIE['go_pricing']['settings']['help'][$user_id] ) && $_COOKIE['go_pricing']['settings']['help'][$user_id] == 2 ? ' selected="selected"' : ''; ?>><?php _e( 'Show', 'go_pricing_textdomain' ); ?></option><option value="0"<?php echo isset( $_COOKIE['go_pricing']['settings']['help'][$user_id] ) && $_COOKIE['go_pricing']['settings']['help'][$user_id] == 0 ? ' selected="selected"' : ''; ?>><?php _e( 'None', 'go_pricing_textdomain' ); ?></option></select></label><a href="<?php echo esc_attr( admin_url( 'admin.php?page=go-pricing' ) ); ?>" title="<?php esc_attr_e( 'Go to Dashboard', 'go_pricing_textdomain' ); ?>" class="gwa-btn-style1 gwa-ml20"><?php _e( 'Go to Dashboard', 'go_pricing_textdomain' ); ?></a></div>
</div>
<!-- /Top Bar -->

<!-- Page Content -->
<div class="gwa-pcontent" data-ajax="<?php echo esc_attr( isset( $general_settings['admin']['ajax'] ) ? "true" : "false" ); ?>" data-help="<?php echo esc_attr( isset( $_COOKIE['go_pricing']['settings']['help'][$user_id] ) ? $_COOKIE['go_pricing']['settings']['help'][$user_id] : '' ); ?>">

	<!-- Admin Box -->
	<div class="gwa-abox">
		<div class="gwa-abox-header">
			<div class="gwa-abox-header-icon"><i class="fa fa-database"></i></div>
			<div class="gwa-abox-title"><?php _e( 'Export', 'go_pricing_textdomain' ); ?></div>
			<div class="gwa-abox-ctrl"></div>
		</div>
		<div class="gwa-abox-content-wrap">
			<div class="gwa-abox-content">
				<table class="gwa-table">
					<tr class="gwa-row-fullwidth">
						<th><label><?php _e( 'Export Data', 'go_pricing_textdomain' ); ?></label></strong></th>
						<td><textarea rows="10"><?php echo  !empty( $db_data ) ? esc_textarea( $db_data ) : ''; ?></textarea></td>
						<td class="gwa-abox-info"><p class="gwa-info"><i class="fa fa-info-circle"></i><?php _e( 'Copy the content of the textarea and save into a file on your hard drive.', 'go_pricing_textdomain' ); ?></p></td>									
					</tr>
				</table>			
			</div>
		 </div>
	</div>
	<!-- /Admin Box -->
	
	<!-- Submit -->
	<div class="gwa-submit"><a href="<?php echo esc_attr( admin_url( 'admin.php?page=go-pricing' ) ); ?>" title="<?php esc_attr_e( 'Go to Dashboard', 'go_pricing_textdomain' ); ?>" class="gwa-btn-style1"><?php _e( 'Go to Dashboard', 'go_pricing_textdomain' ); ?></a></div>
	<!-- /Submit -->

</div>
<!-- /Page Content -->