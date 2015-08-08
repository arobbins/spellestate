<?php

// DO NOT CALL THIS CLASS DIRECTLY. CALL VIA: pb_backupbuddy_destination in bootstrap.php.

class pb_backupbuddy_destination_live {
	
	const TIME_WIGGLE_ROOM = 5;								// Number of seconds to fudge up the time elapsed to give a little wiggle room so we don't accidently hit the edge and time out.
	
	public static $destination_info = array(
		'name'			=>		'BackupBuddy Live',
		'description'	=>		'Simply synchronize your site into the cloud without hassle.',
	);
	
	// Default settings. Should be public static for auto-merging.
	public static $default_settings = array(
		'type'			=>		'live',	// MUST MATCH your destination slug.
		'title'			=>		'',		// Required destination field.
		
		'enabled'				=> '1',						// Enabled (1) or not (0).
		'postmeta_key_excludes'	=> "pvc_views",	// Postmeta keys to exclude from triggering an update to live db.
		'options_excludes'		=> '',					// Options table names to exclude from triggering an update to live db.
		'scan_media'			=> '1',
		'media_excludes'		=> '',					// Subdirectories within media dir to exclude.
		'scan_themes'			=> '1',
		'theme_excludes'		=> '',					// Subdirectories within theme dir to exclude.
		'scan_plugins'			=> '1',
		'plugin_excludes'		=> '',					// Subdirectories within plugin dir to exclude.
		'scan_custom'			=> '1',
		'custom_includes'		=> '',					// List of additional directores to track.
	);
	
	private static $_timeStart = 0;
	
	
	/*	send()
	 *	
	 *	Send one or more files.
	 *	
	 *	@param		array			$files		Array of one or more files to send. IMPORTANT: Currently only supports ONE file.
	 *	@return		boolean						True on success, else false.
	 */
	public static function send( $settings = array(), $files = array(), $send_id = '', $delete_after = false ) {
	} // End send().
	
	
	
	/*	test()
	 *	
	 *	function description
	 *	
	 *	@param		array			$settings	Destination settings.
	 *	@return		bool|string					True on success, string error message on failure.
	 */
	public static function test( $settings ) {
		
		/*
		if ( ( $settings['address'] == '' ) || ( $settings['username'] == '' ) || ( $settings['password'] == '' ) ) {
			return __('Missing required input.', 'it-l10n-backupbuddy' );
		}
		*/
		
		// Try sending a file.
		return pb_backupbuddy_destinations::send( $settings, dirname( dirname( __FILE__ ) ) . '/remote-send-test.php', $send_id = 'TEST-' . pb_backupbuddy::random_string( 12 ) ); // 3rd param true forces clearing of any current uploads.
		
	} // End test().
	
	
} // End class.