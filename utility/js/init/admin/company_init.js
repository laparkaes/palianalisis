function partial_update(dom){
	$.ajax({
		url: "/admin/company/record_update",
		type: "POST",
		data: {nemonico: $(dom).html(), is_partial: true},
		success:function(res){
			if (res.status == true){
				toast_success(res.msg);
				$(dom).remove();
				if ($(".btn_partial").length == 0) $("#partial_blank").removeClass("d-none");
			}
			else toast_error(res.msg);
		}
	});
}

function initial_update(dom){
	$.ajax({
		url: "/admin/company/record_update",
		type: "POST",
		data: {nemonico: $(dom).html(), is_partial: false},
		success:function(res){
			if (res.status == true){
				toast_success(res.msg);
				$(dom).remove();
				if ($(".btn_initial").length == 0) $("#initial_blank").removeClass("d-none");
			}
			else toast_error(res.msg);
		}
	});
}

function load_partial_update(){
	$.ajax({
		url: "/admin/company/load_partial_update",
		type: "POST",
		success:function(res){
			$("#bl_partial_update").html(res);
			if ($(".btn_partial").length == 0) $("#partial_blank").removeClass("d-none");
			else $(".btn_partial").on('click',(function(e) {partial_update(this);}));
		}
	});
}

$(document).ready(function() {
	if ($(".btn_partial").length == 0) $("#partial_blank").removeClass("d-none");
	else $(".btn_partial").on('click',(function(e) {partial_update(this);}));
	
	if ($(".btn_initial").length == 0) $("#initial_blank").removeClass("d-none");
	else $(".btn_initial").on('click',(function(e) {initial_update(this);}));
	
	setInterval(function(){
		var btns = $(".btn_partial, .btn_initial");
		if (btns.length > 0) $(btns[0]).trigger("click");
		else $("#partial_blank").removeClass("d-none");
	}, 10000);
	
	setInterval(function(){
		load_partial_update();
	}, 60000);
});