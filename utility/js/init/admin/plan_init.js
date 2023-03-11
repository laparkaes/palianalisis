function add_plan(dom){
	if (confirm($("#confirm_add_plan").val()) == true) {
		$.ajax({
			url: "/admin/plan/add",
			type: "POST",
			data: new FormData(dom),
			contentType: false,
			processData:false,
			success:function(data){
				if (data.status == true){
					$(dom)[0].reset();
					sweet_success(data.msg);
					$("#plan_list .row_plan").remove();
					$("#plan_list").append(data.content);
					$(".remove_plan").submit(function(e) {e.preventDefault(); remove_plan(this);});
				}else sweet_error(data.msg);
			}
		});	
	}
}

function remove_plan(dom){
	if (confirm($("#confirm_remove_plan").val()) == true) {
		$.ajax({
			url: "/admin/plan/remove",
			type: "POST",
			data: new FormData(dom),
			contentType: false,
			processData:false,
			success:function(data){
				if (data.status == true){
					sweet_success(data.msg);
					$("#plan_list .row_plan").remove();
					$("#plan_list").append(data.content);
					$(".remove_plan").submit(function(e) {e.preventDefault(); remove_plan(this);});
				}else sweet_error(res.msg);
			}
		});	
	}
}

function add_service(dom){
	if (confirm($("#confirm_add_service").val()) == true) {
		$.ajax({
			url: "/admin/plan/service_add",
			type: "POST",
			data: new FormData(dom),
			contentType: false,
			processData:false,
			success:function(data){
				if (data.status == true){
					$(dom)[0].reset();
					sweet_success(data.msg);
					$("#service_list").html(data.srv);
					$("#service_plan_list").html(data.srv_plan);
					$(".remove_service").submit(function(e) {e.preventDefault(); remove_service(this);});
					$(".chk_srv_plan").on('click',(function(e) {control_service(this);}));
				}else sweet_error(res.msg);
			}
		});	
	}
}

function remove_service(dom){
	if (confirm($("#confirm_remove_service").val()) == true) {
		$.ajax({
			url: "/admin/plan/service_remove",
			type: "POST",
			data: new FormData(dom),
			contentType: false,
			processData:false,
			success:function(data){
				if (data.status == true){
					sweet_success(data.msg);
					$("#service_list").html(data.srv);
					$("#service_plan_list").html(data.srv_plan);
					$(".remove_service").submit(function(e) {e.preventDefault(); remove_service(this);});
					$(".chk_srv_plan").on('click',(function(e) {control_service(this);}));
				}else sweet_error(res.msg);
			}
		});	
	}
}

function control_service(dom){
	$.ajax({
		url: "/admin/plan/service_control",
		type: "POST",
		data: {ids: $(dom).val(), is_checked:$(dom).is(':checked')},
		success:function(data){
			toast_success(data);
		}
	});
}

function add_indicator(dom){
	if (confirm($("#confirm_add_indicator").val()) == true) {
		$.ajax({
			url: "/admin/plan/indicator_add",
			type: "POST",
			data: new FormData(dom),
			contentType: false,
			processData:false,
			success:function(data){
				if (data.status == true){
					$(dom)[0].reset();
					sweet_success(data.msg);
					$("#indicator_list").html(data.ind);
					$("#indicator_plan_list").html(data.ind_plan);
					$(".remove_indicator").submit(function(e) {e.preventDefault(); remove_indicator(this);});
					$(".chk_ind_plan").on('click',(function(e) {control_indicator(this);}));
				}else sweet_error(res.msg);
			}
		});	
	}
}

function remove_indicator(dom){
	if (confirm($("#confirm_remove_indicator").val()) == true) {
		$.ajax({
			url: "/admin/plan/indicator_remove",
			type: "POST",
			data: new FormData(dom),
			contentType: false,
			processData:false,
			success:function(data){
				if (data.status == true){
					sweet_success(data.msg);
					$("#indicator_list").html(data.ind);
					$("#indicator_plan_list").html(data.ind_plan);
					$(".remove_indicator").submit(function(e) {e.preventDefault(); remove_indicator(this);});
					$(".chk_ind_plan").on('click',(function(e) {control_indicator(this);}));
				}else sweet_error(res.msg);
			}
		});
	}
}

function control_indicator(dom){
	$.ajax({
		url: "/admin/plan/indicator_control",
		type: "POST",
		data: {ids: $(dom).val(), is_checked:$(dom).is(':checked')},
		success:function(data){
			toast_success(data);
		}
	});
}

$(document).ready(function() {
	$("#add_plan").submit(function(e) {e.preventDefault(); add_plan(this);});
	$(".remove_plan").submit(function(e) {e.preventDefault(); remove_plan(this);});
	
	$("#add_service").submit(function(e) {e.preventDefault(); add_service(this);});
	$(".remove_service").submit(function(e) {e.preventDefault(); remove_service(this);});
	$(".chk_srv_plan").on('click',(function(e) {control_service(this);}));
	
	$("#add_indicator").submit(function(e) {e.preventDefault(); add_indicator(this);});
	$(".remove_indicator").submit(function(e) {e.preventDefault(); remove_indicator(this);});
	$(".chk_ind_plan").on('click',(function(e) {control_indicator(this);}));
	
});