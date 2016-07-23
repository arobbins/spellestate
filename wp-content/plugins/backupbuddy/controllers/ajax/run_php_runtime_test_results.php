<?php
backupbuddy_core::verifyAjaxAccess();

pb_backupbuddy::load();

if ( false === ( $results = backupbuddy_core::php_runtime_test_results() ) ) {
	$tested_runtime_sofar = '';
	$test_file = backupbuddy_core::getLogDirectory() . 'php_runtime_test.txt'; 
	if ( file_exists( $test_file ) ) {
		if ( false !== ( $tested_runtime = @file_get_contents( $test_file ) ) ) {
			if ( is_numeric( trim( $tested_runtime ) ) ) {
				$tested_runtime_sofar = ' ' . $tested_runtime . ' ' . __( 'secs so far.', 'it-l10n-backupbuddy' );
			}
		}
	}
	
	die( __( 'This may take a few minutes...', 'it-l10n-backupbuddy' ) . $tested_runtime_sofar );
} else {
	die( $results );
}