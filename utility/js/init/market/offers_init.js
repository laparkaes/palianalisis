function apply_filter(){
	var selected = $("input[name='f_type']:checked").val();
	$(".all").addClass("d-none");
	$("." + selected).removeClass("d-none");
	
	var f_text = $("#f_text").val().toUpperCase();
	var cards = $(".all");
	var limit = cards.length;
	var count = 0;
	var company_text = "";
	
	for(var i = 0; i < limit; i++){
		if (!$(cards[i]).hasClass("d-none")){
			company_text = $(cards[i]).find(".name").html() + " " + $(cards[i]).find(".nemonico").html();
			company_text = company_text.toUpperCase().replace('&AMP;', '');
			if (company_text.includes(f_text)) count++;
			else $(cards[i]).addClass("d-none");
		}
	}
	
	if (count > 0) $("#no_result").addClass("d-none");
	else $("#no_result").removeClass("d-none");
}

function update_canvas(){
	$("#ic_update").addClass("fa-spin");
	$.ajax({
		url: "/market/offers_update_canvas",
		type: "POST",
		success:function(res){
			if (res.status == true){
				$("#updated_at").html(res.updated_at);
				$("#offer_canvas").html(res.content);
				apply_filter();
			}else sweet_error(res.msg);
			$("#ic_update").removeClass("fa-spin");
		}
	});
}

$(document).ready(function() {
	$("#bd_update_canvas").unbind('click').on('click',(function(e) {update_canvas();}));
	$(".f_type").on('click',(function(e) {apply_filter();}));
	$("#f_text").keyup(function() {apply_filter();});
	
	window.setInterval(function () {update_canvas();}, 60000);
});