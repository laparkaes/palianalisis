<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Offers extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		$this->sess_end_msg = "Su session ha sido finalizada. Ingrese nuevamente.";
		$this->load->model('subscription_model','subscription');
		$this->load->model('plan_model','plan');
		$this->load->model('company_model','company');
		date_default_timezone_set('America/Lima');
	}
	
	public function realtime(){
		if (!$this->session->userdata('logged_in')) redirect("/");
		
		$nationals = $foreigns = array();
		$all_stocks = $this->utility_lib->get_last_from_bvl(null, null, true);
		foreach($all_stocks as $item){
			$all_stocks_nemonico[$item->nemonico] = $item;
			
			if ($item->percentageChange > 0) $item->color = "success";
			elseif ($item->percentageChange < 0) $item->color = "danger";
			else $item->color = "info";
			
			$item->decimal = strlen(substr(strrchr($item->close, "."), 1));
			if ($item->decimal < 2) $item->decimal = 2;
			
			$item->sell_t = number_format($item->sell, $item->decimal);
			$item->buy_t = number_format($item->buy, $item->decimal);
			$item->close_t = number_format($item->close, $item->decimal);
			
			if ($item->sectorCode) array_push($nationals, $item); else array_push($foreigns, $item);
		}
		
		$data["title1"] = "Ofertas";
		$data["title2"] = "Tiempo Real";
		$data["show_qty"] = 999;
		$data["realtimes"] = array();
		$data["nationals"] = $nationals;
		$data["foreigns"] = $foreigns;
		$data["main"] = "offer/realtime";
		$this->load->view('layout',$data);
	}
	
	public function realtime_update(){
		if ($this->session->userdata('logged_in')){
			$realtimes = $this->input->post("nemonicos"); if (!$realtimes) $realtimes = array();
			
			$nationals = $foreigns = $rt_datas = array();
			$all_stocks = $this->utility_lib->get_last_from_bvl(null, null, true);
			foreach($all_stocks as $item){
				$all_stocks_nemonico[$item->nemonico] = $item;
				
				if ($item->percentageChange > 0) $item->color = "success";
				elseif ($item->percentageChange < 0) $item->color = "danger";
				else $item->color = "info";
				
				$item->decimal = strlen(substr(strrchr($item->close, "."), 1));
				if ($item->decimal < 2) $item->decimal = 2;
				
				$item->sell_t = number_format($item->sell, $item->decimal);
				$item->buy_t = number_format($item->buy, $item->decimal);
				$item->close_t = number_format($item->close, $item->decimal);
				
				if ($item->sectorCode) array_push($nationals, $item); else array_push($foreigns, $item);
				
				/* on production 
				if (in_array($item->nemonico, $realtimes)) array_push($rt_datas, $item); */
				/* on chart movements testing */
				if (in_array($item->nemonico, $realtimes)){
					$item->close = round($item->close * random_int(80, 120) / 100, 2);
					$item->sell = round($item->close * random_int(100, 180) / 100, 2);
					$item->buy = round($item->close * random_int(20, 100) / 100, 2);
					
					$item->sell_t = number_format($item->sell, $item->decimal);
					$item->buy_t = number_format($item->buy, $item->decimal);
					$item->close_t = number_format($item->close, $item->decimal);
					array_push($rt_datas, $item);
				} 
			}
			
			$datas = array("realtimes" => $realtimes, "show_qty" => 999);
			$datas["stocks"] = $nationals; $national_tb = $this->load->view('comp/offer_table', $datas, true);
			$datas["stocks"] = $foreigns; $foreign_tb = $this->load->view('comp/offer_table', $datas, true);	
			
			$response = array("status" => true, "ntb" => $national_tb, "ftb" => $foreign_tb, "rtd" => $rt_datas, "updated_at" => date('h:i a [Y-m-d]', time()));
		}else $response = array("status" => false, "msg" => $this->sess_end_msg);
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
	public function realtime_validation(){
		$qty = $this->input->post("qty");
		$today = date("Y-m-d");
		$msg = "";
		if ($this->session->userdata('logged_in')){
			$subscription = $this->subscription->get_valid($this->session->userdata('aid'));
			if ($subscription) $plan_id = $subscription->plan_id;
			else $plan_id = $this->plan->get_by_price(0)->id; 
				
			$rt_qty = $this->plan->get_by_id($plan_id)->monitoring_qty;
			if ($rt_qty) if ($qty >= $rt_qty) $msg = "Suscripcion actual cuenta monitoreo hasta ".$rt_qty." empresas.";
		}else $msg = $this->sess_end_msg;
		
		if ($msg) $response = array("status" => false, "msg" => $msg);
		else $response = array("status" => true);
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
}
