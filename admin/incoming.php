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

	wp_enqueue_script( 'logs-datatable' );

	// Get WordPress Time Zone Settings.
	$gmt_offset = get_option( 'gmt_offset' ) ?? 0;


	$api_log_pro = new API_Log_Pro();

	$logs = $api_log_pro->get_logs();

	$data = array();

	if ( 0 != $gmt_offset ) {
		date_default_timezone_set( $gmt_offset );
	}


	foreach ( $logs as $log ) {

		$requested_at = current_time( strtotime( $log->requested_at, $gmt_offset ), $gmt_offset );

		$data[] = array(
			'id'                => $log->id ?? '',
			'path'              => $log->path ?? '',
			'status'            => $log->status ?? '',
			'method'            => $log->method ?? '',
			'requested_at'      => esc_attr( date( 'F j, Y, g:i A', $requested_at ) ) ?? '',
			'requested_at_diff' => esc_attr( human_time_diff( $requested_at, current_time( 'timestamp' ) ) . esc_html( ' ago', 'api-log-pro' ) ) ?? '',
		);
	}

	wp_localize_script( 'data-tables', 'logs_data', array( 'data' => $data ) );

	echo '<p>All logs are kept for 15 days.</p>';
	echo '<table class="logs-table table table-responsive wp-list-table widefat fixed striped display nowrap" id="logs-table" width="100%"></table>';


}
