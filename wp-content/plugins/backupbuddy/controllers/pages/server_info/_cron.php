<?php
// OUTPUT: $crons populated

if ( ! isset( $cron ) ) {
	$cron = get_option('cron');
}
if ( ! isset( $cron_warnings ) ) {
	$cron_warnings = array();
}

// Loop through each cron time to create $crons array for displaying later.
$crons = array();
foreach ( (array) $cron as $time => $cron_item ) {
	if ( is_numeric( $time ) ) {
		// Loop through each schedule for this time
		foreach ( (array) $cron_item as $hook_name => $event ) {
			foreach ( (array) $event as $item_name => $item ) {
				
				// Determine period.
				if ( !empty( $item['schedule'] ) ) { // Recurring schedule.
					$period = '';
					if ( false !== ( $prettyInterval = backupbuddy_core::prettyCronInterval( $item['interval'] ) ) ) {
						$period .= '<span title="Interval tag: `' . $prettyInterval[0] . '`.">' . $prettyInterval[1] . '</span>';
					} else {
						$period .= '<span title="Interval tag: `' . $item['schedule'] . '`.">' . $item['schedule'] . '</span>';
					}
				} else { // One-time only cron.
					$period = __('one time only', 'it-l10n-backupbuddy' );
				}
				
				// Determine interval.
				if ( ! empty( $item['interval'] ) ) {
					$interval = $item['interval'] . ' seconds';
				} else {
					$interval = __('one time only', 'it-l10n-backupbuddy' );
				}
				
				// Determine arguments.
				if ( !empty( $item['args'] ) ) {
					//$arguments = implode( ',', $item['args'] );
					$arguments = '';
					foreach( $item['args'] as $args ) {
						$arguments_inner = array();
						$is_array = false;
						if ( ! is_array( $args ) ) {
							$arguments_inner[] = $args;
						} else {
							$is_array = true;
							foreach( $args as $arg ) {
								if ( is_array( $arg ) ) {
									$arguments_inner[] = print_r( $arg, true );
								} else {
									$arguments_inner[] = $arg;
								}
							}
						}
						if ( true === $is_array ) {
							$arguments_inner = 'Array( ' . implode( ', ', $arguments_inner ) . ' )';
						} else {
							$arguments_inner = implode( ', ', $arguments_inner );
						}
						$arguments .= '<textarea wrap="off">' . $arguments_inner . '</textarea>';
						/*
						if ( is_array( $arg ) ) {
							$arguments .=  '[' . print_r( $arg, true ) . ']';//pb_backupbuddy::$format->multi_implode( $arg , '; ' )
						} else {
							$arguments .= $arg;
						}
						*/
					}
				} else {
					$arguments = __('none', 'it-l10n-backupbuddy' );
				}
				
				// If run time is in the past, note this.
				$past_time = '';
				if ( $time < time() ) {
					$warning = 'WARNING: ' . __( 'Next run time has passed. It should have already run. Cron problem?', 'it-l10n-backupbuddy' );
					$past_time = '<br><span style="color: red;"> ** ' . $warning . ' ** ' . pb_backupbuddy::$ui->tip( 'Something may be wrong with your WordPress cron such as a malfunctioning caching plugin or webhost problems.', '', false ) . '</span>';
					$cron_warnings[] = $warning;
				}
				
				// Populate crons array for displaying later.
				$crons[ $time . '|' . $hook_name . '|' . $item_name] = array(
					'<span title=\'Key: ' . $item_name . '\'>' . $hook_name . '</span>',
					pb_backupbuddy::$format->date( pb_backupbuddy::$format->localize_time( $time ) ) . '<br><span class="description"> Timestamp: ' . $time . '</span>' . $past_time,
					$period,
					$interval,
					$arguments,
				);
				
			} // End foreach.
			unset( $item );
			unset( $item_name );
		} // End foreach.
		unset( $event );
		unset( $hook_name );
	} // End if is_numeric.
} // End foreach.
unset( $cron_item );
unset( $time );