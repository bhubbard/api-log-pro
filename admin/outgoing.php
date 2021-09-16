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
	include_once 'details-outgoing.php';
} else {

	wp_enqueue_script( 'logs-datatable-outgoing' );


	$api_log_pro = new API_Log_Pro_Outgoing();

	$logs = $api_log_pro->get_logs();

	$data = array();

	foreach ( $logs as $log ) {

		$data[] = array(
			'id'           => $log->id ?? '',
			'url'          => $log->url ?? '',
			'domain'       => $log->domain ?? '',
			'status'       => $log->status ?? '',
			'method'       => $log->method ?? '',
			'runtime'      => $log->runtime ?? '',
			'requested_at' => $log->requested_at ?? '',
		);
	}

	wp_localize_script( 'data-tables', 'logs_data', array( 'data' => $data ) );

	echo '<table class="logs-table table table-responsive wp-list-table widefat fixed striped display nowrap" id="logs-table" width="100%"></table>';

}
