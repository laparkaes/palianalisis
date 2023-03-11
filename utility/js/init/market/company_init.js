Apex = {
	dataLabels: {enabled: false},
	xaxis: {type: 'datetime', labels: {show: false}, axisTicks: {show: false}},
}

function make_chart_indicator(options){
	options.tooltip = {
		enabled: true, fixed: {enabled: true, position: 'bottomLeft'}, 
		custom: function({series, seriesIndex, dataPointIndex, w}){
			return apexcharts_tooltip_tb(series, seriesIndex, dataPointIndex, w);
		}
	};
	$("#cht_indicator").html("<div id='cht_indicator_apex'></div>");
	var chart_ind = new ApexCharts(document.querySelector("#cht_indicator_apex"), options);
	chart_ind.render();
}

function post_server_indicator(url, datas){
	$.ajax({
		url: url,
		type: "POST",
		data: datas,
		success:function(res){
			$("#form_btns").removeClass("d-none");
			$(".ic_indicator_chart_loading").addClass("d-none");
			if (res.status == true) make_chart_indicator(res.options);
			else set_msg(res.msgs);
		}
	});
}

function chart_adx(){
	$("#itp_block").removeClass("d-none");
	var itp_checked = ""; if ($("#chk_itp").is(':checked') == true) itp_checked = "1";
	var datas = {
		period : $("#adx_period").val(), term : $("#btns_daterange .op.selected").val(), dates : $("#ddate").html(),
		highs : $("#dhigh").html(), lows : $("#dlow").html(), closes : $("#dclose").html(), chk_itp : itp_checked
	}
	post_server_indicator("/chart/adx", datas);
}

function chart_atr(){
	$("#itp_block").addClass("d-none");
	var datas = {
		period : $("#atr_period").val(), term : $("#btns_daterange .op.selected").val(), dates : $("#ddate").html(),
		highs : $("#dhigh").html(), lows : $("#dlow").html(), closes : $("#dclose").html()
	}
	post_server_indicator("/chart/atr", datas);
}

function chart_bb(){
	$("#itp_block").removeClass("d-none");
	var itp_checked = ""; if ($("#chk_itp").is(':checked') == true) itp_checked = "1";
	var datas = {
		period : $("#bb_period").val(), term : $("#btns_daterange .op.selected").val(), dates : $("#ddate").html(),
		closes : $("#dclose").html(), mupper : $("#bb_mupper").val(), mlower : $("#bb_mlower").val(),
		avgtype : $("#bb_avgtype").val(), chk_itp : itp_checked
	}
	post_server_indicator("/chart/bb", datas);
}

function chart_cci(){
	$("#itp_block").removeClass("d-none");
	var itp_checked = ""; if ($("#chk_itp").is(':checked') == true) itp_checked = "1";
	var datas = {period : $("#cci_period").val(), term : $("#btns_daterange .op.selected").val(), dates : $("#ddate").html(),
		highs : $("#dhigh").html(), lows : $("#dlow").html(), closes : $("#dclose").html(), chk_itp : itp_checked
	}
	post_server_indicator("/chart/cci", datas);
}

function chart_ema(){
	$("#itp_block").addClass("d-none");
	var arr_days = [];
	var chk_days = $(".chk_ema");
	for(i = 0; i < chk_days.length; i++){
		if ($(chk_days[i]).is(':checked')) arr_days.push($(chk_days[i]).val());
	}
	
	var datas = {
		term: $("#btns_daterange .op.selected").val(), dates: $("#ddate").html(),
		closes: $("#dclose").html(), days: arr_days
	}
	post_server_indicator("/chart/ema", datas);
}

function chart_env(){
	$("#itp_block").removeClass("d-none");
	var itp_checked = ""; if ($("#chk_itp").is(':checked') == true) itp_checked = "1";
	var datas = {
		period: $("#env_period").val(), term: $("#btns_daterange .op.selected").val(), dates: $("#ddate").html(),
		closes: $("#dclose").html(), sep: $("#env_sep").val(), chk_itp: itp_checked
	}
	post_server_indicator("/chart/env", datas);
}

