function load_account_detail(dom){
	$.ajax({
		url: "/admin/client/load_account_detail",
		type: "POST",
		data: {aid: $(dom).parent().children(".aid").val()},
		success:function(res){
			if (res.status == true) $("#detail_modal_body").html(res.content);
			else sweet_error(res.msg);
		}
	});
}

function load_account_subscription(dom){
	$.ajax({
		url: "/admin/client/load_add_subscription_form",
		type: "POST",
		data: {aid: $(dom).parent().children(".aid").val()},
		success:function(res){
			if (res.status == true) $("#subscription_modal_body").html(res.content);
			else sweet_error(res.msg);
		}
	});
}

function add_subscription_confirm(){
	$.ajax({
		url: "/admin/client/add_subscription_confirm",
		type: "POST",
		data: {aid: $("#as_acc_id").val(), term: $("#as_term").val()},
		success:function(res){
			if (res.status == true){
				sweet_success(res.msg);
				$("#account_filter_form").submit();
				$("#modal_add_subscription").modal('hide');
			}else sweet_error(res.msg);
		}
	});
}

function account_filter(dom){
	$.ajax({
		url: "/admin/client/account_filter",
		type: "POST",
		data: new FormData(dom),
		contentType: false,
		processData:false,
		success:function(res){
			$("#acc_filtered_qty").html(res.acc_qty);
			$("#bl_account_table").html(res.content);
		}
	});
}

$(document).ready(function() {
	$("#btn_asc").on('click',(function(e) {add_subscription_confirm();}));
	$("#account_filter_form").submit(function(e) {e.preventDefault(); account_filter(this);});
});