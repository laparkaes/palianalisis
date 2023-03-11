<script>
var nemonicos = [];
var charts = [];
var series = [];

function make_base_chart(nemonico){
	if ($("#rt_chart_" + nemonico).length > 0){
		var options = {
			colors : ['#000', '#dc3545', '#28a745'],
			series: series[nemonico],
			chart: {animations: {enabled: false}, type: 'line', width: '100%', height: "80px", 
					zoom: {enabled: false}, toolbar: {show: false}, sparkline: {enabled: true}},
			dataLabels: {enabled: false},
			stroke: {curve: 'smooth', width: 2},
			xaxis: {type: 'datetime', show: false, labels: {show:false}, axisBorder: {show:false}, axisTicks: {show: false}},
			yaxis: {show: false, labels: {show:false}, axisBorder: {show:false}, axisTicks: {show: false}},
			grid: {show: false, padding: {top: 10, right: 0, bottom: 10, left: 0}},
			legend: {show: false},
			tooltip: { enabled: false}
		};
		
		var chart = new ApexCharts(document.querySelector("#rt_chart_" + nemonico), options);
		chart.render();
		charts[nemonico] = chart;	
	}
}

function update_rt_charts(datas){
	last_time = new Date().getTime();
	datas.forEach(function(item){
		if (item.sell > 0){
			$("#rt_last_" + item.nemonico + " .rt_sell").removeClass("d-none");
			$("#rt_last_" + item.nemonico + " .rt_sell span").html(item.currencySymbol + " " + item.sell_t);
		}else $("#rt_last_" + item.nemonico + " .rt_sell").addClass("d-none");
		
		if (item.buy > 0){
			$("#rt_last_" + item.nemonico + " .rt_buy").removeClass("d-none");
			$("#rt_last_" + item.nemonico + " .rt_buy span").html(item.currencySymbol + " " + item.buy_t);
		}else $("#rt_last_" + item.nemonico + " .rt_buy").addClass("d-none");
		
		if (item.close > 0){
			$("#rt_last_" + item.nemonico + " .rt_close").removeClass("d-none");
			$("#rt_last_" + item.nemonico + " .rt_close span").html(item.currencySymbol + " " + item.close_t);
		}else $("#rt_last_" + item.nemonico + " .rt_close").addClass("d-none");
		
		var leng = 0;
		var insert_data = true;
				
		
		if (item.sell == 0) item.sell = null;
		if (item.buy == 0) item.buy = null;
		if (item.close == 0) item.close = null;
		
		if (series[item.nemonico][0]["data"].length == 0){ //insert two datas to make line
			series[item.nemonico][0].data.push({x: last_time - 30000, y: item.close});	
			series[item.nemonico][1].data.push({x: last_time - 30000, y: item.sell});
			series[item.nemonico][2].data.push({x: last_time - 30000, y: item.buy});	
		}
		
		series[item.nemonico][0].data.push({x: last_time, y: item.close});	
		series[item.nemonico][1].data.push({x: last_time, y: item.sell});
		series[item.nemonico][2].data.push({x: last_time, y: item.buy});
		
		if (series[item.nemonico][0].data.length > 20){
			series[item.nemonico][0].data = series[item.nemonico][0].data.slice(leng - 10, leng);
			series[item.nemonico][1].data = series[item.nemonico][1].data.slice(leng - 10, leng);
			series[item.nemonico][2].data = series[item.nemonico][2].data.slice(leng - 10, leng);
		}
		
		charts[item.nemonico].updateSeries(series[item.nemonico]);
	});
}

function page_update(){
	$("#ic_update").addClass("fa-spin");
	$.ajax({
		url: $("#base_url").val() + "offer/realtime_update",
		type: "POST",
		data: {nemonicos:nemonicos},
		success:function(data){
			if (data.status == true){
				$("#updated_at").html(data.updated_at);
				$("#blntb").html(data.ntb);
				$("#blftb").html(data.ftb);
				if (nemonicos.length > 0) update_rt_charts(data.rtd);
			}else toastr.error(data.msg, error_title, toast_op);
			$("#ic_update").removeClass("fa-spin");
		}
	});
}

