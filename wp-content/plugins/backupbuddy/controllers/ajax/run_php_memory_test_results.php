<?php
backupbuddy_core::verifyAjaxAccess();

pb_backupbuddy::load();

if ( false === ( $results = backupbuddy_core::php_memory_test_results() ) ) {
	$tested_memory_sofar = '';
	$test_file = backupbuddy_core::getLogDirectory() . 'php_memory_test.txt'; 
	if ( file_exists( $test_file ) ) {
		if ( false !== ( $tested_memory = @file_get_contents( $test_file ) ) ) {
			if ( is_numeric( trim( $tested_memory ) ) ) {
				$tested_memory_sofar = ' ' . $tested_memory . ' ' . __( 'MB so far.', 'it-l10n-backupbuddy' );
			}
		}
	}
	
	die( __( 'This may take a few minutes...', 'it-l10n-backupbuddy' ) . $tested_memory_sofar );
} else {
	die( $results );
}