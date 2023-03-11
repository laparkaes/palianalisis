function control_detail_view(dom){
	var nemonico = $(dom).val();
	if ($(dom).children(".fa").hasClass("fa-caret-down")){
		$(".detail").addClass("d-none");
		$(".detail_" + nemonico).removeClass("d-none");
		
		$(".btn_dcontrol").children(".fa").removeClass("fa-caret-up");
		$(".btn_dcontrol").children(".fa").addClass("fa-caret-down");
		
		$(dom).children(".fa").removeClass("fa-caret-down");
		$(dom).children(".fa").addClass("fa-caret-up");
	}else{
		$(".detail_" + nemonico).addClass("d-none");
		$(dom).children(".fa").removeClass("fa-caret-up");
		$(dom).children(".fa").addClass("fa-caret-down");
	}
}

function add_wallet(dom){
	$(".sys_msg").html("");
	$.ajax({
		url: "/wallet/add",
		type: "POST",
		data: new FormData(dom),
		contentType: false,
		processData:false,
		success:function(res){
			if (res.status == true) location.reload(true);
			else set_msg(res.msgs);//sweet_error(res.msg);
		}
	});
}

function remove_operation(dom){
	Swal.fire({
		title: $("#lang_sweet_warning").val(),
		html: $("#warning_remove_operation").val(),
		icon: 'warning',
		confirmButtonText: $("#lang_remove").val(),
		cancelButtonText: $("#lang_cancel").val(),
		showCancelButton: true
	}).then((result) => {
		if (result.isConfirmed){
			$.ajax({
				url: "/wallet/remove",
				type: "POST",
				data: {wid:$(dom).val()},
				success:function(res){
					if (res.status == true) location.reload(true);
					else sweet_error(res.msg);
				}
			});
		}
	});
}

function control_resume_row_visible(dom){
	$("#btns_visible .op").removeClass("btn-primary");
	$("#btns_visible .op").addClass("btn-outline-primary");
	$(dom).removeClass("btn-outline-primary");
	$(dom).addClass("btn-primary");
	
	if ($(dom).val() == "all"){
		$(".row_resume").removeClass("d-none");
	}
	else{
		$(".row_resume.inactive").addClass("d-none");
		$(".detail").addClass("d-none");
		$(".btn_dcontrol").children(".fa").removeClass("fa-caret-up");
		$(".btn_dcontrol").children(".fa").addClass("fa-caret-down");
	}
}

function set_company_list_wallet(){
	var companies = JSON.parse($("#companies_list").html());
	$("#nw_nemonico").autocomplete({
		minLength: 0,
		source: function(request, response) {
			var results = $.ui.autocomplete.filter(companies, request.term);
			if (results.length > 0){
				var more_result = false;
				if (results.length > 10) more_result = true;
				
				result = results.slice(0, 10);
				if (more_result == true) result.push({label: "...", value: ""});
				
				response(result);
			}else response([{label: $("#lang_warning_no_result").val(), value: " "}]);
		},
		select: function (e, ui) {
			if (ui.item.value.trim().length == 0){
				setTimeout(function(){
					$("#nw_nemonico").trigger("click");
				}, 100);
			}
		}
	}).focus(function() {
		$(this).val("");
		$(this).autocomplete('search', $(this).val());
	}).on('click',(function(e) {
		$(this).val("");
		$(this).autocomplete('search', $(this).val());
	}));
}

$(document).ready(function() {
	set_company_list_wallet();
	//$("#nw_nemonico").select2({language: { noResults: function(){ return $("#lang_warning_no_result").val(); }}});
	$("#nw_buy").on('click',(function(e) {nw_add_modal_set(true);}));
	$("#nw_sell").on('click',(function(e) {nw_add_modal_set(false);}));
	$("#nw_confirm").on('click',(function(e) {add_wallet();}));
	$('#nw_date').bootstrapMaterialDatePicker({
		weekStart: 0, time: false, lang : 'es', cancelText: 'Cancelar', okText: 'Elegir', maxDate : new Date()
	});

	$("#add_wallet").submit(function(e) {e.preventDefault(); add_wallet(this);});
	$(".btn_dcontrol").unbind( "click" ).on('click',(function(e) {control_detail_view(this);}));
	$(".btn_roperation").unbind( "click" ).on('click',(function(e) {remove_operation(this);}));

	$("#btns_visible .op").unbind( "click" ).on('click',(function(e) {control_resume_row_visible(this);}));

	var table = $('#tb_all_operations').DataTable({
		dom: 'rt<"bottom"p>',
		ordering: false,
		language: {
			info: "_START_ - _END_ / _TOTAL_",
			infoEmpty: "0",
			emptyTable: $("#no_operations").val(),
			paginate: { 
				first: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>', 
				previous: '<i class="fa fa-angle-left" aria-hidden="true"></i>', 
				next: '<i class="fa fa-angle-right" aria-hidden="true"></i>', 
				last: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>'
			}
		}
	});

	$("#tb_all_operations").removeClass("dataTable");
	$('.dataTables_wrapper .dataTables_info').addClass("pb-0");
	$('.dataTables_wrapper .dataTables_filter label').addClass("mb-0");
	$('.dataTables_wrapper .dataTables_empty').addClass("text-center");
});