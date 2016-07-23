<?php
backupbuddy_core::verifyAjaxAccess();
pb_backupbuddy::$ui->ajax_header( $js = true, $padding = false );

require_once( pb_backupbuddy::plugin_path() . '/destinations/live/live_periodic.php' );
require_once( pb_backupbuddy::plugin_path() . '/destinations/live/live.php' );

$destination_id = backupbuddy_live::getLiveID();
$destination = backupbuddy_live_periodic::get_destination_settings();

$hide_quota = true;
$live_mode = true;
require_once( pb_backupbuddy::plugin_path() . '/destinations/stash2/init.php' );
require_once( pb_backupbuddy::plugin_path() . '/destinations/stash2/_manage.php' );

pb_backupbuddy::$ui->ajax_footer( $js_common = true );