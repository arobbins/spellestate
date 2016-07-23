<?php
if ( ! is_admin() ) { die( 'Access denied' ); }

// Check if running PHP 5.3+.
$php_minimum = 5.3;
if ( version_compare( PHP_VERSION, $php_minimum, '<' ) ) { // Server's PHP is insufficient.
	echo '<br>';
	pb_backupbuddy::alert( '<h3>' . __( 'We have a problem...', 'it-l10n-backupbuddy' ) . '</h3><br>' . __( '<span style="font-size:1.5em;font-weight:bold;">Uh oh!</span><br />BackupBuddy Stash Live requires PHP version 5.3 or newer to run. Please upgrade your PHP version or contact your host for details on upgrading.', 'it-l10n-backupbuddy' ) . ' ' . __( 'Current PHP version', 'it-l10n-backupbuddy' ) . ': ' . PHP_VERSION );
	return;
}

if ( ! function_exists( 'curl_version' ) ) {
	echo '<br>';
	pb_backupbuddy::alert( '<h3>' . __( 'We have a problem...', 'it-l10n-backupbuddy' ) . '</h3><br>' . __( 'BackupBuddy Stash Live requires the PHP "curl" extension to run. Please install or contact your host to install curl. This is a standard extension and should be available on all hosts.', 'it-l10n-backupbuddy' ) );
	return;
}


// No PHP runtime calculated yet. Try to see if test is finished.
if ( 0 == pb_backupbuddy::$options['tested_php_runtime'] ) {
	backupbuddy_core::php_runtime_test_results();
}

$liveDestinationID = false;
foreach( pb_backupbuddy::$options['remote_destinations'] as $destination_id => $destination ) {
	if ( 'live' == $destination['type'] ) {
		$liveDestinationID = $destination_id;
		break;
	}
}


// Handle disconnect.
if ( ( 'disconnect' == pb_backupbuddy::_GET( 'live_action' ) ) && ( false !== $liveDestinationID ) ) { // If disconnecting and not already disconnected.
	$disconnected = false;
	require_once( pb_backupbuddy::plugin_path() . '/destinations/live/live_periodic.php' );
	require_once( pb_backupbuddy::plugin_path() . '/destinations/stash2/class.itx_helper2.php' );
	$destination_settings = backupbuddy_live_periodic::get_destination_settings();
	
	if ( 'yes' == pb_backupbuddy::_POST( 'disconnect' ) ) {
		pb_backupbuddy::verify_nonce();
		
		// Pass itxapi_password to disconnect.
		global $wp_version;
		$password_hash = iThemes_Credentials::get_password_hash( $destination_settings['itxapi_username'], pb_backupbuddy::_POST( 'password' ) );
		$access_token = ITXAPI_Helper2::get_access_token( $destination_settings['itxapi_username'], $password_hash, site_url(), $wp_version );
		$settings = array(
			'itxapi_username' => $destination_settings['itxapi_username'],
			'itxapi_password' => $access_token,
			'itxapi_token' => $destination_settings['itxapi_token'],
		);
		$response = pb_backupbuddy_destination_live::stashAPI( $settings, 'disconnect' );
		
		if ( ! is_array( $response ) ) {
			pb_backupbuddy::alert( 'Error Disconnecting: ' . $response );
		} elseif ( ( ! isset( $response['success'] ) ) || ( '1' != $response['success'] ) ) {
			pb_backupbuddy::alert( 'Error #483948944. Unexpected response disconnecting: `' . print_r( $response, true ) . '`.' );
		} else {
			$disconnected = true;
			
			// Clear destination settings.
			unset( pb_backupbuddy::$options['remote_destinations'][ $liveDestinationID ] );
			pb_backupbuddy::save();
			
			// Clear cached Live credentials.
			require_once( pb_backupbuddy::plugin_path() . '/destinations/live/init.php' );
			delete_transient( pb_backupbuddy_destination_live::LIVE_ACTION_TRANSIENT_NAME );
			
			pb_backupbuddy::disalert( '', 'You have disconnected from Stash Live.' );
			$liveDestinationID = false;
		}
		
	}
	
	// Show authentication form.
	if ( false === $disconnected ) {
		if ( is_multisite() ) {
			$admin_url = network_admin_url( 'admin.php' );
		} else {
			$admin_url = admin_url( 'admin.php' );
		}
		?>
		<h3><?php _e( 'Disconnect from Stash Live', 'it-l10n-backupbuddy' ); ?></h3>
		<?php _e( 'Please authenticate with your iThemes Member Login to validate your access and disconnect this site from Stash Live.', 'it-l10n-backupbuddy' ); ?><br><br>
		<form method="post" action="<?php echo pb_backupbuddy::nonce_url( $admin_url . '?page=pb_backupbuddy_live&live_action=disconnect' ); ?>">
			<input type="hidden" name="disconnect" value="yes">
			<table>
				<tr>
					<td>iThemes Username:</td>
					<td><input type="text" name="username" value="<?php echo $destination_settings['itxapi_username']; ?>" disabled="disabled"></td>
				</tr>
				<tr>
					<td>iThemes Password:</td>
					<td><input type="password" name="password"></td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" name="submit" value="Disconnect Stash Live" class="button-primary">
					</td>
				</tr>
			</table>
		</form>
		<?php
		return;
	}
}



// Show setup screen if not yet set up.
if ( false === $liveDestinationID ) {
	require_once( pb_backupbuddy::plugin_path() . '/destinations/live/_live_setup.php' );
	return;
}



// Load normal manage page.



pb_backupbuddy::$ui->title( __( 'BackupBuddy Stash Live', 'it-l10n-backupbuddy' ) . '&nbsp;&nbsp;<a href="' . pb_backupbuddy::ajax_url( 'live_settings' ) . '&#038;TB_iframe=1&#038;width=640&#038;height=600" class="add-new-h2 thickbox">' . __( 'Settings', 'it-l10n-backupbuddy' ) . '</a>' );

$destination = pb_backupbuddy::$options['remote_destinations'][ $liveDestinationID ];
$destination_id = $liveDestinationID;
require_once( pb_backupbuddy::plugin_path() . '/destinations/live/_manage.php' ); // Expects incoming vars: $destination, $destination_id.