function chart_ich(){
	$("#itp_block").addClass("d-none");
	var datas = {closes: $("#dclose").html(), term: $("#btns_daterange .op.selected").val(), dates: $("#ddate").html()}
	post_server_indicator("/chart/ich", datas);
}

function chart_macd(){
	$("#itp_block").removeClass("d-none"); 
	var itp_checked = ""; if ($("#chk_itp").is(':checked') == true) itp_checked = "1";
	var datas = {
		term: $("#btns_daterange .op.selected").val(), dates: $("#ddate").html(), closes: $("#dclose").html(),
		f_period: $("#macd_f_period").val(), s_period: $("#macd_s_period").val(), sig_period: $("#macd_sig_period").val(),
		chk_itp : itp_checked
	}
	post_server_indicator("/chart/macd", datas);
}

function chart_mar(){
	$("#itp_block").addClass("d-none");
	var datas = {
		term: $("#btns_daterange .op.selected").val(), dates: $("#ddate").html(), closes: $("#dclose").html(),
		avgtype: $("#mar_avgtype").val(), days: [15, 20, 25, 30, 35, 40, 45, 50, 55, 60, 65, 70]
	}
	post_server_indicator("/chart/" + datas.avgtype, datas);
}

function chart_mfi(){
	$("#itp_block").removeClass("d-none");
	var itp_checked = ""; if ($("#chk_itp").is(':checked') == true) itp_checked = "1";
	var datas = {
		term: $("#btns_daterange .op.selected").val(), dates: $("#ddate").html(), highs: $("#dhigh").html(), lows: $("#dlow").html(), closes: $("#dclose").html(), volumes: $("#dvolumes").html(), period: $("#mfi_period").val(), chk_itp : itp_checked
	}
	post_server_indicator("/chart/mfi", datas);
}

function chart_mom(){
	$("#itp_block").removeClass("d-none");
	var itp_checked = ""; if ($("#chk_itp").is(':checked') == true) itp_checked = "1";
	var datas = {
		term: $("#btns_daterange .op.selected").val(), period: $("#mom_period").val(), sig_period: $("#mom_sig_period").val(),
		dates: $("#ddate").html(), closes: $("#dclose").html(), chk_itp : itp_checked
	}
	post_server_indicator("/chart/mom", datas);
}

function chart_pbsar(){
	$("#itp_block").removeClass("d-none");
	var itp_checked = ""; if ($("#chk_itp").is(':checked') == true) itp_checked = "1";
	var datas = {
		term: $("#btns_daterange .op.selected").val(), dates: $("#ddate").html(), highs: $("#dhigh").html(),
		lows: $("#dlow").html(), closes: $("#dclose").html(), acceleration: $("#pbsar_acceleration").val(),
		maximum: $("#pbsar_maximum").val(), chk_itp : itp_checked
	}
	post_server_indicator("/chart/pbsar", datas);
}

function chart_ppo(){
	$("#itp_block").removeClass("d-none"); 
	var itp_checked = ""; if ($("#chk_itp").is(':checked') == true) itp_checked = "1";
	var datas = {
		term: $("#btns_daterange .op.selected").val(), dates: $("#ddate").html(), closes: $("#dclose").html(),
		f_period: $("#ppo_f_period").val(), s_period: $("#ppo_s_period").val(),	avgtype: $("#ppo_avgtype").val(),
		chk_itp : itp_checked
	}
	post_server_indicator("/chart/ppo", datas);
}

