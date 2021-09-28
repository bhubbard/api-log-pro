jQuery(function(){
	var table = jQuery("#logs-table").DataTable({
		data   : logs_data.data,
		responsive: true,
		order: [[ 0, "desc" ]],
		createdRow: function (row, data, dataIndex) {
			console.log(data);
			if (data["status"] !== "200") {
    			jQuery(row).addClass('error');
			}
		},
		columnDefs: [
    		{ width: "75px", targets: 0 }
],
		columns: [
			{
				data  : 'id',
				title : 'Log ID',
				render: function(data, type, row){
					if(type == "sort" || type == "type" || type == "undefined" || type == "filter"){
						return data;
					}

					var s = data;
						s = '<a href="/wp-admin/admin.php?page=apilogpro&tab=outgoing&log_id=' + data + '">' + s + '</a>';
					return s;
				}
			},
			{
				data  : 'url',
				title : 'URL',
				render: function(data, type, row){
					if(type == "sort" || type == "type" || type == "undefined" || type == "filter"){
						return data;
					}
					return '<a href="' + data + '" target="_blank">' + data + '</a>';
				}
			},
			{
				data  : 'domain',
				title : 'Domain',
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
				data  : 'runtime',
				title : 'Runtime',
					render: function(data, type, row){
					if(type == "sort" || type == "type" || type == "undefined" || type == "filter"){
						return data;
					}

					var s = data;

					if( data >= 5 ){
						s = '<span style="background-color:red;padding:5px;color:#fff">' + data + '</span>';
					}

					if( data >= 3 && data < 5 ){
						s = '<span style="background-color:yellow;padding:5px;color:#111">' + data + '</span>';
					}


					return s;
				}
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

						s = '<a class="button" href="/wp-admin/admin.php?page=apilogpro&tab=outgoing&log_id=' + data + '">View</a>';

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
			zeroRecords: "<strong>No outgoing api logs could be found.</strong>",
			lengthMenu: '_MENU_ Logs',
		}
	});

});
