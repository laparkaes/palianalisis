var success_title = null;
var error_title = null;
var warning_title = null;
var toast_op = {positionClass: "toast-top-right", timeOut: 5e3, closeButton: 1, newestOnTop: 1, progressBar: 1, preventDuplicates: 1, preventOpenDuplicates: 1, showDuration: "300", hideDuration: "1000", extendedTimeOut: "1000", showEasing: "swing", hideEasing: "linear", showMethod: "fadeIn", hideMethod: "fadeOut", tapToDismiss: 0};

function set_msg(messages){
	$(".sys_msg").removeClass("text-success");
	$(".sys_msg").removeClass("text-danger");
	$(".sys_msg").html("");
	
	messages.forEach(function(item){
		item.dom_id = "#" + item.dom_id;
		$(item.dom_id).html(item.msg);
		if (item.type == "success") $(item.dom_id).addClass("text-success");
		else $(item.dom_id).addClass("text-danger");
	});
}

function toast_success(msg){
	toastr.success(msg, null, toast_op);
}

function toast_error(msg){
	toastr.error(msg, null, toast_op);
}

function toast_warning(msg){
	toastr.warning(msg, null, toast_op);
}

function sweet_success(msg){
	Swal.fire({
		icon: 'success',
		title: $("#lang_sweet_success").val(),
		html: msg,
		confirmButtonText: $("#lang_accept").val(),
	});
}

function sweet_error(msg){
	Swal.fire({
		icon: 'error',
		title: $("#lang_sweet_error").val(),
		html: msg,
		confirmButtonText: $("#lang_accept").val(),
	});
}

function set_search_box(){
	$.ajax({
		url: "/market/set_search_box",
		type: "POST",
		success:function(res){
			$("#txt_search").autocomplete({
				minLength: 0,
				source: function(request, response) {
					var results = $.ui.autocomplete.filter(res, request.term);
					if (results.length > 0){
						var more_result = false;
						if (results.length > 10) more_result = true;
						
						result = results.slice(0, 10);
						if (more_result == true) result.push({label: "...", value: ""});
						
						response(result);
					}else response([{label: $("#lang_warning_no_result").val(), value: " "}]);
				},
				select: function (e, ui) {
					if (ui.item.value.trim().length > 0){
						$("#txt_search").prop('disabled', true);
						window.location.href = "/market/company?n=" + ui.item.value;
					}else{
						setTimeout(function(){
							$("#txt_search").trigger("click");
						}, 100);
					}
				}
			}).focus(function() {
				$(this).val("");
				$(this).autocomplete('search', $(this).val());
			}).focusout(function(){
				$(this).val("");
			}).on('click',(function(e) {
				$(this).val("");
				$(this).autocomplete('search', $(this).val());
			}));
		}
	});
}

function set_validation_action(){
	$("#btn_validation").on('click',(function(e) {
		$("#btn_validation").addClass("d-none");
		$("#ic_loading").removeClass("d-none");
		
		$.ajax({
			url: "/access/send_validation_email",
			type: "POST",
			success:function(res){
				if (res.status == true) sweet_success(res.msg);
				else sweet_error(res.msg);
				$("#btn_validation").removeClass("d-none");
				$("#ic_loading").addClass("d-none");
			}
		});
	}));
}

function favorite_control(){
	$(".ic_favorite").on('click',(function(e) {
		var dom = this;
		$.ajax({
			url: "/account/favorite_control",
			data: {nemonico: $(this).val()},
			type: "POST",
			success:function(res){
				if (res.status == true){
					toast_success(res.msg);
					if (res.type == "add"){
						$(dom).children("i").removeClass("fa-star-o text-muted");
						$(dom).children("i").addClass("fa-star text-warning");
					}else{
						$(dom).children("i").removeClass("fa-star text-warning");
						$(dom).children("i").addClass("fa-star-o text-muted");
					}
					$("#fav_chaged_ic").removeClass("d-none");
				}else toast_error(res.msg);
			}
		});
	}));
}

$(document).ready(function() {
	$(".deznav").removeClass("d-none");
	
	var win_h = window.outerHeight;
	if (win_h > 0 ? win_h : screen.height) {
		$(".content-body").css("min-height", (win_h - 100) + "px");
	};

	if ($("#txt_search").length > 0) set_search_box();
	if ($("#btn_validation").length > 0) set_validation_action();
	if ($(".ic_favorite").length > 0) favorite_control();
});