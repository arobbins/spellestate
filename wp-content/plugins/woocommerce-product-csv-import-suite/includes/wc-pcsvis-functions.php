<?php

/**
 * Read first data row from csv_file and check if it's encoded in specified
 * encoding.
 *
 * @since 1.10.8
 *
 * @param string       $csv_file Path to CSV file
 * @param string|array List of character encoding Encoding order may be specified
 *                     by array or comma separated list string
 *
 * @return string|bool The detected character encoding or FALSE if the encoding
 *                     cannot be detected from the given string
 */
function wc_pcsvis_is_first_row_encoded_in( $csv_file, $encoding ) {
	$handle = fopen( $csv_file, 'r' );

	// Keep reading from the stream until it reaches the end of the line.
	$line = fgets( $handle );
	if ( false === $line ) {
		return false;
	}

	// Second line is the first row.
	$line = fgets( $handle );
	if ( false === $line ) {
		return false;
	}

	fclose( $handle );

	return mb_detect_encoding( $line, $encoding, true );
}
