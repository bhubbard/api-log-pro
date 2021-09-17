jQuery(function(){
	var table = jQuery("#logs-table").DataTable({
		data   : logs_data.data,
		responsive: true,
		columns: [
			{
				data  : 'id',
				title : 'Log ID',
				render: function(data, type, row){
					if(type == "sort" || type == "type" || type == "undefined" || type == "filter"){
						return data;
					}

					var s = data;

						s = '<a href="/wp-admin/admin.php?page=apilogpro&tab=incoming&log_id=' + data + '">' + s + '</a>';

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
				title : 'Requested At',
				render: function(data, type, row){
					if(type == "sort" || type == "type" || type == "undefined" || type == "filter"){
						return data;
					}

					var s = data;

						s = data + '<br /><small>' + row.requested_at_diff + '</small>';

					return s;
				}
			},
			{
				data  : 'id',
				title : 'Tools',
				render: function(data, type, row){
					if(type == "sort" || type == "type" || type == "undefined" || type == "filter"){
						return data;
					}

					var s = data;

						s = '<a class="button" href="/wp-admin/admin.php?page=apilogpro&tab=incoming&log_id=' + data + '">View</a>';

					return s;
				}
			}
		],
		pageLength: 10,
		dom: 'f' + "<'table-responsive't>" + "<'row align-items-center bottom'<'col'il><'col'p>>",
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
