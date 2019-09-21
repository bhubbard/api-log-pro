<?php
/**
 * API Log Pro Admin - Logs Archive.
 *
 * @package api-log-pro
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$log_id = filter_input( INPUT_GET, 'log_id' ) ?? null;

if ( ! empty( $log_id ) || null !== $log_id ) {
	include_once 'details.php';
} else {


	$api_log_pro = new API_Log_Pro();

	$logs = $api_log_pro->get_logs();

	$data = array();

	foreach ( $logs as $log ) {
		$data[] = array(
			'id'           => $log->id ?? '',
			'path'         => $log->path ?? '',
			'status'       => $log->status ?? '',
			'method'       => $log->method ?? '',
			'requested_at' => $log->requested_at ?? '',
		);
	}

	wp_localize_script( 'data-tables', 'logs_data', array( 'data' => $data ) );

	echo '<table class="logs-table table table-responsive wp-list-table widefat fixed striped display nowrap" id="logs-table" width="100%"></table>';

}