function add_rtcd(nemonico){
	$.ajax({
		url: $("#base_url").val() + "offer/realtime_validation",
		type: "POST",
		data: {qty:nemonicos.length},
		success:function(data){
			if (data.status == true){
				if (!nemonicos.includes(nemonico)){
					var card = $($(".cdrt")[0]).clone();
					card.removeClass("d-none");
					card.attr("id", "cdrt_" + nemonico);
					card.find(".rt_nemonicos").val(nemonico);
					card.find(".rt_title").html(nemonico);
					card.find(".rt_title").parent().attr("href", $("#base_url").val() + "company/detail?n=" + nemonico)
					card.find(".rt_chart").attr("id", "rt_chart_" + nemonico); card.find(".rt_chart").children().remove();
					card.find(".resize-triggers").remove();
					card.find(".card-footer").attr("id", "rt_last_" + nemonico);
					card.find(".rt_sell").children("span").html(""); card.find(".rt_sell").addClass("d-none");
					card.find(".rt_buy").children("span").html(""); card.find(".rt_buy").addClass("d-none");
					card.find(".rt_close").children("span").html(""); card.find(".rt_close").addClass("d-none");
					card.find(".rmcd").unbind('click').on('click',(function(e) {remove_rtcd(nemonico);}));
					card.appendTo("#rtch_block");
					
					nemonicos.push(nemonico);
					series[nemonico] = [{name: "Precio", data: []}, {name: "Venta", data: []}, {name: "Compra", data: []}];
					make_base_chart(nemonico);
					
					page_update();
					$("#actions_" + nemonico).children(".tb_adcd").addClass("d-none");
					$("#actions_" + nemonico).children(".tb_rmcd").removeClass("d-none");
				}
			}else toastr.error(data.msg, error_title, toast_op);
		}
	});
}

function remove_rtcd(nemonico){
	nemonicos = nemonicos.filter(e => e !== nemonico);
	$("#cdrt_" + nemonico).remove();
	
	$("#actions_" + nemonico).children(".tb_rmcd").addClass("d-none");
	$("#actions_" + nemonico).children(".tb_adcd").removeClass("d-none");
}

$(document).ready(function() {
	$("#bd_update_page").unbind('click').on('click',(function(e) {page_update();}));
	window.setInterval(function () {page_update();}, 60000);
});
</script>
<div class="row page-titles mx-0">
	<div class="col-sm-6 p-md-0">
		<div class="welcome-text">
			<h4>Ofertas en Tiempo Real</h4>
			<span>Conoce ultimos precios de compra y venta</span><br/>
			<span id="bd_update_page" class="badge light badge-info mt-2" style="cursor: pointer;"><i id="ic_update" class="fa fa-refresh mr-1" aria-hidden="true"></i><span id="updated_at"></span></span>
		</div>
	</div>
	<div class="col-sm-6 p-md-0 justify-content-sm-end mt-2 mt-sm-0 d-flex">
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="<?= base_url() ?>market/offers"><?= $title1 ?></a></li>
			<li class="breadcrumb-item active"><a href="<?= base_url() ?>market/offers"><?= $title2 ?></a></li>
		</ol>
	</div>
</div>
<div id="rtch_block" class="row">
	<div class="cdrt d-none col-xl-3 col-sm-6">
		<input class="rt_nemonicos" type="hidden" value="">
		<div class="card overflow-hidden">
			<div class="card-header align-items-start border-0 pb-0">
				<div class="mr-auto">
					<a href="">
						<h3 class="rt_title text-black font-w600"></h3>
					</a>
				</div>
				<button class="rmcd btn tp-btn btn-danger btn-xs" style="position: absolute; right: 0.3rem; top: 0.3rem;">
					<i class="fa fa-times"></i>
				</button>
			</div>
			<div class="card-body p-0" style="position: relative;">
				<div class="rt_chart max-h80 mt-auto" style="min-height: 80px;"></div>
			</div>
			<div class="card-footer border-0 pt-0 text-right">
				<small>
					<div class="rt_sell text-danger d-none">Venta <span></span></div>
					<div class="rt_buy text-success d-none">Compra <span></span></span></div>
					<div class="rt_close text-dark d-none">Ultimo <span></span></div>
				</small>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-xl-6 col-xxl-12">
		<div class="row">
			<div class="col-sm-6">
				<div class="card">
					<div class="card-header border-0 pb-0">
						<h4 class="mb-0 text-black fs-20">Ofertas Nacionales</h4>
					</div>
					<div class="card-body p-3">
						<div id="blntb" class="table-responsive">
							<?php $this->load->view('comp/offer_table', array("stocks" => $nationals, "show_qty" => $show_qty)); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-6">
				<div class="card">
					<div class="card-header border-0 pb-0">
						<h4 class="mb-0 text-black fs-20">Ofertas Extranjeras</h4>
					</div>
					<div class="card-body p-3">
						<div id="blftb" class="table-responsive">
							<?php $this->load->view('comp/offer_table', array("stocks" => $foreigns, "show_qty" => $show_qty)); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>