function chart_pch(){
	$("#itp_block").removeClass("d-none"); 
	var itp_checked = ""; if ($("#chk_itp").is(':checked') == true) itp_checked = "1";
	var datas = {
		term: $("#btns_daterange .op.selected").val(), dates: $("#ddate").html(), highs: $("#dhigh").html(), lows: $("#dlow").html(), closes: $("#dclose").html(), period: $("#pch_period").val(), chk_itp : itp_checked
	}
	post_server_indicator("/chart/pch", datas);
}

function chart_rsi(){
	$("#itp_block").removeClass("d-none");
	var itp_checked = ""; if ($("#chk_itp").is(':checked') == true) itp_checked = "1";
	var datas = {
		term: $("#btns_daterange .op.selected").val(), period: $("#rsi_period").val(), dates: $("#ddate").html(),
		closes: $("#dclose").html(), chk_itp : itp_checked
	}
	post_server_indicator("/chart/rsi", datas);
}

function chart_sma(){
	$("#itp_block").addClass("d-none");
	var arr_days = [];
	var chk_days = $(".chk_sma");
	for(i = 0; i < chk_days.length; i++){
		if ($(chk_days[i]).is(':checked')) arr_days.push($(chk_days[i]).val());
	}
	
	var datas = {
		term: $("#btns_daterange .op.selected").val(), dates: $("#ddate").html(),
		closes: $("#dclose").html(), days: arr_days
	}
	post_server_indicator("/chart/sma", datas);
}

function chart_sto(){
	$("#itp_block").removeClass("d-none");
	var itp_checked = ""; if ($("#chk_itp").is(':checked') == true) itp_checked = "1";
	var datas = {
		term: $("#btns_daterange .op.selected").val(), dates: $("#ddate").html(), highs: $("#dhigh").html(),
		lows: $("#dlow").html(), closes: $("#dclose").html(), fk_period: $("#sto_fk_period").val(),
		sk_period: $("#sto_sk_period").val(), d_period: $("#sto_d_period").val(), k_avgtype: $("#sto_k_avgtype").val(),
		d_avgtype: $("#sto_d_avgtype").val(), chk_itp : itp_checked
	}
	post_server_indicator("/chart/sto", datas);
}

function chart_trix(){
	$("#itp_block").removeClass("d-none");
	var itp_checked = ""; if ($("#chk_itp").is(':checked') == true) itp_checked = "1";
	var datas = {
		term: $("#btns_daterange .op.selected").val(), period: $("#trix_period").val(), sig_period: $("#trix_sig_period").val(),
		dates: $("#ddate").html(), closes: $("#dclose").html(), chk_itp : itp_checked
	}
	post_server_indicator("/chart/trix", datas);
}

function chart_vol(){
	$("#itp_block").addClass("d-none"); 
	var datas = {
		term: $("#btns_daterange .op.selected").val(), dates: $("#ddate").html(), opens: $("#dopen").html(),
		closes: $("#dclose").html(), volumes: $("#dvolumes").html()
	}
	post_server_indicator("/chart/volume", datas);
}

function chart_indicator(){
	$(".sys_msg").html("");
	$(".ic_indicator_chart_loading").removeClass("d-none");
	$("#form_btns").addClass("d-none");
	$("#cht_indicator_apex").remove();
	switch ($("#sl_indicator").val()) {
		case 'adx': chart_adx(); break;
		case 'atr': chart_atr(); break;
		case 'bb': chart_bb(); break;
		case 'cci': chart_cci(); break;
		case 'ema': chart_ema(); break;
		case 'env': chart_env(); break;
		case 'ich': chart_ich(); break;
		case 'macd': chart_macd(); break;
		case 'mar': chart_mar(); break;
		case 'mfi': chart_mfi(); break;
		case 'mom': chart_mom(); break;
		case 'pbsar': chart_pbsar(); break;
		case 'ppo': chart_ppo(); break;
		case 'pch': chart_pch(); break;
		case 'rsi': chart_rsi(); break;
		case 'sma': chart_sma(); break;
		case 'sto': chart_sto(); break;
		case 'trix': chart_trix(); break;
		case 'vol': chart_vol(); break;
		default: chart_vol();
	}
}

