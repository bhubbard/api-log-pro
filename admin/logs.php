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

	echo '<table class="table table-responsive wp-list-table widefat fixed striped logs-table" id="logs-table"></table>';

	?>

<script>
jQuery(function(){
	var table = jQuery("#logs-table").DataTable({
		data   : logs_data.data,
		columns: [
			{
				data  : 'id',
				title : 'Log ID',
				render: function(data, type, row){
					if(type == "sort" || type == "type" || type == "undefined" || type == "filter"){
						return data;
					}

					var s = data;

						s = '<a href="/wp-admin/admin.php?page=apilogpro&log_id=' + data + '">' + s + '</a>';

					return s;
				}
			},
			{
				data  : 'path',
				title : 'Path',
				render: function(data, type, row){
					if(type == "sort" || type == "type" || type == "undefined" || type == "filter"){
						return data;
					}

					var s = data;

						s = '<a href="/wp-json' + data + '">' + s + '</a>';

					return s;
				}

			},
			{
				data  : 'status',
				title : 'Status'
			},
			{
				data  : 'method',
				title : 'Method'
			},
			{
				data  : 'requested_at',
				title : 'Requested At'
			},
			{
				data  : 'id',
				title : 'Tools',
				render: function(data, type, row){
					if(type == "sort" || type == "type" || type == "undefined" || type == "filter"){
						return data;
					}

					var s = data;

						s = '<a class="button" href="/wp-admin/admin.php?page=apilogpro&log_id=' + data + '">View</a>';

					return s;
				}
			}
		],
		pageLength: 10,
		dom: 'f' + "<'table-responsive't>" + "<'row align-items-center bottom'<'col-sm-5'il><'col-sm-7'p>>",
		language: {
			searchPlaceholder: 'Search Logs ...',
			info: '_START_ to _END_ of _TOTAL_',
			infoEmpty: "",
			infoFiltered: "",
			zeroRecords: "<strong>No api logs could be found.</strong>",
			lengthMenu: '_MENU_ Logs',
		}
	});

});
</script>

<?php } ?>
