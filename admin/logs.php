<?php

	$api_log_pro = new API_Log_Pro();

	$logs = $api_log_pro->get_logs();

foreach ( $logs as $log ) {
	$obj    = array(
		'id'          => $log->id ?? '',
		'path'        => $log->path ?? '',
		'status'      => $log->status ?? '',
		'method'      => $log->method ?? '',
		'requested_at' => $log->requested_at ?? '',
	);
	$data[] = $obj;
}

	wp_localize_script( 'data-tables', 'logs_data', array( 'data' => $data ) );


echo '<table class="table table-responsive wp-list-table widefat fixed striped" id="logs-table"></table>';

?>

<script>
jQuery(function(){
	var table = jQuery("#logs-table").DataTable({
		data   : logs_data.data,
		columns: [
			{
				data  : 'id',
				title : 'Log ID'
			},
			{
				data  : 'path',
				title : 'Path'
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
			}
		],
		pageLength: 10,
		dom: 'f' + "<'table-responsive't>" + "<'row align-items-center bottom'<'col-sm-5'il><'col-sm-7'p>>",
		language: {
			search: '<i class="fas fa-search"></i>',
			searchPlaceholder: 'Search Logs ...',
			info: '_START_ to _END_ of _TOTAL_',
			infoEmpty: "",
			infoFiltered: "",
			zeroRecords: "<strong>No api logs could be found.</strong>",
			lengthMenu: '_MENU_ logs',
			oPaginate: {
				sNext: 'Next<i class="far fa-arrow-alt-circle-right ml-2"></i>',
				sPrevious: '<i class="far fa-arrow-alt-circle-left mr-2"></i></i>Prev',
			}
		}
	});

});
</script>
