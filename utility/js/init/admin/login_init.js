function login(dom){
	$(".btn-block").addClass("d-none");
	$(".ic_loading").removeClass("d-none");
	$.ajax({
		url: "/admin/access/login_validation",
		type: "POST",
		data: new FormData(dom),
		contentType: false,
		processData:false,
		success:function(res){
			if (res.status == true) window.location.href = "/admin/dashboard";
			else{
				$(".btn-block").removeClass("d-none");
				$(".ic_loading").addClass("d-none");
				sweet_error(res.msg);
			}
		}
	});
}

$(document).ready(function() {
	$("#login_form").submit(function(e) {e.preventDefault(); login(this);});
});