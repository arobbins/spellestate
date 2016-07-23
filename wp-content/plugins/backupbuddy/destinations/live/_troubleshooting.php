<?php
// @since 7.0.3.12
// @author Dustin Bolton

/*
$recent_send_fails = array(
	'file' => '',
	'log_tail' => '',
	'error' => '',
);
*/




// backupbuddy_live_troubleshooting::run();
// $results = backupbuddy_live_troubleshooting::get_raw_results();


class backupbuddy_live_troubleshooting {
	
	private static $_finished = false;
	
	private static $_settings = array(
		'max_notifications' => 10,								// Max number of most recent sync notifications to return.
		'status_log_recent_lines' => 75,						// Max number of most recent lines to get from status log.
		'extraneous_log_recent_lines' => 30,					// Max number of most recent lines to get from extraneous overall status log.
		'send_fail_status_log_recent_lines' => 20,				// Max number of most recent lines to get from a REMOTE TRANSFER status log for Stash Live sends that FAILED.
	);
	
	
	private static $_results = array(
		'bb_version' => '',
		'wp_version' => '',
		'site_url' => '',
		'home_url' => '',
		'start_troubleshooting' => 0,
		'finish_troubleshooting' => 0,
		'gmt_offset' => 0,
		'current_time' => 0,
		'localized_time' => 0,
		'current_timestamp' => 0,
		
		'highlights' => array(),
		'php_notices' => array(),								// Any PHP errors, warnings, notices found in any of the log searched.
		'log_file_info' => array(),								// File sizes, mtime, etc of various logs.
		'bb_notices' => array(),								// Any BackupBuddy errors or warnings logged.
		'recent_waiting_on_files' => array(),					// Recent list of files waiting on as per $files_pending_send_file file contents.
		'recent_waiting_on_files_time' => 0,					// File modified time.
		'recent_waiting_on_files_time_ago' => 0,				// File modified time.
		'recent_waiting_on_tables' => array(),					
		'recent_waiting_on_tables_time' => 0,					// File modified time.
		'recent_waiting_on_tables_time_ago' => 0,				// File modified time.
		'recent_live_send_fails' => array(),					// Remote destination send failures to Live.
		'recent_sync_notifications' => array(),					// Sync Notification errors (live_error).
		'live_status_log_tail' => '',							// Recent Stash Live Status Log.
		'live_stats' => array(),
		'server_stats' => array(),
		'crons' => array(),										// Listing of crons for troubleshooting.
		'extraneous_log_tail' => '',							// Tail end of extraneous log file.
	);
	
	
	
	public static function run() {
		self::$_results['start_troubleshooting'] = microtime( true );
		self::$_results['gmt_offset'] = get_option( 'gmt_offset' );
		self::$_results['current_time'] = pb_backupbuddy::$format->date( time() );
		self::$_results['localized_time'] = pb_backupbuddy::$format->date( pb_backupbuddy::$format->localize_time( time() ) );
		self::$_results['current_timestamp'] = time();
		
		// Recent send fails (remote destination failures).
		// HIGHLIGHTS: PHP errors, send errors, NUMBER of sends failed in X hours
		
		// Populates tail of status log and looks for PHP and BB notices.
		self::_test_site_home_url();
		self::_test_versions();
		self::_test_log_file_info();
		self::_test_status_log();
		self::_test_recent_sync_notifications();
		self::_test_live_state();
		self::_test_server_stats();
		self::_test_cron_scheduled();
		self::_test_recent_live_send_fails();
		self::_test_extraneous_log();
		self::_recent_waiting_on_files_tables();
		
		self::$_results['finish_troubleshooting'] = microtime( true );
		
		self::$_finished = true;
		
	} // End run().
	
	
	
	public static function _test_site_home_url() {
		self::$_results['site_url'] = site_url();
		self::$_results['home_url'] = home_url();
	}
	
	
	
	public static function _test_versions() {
		global $wp_version;
		self::$_results['bb_version'] = pb_backupbuddy::settings( 'version' );
		self::$_results['wp_version'] = $wp_version;
	}
	
	
	
