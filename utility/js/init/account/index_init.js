function update_account(dom){
	$.ajax({
		url: "/account/update",
		type: "POST",
		data: new FormData(dom),
		contentType: false,
		processData:false,
		success:function(res){
			if (res.status == true){
				sweet_success(res.msg);
				$("#logged_name").html(res.name);
			}else sweet_error(res.msg);
		}
	});
}

function remove_account(){
	Swal.fire({
		title: $("#lang_sweet_warning").val(),
		html: $("#warning_remove_account").val(),
		icon: 'warning',
		confirmButtonText: $("#lang_accept").val(),
		cancelButtonText: $("#lang_cancel").val(),
		showCancelButton: true
	}).then((result) => {
		if (result.isConfirmed){
			$("#account_data_btns").addClass("d-none");
			$("#ic_loading_account_data").removeClass("d-none");
			$.ajax({
				url: "/account/remove",
				type: "POST",
				success:function(res){
					if (res.status == true) window.location.href = res.move_to;
					else{
						sweet_error(res.msg);
						$("#account_data_btns").removeClass("d-none");
						$("#ic_loading_account_data").addClass("d-none");
					}	
				}
			});
		}
	});
}

function update_pass(dom){
	Swal.fire({
		title: $("#lang_sweet_warning").val(),
		html: $("#warning_session_end").val(),
		icon: 'warning',
		confirmButtonText: $("#lang_accept").val(),
		cancelButtonText: $("#lang_cancel").val(),
		showCancelButton: true
	}).then((result) => {
		if (result.isConfirmed){
			$.ajax({
				url: "/account/update_pass",
				type: "POST",
				data: new FormData(dom),
				contentType: false,
				processData:false,
				success:function(res){
					if (res.status == true) window.location.href = "/";
					else sweet_error(res.msg);
				}
			});
		}
	});
}

function cancel_subscription(){
	Swal.fire({
		title: $("#lang_sweet_warning").val(),
		html: $("#warning_cancel_subs").val(),
		icon: 'warning',
		confirmButtonText: $("#lang_accept").val(),
		cancelButtonText: $("#lang_cancel").val(),
		showCancelButton: true
	}).then((result) => {
		if (result.isConfirmed){
			$("#btn_cancel_subs").addClass("d-none");
			$("#ic_loading_subs").removeClass("d-none");
			$.ajax({
				url: "/subscription/cancel",
				type: "POST",
				success:function(res){
					if (res.status == true) document.location.reload();
					else{
						sweet_error(res.msg);
						$("#btn_cancel_subs").removeClass("d-none");
						$("#ic_loading_subs").addClass("d-none");
					}
				}
			});
		}
	});
}

$(document).ready(function() {
	$("#update_pass_form").submit(function(e) {e.preventDefault(); update_pass(this);});
	$("#update_account_form").submit(function(e) {e.preventDefault(); update_account(this);});
	$("#btn_cancel_subs").on('click',(function(e) {cancel_subscription();}));
	$("#btn_remove_account").on('click',(function(e) {remove_account();}));
});