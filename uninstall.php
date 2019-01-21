<?php
/**
 * API Log Pro Uninstall. Sorry to see you leave.
 *
 * @package api-log-pro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$api_log_pro_db = new API_Log_Pro_DB();


$delete_log_table = $api_log_pro_db->delete_log_db();

$delete_meta_table = $api_log_pro_db->delete_log_meta_table();