	public static function _test_log_file_info() {
		$log_files = array(
			'files_pending_log' => backupbuddy_core::getLogDirectory() . 'live/files_pending_send-' . pb_backupbuddy::$options['log_serial'] . '.txt',
			'tables_pending_log' => backupbuddy_core::getLogDirectory() . 'live/tables_pending_send-' . pb_backupbuddy::$options['log_serial'] . '.txt',
			'extraneous_log' => backupbuddy_core::getLogDirectory() . 'log-' . pb_backupbuddy::$options['log_serial'] . '.txt',
			'live_log' => backupbuddy_core::getLogDirectory() . 'status-live_periodic_' . pb_backupbuddy::$options['log_serial'] . '.txt',
		);
		
		foreach( $log_files as $title => $log_file ) {
			if ( ! file_exists( $log_file ) ) {
				self::$_results['log_file_info'][ $title ] = '[does not exist]';
				continue;
			}
			
			$mtime = @filemtime( $log_file );
			
			self::$_results['log_file_info'][ $title ] = array(
				'size' => pb_backupbuddy::$format->file_size( @filesize( $log_file ) ),
				'modified' => $mtime,
				'modified_pretty' => pb_backupbuddy::$format->date( $mtime ),
				'modified_ago' => pb_backupbuddy::$format->time_ago( $mtime ),
			);
		}
	} // End _test_log_file_sizes().
	
	
	
	public static function get_raw_results() {
		if ( false === self::$_finished ) {
			return false;
		}
		return self::$_results;
	}
	
	
	
	public static function get_html_results() {
		if ( false === self::$_finished ) {
			return false;
		}
		echo 'TODO #483948434';
	}
	
	
	
	private static function _recent_waiting_on_files_tables() {
		$files_pending_send_file = backupbuddy_core::getLogDirectory() . 'live/files_pending_send-' . pb_backupbuddy::$options['log_serial'] . '.txt';
		$tables_pending_send_file = backupbuddy_core::getLogDirectory() . 'live/tables_pending_send-' . pb_backupbuddy::$options['log_serial'] . '.txt';
		
		if ( file_exists( $files_pending_send_file ) ) {
			if ( false !== ( $files_pending_send = @file_get_contents( $files_pending_send_file ) ) ) {
				self::$_results['recent_waiting_on_files'] = explode( "\n", $files_pending_send );
				self::$_results['recent_waiting_on_files_time'] = @filemtime( $files_pending_send_file );
				self::$_results['recent_waiting_on_files_time_ago'] = pb_backupbuddy::$format->time_ago( self::$_results['recent_waiting_on_files_time'] ) . ' ' . __( 'ago', 'it-l10n-backupbuddy' );
				if ( count( self::$_results['recent_waiting_on_files'] ) > 0 ) {
					self::$_results['highlights'][] = '`' . count( self::$_results['recent_waiting_on_files'] ) . '` total files recently needed waiting on.';
				}
			}
		}
		if ( file_exists( $tables_pending_send_file ) ) {
			if ( false !== ( $tables_pending_send = @file_get_contents( $tables_pending_send_file ) ) ) {
				self::$_results['recent_waiting_on_tables'] = explode( "\n", $tables_pending_send );
				self::$_results['recent_waiting_on_tables_time'] = @filemtime( $tables_pending_send_file );
				self::$_results['recent_waiting_on_tables_time_ago'] = pb_backupbuddy::$format->time_ago( self::$_results['recent_waiting_on_tables_time'] ) . ' ' . __( 'ago', 'it-l10n-backupbuddy' );
				if ( count( self::$_results['recent_waiting_on_tables'] ) > 0 ) {
					self::$_results['highlights'][] = '`' . count( self::$_results['recent_waiting_on_tables'] ) . '` total tables recently needed waiting on.';
				}
			}
		}
	}
	
	private static function _test_extraneous_log() {
		$status_log_file = backupbuddy_core::getLogDirectory() . 'log-' . pb_backupbuddy::$options['log_serial'] . '.txt';
		$status_log_file_contents = file_get_contents( $status_log_file );
		self::_find_notices( $status_log_file_contents, $status_log_file );
		
		// Get tail of status log.
		if ( file_exists( $status_log_file ) ) {
			self::$_results['extraneous_log_tail'] = backupbuddy_core::read_backward_line( $status_log_file, self::$_settings['extraneous_log_recent_lines'] );
		} else {
			self::$_results['extraneous_log_tail'] = '**Log file `' . $status_log_file . '` not found.**';
		}
	}
	
