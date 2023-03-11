<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use LupeCode\phpTraderNative\TALib\Enum\MovingAverageType;
use LupeCode\phpTraderNative\Trader;

class Chart extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		if ($this->session->userdata('is_admin')) redirect("/admin/dashboard");
		else{
			date_default_timezone_set('America/Lima');
			$this->lang->load("chart", "spanish");
			$this->lang->load("system", "spanish");
			$this->default_op = array(
				"title" => array("margin" => 0, "floating" => true),
				"chart" => array("id" => "indicator", "group" => "palichart", "height" => 180, "toolbar" => array("show" => false), "zoom" => array("enabled" => false), "animations" => array("enabled" => false)),
				"colors" => array("#000", "#0bb4ff", "#e60049", "#50e991", "#9b19f5", "#ffa300", "#dc0ab4", "#b3d4ff", "#00bfa0"),
				"legend" => array("show" => true)
			);
		}
	}
	
	private function get_i($dates, $range){
		$term = explode(" ~ ", $range); $from = $term[0]; $to = $term[1];
		$i_start = $i_end = -1;
		$run_date = count($dates) - 1;
		for($i = $run_date; $i >= 0; $i--){
			if ($i_end == -1) $i_end = $i;
			if ($from <= date('Y-m-d', $dates[$i] / 1000)) $i_start = $i;
			else break;
		}
		return array("start" => $i_start, "end" => $i_end);
	}
	
	private function general_validation($i_term = null){
		if ($this->session->userdata('logged_in')){
			if ($i_term){
				if (($i_term["start"] > -1) and ($i_term["end"] >= -1)) $msg = "";
				else $msg = $this->lang->line('error_no_record_enough');
			}else $msg = "";
		}else $msg = $this->lang->line('error_session');
		return $msg;
	}
	
	private function period_validation($short, $long = null){
		if ($short > 1){
			if ($long != null){
				if ($long > 1){
					if ($short < $long) $msg = "";
					else $msg = $this->lang->line('error_period_cmp');		
				}else $msg = $this->lang->line('error_period');
			}else $msg = "";
		}else $msg = $this->lang->line('error_period');
		return $msg;
	}
	
	public function price(){
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		
		$msgs = array();
		$msg = $this->general_validation($i_term);
		if ($msg){
			array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
			$status = false;
			$options = null;
		}else{
			$opens = json_decode($this->input->post("opens"));
			$highs = json_decode($this->input->post("highs"));
			$lows = json_decode($this->input->post("lows"));
			$closes = json_decode($this->input->post("closes"));
		
			$prices = $pvalues = array();
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($prices, array("x" => $dates[$i], "y" => array($opens[$i], $highs[$i], $lows[$i], $closes[$i])));
				array_push($pvalues, $highs[$i]);
				array_push($pvalues, $lows[$i]);
			}
			$pmax = floatval(max($pvalues)); if ($pmax >= 0) $pmax = $pmax * 1.05; else $pmax = abs($pmax) * (-0.95);
			$pmin = floatval(min($pvalues)); if ($pmin >= 0) $pmin = $pmin * 0.95; else $pmin = abs($pmin) * (-1.05);
			
			$options = array(
				"chart" => array("id" => "price", "group" => "palichart", "height" => 290, "type" => "candlestick", "toolbar" => array("show" => false), "zoom" => array("enabled" => false), "animations" => array("enabled" => false)),
				"legend" => array("show" => false),
				"series" => array(array("name" => $this->lang->line('price'), "data" => $prices)),
				"stroke" => array("width" => array(1)),
				"title" => array("text" => $this->lang->line('price'), "margin" => 0, "floating" => true),
				"yaxis" => array("min" => $pmin, "max" => $pmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function adx(){
		$period = $this->input->post("period");	
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($period);
		if ($msg) array_push($msgs, array("dom_id" => "adx_period_msg", "type" => "error", "msg" => $msg));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$highs = json_decode($this->input->post("highs"));
			$lows = json_decode($this->input->post("lows"));
			$closes = json_decode($this->input->post("closes"));

			$result = $this->get_adx($highs, $lows, $closes, $period);
			$pdi = $result["pdi"]; $mdi = $result["mdi"]; $adx = $result["adx"];
			
			$show_interpretation = ($this->session->userdata('plan')->price and $this->input->post("chk_itp"));
			$chprice = $chpdi = $chmdi = $chadx = $pvalues = $annt = array();
			$avalues = array(0);
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($pvalues, $closes[$i]);
				
				if ($pdi[$i]){
					array_push($chpdi, array("x" => $dates[$i], "y" => $pdi[$i]));
					array_push($avalues, $pdi[$i]);
				}
				if ($pdi[$i]){
					array_push($chmdi, array("x" => $dates[$i], "y" => $mdi[$i]));
					array_push($avalues, $mdi[$i]);
				}
				if ($pdi[$i]){
					array_push($chadx, array("x" => $dates[$i], "y" => $adx[$i]));
					array_push($avalues, $adx[$i]);
				}
				
				if ($show_interpretation) switch(true){
					case (($adx[$i] > $pdi[$i]) and ($mdi[$i] > $pdi[$i])):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#28a74550", "strokeDashArray" => 0));
						break;
					case (($adx[$i] > $mdi[$i]) and ($pdi[$i] > $mdi[$i])):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#dc354550", "strokeDashArray" => 0));
						break;
				}
			}
			
			$pmax = floatval(max($pvalues)); if ($pmax >= 0) $pmax = $pmax * 1.05; else $pmax = abs($pmax) * (-0.95);
			$pmin = floatval(min($pvalues)); if ($pmin >= 0) $pmin = $pmin * 0.95; else $pmin = abs($pmin) * (-1.05);
			$amax = floatval(max($avalues)); if ($amax >= 0) $amax = $amax * 1.05; else $amax = abs($amax) * (-0.95);
			$amin = floatval(min($avalues)); if ($amin >= 0) $amin = $amin * 0.95; else $amin = abs($amin) * (-1.05);
			
			$options = $this->default_op;
			$options["title"]["text"] = "ADX";
			$options["chart"]["type"] = "line";
			$options["annotations"] = array("xaxis" => $annt);
			$options["stroke"] = array("width" => array(2, 1, 1, 1));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "line", "name" => "PDI", "data" => $chpdi),
				array("type" => "line", "name" => "MDI", "data" => $chmdi),
				array("type" => "line", "name" => "ADX", "data" => $chadx)
			);
			$options["yaxis"] = array(
				array("min" => $pmin, "max" => $pmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $amin, "max" => $amax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $amin, "max" => $amax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $amin, "max" => $amax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function atr(){
		$period = $this->input->post("period");	
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($period);
		if ($msg) array_push($msgs, array("dom_id" => "atr_period_msg", "type" => "error", "msg" => $msg));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$highs = json_decode($this->input->post("highs"));
			$lows = json_decode($this->input->post("lows"));
			$closes = json_decode($this->input->post("closes"));
			
			$atr = $this->get_atr($highs, $lows, $closes, $period);
			$chprice = $chatr = $pvalues = array();
			$avalues = array(0);
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i])); array_push($pvalues, $closes[$i]);
				if ($atr[$i]){
					array_push($chatr, array("x" => $dates[$i], "y" => $atr[$i]));
					array_push($avalues, $atr[$i]);	
				}
			}
					
			$pmax = floatval(max($pvalues)); if ($pmax >= 0) $pmax = $pmax * 1.05; else $pmax = abs($pmax) * (-0.95);
			$pmin = floatval(min($pvalues)); if ($pmin >= 0) $pmin = $pmin * 0.95; else $pmin = abs($pmin) * (-1.05);
			$amax = floatval(max($avalues)); if ($amax >= 0) $amax = $amax * 1.05; else $amax = abs($amax) * (-0.95);
			$amin = floatval(min($avalues)); if ($amin >= 0) $amin = $amin * 0.95; else $amin = abs($amin) * (-1.05);
			
			$options = $this->default_op;
			$options["title"]["text"] = "ATR";
			$options["chart"]["type"] = "line";
			$options["stroke"] = array("width" => array(2, 1));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "line", "name" => "ATR", "data" => $chatr)
			);
			$options["yaxis"] = array(
				array("min" => $pmin, "max" => $pmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $amin, "max" => $amax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function bb(){
		$period = $this->input->post("period");	
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($period);
		if ($msg) array_push($msgs, array("dom_id" => "bb_period_msg", "type" => "error", "msg" => $msg));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$closes = json_decode($this->input->post("closes"));
			$mupper = $this->input->post("mupper");
			$mlower = $this->input->post("mlower");
			$avgtype = $this->input->post("avgtype");	
			
			if (!strcmp("sma", $avgtype)) $is_sma = true; else $is_sma = false;
			$result = $this->get_bollinger($closes, $period, $mupper, $mlower, $is_sma);
			$uppers = $result["uppers"];
			$middles = $result["middles"];
			$lowers = $result["lowers"];
			
			$show_interpretation = ($this->session->userdata('plan')->price and $this->input->post("chk_itp"));
			$chprice = $chupper = $chmiddles = $chlowers = $values = $annt = array();
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($values, $closes[$i]);
				
				if ($uppers[$i]){
					array_push($chupper, array("x" => $dates[$i], "y" => $uppers[$i]));
					array_push($values, $uppers[$i]);
				}
				if ($middles[$i]) array_push($chmiddles, array("x" => $dates[$i], "y" => $middles[$i]));
				if ($lowers[$i]){
					array_push($chlowers, array("x" => $dates[$i], "y" => $lowers[$i]));
					array_push($values, $lowers[$i]);
				}
				
				if ($show_interpretation) switch(true){
					case ($lowers[$i] >= $closes[$i]):
						if ($lowers[$i]) array_push($annt, array("x" => $dates[$i], "borderColor" => "#28a74550", "strokeDashArray" => 0));
						break;
					case ($uppers[$i] <= $closes[$i]):
						if ($uppers[$i]) array_push($annt, array("x" => $dates[$i], "borderColor" => "#dc354550", "strokeDashArray" => 0));
						break;
				}
			}
			
			$max = floatval(max($values)); if ($max >= 0) $max = $max * 1.05; else $max = abs($max) * (-0.95);
			$min = floatval(min($values)); if ($min >= 0) $min = $min * 0.95; else $min = abs($min) * (-1.05);

			$options = $this->default_op;
			$options["title"]["text"] = "Bollinger Band";
			$options["chart"]["type"] = "line";
			$options["annotations"] = array("xaxis" => $annt);
			$options["stroke"] = array("width" => array(2, 1, 1, 1));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "line", "name" => $this->lang->line('top'), "data" => $chupper),
				array("type" => "line", "name" => $this->lang->line('bottom'), "data" => $chmiddles),
				array("type" => "line", "name" => $this->lang->line('middle'), "data" => $chlowers)
			);
			$options["yaxis"] = array(
				array("min" => $min, "max" => $max, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function cci(){
		$period = $this->input->post("period");	
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($period);
		if ($msg) array_push($msgs, array("dom_id" => "cci_period_msg", "type" => "error", "msg" => $msg));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$highs = json_decode($this->input->post("highs"));
			$lows = json_decode($this->input->post("lows"));
			$closes = json_decode($this->input->post("closes"));
			
			$cci = $this->get_cci($highs, $lows, $closes, $period);
			$show_interpretation = ($this->session->userdata('plan')->price and $this->input->post("chk_itp"));
			$chprice = $chcci = $pvalues = $annt = array();
			$cvalues = array(0);
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($pvalues, $closes[$i]);
				
				if ($cci[$i]){
					array_push($chcci, array("x" => $dates[$i], "y" => $cci[$i]));
					array_push($cvalues, $cci[$i]);
				}
				
				if ($show_interpretation) switch(true){
					case ($cci[$i] < -100):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#28a74550", "strokeDashArray" => 0));
						break;
					case ($cci[$i] > 100):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#dc354550", "strokeDashArray" => 0));
						break;
				}
			}
			
			$pmax = floatval(max($pvalues)); if ($pmax >= 0) $pmax = $pmax * 1.05; else $pmax = abs($pmax) * (-0.95);
			$pmin = floatval(min($pvalues)); if ($pmin >= 0) $pmin = $pmin * 0.95; else $pmin = abs($pmin) * (-1.05);
			$cmax = floatval(max($cvalues)); if ($cmax >= 0) $cmax = $cmax * 1.05; else $cmax = abs($cmax) * (-0.95);
			$cmin = floatval(min($cvalues)); if ($cmin >= 0) $cmin = $cmin * 0.95; else $cmin = abs($cmin) * (-1.05);
			
			$options = $this->default_op;
			$options["title"]["text"] = "CCI";
			$options["chart"]["type"] = "line";
			$options["annotations"] = array("xaxis" => $annt);
			$options["stroke"] = array("width" => array(2, 1));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "line", "name" => "CCI", "data" => $chcci)
			);
			$options["yaxis"] = array(
				array("min" => $pmin, "max" => $pmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $cmin, "max" => $cmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function ema(){
		$days = $this->input->post("days");
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		if (!$days) array_push($msgs, array("dom_id" => "ema_days_msg", "type" => "error", "msg" => $this->lang->line('error_option')));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$closes = json_decode($this->input->post("closes"));
			
			$chprice = $ema = $datas = $values = $series = $stroke = array();
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($values, $closes[$i]);
			}
			array_push($series, array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice));
			array_push($stroke, 2);
			
			foreach($days as $day) $ema[$day] = $this->get_ema($closes, $day);
			foreach($ema as $day => $item){
				$aux = array();
				for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
					if ($item[$i] > 0){
						array_push($aux, array("x" => $dates[$i], "y" => $item[$i]));
						array_push($values, $item[$i]);	
					}
				}
				array_push($series, array("type" => "line", "name" => "EMA".$day, "data" => $aux));
				array_push($stroke, 1);
			}
			
			$max = floatval(max($values)); if ($max >= 0) $max = $max * 1.05; else $max = abs($max) * (-0.95);
			$min = floatval(min($values)); if ($min >= 0) $min = $min * 0.95; else $min = abs($min) * (-1.05);
			
			$options = $this->default_op;
			$options["title"]["text"] = "CCI";
			$options["chart"]["type"] = "line";
			$options["stroke"] = array("width" => $stroke);
			$options["series"] = $series;
			$options["yaxis"] = array(
				array("min" => $min, "max" => $max, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function env(){
		$period = $this->input->post("period");	
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($period);
		if ($msg) array_push($msgs, array("dom_id" => "env_period_msg", "type" => "error", "msg" => $msg));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$sep = $this->input->post("sep");
			$closes = json_decode($this->input->post("closes"));
			
			$result = $this->get_envelope($closes, $period, $sep / 100);
			$uppers = $result["uppers"];
			$lowers = $result["lowers"];
			
			$show_interpretation = ($this->session->userdata('plan')->price and $this->input->post("chk_itp"));
			$chprice = $chupper = $chlowers = $values = $annt = array();
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($values, $closes[$i]);
				if ($uppers[$i]){
					array_push($chupper, array("x" => $dates[$i], "y" => $uppers[$i]));
					array_push($values, $uppers[$i]);
				}
				if ($lowers[$i]){
					array_push($chlowers, array("x" => $dates[$i], "y" => $lowers[$i]));
					array_push($values, $lowers[$i]);
				}
				
				
				if ($show_interpretation) switch(true){
					case ($lowers[$i] >= $closes[$i]):
						if ($lowers[$i]) array_push($annt, array("x" => $dates[$i], "borderColor" => "#28a74550", "strokeDashArray" => 0));
						break;
					case ($uppers[$i] <= $closes[$i]):
						if ($uppers[$i]) array_push($annt, array("x" => $dates[$i], "borderColor" => "#dc354550", "strokeDashArray" => 0));
						break;
				}
			}
			
			$max = floatval(max($values)); if ($max >= 0) $max = $max * 1.05; else $max = abs($max) * (-0.95);
			$min = floatval(min($values)); if ($min >= 0) $min = $min * 0.95; else $min = abs($min) * (-1.05);
			
			$options = $this->default_op;
			$options["title"]["text"] = "Envelope";
			$options["chart"]["type"] = "line";
			$options["annotations"] = array("xaxis" => $annt);
			$options["stroke"] = array("width" => array(2, 1, 1));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "line", "name" => $this->lang->line('top'), "data" => $chupper),
				array("type" => "line", "name" => $this->lang->line('bottom'), "data" => $chlowers)
			);
			$options["yaxis"] = array(
				array("min" => $min, "max" => $max, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function ich(){
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg){
			array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
			$options = null;
			$status = false;
		}else{
			$closes = json_decode($this->input->post("closes"));
			
			$result = $this->get_icloud($closes);
			$span_a = $result["span_a"];
			$span_b = $result["span_b"];
			
			$chprice = $chich = $values = array();
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($values, $closes[$i]);
				if (($span_b[$i] > 0) and ($span_a[$i] > 0)){
					array_push($chich, array("x" => $dates[$i], "y" => array($span_b[$i], $span_b[$i], $span_a[$i], $span_a[$i])));
					array_push($values, $span_a[$i]);
					array_push($values, $span_b[$i]);
				}
			}
			
			$max = floatval(max($values)); if ($max >= 0) $max = $max * 1.05; else $max = abs($max) * (-0.95);
			$min = floatval(min($values)); if ($min >= 0) $min = $min * 0.95; else $min = abs($min) * (-1.05);
			
			$i_end = $i_term["end"];
			$date = $dates[$i_end] / 1000;
			$i_end++;
			$limit = count($span_a);
			for($i = $i_end; $i < $limit; $i++){
				if (($span_b[$i] > 0) and ($span_a[$i] > 0)){
					$x_aux = strtotime('+'.(27 - ($limit - $i)).' day', $date) * 1000;
					array_push($chich, array("x" => $x_aux, "y" => array($span_b[$i], $span_b[$i], $span_a[$i], $span_a[$i])));
				}
			}
			
			$options = $this->default_op;
			$options["title"]["text"] = "Ichimoku cloud";
			$options["chart"]["type"] = "line";
			$options["stroke"] = array("width" => array(2, 0));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "candlestick", "name" => "Ichimoku Cloud", "data" => $chich)
			);
			$options["yaxis"] = array(
				array("min" => $min, "max" => $max, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msg" => $msgs, "options" => $options));
	}
	
	public function macd(){
		$f_period = $this->input->post("f_period");
		$s_period = $this->input->post("s_period");
		$sig_period = $this->input->post("sig_period");
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($f_period, $s_period);
		if ($msg) array_push($msgs, array("dom_id" => "macd_period_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($sig_period);
		if ($msg) array_push($msgs, array("dom_id" => "macd_sig_period_msg", "type" => "error", "msg" => $msg));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$closes = json_decode($this->input->post("closes"));
			
			$result = $this->get_macd($closes, $f_period, $s_period, $sig_period);
			$macd = $result["macd"];
			$macd_sig = $result["macd_sig"];
			$macd_div = $result["macd_div"];
			
			$show_interpretation = ($this->session->userdata('plan')->price and $this->input->post("chk_itp"));
			$chprice = $chmacd = $chmacd_sig = $chmacd_div = $pvalues = $annt = array();
			$mvalues = $dvalues = array(0);
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($pvalues, $closes[$i]);
				
				if ($macd[$i]){
					array_push($chmacd, array("x" => $dates[$i], "y" => $macd[$i]));
					array_push($mvalues, $macd[$i]);
				}
				if ($macd_sig[$i]){
					array_push($chmacd_sig, array("x" => $dates[$i], "y" => $macd_sig[$i]));
					array_push($mvalues, $macd_sig[$i]);
				}
				if ($macd_sig[$i]){
					array_push($chmacd_div, array("x" => $dates[$i], "y" => $macd_div[$i]));
					array_push($dvalues, $macd_div[$i]);
				}
				
				if ($show_interpretation) if ($i > 0) switch(true){
					case (($macd_div[$i] < 0) and ($macd_div[$i-1] < $macd_div[$i])):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#28a74550", "strokeDashArray" => 0));
						break;
					case (($macd_div[$i] > 0) and ($macd_div[$i-1] > $macd_div[$i])):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#dc354550", "strokeDashArray" => 0));
						break;
				}
			}
			
			$pmax = floatval(max($pvalues)); if ($pmax >= 0) $pmax = $pmax * 1.05; else $pmax = abs($pmax) * (-0.95);
			$pmin = floatval(min($pvalues)); if ($pmin >= 0) $pmin = $pmin * 0.95; else $pmin = abs($pmin) * (-1.05);
			$mmax = floatval(max($mvalues)); if ($mmax >= 0) $mmax = $mmax * 1.05; else $mmax = abs($mmax) * (-0.95);
			$mmin = floatval(min($mvalues)); if ($mmin >= 0) $mmin = $mmin * 0.95; else $mmin = abs($mmin) * (-1.05);
			$dmax = floatval(max($dvalues)); if ($dmax >= 0) $dmax = $dmax * 1.05; else $dmax = abs($dmax) * (-0.95);
			$dmin = floatval(min($dvalues)); if ($dmin >= 0) $dmin = $dmin * 0.95; else $dmin = abs($dmin) * (-1.05);

			$options = $this->default_op;
			$options["title"]["text"] = "MACD";
			$options["chart"]["type"] = "line";
			$options["annotations"] = array("xaxis" => $annt);
			$options["stroke"] = array("width" => array(2, 1, 1, 0));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "line", "name" => "MACD", "data" => $chmacd),
				array("type" => "line", "name" => $this->lang->line('signal_of')." MACD", "data" => $chmacd_sig),
				array("type" => "bar", "name" => $this->lang->line('divergence'), "data" => $chmacd_div)
			);
			$options["yaxis"] = array(
				array("min" => $pmin, "max" => $pmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $mmin, "max" => $mmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $mmin, "max" => $mmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $dmin, "max" => $dmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			$options["plotOptions"] = array("bar" => array("horizontal" => false, "columnWidth" => "100%", "endingShape" => "rounded", "colors" => array("ranges" => array(array("from" => -9999, "to" => 0, "color" => "rgba(0, 0, 255, 0.5)"), array("from" => 0.0000001, "to" => 9999, "color" => "rgba(255, 0, 0, 0.5)")))));
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function mfi(){
		$period = $this->input->post("period");	
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($period);
		if ($msg) array_push($msgs, array("dom_id" => "mfi_period_msg", "type" => "error", "msg" => $msg));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$highs = json_decode($this->input->post("highs"));
			$lows = json_decode($this->input->post("lows"));
			$closes = json_decode($this->input->post("closes"));
			$volumes = json_decode($this->input->post("volumes"));
			$mfi = $this->get_mfi($highs, $lows, $closes, $volumes, $period);
			
			$show_interpretation = ($this->session->userdata('plan')->price and $this->input->post("chk_itp"));
			$chprice = $chmfi = $pvalues = $annt = array();
			$mvalues = array(0);
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($pvalues, $closes[$i]);
				
				if ($mfi[$i]){
					array_push($chmfi, array("x" => $dates[$i], "y" => $mfi[$i]));
					array_push($mvalues, $mfi[$i]);
				}
				
				if ($show_interpretation) switch(true){
					case ($mfi[$i] < 20):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#28a74550", "strokeDashArray" => 0));
						break;
					case ($mfi[$i] > 80):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#dc354550", "strokeDashArray" => 0));
						break;
				}
			}
			
			$pmax = floatval(max($pvalues)); if ($pmax >= 0) $pmax = $pmax * 1.05; else $pmax = abs($pmax) * (-0.95);
			$pmin = floatval(min($pvalues)); if ($pmin >= 0) $pmin = $pmin * 0.95; else $pmin = abs($pmin) * (-1.05);
			$mmax = floatval(max($mvalues)); if ($mmax >= 0) $mmax = $mmax * 1.05; else $mmax = abs($mmax) * (-0.95);
			$mmin = floatval(min($mvalues)); if ($mmin >= 0) $mmin = $mmin * 0.95; else $mmin = abs($mmin) * (-1.05);

			$options = $this->default_op;
			$options["title"]["text"] = "MFI";
			$options["chart"]["type"] = "line";
			$options["annotations"] = array("xaxis" => $annt);
			$options["stroke"] = array("width" => array(2, 1));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "line", "name" => "MFI", "data" => $chmfi)
			);
			$options["yaxis"] = array(
				array("min" => $pmin, "max" => $pmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $mmin, "max" => $mmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function mom(){
		$period = $this->input->post("period");
		$sig_period = $this->input->post("sig_period");
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($period);
		if ($msg) array_push($msgs, array("dom_id" => "mom_period_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($sig_period);
		if ($msg) array_push($msgs, array("dom_id" => "mom_sig_period_msg", "type" => "error", "msg" => $msg));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$closes = json_decode($this->input->post("closes"));
			
			$result = $this->get_mom($closes, $period, $sig_period);
			$mom = $result["mom"];
			$mom_signal = $result["mom_signal"];
			
			$show_interpretation = ($this->session->userdata('plan')->price and $this->input->post("chk_itp"));
			$chprice = $chmom = $chmom_sig = $pvalues = $annt = array();
			$mvalues = array(0);
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($pvalues, $closes[$i]);
				if ($mom[$i]){
					array_push($chmom, array("x" => $dates[$i], "y" => $mom[$i]));
					array_push($mvalues, $mom[$i]);
				}
				if ($mom_signal[$i]){
					array_push($chmom_sig, array("x" => $dates[$i], "y" => $mom_signal[$i]));
					array_push($mvalues, $mom_signal[$i]);
				}
				
				if ($show_interpretation) switch(true){
					case ($this->is_golden_cross($mom, $mom_signal, $i)):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#28a74550", "strokeDashArray" => 0));
						break;
					case ($this->is_dead_cross($mom, $mom_signal, $i)):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#dc354550", "strokeDashArray" => 0));
						break;
				}
			}
			
			$pmax = floatval(max($pvalues)); if ($pmax >= 0) $pmax = $pmax * 1.05; else $pmax = abs($pmax) * (-0.95);
			$pmin = floatval(min($pvalues)); if ($pmin >= 0) $pmin = $pmin * 0.95; else $pmin = abs($pmin) * (-1.05);
			$mmax = floatval(max($mvalues)); if ($mmax >= 0) $mmax = $mmax * 1.05; else $mmax = abs($mmax) * (-0.95);
			$mmin = floatval(min($mvalues)); if ($mmin >= 0) $mmin = $mmin * 0.95; else $mmin = abs($mmin) * (-1.05);
			
			$options = $this->default_op;
			$options["title"]["text"] = "MOM";
			$options["chart"]["type"] = "line";
			$options["annotations"] = array("xaxis" => $annt);
			$options["stroke"] = array("width" => array(2, 1, 1));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "line", "name" => "MOM", "data" => $chmom),
				array("type" => "line", "name" => $this->lang->line('signal_of')." MOM", "data" => $chmom_sig)
			);
			$options["yaxis"] = array(
				array("min" => $pmin, "max" => $pmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $mmin, "max" => $mmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $mmin, "max" => $mmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function pbsar(){
		$acceleration = $this->input->post("acceleration");
		$maximum = $this->input->post("maximum");
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		if ($acceleration <= 0) array_push($msgs, array("dom_id" => "pbsar_acceleration_msg", "type" => "error", "msg" => $this->lang->line('error_acceleration')));
		if ($maximum <= 0) array_push($msgs, array("dom_id" => "pbsar_maximum_msg", "type" => "error", "msg" => $this->lang->line('error_maximum')));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$highs = json_decode($this->input->post("highs"));
			$lows = json_decode($this->input->post("lows"));
			$closes = json_decode($this->input->post("closes"));
			
			$pbsar = $this->get_parabolic_sar($highs, $lows, $acceleration, $maximum);
			$show_interpretation = ($this->session->userdata('plan')->price and $this->input->post("chk_itp"));
			$chprice = $chpbsar = $values = $annt = array();
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($values, $closes[$i]);
				if ($pbsar[$i]){
					array_push($chpbsar, array("x" => $dates[$i], "y" => $pbsar[$i]));
					array_push($values, $pbsar[$i]);
				}
				
				if ($show_interpretation) if ($i > 0) switch(true){
					case (($pbsar[$i-1] >= $closes[$i-1]) and ($pbsar[$i] < $closes[$i])):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#28a74550", "strokeDashArray" => 0));
						break;
					case (($pbsar[$i-1] <= $closes[$i-1]) and ($pbsar[$i] > $closes[$i])):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#dc354550", "strokeDashArray" => 0));
						break;
				}
			}
			
			$max = floatval(max($values)); if ($max >= 0) $max = $max * 1.05; else $max = abs($max) * (-0.95);
			$min = floatval(min($values)); if ($min >= 0) $min = $min * 0.95; else $min = abs($min) * (-1.05);
			
			$options = $this->default_op;
			$options["title"]["text"] = "Parabolic Sar";
			$options["chart"]["type"] = "line";
			$options["annotations"] = array("xaxis" => $annt);
			$options["stroke"] = array("width" => array(2, 0));
			$options["markers"] = array("size" => array(0, 2));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "scatter", "name" => "Parabolic Sar", "data" => $chpbsar),
			);
			$options["yaxis"] = array(
				array("min" => $min, "max" => $max, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function ppo(){
		$f_period = $this->input->post("f_period");
		$s_period = $this->input->post("s_period");
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($f_period, $s_period);
		if ($msg) array_push($msgs, array("dom_id" => "ppo_period_msg", "type" => "error", "msg" => $msg));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$dates = json_decode($this->input->post("dates"));
			$closes = json_decode($this->input->post("closes"));
			$avgtype = $this->input->post("avgtype");
		
			if (!strcmp("sma", $avgtype)) $is_sma = true; else $is_sma = false;
			$ppo = $this->get_ppo($closes, $f_period, $s_period, $is_sma);
			
			$show_interpretation = ($this->session->userdata('plan')->price and $this->input->post("chk_itp"));
			$chprice = $chppo = $pvalues = $ppvalues = $annt = array();
			$ppvalues = array(0);
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($pvalues, $closes[$i]);
				
				if ($ppo[$i]){
					array_push($chppo, array("x" => $dates[$i], "y" => $ppo[$i]));
					array_push($ppvalues, $ppo[$i]);
				}
				
				if ($show_interpretation) switch(true){
					case ($ppo[$i] < 0):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#28a74550", "strokeDashArray" => 0));
						break;
					case ($ppo[$i] > 0):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#dc354550", "strokeDashArray" => 0));
						break;
				}
			}
			
			$pmax = floatval(max($pvalues)); if ($pmax >= 0) $pmax = $pmax * 1.05; else $pmax = abs($pmax) * (-0.95);
			$pmin = floatval(min($pvalues)); if ($pmin >= 0) $pmin = $pmin * 0.95; else $pmin = abs($pmin) * (-1.05);
			$ppmax = floatval(max($ppvalues)); if ($ppmax >= 0) $ppmax = $ppmax * 1.05; else $ppmax = abs($ppmax) * (-0.95);
			$ppmin = floatval(min($ppvalues)); if ($ppmin >= 0) $ppmin = $ppmin * 0.95; else $ppmin = abs($ppmin) * (-1.05);
		
			$options = $this->default_op;
			$options["title"]["text"] = "PPO";
			$options["chart"]["type"] = "line";
			$options["annotations"] = array("xaxis" => $annt);
			$options["stroke"] = array("width" => array(2, 1));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "line", "name" => "PPO", "data" => $chppo)
			);
			$options["yaxis"] = array(
				array("min" => $pmin, "max" => $pmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $ppmin, "max" => $ppmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function pch(){
		$period = $this->input->post("period");	
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($period);
		if ($msg) array_push($msgs, array("dom_id" => "pch_period_msg", "type" => "error", "msg" => $msg));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$highs = json_decode($this->input->post("highs"));
			$lows = json_decode($this->input->post("lows"));
			$closes = json_decode($this->input->post("closes"));
		
			$result = $this->get_price_channel($highs, $lows, $period);
			$pch_u = $result["uppers"];
			$pch_l = $result["lowers"];
			
			$show_interpretation = ($this->session->userdata('plan')->price and $this->input->post("chk_itp"));
			$chprice = $chpch_u = $chpch_l = $values = $annt = array();
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($values, $closes[$i]);
				if ($pch_u[$i]){
					array_push($chpch_u, array("x" => $dates[$i], "y" => $pch_u[$i]));
					array_push($values, $pch_u[$i]);
				}
				if ($pch_l[$i]){
					array_push($chpch_l, array("x" => $dates[$i], "y" => $pch_l[$i]));
					array_push($values, $pch_l[$i]);
				}
				
				if ($show_interpretation) switch(true){
					case ($pch_l[$i] >= $closes[$i]):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#28a74550", "strokeDashArray" => 0));
						break;
					case ($pch_u[$i] <= $closes[$i]):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#dc354550", "strokeDashArray" => 0));
						break;
				}
			}
			
			$max = floatval(max($values)); if ($max >= 0) $max = $max * 1.05; else $max = abs($max) * (-0.95);
			$min = floatval(min($values)); if ($min >= 0) $min = $min * 0.95; else $min = abs($min) * (-1.05);
			
			$options = $this->default_op;
			$options["title"]["text"] = "Price Channel";
			$options["chart"]["type"] = "line";
			$options["annotations"] = array("xaxis" => $annt);
			$options["stroke"] = array("width" => array(2, 1, 1));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "line", "name" => $this->lang->line('top'), "data" => $chpch_u),
				array("type" => "line", "name" => $this->lang->line('bottom'), "data" => $chpch_l)
			);
			$options["yaxis"] = array(
				array("min" => $min, "max" => $max, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;	
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function rsi(){
		$period = $this->input->post("period");	
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($period);
		if ($msg) array_push($msgs, array("dom_id" => "rsi_period_msg", "type" => "error", "msg" => $msg));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$closes = json_decode($this->input->post("closes"));
			$rsi = $this->get_rsi($closes, $period);
			
			$show_interpretation = ($this->session->userdata('plan')->price and $this->input->post("chk_itp"));
			$chprice = $chrsi = $pvalues = $annt = array();
			$rvalues = array(0);
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($pvalues, $closes[$i]);
				
				if ($rsi[$i]){
					array_push($chrsi, array("x" => $dates[$i], "y" => $rsi[$i]));
					array_push($rvalues, $rsi[$i]);
				}
				
				if ($show_interpretation) switch(true){
					case ($rsi[$i] < 30):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#28a74550", "strokeDashArray" => 0));
						break;
					case ($rsi[$i] > 70):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#dc354550", "strokeDashArray" => 0));
						break;
				}
			}
			
			$pmax = floatval(max($pvalues)); if ($pmax >= 0) $pmax = $pmax * 1.05; else $pmax = abs($pmax) * (-0.95);
			$pmin = floatval(min($pvalues)); if ($pmin >= 0) $pmin = $pmin * 0.95; else $pmin = abs($pmin) * (-1.05);
			$rmax = floatval(max($rvalues)); if ($rmax >= 0) $rmax = $rmax * 1.05; else $rmax = abs($rmax) * (-0.95);
			$rmin = floatval(min($rvalues)); if ($rmin >= 0) $rmin = $rmin * 0.95; else $rmin = abs($rmin) * (-1.05);
			
			$options = $this->default_op;
			$options["title"]["text"] = "RSI";
			$options["chart"]["type"] = "line";
			$options["annotations"] = array("xaxis" => $annt);
			$options["stroke"] = array("width" => array(2, 1));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "line", "name" => "RSI", "data" => $chrsi)
			);
			$options["yaxis"] = array(
				array("min" => $pmin, "max" => $pmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $rmin, "max" => $rmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function sma(){
		$days = $this->input->post("days");
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		if (!$days) array_push($msgs, array("dom_id" => "sma_days_msg", "type" => "error", "msg" => $this->lang->line('error_option')));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$closes = json_decode($this->input->post("closes"));
			
			$chprice = $ema = $datas = $values = $series = $stroke = array();
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($values, $closes[$i]);
			}
			array_push($series, array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice));
			array_push($stroke, 2);
			
			foreach($days as $day) $ema[$day] = $this->get_sma($closes, $day);
			foreach($ema as $day => $item){
				$aux = array();
				for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
					if ($item[$i] > 0){
						array_push($aux, array("x" => $dates[$i], "y" => $item[$i]));
						array_push($values, $item[$i]);	
					}
				}
				array_push($series, array("type" => "line", "name" => "SMA".$day, "data" => $aux));
				array_push($stroke, 1);
			}
			
			$max = floatval(max($values)); if ($max >= 0) $max = $max * 1.05; else $max = abs($max) * (-0.95);
			$min = floatval(min($values)); if ($min >= 0) $min = $min * 0.95; else $min = abs($min) * (-1.05);
			
			$options = $this->default_op;
			$options["title"]["text"] = "SMA";
			$options["chart"]["type"] = "line";
			$options["stroke"] = array("width" => $stroke);
			$options["series"] = $series;
			$options["yaxis"] = array(
				array("min" => $min, "max" => $max, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function sto(){
		$fk_period = $this->input->post("fk_period");
		$sk_period = $this->input->post("sk_period");
		$d_period = $this->input->post("d_period");
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($fk_period, $sk_period);
		if ($msg) array_push($msgs, array("dom_id" => "sto_f_period_msg", "type" => "error", "msg" => $msg));

		$msg = $this->period_validation($d_period);
		if ($msg) array_push($msgs, array("dom_id" => "sto_d_period_msg", "type" => "error", "msg" => $msg));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$highs = json_decode($this->input->post("highs"));
			$lows = json_decode($this->input->post("lows"));
			$closes = json_decode($this->input->post("closes"));
			$k_avgtype = $this->input->post("k_avgtype");
			$d_avgtype = $this->input->post("d_avgtype");
		
			if (!strcmp("sma", $k_avgtype)) $is_k_sma = true; else $is_k_sma = false;
			if (!strcmp("sma", $d_avgtype)) $is_d_sma = true; else $is_d_sma = false;
			
			$sto = $this->get_stochastic($highs, $lows, $closes, $fk_period, $sk_period, $is_k_sma, $d_period, $is_d_sma);
			$sto_k = $sto["k"];
			$sto_d = $sto["d"];
			
			$show_interpretation = ($this->session->userdata('plan')->price and $this->input->post("chk_itp"));
			$chprice = $chsto_k = $chsto_d = $pvalues = $annt = array();
			$svalues = array(0);
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i])); array_push($pvalues, $closes[$i]);
				array_push($chsto_k, array("x" => $dates[$i], "y" => $sto_k[$i])); array_push($svalues, $sto_k[$i]);
				array_push($chsto_d, array("x" => $dates[$i], "y" => $sto_d[$i])); array_push($svalues, $sto_d[$i]);
				
				if ($show_interpretation) switch(true){
					case (($sto_k[$i] < 20) and ($sto_d[$i] < 20)):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#28a74550", "strokeDashArray" => 0));
						break;
					case (($sto_k[$i] > 80) and ($sto_d[$i] > 80)):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#dc354550", "strokeDashArray" => 0));
						break;
				}
			}
			
			$pmax = floatval(max($pvalues)); if ($pmax >= 0) $pmax = $pmax * 1.05; else $pmax = abs($pmax) * (-0.95);
			$pmin = floatval(min($pvalues)); if ($pmin >= 0) $pmin = $pmin * 0.95; else $pmin = abs($pmin) * (-1.05);
			$smax = floatval(max($svalues)); if ($smax >= 0) $smax = $smax * 1.05; else $smax = abs($smax) * (-0.95);
			$smin = floatval(min($svalues)); if ($smin >= 0) $smin = $smin * 0.95; else $smin = abs($smin) * (-1.05);
			
			$options = $this->default_op;
			$options["title"]["text"] = "Stochastic oscillator";
			$options["chart"]["type"] = "line";
			$options["annotations"] = array("xaxis" => $annt);
			$options["stroke"] = array("width" => array(2, 1, 1));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "line", "name" => "Stochastic K", "data" => $chsto_k),
				array("type" => "line", "name" => "Stochastic D", "data" => $chsto_d)
			);
			$options["yaxis"] = array(
				array("min" => $pmin, "max" => $pmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $smin, "max" => $smax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $smin, "max" => $smax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function trix(){
		$period = $this->input->post("period");
		$sig_period = $this->input->post("sig_period");
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg) array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($period);
		if ($msg) array_push($msgs, array("dom_id" => "trix_period_msg", "type" => "error", "msg" => $msg));
		
		$msg = $this->period_validation($sig_period);
		if ($msg) array_push($msgs, array("dom_id" => "trix_sig_period_msg", "type" => "error", "msg" => $msg));
		
		if ($msgs){
			$options = null;
			$status = false;
		}else{
			$closes = json_decode($this->input->post("closes"));

			$result = $this->get_trix($closes, $period, $sig_period);
			$trix = $result["trix"];
			$trix_sig = $result["trix_signal"];
			
			$show_interpretation = ($this->session->userdata('plan')->price and $this->input->post("chk_itp"));
			$chprice = $chtrix = $chtrix_sig = $pvalues = $tvalues = $annt = array();
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++){
				array_push($chprice, array("x" => $dates[$i], "y" => $closes[$i]));
				array_push($pvalues, $closes[$i]);
				if ($trix[$i]){
					array_push($chtrix, array("x" => $dates[$i], "y" => $trix[$i]));
					array_push($tvalues, $trix[$i]);
				}
				if ($trix_sig[$i]){
					array_push($chtrix_sig, array("x" => $dates[$i], "y" => $trix_sig[$i]));
					array_push($tvalues, $trix_sig[$i]);
				}
				
				if ($show_interpretation) switch(true){
					case ($this->is_golden_cross($trix, $trix_sig, $i)):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#28a74550", "strokeDashArray" => 0));
						break;
					case ($this->is_dead_cross($trix, $trix_sig, $i)):
						array_push($annt, array("x" => $dates[$i], "borderColor" => "#dc354550", "strokeDashArray" => 0));
						break;
				}
			}
			
			$pmax = floatval(max($pvalues)); if ($pmax >= 0) $pmax = $pmax * 1.05; else $pmax = abs($pmax) * (-0.95);
			$pmin = floatval(min($pvalues)); if ($pmin >= 0) $pmin = $pmin * 0.95; else $pmin = abs($pmin) * (-1.05);
			$tmax = floatval(max($tvalues)); if ($tmax >= 0) $tmax = $tmax * 1.05; else $tmax = abs($tmax) * (-0.95);
			$tmin = floatval(min($tvalues)); if ($tmin >= 0) $tmin = $tmin * 0.95; else $tmin = abs($tmin) * (-1.05);
		
			$options = $this->default_op;
			$options["title"]["text"] = "Trix";
			$options["chart"]["type"] = "line";
			$options["annotations"] = array("xaxis" => $annt);
			$options["stroke"] = array("width" => array(2, 1, 1));
			$options["series"] = array(
				array("type" => "line", "name" => $this->lang->line('price'), "data" => $chprice),
				array("type" => "line", "name" => "Trix", "data" => $chtrix),
				array("type" => "line", "name" => $this->lang->line('signal_of')." Trix", "data" => $chtrix_sig)
			);
			$options["yaxis"] = array(
				array("min" => $pmin, "max" => $pmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $tmin, "max" => $tmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false)),
				array("min" => $tmin, "max" => $tmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	public function volume(){
		$dates = json_decode($this->input->post("dates"));
		$i_term = $this->get_i($dates, $this->input->post("term"));
		$msgs = array();
		
		$msg = $this->general_validation($i_term);
		if ($msg){
			array_push($msgs, array("dom_id" => "result_chart_msg", "type" => "error", "msg" => $msg));
			$options = null;
			$status = false;
		}else{
			$opens = json_decode($this->input->post("opens"));
			$closes = json_decode($this->input->post("closes"));
			$volumes = json_decode($this->input->post("volumes"));
			
			$color_up = "#00b746"; $color_down = "#ef403c";
			$chprice = $chvolume = $pvalues = $annotations = array();
			$vvalues = array(0);
			for($i = $i_term["start"]; $i <= $i_term["end"]; $i++) if ($volumes[$i]){
				if ($opens[$i] > $closes[$i]) $color = $color_down; else $color = $color_up;
				array_push($chvolume, array("x" => $dates[$i], "y" => $volumes[$i], "fillColor" => $color));
				array_push($vvalues, $volumes[$i]);
			}
			
			$vmax = floatval(max($vvalues)); if ($vmax >= 0) $vmax = $vmax * 1.05; else $vmax = abs($vmax) * (-0.95);
			$vmin = floatval(min($vvalues)); if ($vmin >= 0) $vmin = $vmin * 0.95; else $vmin = abs($vmin) * (-1.05);
			
			$options = $this->default_op;
			$options["title"]["text"] = $this->lang->line('volume');
			$options["chart"]["id"] = "";
			$options["chart"]["group"] = "";
			$options["chart"]["type"] = "bar";
			$options["series"] = array(
				array("type" => "bar", "name" => $this->lang->line('volume'), "data" => $chvolume)
			);
			$options["yaxis"] = array(
				array("min" => $vmin, "max" => $vmax, "tickAmount" => 2, "decimalsInFloat" => 2, "floating" => true, "labels" => array("show" => false))
			);
			
			$status = true;
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs, "options" => $options));
	}
	
	private function blank_array($count){
		$arr = array();
		for($i = 0; $i < $count; $i++) $arr[$i] = 0;
		return $arr;
	}
	
	private function get_adx($highs, $lows, $closes, $period){
		$pdi = Trader::plus_di($highs, $lows, $closes, $period);
		$mdi = Trader::minus_di($highs, $lows, $closes, $period);
		$adx = Trader::adx($highs, $lows, $closes, $period);
		
		if ($pdi){
			$arr = $this->blank_array(count($closes) - count($pdi));
			$pdi = array_merge($arr, $pdi);
			$mdi = array_merge($arr, $mdi);
		}else $pdi = $mdi = $this->blank_array(count($closes));
		
		if ($adx) $adx = array_merge($this->blank_array(count($closes) - count($adx)), $adx);
		else $adx = $this->blank_array(count($closes));
		
		return array("pdi" => $pdi, "mdi" => $mdi, "adx" => $adx);
	}
	
	private function get_atr($highs, $lows, $closes, $period){
		$atr = Trader::atr($highs, $lows, $closes, $period);
		if ($atr) return array_merge($this->blank_array(count($closes) - count($atr)), $atr);
		else return $this->blank_array(count($closes));
	}
	
	private function get_bollinger($closes, $period, $mupper, $mlower, $is_sma){
		if ($is_sma) $avg_type = MovingAverageType::SMA; else $avg_type = MovingAverageType::EMA;
		$bollinger_general = Trader::bbands($closes, $period, $mupper, $mlower, $avg_type);
		if ($bollinger_general){
			$arr = $this->blank_array(count($closes) - count($bollinger_general["UpperBand"]));
			$uppers = array_merge($arr, $bollinger_general["UpperBand"]);
			$middles = array_merge($arr, $bollinger_general["MiddleBand"]);
			$lowers = array_merge($arr, $bollinger_general["LowerBand"]);
		}else $uppers = $middles = $lowers = $this->blank_array(count($closes));
		
		return array("uppers" => $uppers, "middles" => $middles, "lowers" => $lowers);
	}
	
	private function get_cci($highs, $lows, $closes, $period){
		$cci = Trader::cci($highs, $lows, $closes, $period);
		if ($cci) return array_merge($this->blank_array(count($closes) - count($cci)), $cci);
		else return $this->blank_array(count($closes));
	}
	
	private function get_ema($closes, $period){
		$ema = Trader::ema($closes, $period);
		if ($ema) return array_merge($this->blank_array(count($closes) - count($ema)), $ema);
		else return $this->blank_array(count($closes));
	}
	
	private function get_envelope($closes, $period, $diff){
		$sma_20 = $this->get_sma($closes, $period);
		$top = 1 + $diff;
		$bottom = 1 - $diff;
		
		$uppers = $lowers = array();
		foreach($sma_20 as $item){
			array_push($uppers, $item * $top);
			array_push($lowers, $item * $bottom);
		}
		
		return array("uppers" => $uppers, "lowers" => $lowers);
	}
	
	private function get_icloud($closes){
		$span_a = $span_b = array();
		if ($closes){
			$max_9 = Trader::max($closes, 9);
			$min_9 = Trader::min($closes, 9);
			if ($max_9){
				$arr = $this->blank_array(count($closes) - count($max_9));
				$max_9 = array_merge($arr, $max_9);
				$min_9 = array_merge($arr, $min_9);
			}else $max_9 = $min_9 = $this->blank_array(count($closes));
			
			$max_26 = Trader::max($closes, 26);
			$min_26 = Trader::min($closes, 26);
			if ($max_26){
				$arr = $this->blank_array(count($closes) - count($max_26));
				$max_26 = array_merge($arr, $max_26);
				$min_26 = array_merge($arr, $min_26);
			}else $max_26 = $min_26 = $this->blank_array(count($closes));
			
			$max_52 = Trader::max($closes, 52);
			$min_52 = Trader::min($closes, 52);
			if ($max_52){
				$arr = $this->blank_array(count($closes) - count($max_52));
				$max_52 = array_merge($arr, $max_52);
				$min_52 = array_merge($arr, $min_52);
			}else $max_52 = $min_52 = $this->blank_array(count($closes));
			
			for($i = 0; $i < 26; $i++){
				$span_a[$i] = 0;
				$span_b[$i] = 0;
			}
			
			foreach($closes as $i => $value){
				$conversion_line = ($min_9[$i] + $max_9[$i]) / 2;
				$base_line = ($min_26[$i] + $max_26[$i]) / 2;
				
				$span_a[$i + 26] = ($conversion_line + $base_line) / 2;
				$span_b[$i + 26] = ($min_52[$i] + $max_52[$i]) / 2;
			}
		}
		
		return array("span_a" => $span_a, "span_b" => $span_b);
	}
	
	private function get_macd($closes, $fast_period, $slow_period, $signal_period){
		$macd_general = Trader::macd($closes, $fast_period, $slow_period, $signal_period);
		if ($macd_general){
			$arr = $this->blank_array(count($closes) - count($macd_general["MACD"]));
			$macd = array_merge($arr, $macd_general["MACD"]);
			$macd_signal = array_merge($arr, $macd_general["MACDSignal"]);
			$macd_divergence = array_merge($arr, $macd_general["MACDHist"]);
		}else $macd = $macd_signal = $macd_divergence = $this->blank_array(count($closes));
		
		return array("macd" => $macd, "macd_sig" => $macd_signal, "macd_div" => $macd_divergence);
	}
	
	private function get_mfi($highs, $lows, $closes, $negos, $period){
		$mfi = Trader::mfi($highs, $lows, $closes, $negos, $period);
		if ($mfi) return array_merge($this->blank_array(count($closes) - count($mfi)), $mfi);
		else return $this->blank_array(count($closes));
	}
	
	private function get_mom($closes, $period, $period_signal){
		$mom = Trader::mom($closes, $period);
		if ($mom){
			$mom = array_merge($this->blank_array(count($closes) - count($mom)), $mom);
			$mom_signal = $this->get_sma($mom, $period_signal);
		}else $mom = $mom_signal = $this->blank_array(count($closes));
		
		return array("mom" => $mom, "mom_signal" => $mom_signal);
	}
	
	private function get_parabolic_sar($highs, $lows, $acceleration, $maximum){
		$parabolic_sar = Trader::sar($highs, $lows, $acceleration, $maximum);
		if ($parabolic_sar) return array_merge($this->blank_array(count($highs) - count($parabolic_sar)), $parabolic_sar);
		else return $this->blank_array(count($highs));
	}
	
	private function get_ppo($closes, $fast_period, $slow_period, $is_sma){
		if ($is_sma) $ma = MovingAverageType::SMA; else $ma = MovingAverageType::EMA;
		$ppo = Trader::ppo($closes, $fast_period, $slow_period, $ma);
		if ($ppo) return array_merge($this->blank_array(count($closes) - count($ppo)), $ppo);
		else return $this->blank_array(count($closes));
	}
	
	private function get_price_channel($highs, $lows, $period){
		$uppers = Trader::max($highs, $period);
		$lowers = Trader::min($lows, $period);
		if ($uppers){
			$arr = $this->blank_array(count($highs) - count($uppers));
			$uppers = array_merge($arr, $uppers);
			$lowers = array_merge($arr, $lowers);
		}else $uppers = $lowers = $this->blank_array(count($highs));
		
		return array("uppers" => $uppers, "lowers" => $lowers);
	}
	
	private function get_rsi($closes, $period){
		$rsi = Trader::rsi($closes, $period);
		if ($rsi) return array_merge($this->blank_array(count($closes) - count($rsi)), $rsi);
		else return $this->blank_array(count($closes));
	}
	
	private function get_sma($closes, $period){
		$sma = Trader::sma($closes, $period);
		if ($sma) return array_merge($this->blank_array(count($closes) - count($sma)), $sma);
		else return $this->blank_array(count($closes));
	}
	
	private function get_stochastic($highs, $lows, $closes, $fk_period, $sk_period, $is_k_sma, $d_period, $is_d_sma){
		if ($is_k_sma) $k_ma = MovingAverageType::SMA; else $k_ma = MovingAverageType::EMA;
		if ($is_d_sma) $d_ma = MovingAverageType::SMA; else $d_ma = MovingAverageType::EMA;
		$stochastic =  Trader::stoch($highs, $lows, $closes, $fk_period, $sk_period, $k_ma, $d_period, $d_ma);
		if ($stochastic){
			$arr = $this->blank_array(count($highs) - count($stochastic["SlowK"]));
			$k = array_merge($arr, $stochastic["SlowK"]);
			$d = array_merge($arr, $stochastic["SlowD"]);
		}else $k = $d = $this->blank_array(count($closes));
		
		return array("k" => $k, "d" => $d);
	}
	
	private function get_trix($closes, $period, $period_signal){
		$trix = Trader::trix($closes, $period);
		if ($trix){
			$trix = array_merge($this->blank_array(count($closes) - count($trix)), $trix);
			$trix_signal = $this->get_sma($trix, $period_signal);
		}else $trix = $trix_signal = $this->blank_array(count($closes));
		
		return array("trix" => $trix, "trix_signal" => $trix_signal);
	}
	
	private function is_golden_cross($ind1, $ind2, $i){
		if ($i > 0) return (($ind1[$i - 1] <= $ind2[$i - 1]) and ($ind1[$i] > $ind2[$i]));
		else return false;
	}
	
	private function is_dead_cross($ind1, $ind2, $i){
		if ($i > 0) return (($ind1[$i - 1] >= $ind2[$i - 1]) and ($ind1[$i] < $ind2[$i]));
		else return false;
		
	}
}
