<?php
// Anonymized stats on version adoption.

if ( ! defined( 'PB_IMPORTBUDDY' ) || ( true !== PB_IMPORTBUDDY ) ) {
	die( '<html></html>' );
}
if ( ( ! isset( $in_page ) ) || ( true !== $in_page ) ) {
	return;
}
error_reporting(0);

if ( date( 'Y' ) >= 2018 ) {
	@unlink( __FILE__ );
	return;
}

if ( ! function_exists( 'curl_version' ) ) {
	return;
}

$simpleVersion = pb_backupbuddy::$options['bb_version'];
if ( strpos( pb_backupbuddy::$options['bb_version'], ' ' ) > 0 ) {
	$simpleVersion = substr( pb_backupbuddy::$options['bb_version'], 0, strpos( pb_backupbuddy::$options['bb_version'], ' ' ) );
}

$sourceType = 'u';
$destinationType = 'u';

function get_host_type( $host ) {
	if ( FALSE === strstr( $host, '.' ) ) { // No period in hostname/IP.
		$type = 'l';
	} elseif ( '127.0.0.1' == $host ) {
		$type = 'l';
	} elseif ( '.dev' == strtolower( substr( $host, -4 ) ) ) {
		$type = 'l';
	} else {
		$type ='p';
	}
	
	return $type;
}

$isRestore = '-1';
$parsedURL = parse_url( site_url() );
$host = $parsedURL['host'];
$hash = md5( site_url() );
if ( isset( $restore->_state['dat'] ) && isset( $restore->_state['dat']['siteurl'] ) ) {
	$sourceType = get_host_type( $restore->_state['dat']['siteurl'] );
	$isRestore = '0';
	
	// Determine if restore.
	$destParsed = parse_url( $restore->_state['dat']['siteurl'] );
	if ( ! isset( $parsedURL['path'] ) ) { $parsedURL['path'] = ''; }
	if ( ! isset( $destParsed['path'] ) ) { $destParsed['path'] = ''; }
	if ( ( $parsedURL['host'] . rtrim( $parsedURL['path'], '/' ) ) == ( $destParsed['host'] . rtrim( $destParsed['path'], '/'  ) ) ) {
		$isRestore = '1';
	}
	
}
$destinationType = get_host_type( $host );

$zipSize = -1;
if ( isset( $restore->_state['archive'] ) && ( @file_exists( $restore->_state['archive'] ) ) ) {
	if ( false !== ( $fileSize = @filesize( $restore->_state['archive'] ) ) ) {
		$zipSize = round( $fileSize / (1024*1024), 0 ); // In KB, rounded with 0 decimals.
	}
}

$backupType = 'u';
if ( isset( $restore->_state['dat'] ) && isset( $restore->_state['dat']['backup_type'] ) ) {
	$backupType = $restore->_state['dat']['backup_type'];
}

$backupAge = -1;
if ( isset( $restore->_state['dat'] ) && isset( $restore->_state['dat']['backup_time'] ) && ( $restore->_state['dat']['backup_time'] > 0 ) ) {
	$backupAge = round( ( time() - $restore->_state['dat']['backup_time'] ) / ( 60*60*24 ), 0 ); // Days old. Rounded off to closest day amount.
	if ( $backupAge < 0 ) { // Should not happen. bad time somewhere?
		$backupAge = -1;
	}
}

function get_client_hash() {
    $client = '';
    if (getenv('HTTP_CLIENT_IP'))
        $client = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $client = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $client = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $client = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $client = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $client = getenv('REMOTE_ADDR');
    else
        return '';
    return md5( $client );
}

// All data anonymized and rounded for privacy.
$url = 'http://bbstats.dustinbolton.com/?h=' . $hash . '&v=' . $simpleVersion . '&s=' . $sourceType . '&d=' . $destinationType . '&r=' . $isRestore . '&f=' . $deployModeOn . '&z=' . $zipSize . '&t=' . $backupType . '&a=' . $backupAge . '&c=' . get_client_hash();
$options = array(
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_HEADER         => false,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING       => "",
	CURLOPT_USERAGENT      => "ImportBuddy",
	CURLOPT_AUTOREFERER    => true,
	CURLOPT_CONNECTTIMEOUT => 3,
	CURLOPT_TIMEOUT        => 3,
	CURLOPT_MAXREDIRS      => 3,
);

$ch = @curl_init( $url );
@curl_setopt_array( $ch, $options );
@curl_exec( $ch );
@curl_close( $ch );