	private static function _test_status_log() {
		$status_log_file = backupbuddy_core::getLogDirectory() . 'status-live_periodic_' . pb_backupbuddy::$options['log_serial'] . '.txt';
		$status_log_file_contents = file_get_contents( $status_log_file );
		self::_find_notices( $status_log_file_contents, $status_log_file );
		
		// Get tail of status log.
		if ( file_exists( $status_log_file ) ) {
			self::$_results['live_status_log_tail'] = backupbuddy_core::read_backward_line( $status_log_file, self::$_settings['status_log_recent_lines'] );
		} else {
			self::$_results['extraneous_log_tail'] = '**Log file `' . $status_log_file . '` not found.**';
		}
	}
	
	
	
	private static function _test_recent_sync_notifications() {
		$notifications = backupbuddy_core::getNotifications();
		
		// Remove non-Stash Live notifications.
		foreach( $notifications as $key => $notification ) {
			if ( 'live_error' != $notification['slug'] ) {
				unset( $notifications[ $key ] );
				continue;
			}
		}
		
		if ( count( $notifications ) > 0 ) {
			self::$_results['highlights'][] = '`' . count( $notifications ) . '` recent Stash Live Sync error notifications.';
		}
		
		// Limit to X number of most recent notifications.
		self::$_results['recent_sync_notifications'] = array_slice( $notifications, -1 * ( self::$_settings['max_notifications'] ), self::$_settings['max_notifications'], $preserve_key = false );
		
	}
	
	
	
	private static function _test_live_state() {
		self::$_results['live_stats'] = backupbuddy_api::getLiveStats();
	}
	
	
	
	private static function _test_server_stats() {
		require( pb_backupbuddy::plugin_path() . '/controllers/pages/server_info/_server_tests.php' ); // Populates $tests.
		self::$_results['server_stats'] = $tests;
		foreach( self::$_results['server_stats'] as &$stat ) {
			$stat = self::_strip_tags_content( $stat );
			if ( ( 'FAIL' == $stat['status'] ) || ( 'WARNING' == $stat['status'] ) ) {
				self::$_results['highlights'][] = $stat;
			}
		}
	}
	
	
	
	private static function _test_cron_scheduled() {
		$cron_warnings = array();
		require( pb_backupbuddy::plugin_path() . '/controllers/pages/server_info/_cron.php' );
		self::$_results['crons'] = self::_strip_tags_content( $crons );
		if ( count( $cron_warnings ) > 0 ) {
			self::$_results['highlights'][] = count( $cron_warnings ) . ' cron(s) warnings were found (such as past due; see cron section for details): ' . implode( '; ', array_unique( $cron_warnings ) );
		}
	}
	
	
	
	private static function _test_recent_live_send_fails() {
		$troubleshooting = true; // Tell _remote_sends.php to run in troubleshooting/text mode.
		require( pb_backupbuddy::plugin_path() . '/controllers/pages/server_info/_remote_sends.php' ); // Populates $sends.
		self::$_results['recent_live_send_fails'] = $sends;
		foreach( self::$_results['recent_live_send_fails'] as $key => &$send ) {
			
			// Only include Live sends.
			if ( 'live' != $send['type'] ) {
				unset( self::$_results['recent_live_send_fails'][ $key ] );
				continue;
			}
			
			// Only include FAILED sends.
			if ( true !== $send['failed'] ) {
				unset( self::$_results['recent_live_send_fails'][ $key ] );
				continue;
			}
			
			// If error message, set as highlight.
			if ( '' != $send['error'] ) {
				self::$_results['highlights'][] = array(
					'error' => 'File send Error #4389844959:' . $send['error'],
					'send_details' => $send
				);
			}
			
			if ( file_exists( $send['log_file'] ) ) {
				$send['log_tail'] = backupbuddy_core::read_backward_line( $send['log_file'], self::$_settings['send_fail_status_log_recent_lines'] );
			} else {
				self::$_results['extraneous_log_tail'] = '**Log file `' . $send['log_file'] . '` not found.**';
			}
		}
		
		if ( count( self::$_results['recent_live_send_fails'] ) > 0 ) {
			self::$_results['highlights'][] = count( self::$_results['recent_live_send_fails'] ) . ' total files pending send before Snapshot can be made.';
		}
	}
	
	
	
