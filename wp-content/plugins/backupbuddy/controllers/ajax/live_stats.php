<?php
backupbuddy_core::verifyAjaxAccess();

// Check if running PHP 5.3+.
$php_minimum = 5.3;
if ( version_compare( PHP_VERSION, $php_minimum, '<' ) ) { // Server's PHP is insufficient.
	die( '-1' );
}

if ( false === ( $stats = backupbuddy_api::getLiveStats() ) ) { // Live is disconnected.
	die( '-1' );
}

echo json_encode( $stats );



// If there is more to do and too long of time has passed since activity then try to jumpstart the process at the beginning.
if ( ( ( 0 == $stats['files_total'] ) || ( $stats['files_sent'] < $stats['files_total'] ) ) && ( 'wait_on_transfers' != $stats['current_function'] ) ) { // ( Files to send not yet calculated OR more remain to send ) AND not on the wait_on_transfers step.
	$time_since_last_activity = microtime( true ) - $stats['last_periodic_activity'];
	
	if ( $time_since_last_activity < 30 ) { // Don't even bother getting max execution time if it's been less than 30 seconds since run.
		// do nothing
	} else { // More than 30 seconds since last activity.
		
		// Detect max PHP execution time. If TESTED value is higher than PHP value then go with that since we want to err on not overlapping processes here.
		$detected_execution = backupbuddy_core::detectLikelyHighestExecutionTime();
		
		if ( $time_since_last_activity > ( $detected_execution + backupbuddy_constants::TIMED_OUT_PROCESS_RESUME_WIGGLE_ROOM ) ) { // Enough time has passed to assume timed out.
			
			require_once( pb_backupbuddy::plugin_path() . '/destinations/live/live.php' );
			if ( false === ( $liveID = backupbuddy_live::getLiveID() ) ) {
				die( '-1' );
			}
			if ( '1' != pb_backupbuddy::$options['remote_destinations'][ $liveID ]['pause_periodic'] ) { // Only proceed if NOT paused.
				
				pb_backupbuddy::status( 'warning', 'BackupBuddy Stash Live process appears timed out while user it viewing Live page. Forcing run now.' );
				
				$cronArgs = array();
				$schedule_result = backupbuddy_core::schedule_single_event( time(), 'live_periodic', $cronArgs );
				if ( true === $schedule_result ) {
					pb_backupbuddy::status( 'details', 'Next Live Periodic chunk step cron event scheduled.' );
				} else {
					pb_backupbuddy::status( 'error', 'Next Live Periodic chunk step cron event FAILED to be scheduled.' );
				}
				if ( '1' != pb_backupbuddy::$options['skip_spawn_cron_call'] ) {
					pb_backupbuddy::status( 'details', 'Spawning cron now.' );
					update_option( '_transient_doing_cron', 0 ); // Prevent cron-blocking for next item.
					spawn_cron( time() + 150 ); // Adds > 60 seconds to get around once per minute cron running limit.
				}
			}
			
		}
	}
	
}



die();