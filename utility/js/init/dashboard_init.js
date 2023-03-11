$(document).ready(function() {
	$("#btn_favorite_view_all").on('click',(function(e) {
		$("#tb_favorites tr").removeClass("d-none");
		$("#btn_favorite_view_all").remove();
	}));
	
	$("#btn_activate_free").on('click',(function(e) {
		$("#ic_activate_free").removeClass("d-none");
		$("#btn_activate_free").addClass("d-none");
		$.ajax({
			url: "/subscription/gift",
			type: "POST",
			success:function(res){
				if (res.status == true) document.location.reload();
				else{
					$("#ic_activate_free").addClass("d-none");
					$("#btn_activate_free").removeClass("d-none");
					sweet_error(res.msg);
				}
			}
		});
	}));
	
	
	var table = $('#tb_market').DataTable({
		dom: '<"top"f>rt<"bottom"ip><"clear">',
		language: {
			search: "",
			searchPlaceholder: $("#filter").val(),
			zeroRecords: "<span class='text-danger'>" + $("#error_no_result_movement").val() + "</span>",
			info: "_START_ - _END_ / _TOTAL_",
			infoEmpty: "0",
			infoFiltered: "/ " + $("#total").val() + ": _MAX_",
			paginate: { 
				first: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>', 
				previous: '<i class="fa fa-angle-left" aria-hidden="true"></i>', 
				next: '<i class="fa fa-angle-right" aria-hidden="true"></i>', 
				last: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>'
			}
		},
		order: [[ 7, "desc" ]]
	});

	$('.dataTables_wrapper .dataTables_info').addClass("pb-0");
	$('.dataTables_wrapper .dataTables_filter label').addClass("mb-0");

	setTimeout(function(){
		$(".chart_datas").each(function(i, e) {
			var datas = JSON.parse($(e).html());
			var nemonico = $(e).parent().children(".nemonico").val();
			var color = $(e).parent().children(".color").val();
			var chart_dom_id = $(e).attr("id").replace("chart_data_", "hl_chart_");
			var options = {
				series: [{ name: "Precio", data: datas, color: color }],
				chart: {type: 'area', height: "80px", zoom: {enabled: false}, toolbar: {show: false}, sparkline: {enabled: true}},
				dataLabels: {enabled: false},
				stroke: {show: false},
				xaxis: {type: 'datetime', show: false},
				yaxis: {show: false, min: (min) => { return min * 0.80; }},
				fill: {type: "gradient", gradient: {type: "horizontal", opacityFrom: 1, opacityTo: 0.95}},
				tooltip: {enabled: false}
			};

			var chart = new ApexCharts(document.querySelector("#hl_chart_" + nemonico.replace("/","")), options);
			chart.render();
		});
	}, 1000);
});