function login(dom){
	$(".sys_msg").html("");
	$(".auth-form button").addClass("d-none");
	$(".ic_loading").removeClass("d-none");
	$.ajax({
		url: "/access/login",
		type: "POST",
		data: new FormData(dom),
		contentType: false,
		processData:false,
		success:function(res){
			if (res.status == true) window.location.href = "/dashboard";
			else{
				$(".auth-form button").removeClass("d-none");
				$(".ic_loading").addClass("d-none");
				set_msg(res.msgs); //sweet_error(res.msg);
			}
		}
	});
}

function register(dom){
	$(".sys_msg").html("");
	$(".auth-form button").addClass("d-none");
	$(".ic_loading").removeClass("d-none");
	$.ajax({
		url: "/access/register",
		type: "POST",
		data: new FormData(dom),
		contentType: false,
		processData:false,
		success:function(res){
			$(".auth-form button").removeClass("d-none");
			$(".ic_loading").addClass("d-none");
			if (res.status == true){
				$(".auth-form").addClass("d-none");
				$("#login_block").removeClass("d-none");
				sweet_success(res.msgs[0].msg);
			}else set_msg(res.msgs);
		}
	});
}

function reset_pass(dom){
	$(".sys_msg").html("");
	$(".auth-form button").addClass("d-none");
	$(".ic_loading").removeClass("d-none");
	$.ajax({
		url: "/access/reset_pass",
		type: "POST",
		data: new FormData(dom),
		contentType: false,
		processData:false,
		success:function(res){
			$(".auth-form button").removeClass("d-none");
			$(".ic_loading").addClass("d-none");
			if (res.status == true){
				$(".auth-form").addClass("d-none");
				$("#login_block").removeClass("d-none");
				sweet_success(res.msgs[0].msg);
			}else set_msg(res.msgs);
		}
	});
}

$(document).ready(function() {
	$("#login_form").submit(function(e) {e.preventDefault(); login(this);});
	$("#register_form").submit(function(e) {e.preventDefault(); register(this);});
	$("#reset_pass_form").submit(function(e) {e.preventDefault(); reset_pass(this);});
	
	$("#go_register").on('click',(function(e) {
		$(".auth-form").addClass("d-none");
		$("#register_block").removeClass("d-none");
	}));
	$("#go_reset_pass").on('click',(function(e) {
		$(".auth-form").addClass("d-none");
		$("#reset_pass_block").removeClass("d-none");
	}));
	$("#btn_modal_login, .go_login").on('click',(function(e) {
		$(".auth-form").addClass("d-none");
		$("#login_block").removeClass("d-none");
	}));
});