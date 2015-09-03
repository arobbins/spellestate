<?php

$default_name = NULL;
if ( 'add' == $mode ) {
	$default_name = 'BackupBuddy Live';
}
$settings_form->add_setting( array(
	'type'		=>		'text',
	'name'		=>		'title',
	'title'		=>		__( 'Destination name', 'it-l10n-backupbuddy' ),
	'tip'		=>		__( 'Name of the new destination to create. This is for your convenience only.', 'it-l10n-backupbuddy' ),
	'rules'		=>		'required|string[1-45]',
	'default'	=>		$default_name,
) );


$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'scan_media',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Scan Media Files', 'it-l10n-backupbuddy' ) . '*',
) );
$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'scan_themes',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Scan Theme Files', 'it-l10n-backupbuddy' ) . '*',
) );
$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'scan_plugins',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Scan Plugin Files', 'it-l10n-backupbuddy' ) . '*',
) );


$settings_form->add_setting( array(
	'type'		=>		'title',
	'name'		=>		'advanced_begin',
	'title'		=>		'<span class="dashicons dashicons-arrow-right"></span> ' . __( 'Advanced Options', 'it-l10n-backupbuddy' ),
	'row_class'	=>		'advanced-toggle-title',
) );




$settings_form->add_setting( array(
	'type'		=>		'textarea',
	'name'		=>		'postmeta_key_excludes',
	'title'		=>		__( 'Postmeta Key Exclusions', 'it-l10n-backupbuddy' ),
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'textarea',
	'name'		=>		'options_excludes',
	'title'		=>		__( 'Options Exclusions', 'it-l10n-backupbuddy' ),
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'textarea',
	'name'		=>		'media_excludes',
	'title'		=>		__( 'Media Exclusions', 'it-l10n-backupbuddy' ),
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'textarea',
	'name'		=>		'theme_excludes',
	'title'		=>		__( 'Theme Exclusions', 'it-l10n-backupbuddy' ),
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'textarea',
	'name'		=>		'plugin_excludes',
	'title'		=>		__( 'Plugin Exclusions', 'it-l10n-backupbuddy' ),
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'checkbox',
	'name'		=>		'scan_custom',
	'options'	=>		array( 'unchecked' => '0', 'checked' => '1' ),
	'title'		=>		__( 'Scan Media Files', 'it-l10n-backupbuddy' ) . '*',
	'row_class'	=>		'advanced-toggle',
) );
$settings_form->add_setting( array(
	'type'		=>		'textarea',
	'name'		=>		'custom_includes',
	'title'		=>		__( 'Custom Inclusions', 'it-l10n-backupbuddy' ),
	'row_class'	=>		'advanced-toggle',
) );