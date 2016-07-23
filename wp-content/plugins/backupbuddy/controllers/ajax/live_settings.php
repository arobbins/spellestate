<?php
backupbuddy_core::verifyAjaxAccess();
pb_backupbuddy::$ui->ajax_header( $js = true, $padding = true );

require_once( pb_backupbuddy::plugin_path() . '/destinations/bootstrap.php' );
require_once( pb_backupbuddy::plugin_path() . '/destinations/live/live.php' );
require_once( pb_backupbuddy::plugin_path() . '/destinations/live/live_periodic.php' );

$destination_id = backupbuddy_live::getLiveID();
$destination_settings = backupbuddy_live_periodic::get_destination_settings();

echo '<h2>' . __( 'BackupBuddy Stash Live Settings', 'it-l10n-backupbuddy' ) . '</h2>';


// Settings form setup.
$settings_form = pb_backupbuddy_destinations::configure( $destination_settings, $mode = 'edit', $destination_id, $url = pb_backupbuddy::ajax_url( 'live_settings' ) );

// Process saving.
if ( '' != pb_backupbuddy::_POST( 'pb_backupbuddy_' ) ) {
	pb_backupbuddy::verify_nonce();
	$save_result = $settings_form->process();
	if ( count( $save_result['errors'] ) == 0 ) { // NO ERRORS SO SAVE.
		pb_backupbuddy::$options['remote_destinations'][$destination_id] = array_merge( pb_backupbuddy::$options['remote_destinations'][$destination_id], $save_result['data'] );
		pb_backupbuddy::save();
		pb_backupbuddy::alert( __( 'Settings saved. Restarting Live process so they take immediate effect.', 'it-l10n-backupbuddy' ) );
		set_transient( 'backupbuddy_live_jump', array( 'daily_init', array() ), 60*60*48 ); // Tells Live process to restart from the beginning (if mid-process) so new settigns apply.

		// Add final entry to log if disabled
		if ( $destination_settings['disable_logging'] == 0 && $save_result['data']['disable_logging'] == 1 ) {
			$previous_status_serial = pb_backupbuddy::get_status_serial(); // Hold current serial.
			pb_backupbuddy::set_status_serial( 'live_periodic' ); // Redirect logging output to a certain log file.
			pb_backupbuddy::status( 'details', '-----' );
			pb_backupbuddy::status( 'details', 'Logging disabled in Stash Live --> Settings --> Advanced.' );
			pb_backupbuddy::status( 'details', '-----' );
			pb_backupbuddy::set_status_serial( $previous_status_serial );
		}
		
		// Send new settings for archive limiting to Stash API.
		backupbuddy_live::send_trim_settings();
	} else {
		pb_backupbuddy::alert( 'Error saving settings. ' . implode( "\n", $save_result['errors'] ) );
	}
}

// Show settings form.
echo $settings_form->display_settings( 'Save Settings', $before = '', $afterText = ' <img class="pb_backupbuddy_destpicker_saveload" src="' . pb_backupbuddy::plugin_url() . '/images/loading.gif" title="Saving... This may take a few seconds..." style="display: none;">', 'pb_backupbuddy_destpicker_save' ); // title, before, after, class


if ( 'live' == $destination_settings['type'] ) {
	if ( is_multisite() ) {
		$admin_url = network_admin_url( 'admin.php' );
	} else {
		$admin_url = admin_url( 'admin.php' );
	}
	?>
	<a href="<?php echo pb_backupbuddy::nonce_url( $admin_url . '?page=pb_backupbuddy_live&live_action=disconnect' ); ?>" target="_top" style="float:right;margin-top:-3em;color:#f95050;"><?php _e( 'Disconnect from Stash Live', 'it-l10n-backupbuddy' ); ?></a>
	<?php
}


pb_backupbuddy::$ui->ajax_footer( $js_common = true );
