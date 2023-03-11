$(document).ready(function() {
	var table = $('#tb_companies').DataTable({
		dom: '<"top"if>rt<"bottom"p><"clear">',
		language: {
			lengthMenu: "_MENU_ " + $("#msg_per_page").val(),
			search: "",
			searchPlaceholder: $("#msg_filter").val(),
			zeroRecords: "<span class='text-danger'>" + $("#msg_no_result").val() + "</span>",
			info: "_START_ - _END_ / _TOTAL_",
			infoEmpty: "0",
			infoFiltered: "(" + $("#msg_of").val() + " _MAX_ " + $("#msg_total").val() + ")",
			paginate: { 
				first: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>', 
				previous: '<i class="fa fa-angle-left" aria-hidden="true"></i>', 
				next: '<i class="fa fa-angle-right" aria-hidden="true"></i>', 
				last: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>'
			}
		},
		order: [[ 2, "asc" ]]
	});
	
	$('.dataTables_wrapper .dataTables_info').addClass("pb-0");
	$('.dataTables_wrapper .dataTables_filter label').addClass("mb-0");
	$('.dataTables_wrapper select').selectpicker();
});