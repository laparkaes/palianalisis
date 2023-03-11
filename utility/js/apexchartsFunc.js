const convert_option = {maximumFractionDigits: 3};

function decimal_counter(num){
	var str = num.toString().split('.');
	if (str.length > 1) return str[1].length; else return 0;
}

function format_number(num, decimal){
	var aux = num.toFixed(decimal).split('.');
	if (aux.length > 1) return parseInt(aux[0]).toLocaleString('ko-KR', convert_option) + "." + aux[1];
	else return parseInt(aux[0]).toLocaleString('ko-KR', convert_option);
}

function apexcharts_normal_tooltip(series, seriesIndex, dataPointIndex, w){
	var date = new Date(w.globals.seriesX[seriesIndex][dataPointIndex]);
	var year = date.getFullYear();
	var month = date.getMonth() + 1; if (month < 10) month = "0" + month;
	var day = date.getDate(); if (day < 10) day = "0" + day;
	var dateString = year + "-" + month + "-" + day;
	
	var title = '<div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px; border-bottom: 1px solid ' + w.globals.colors[0] + ';">' + dateString + '</div>';
	var content = "";
	var series_length = w.globals.seriesNames.length;
	for(i = 0; i < series_length; i++){
		if (w.globals.series[i].hasOwnProperty(dataPointIndex)) content = content + '<div class="apexcharts-tooltip-series-group" style="order: ' + i + '; display: flex; color: ' + w.globals.colors[i] + '"><div class="apexcharts-tooltip-text w-100" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label float-left">' + w.globals.seriesNames[i] + '</span><span class="apexcharts-tooltip-text-y-value float-right">' + format_number(w.globals.series[i][dataPointIndex], 2) + '</span></div></div></div>';	
		
	}
	
	return (title + content);
}

function apexcharts_candle_tooltip_tb(series, seriesIndex, dataPointIndex, w){
	var date = new Date(w.globals.seriesX[seriesIndex][dataPointIndex]);
	var year = date.getFullYear();
	var month = date.getMonth() + 1; if (month < 10) month = "0" + month;
	var day = date.getDate(); if (day < 10) day = "0" + day;
	var dateString = year + "-" + month + "-" + day;
	
	var title = '<div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px; border-bottom: 1px solid ' + w.globals.colors[0] + ';">' + dateString + '</div>';
	var content = '<table style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;" class="text-center"><tr>';
	
	var o = w.globals.seriesCandleO[0][dataPointIndex];
	var h = w.globals.seriesCandleH[0][dataPointIndex];
	var l = w.globals.seriesCandleL[0][dataPointIndex];
	var c = w.globals.seriesCandleC[0][dataPointIndex];
	var decimal = Math.max(decimal_counter(o), decimal_counter(h), decimal_counter(l), decimal_counter(c));
	if (decimal < 2) decimal = 2;
	
	content = content + '<td class="px-1"><span>Apertura</span><br/><strong>' + format_number(o, decimal) + '</strong></td>';
	content = content + '<td class="px-1"><span>Maximo</span><br/><strong>' + format_number(h, decimal) + '</strong></td>';
	content = content + '<td class="px-1"><span>Minimo</span><br/><strong>' + format_number(l, decimal) + '</strong></td>';
	content = content + '<td class="px-1"><span>Cierre</span><br/><strong>' + format_number(c, decimal) + '</strong></td>';
	
	var series_length = w.globals.seriesNames.length;
	for(i = 1; i < series_length; i++){
		if (w.globals.series[i].hasOwnProperty(dataPointIndex)) content = content + '<td class="px-1" style="color: ' + w.globals.colors[i] + ';"><span>' + w.globals.seriesNames[i] + '</span><br/><strong>' + format_number(w.globals.series[i][dataPointIndex], 0) + '</strong></td>';
	}
	
	content = content + '</tr><table>';
	
	return (title + content);
}

function apexcharts_tooltip_tb(series, seriesIndex, dataPointIndex, w){
	var date = new Date(w.globals.seriesX[seriesIndex][dataPointIndex]);
	var year = date.getFullYear();
	var month = date.getMonth() + 1; if (month < 10) month = "0" + month;
	var day = date.getDate(); if (day < 10) day = "0" + day;
	var dateString = year + "." + month + "." + day;
	
	var title = '<div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px; border-bottom: 1px solid ' + w.globals.colors[seriesIndex] + ';">' + dateString + '</div>';
	var content = '<table style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;" class="text-center"><tr>';
	
	var series_length = w.globals.seriesNames.length;
	for(i = 0; i < series_length; i++){
		if (w.globals.series[i].hasOwnProperty(dataPointIndex)) content = content + '<td class="px-1" style="color: ' + w.globals.colors[i] + ';"><span>' + w.globals.seriesNames[i] + '</span><br/><strong>' + w.globals.series[i][dataPointIndex].toLocaleString('ko-KR', convert_option) + '</strong></td>';
	}
	
	content = content + '</tr><table>';
	
	return (title + content);
}

function apexcharts_tooltip(series, seriesIndex, dataPointIndex, w){
	var date = new Date(w.globals.seriesX[seriesIndex][dataPointIndex]);
	var year = date.getFullYear();
	var month = date.getMonth() + 1; if (month < 10) month = "0" + month;
	var day = date.getDate(); if (day < 10) day = "0" + day;
	var dateString = year + "." + month + "." + day;
	
	var title = '<div class="apexcharts-tooltip-title" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px; border-bottom: 1px solid ' + w.globals.colors[seriesIndex] + ';">' + dateString + '</div>';
	var content = "";
	
	var color = "";
	var name = "";
	var data = 0;
	var series_length = w.globals.seriesNames.length;
	if (series_length > 5) series_length = 5;
		
	for(i = 0; i < series_length; i++){
		if (w.globals.series[i].hasOwnProperty(dataPointIndex)) content = content + '<div class="apexcharts-tooltip-series-group" style="order: ' + (i+1) + '; display: flex;"><span class="apexcharts-tooltip-marker" style="background-color: '+ w.globals.colors[i] +';"></span><div class="apexcharts-tooltip-text" style="font-family: Helvetica, Arial, sans-serif; font-size: 12px;"><div class="apexcharts-tooltip-y-group"><span class="apexcharts-tooltip-text-y-label">' + w.globals.seriesNames[i] + ': </span><span class="apexcharts-tooltip-text-y-value">' + w.globals.series[i][dataPointIndex].toLocaleString('ko-KR', convert_option) + '</span></div></div></div>';
	}

	return (title + content);
}