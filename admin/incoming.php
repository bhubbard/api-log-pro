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

	foreach ( $logs as $log ) {
		$data[] = array(
			'id'           => $log->id ?? '',
			'path'         => $log->path ?? '',
			'status'       => $log->status ?? '',
			'method'       => $log->method ?? '',
			'requested_at' => esc_attr( date( 'F j, Y, g:i A T', current_time( strtotime( $log->requested_at ), $gmt_offset ) ) ) ?? '',
			'requested_at_diff' => esc_attr( human_time_diff( current_time( strtotime( $log->requested_at ), $gmt_offset ), current_time( 'timestamp', $gmt_offset ) ) . esc_html( ' ago', 'api-log-pro' ) ) ?? '',
		);
	}

	wp_localize_script( 'data-tables', 'logs_data', array( 'data' => $data ) );

	echo '<p>All logs are kept for 15 days.</p>';
	echo '<table class="logs-table table table-responsive wp-list-table widefat fixed striped display nowrap" id="logs-table" width="100%"></table>';

}