	/* _find_notices()
	 *
	 * Finds any PHP errors, warnings, notices + BackupBuddy errors and warnings in a log file.
	 *
	 * @param	string	$log	Newline-deliminated log file.
	 *
	 */
	private static function _find_notices( $log, $log_file ) {
		$php_notices = array();
		$bb_notices = array();
		
		$newProcessStarting = false;
		$prevLine = '';
		$lastMem = 0;
		$recordNextLines = 0;
		
		$separator = "\r\n";
		$line = strtok( $log, $separator ); // Attribution: http://stackoverflow.com/questions/1462720/iterate-over-each-line-in-a-string-in-php
		while ($line !== false) {
			# do something with $line
			$line = strtok( $separator );
			
			if ( $recordNextLines > 0 ) {
				$php_notices[] = $line;
				$recordNextLines--;
			}
			
			if ( true === $newProcessStarting ) {
				if ( false !== stripos( $line, 'possible_timeout' ) ) {
					self::$_results['highlights'][] = 'Possible timeout or memory ceiling (peak before new process: `' . $lastMem . '`) detected in `' . $log_file . '`. Pre-timeout line: `' . $preTimeoutLine . '`. Post-timeout line: `' . $prevLine . '`.';
					$newProcessStarting = false;
				}
			}
			
			if ( false !== strpos( $line, '"-----"' ) ) {
				$newProcessStarting = true;
				// Get memory value from previous line.
				if ( null != ( $line_array = json_decode( trim( $prevLine ), $assoc = true ) ) ) {
					if ( isset( $line_array[ 'mem' ] ) ) {
						$lastMem = $line_array[ 'mem' ];
						$preTimeoutLine = $prevLine;
					}
				}
			
			// BackupBuddy Error #
			} elseif ( false !== stripos( $line, 'Error #' ) ) {
				$bb_notices[] = $line;
			
			// BackupBuddy Warning #
			} elseif ( false !== stripos( $line, 'Warning #' ) ) {
				$bb_notices[] = $line;
			
			} elseif ( false !== stripos( $line, 'Fatal PHP error encountered:' ) ) { // BB-prefix.
				$php_notices[] = $line;
				$recordNextLines = 5; // Record the next 5 lines after this so we get the actual error.
			
			// fatal PHP error
			} elseif ( false !== stripos( $line, 'Fatal error:' ) ) {
				$php_notices[] = $line;
			
			// PHP parse error
			} elseif ( false !== stripos( $line, 'Parse error:' ) ) {
				$php_notices[] = $line;
			
			// out of memory error
			} elseif ( false !== stripos( $line, 'allowed memory size of' ) ) {
				$php_notices[] = $line;
			}
			
			
			$prevLine = $line;
		}
		
		
		if ( ( count( $php_notices ) > 0 ) || ( count( $bb_notices ) > 0 ) ) {
			self::$_results['highlights'][] = 'Detected `' . count( $php_notices ) . '` possible PHP notices and `' . count( $bb_notices ) . '` possible BackupBuddy notices in log `' . $log_file . '`. See php_notices or bb_notices section for details.';
		}
		
		self::$_results['php_notices'] = array_merge( self::$_results['php_notices'], $php_notices );
		self::$_results['bb_notices'] = array_merge( self::$_results['bb_notices'], $bb_notices );
		
		return array( $php_notices, $bb_notices );
	}
	
	
	public static function _strip_tags_content( $array ) {
		foreach( $array as &$array_contents ) {
			if ( is_array( $array_contents ) ) {
				$array_contents = self::_strip_tags_content( $array_contents );
			} else {
				$array_contents = strip_tags( $array_contents );
			}
		}
		return $array;
	} 
	
	
} // End class.