function chart_price(){
	$(".sys_msg").html("");
	$("#ic_price_chart_loading").removeClass("d-none");
	$("#cht_price_apex").remove();
	$("#cht_indicator_apex").remove();
	$.ajax({
		url: "/chart/price",
		type: "POST",
		data: {
			term: $("#btns_daterange .op.selected").val(),
			dates: $("#ddate").html(),
			opens: $("#dopen").html(),
			highs: $("#dhigh").html(),
			lows: $("#dlow").html(),
			closes: $("#dclose").html()
		},
		success:function(res){
			if (res.status == true){
				res.options.tooltip = {enabled: true, fixed: {enabled: true, position: 'bottomLeft'}, custom: function({series, seriesIndex, dataPointIndex, w}){return apexcharts_candle_tooltip_tb(series, seriesIndex, dataPointIndex, w);}};
				
				$("#cht_price").html("<div id='cht_price_apex'></div>");
				var chart_p = new ApexCharts(document.querySelector("#cht_price_apex"), res.options);
				chart_p.render();
				chart_indicator();
			}else set_msg(res.msgs);
			$("#ic_price_chart_loading").addClass("d-none");
		}
	});
}

function select_datarange(dom){
	$("#btns_daterange .op").removeClass("selected");
	$("#btns_daterange .op").removeClass("btn-primary");
	$("#btns_daterange .op").addClass("btn-outline-primary");
	$(dom).removeClass("btn-outline-primary");
	$(dom).addClass("btn-primary");
	$(dom).addClass("selected");
	
	chart_price();
}

function control_indicator_form(indicator){
	$(".indicator_forms").addClass("d-none");
	$(".indicator_forms#" + indicator + "_form").removeClass("d-none");
	setTimeout(function(){ 
		chart_indicator();
		$("#btn_indicator").focus();
	}, 500);
}

function reset_forms(){
	var indicator = $("#sl_indicator").val();
	$.ajax({
		url: "/market/company_reset_forms",
		type: "POST",
		success:function(data){
			$("#indicator_form_block").children().remove();
			$("#indicator_form_block").html(data.content);
			control_indicator_form(indicator);
			toast_success(data.msg);
		}
	});
}

$(document).ready(function(){
	if ($("#sl_indicator").length > 0){/* records exists */	
		var table = $('#tb_records').DataTable({
			dom: '<"top"p>rt<"clear"><"bottom"i>',
			language: {
				info: "_START_ - _END_ / _TOTAL_",
				infoEmpty: "0",
				paginate: { 
					first: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>', 
					previous: '<i class="fa fa-angle-left" aria-hidden="true"></i>', 
					next: '<i class="fa fa-angle-right" aria-hidden="true"></i>', 
					last: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>'
				}
			},
			order: [[ 0, "desc" ]]
		});
		$('.dataTables_wrapper .top').addClass("");
		$('.dataTables_wrapper .top div').addClass("p-0");
		
		$("#btn_indicator").on('click',(function(e) {chart_indicator();}));
		$("#btn_reset_forms").on('click',(function(e) {reset_forms();}));
		$("#chk_itp").change(function() {chart_indicator();});
		$("#sl_indicator").change(function() {control_indicator_form($(this).val());});
		$('#sl_indicator').selectpicker('val', "vol");
		$(".indicator_forms#vol_form").removeClass("d-none");
		var date_checked = 0;
		var dateranges = $("#btns_daterange").children(".btn");
		for (let i = 0; i < dateranges.length; i++) {
			if (($(dateranges[i]).is(':disabled')) || (date_checked >= 3)) break;
			date_checked = i;
		}
		$("#btns_daterange .op").on('click',(function(e) {select_datarange(this);}));
		$($("#btns_daterange").children(".btn")[date_checked]).trigger("click");
	}
});