<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Market extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		if ($this->session->userdata('is_admin')) redirect("/admin/dashboard");
		else{
			date_default_timezone_set('America/Lima');
			$this->lang->load("system", "spanish");
			$this->lang->load("market", "spanish");
			$this->load->model('favorite_model','favorite');
			$this->load->model('subscription_model','subscription');
			$this->load->model('plan_model','plan');
			$this->load->model('company_model','company');
			$this->load->model('indicator_model','indicator');
			$this->load->model('indicator_plan_model','indicator_plan');
			$this->load->model('record_model','record');
		}
	}
	
	private function get_last_market(){
		$records = $this->utility_lib->get_last_from_bvl(null, null, false)->records;
		foreach($records as $i => $item){
			if (!property_exists($item, "buy")) $item->buy = 0;
			if (!property_exists($item, "sell")) $item->sell = 0;
				
			if ($item->buy or $item->sell){
				$item->decimal = strlen(substr(strrchr($item->close, "."), 1));
				if ($item->decimal < 2) $item->decimal = 2;
				
				if ($item->sell) $item->sell_t = $item->currencySymbol." ".number_format(floatval($item->sell), $item->decimal);
				else $item->sell_t = "-";
				if ($item->buy) $item->buy_t = $item->currencySymbol." ".number_format(floatval($item->buy), $item->decimal);
				else $item->buy_t = "-";
				
				if (!property_exists($item, "close")) $item->close = 0;
				if (!$item->close) $item->close = $item->yesterdayClose;
				if ($item->close) $item->close_t = $item->currencySymbol." ".number_format(floatval($item->close), $item->decimal);
				else $item->close_t = "-";
				
				if ($item->is_national) $item->type_class = "national"; else $item->type_class = "foreign"; 
			}else unset($records[$i]);
		}
		
		return $records;
	}

	public function offers(){
		if (!$this->session->userdata('logged_in')) redirect("/");
		
		$data = array(
			"title" => $this->lang->line('offers'),
			"records" => $this->get_last_market(),
			"updated_at" => date('Y-m-d h:i a', time()),
			"js_init" => "market/offers_init.js",
			"main" => "market/offers"
		);
		$this->load->view('layout',$data);
	}
	
	public function offers_update_canvas(){
		$response = array();
		if ($this->session->userdata('logged_in')){
			$response["status"] = true;
			$response["content"] = $this->load->view('market/offers_canvas', array("records" => $this->get_last_market()), true);
			$response["updated_at"] = date('Y-m-d h:i a', time());	
		}else{
			$response["status"] = false;
			$response["msg"] = $this->lang->line('error_session');
		}
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
	public function companies(){
		if (!$this->session->userdata('logged_in')) redirect("/");
		
		$favorites_nemonicos = array();
		$my_favorites = $this->favorite->get($this->session->userdata('aid'), null);
		foreach($my_favorites as $item) array_push($favorites_nemonicos, $item->nemonico);
		
		$data = array(
			"title" => $this->lang->line('companies'),
			"companies" => $this->company->get_general(),
			"favorites_nemonicos" => $favorites_nemonicos,
			"js_init" => "market/companies_init.js",
			"main" => "market/companies"
		);
		$this->load->view('layout',$data);
	}
	
	private function get_last_year($records){
		$data_per = array();
		foreach($records as $i => $item){
			if ($item->close){
				if ($item->last_year_per) break;
				else {
					$min = $max = $item->close;
					$one_year = date('Y-m-d', strtotime('-1 year', strtotime($item->date)));
					$i_run = $i;
					while($records[$i_run]->date >= $one_year){
						if ($records[$i_run]->close){
							if ($min > $records[$i_run]->close) $min = $records[$i_run]->close;
							elseif ($max < $records[$i_run]->close) $max = $records[$i_run]->close;	
						}
						$i_run++;
						if ($i_run >= count($records)) break;
					}
					
					if ($max == $min) $per = 0;
					else $per = ($item->close - $min) * 100 / ($max - $min);
					
					array_push($data_per, array("id" => $item->id, "last_year_per" => $per));
				}	
			}
		}
		return $data_per;
	}
	
	public function company(){
		if (!$this->session->userdata('logged_in')) redirect("/");
		
		$nemonico = $this->input->get("n");
		$company = $this->company->get_by_nemonico($nemonico);
		if (!$company) redirect("/market/companies");
		if ($this->favorite->get($this->session->userdata('aid'), $nemonico)) $company->ic_fav = "fa-star text-warning";
		else $company->ic_fav = "fa-star-o text-muted";
		
		$last_record = $this->record->get_last_single($nemonico);
		$load_bvl = $this->utility_lib->get_last_from_bvl(str_replace("/", "%2F", $nemonico), null, false);
		if ($load_bvl->records){
			$last = $load_bvl->records[0];
			
			if ($last->close) $last->price_t = $last->currencySymbol." ".number_format($last->close, 2);
			elseif ($last->yesterdayClose) $last->price_t = $last->currencySymbol." ".number_format($last->yesterdayClose, 2);
			else $last->price_t = "-";
		
			if ($last->percentageChange > 0){
				$last->var_t = abs($last->percentageChange)."%";
				$last->var_ic = "caret-up";
				$last->var_color = "success";
			}elseif ($last->percentageChange < 0){
				$last->var_t = abs($last->percentageChange)."%";
				$last->var_ic = "caret-down";
				$last->var_color = "danger";
			}else{
				$last->var_t = "-";
				$last->var_ic = "";
				$last->var_color = "";
			}
			
			if ($last->amountNegotiated) $last->amountNegotiated_t = $last->currencySymbol." ".number_format($last->amountNegotiated, 2); else $last->amountNegotiated_t = "-";
			
			if ($last->quantityNegotiated) $last->quantityNegotiated_t = number_format($last->quantityNegotiated);
			else $last->quantityNegotiated_t = "-";
			
			if ($last->buy) $last->buy_t = $last->currencySymbol." ".number_format($last->buy, 2); else $last->buy_t = "-";
			if ($last->sell) $last->sell_t = $last->currencySymbol." ".number_format($last->sell, 2); else $last->sell_t = "-";
			
			if ($last_record){
				if (strtotime($last_record->date) >= strtotime($last->yesterday)) $from = null;
				else $from = date('Y-m-d', strtotime('+1 day', strtotime($last_record->date)));
			}else $from = "2000-01-01";
			
			if ($from) {
				$histories = $this->utility_lib->get_stocks_from_bvl(str_replace("/", "%2F", $nemonico), $from, $last->date);
				if ($histories){
					$arr_dates = array();
					$dates = $this->record->get_dates($nemonico, $from, $last->date);
					foreach($dates as $item) array_push($arr_dates, $item->date);
					
					foreach($histories as $i => $item){
						if (in_array($item->date, $arr_dates)) unset($histories[$i]);
						else{
							if (!strcmp($item->currencySymbol, $this->lang->line('usd_symbol'))) $item->amountNegotiated = $item->dollarAmountNegotiated;
							else $item->amountNegotiated = $item->solAmountNegotiated;
							
							unset($item->id);
							unset($item->solAmountNegotiated);
							unset($item->dollarAmountNegotiated);	
						}
					}
					
					if ($histories) $this->record->insert_multi($histories);
				}
			}
		}else{
			$last = new stdClass;
			$last->close = 0;
			$last->price_t = "-";
			$last->var_t = "-";
			$last->var_ic = "";
			$last->var_color = "";
			$last->amountNegotiated_t = "-";
			$last->quantityNegotiated_t = "-";
			$last->buy_t = "-";
			$last->sell_t = "-";
		}
		
		$indicators = $btns_daterange = array();
		$records = $this->record->get_by_nemonico($nemonico);
		if ($records){
			$data_per = $this->utility_lib->calculate_last_year_per_data($records);
			if ($data_per) $this->record->update_multi($data_per);
			if ($last) if ($last->close) if (strtotime($last->date) > strtotime($records[0]->date)) array_unshift($records, $last);
			
			$ind_plan = $this->indicator_plan->get_by_ids(null, $this->session->userdata('plan')->id);
			$indicator_ids = array();
			foreach($ind_plan as $item) array_push($indicator_ids, $item->indicator_id);
			
			$indicators = $this->indicator->get_by_ids($indicator_ids);
			foreach($indicators as $i => $item) /* removing indicators without view file */
				if (!is_file(APPPATH."views/market/indicators/".$item->code."_form.php")) unset($indicators[$i]);
			
			$to = date('Y-m-d');
			$arr_btns = array(
				array($this->lang->line('all'), strtotime("2000-01-01")),
				array("3A", strtotime('-3 years', strtotime($to))),
				array("1A", strtotime('-1 year', strtotime($to))),
				array("6M", strtotime('-6 months', strtotime($to))),
				array("3M", strtotime('-3 months', strtotime($to))),
				array("1M", strtotime('-1 month', strtotime($to)))
			);
			
			$btns_daterange = array();
			$last_time = strtotime($records[0]->date);
			foreach($arr_btns as $item){
				if ($last_time < $item[1]) $disabled = "disabled"; else $disabled = "";
				array_push($btns_daterange, array($item[0], date('Y-m-d', $item[1])." ~ ".$to, $disabled));
			}
		}elseif ($last) if ($last->close) $records = array($last);

		$data = array(
			"title" => $company->name,
			"company" => $company,
			"updated_at" => $load_bvl->updated_at,
			"last" => $last,
			"records" => $records,
			"indicators" => $indicators,
			"btns_daterange" => $btns_daterange,
			"js_init" => "market/company_init.js",
			"main" => "market/company"
		);
		$this->load->view('layout',$data);
	}
	
	public function company_reset_forms(){
		$status = true;
		$content = $this->load->view("market/indicators/adx_form", "", true);
		$content = $content.$this->load->view("market/indicators/atr_form", "", true);
		$content = $content.$this->load->view("market/indicators/bb_form", "", true);
		$content = $content.$this->load->view("market/indicators/cci_form", "", true);
		$content = $content.$this->load->view("market/indicators/ema_form", "", true);
		$content = $content.$this->load->view("market/indicators/env_form", "", true);
		$content = $content.$this->load->view("market/indicators/ich_form", "", true);
		$content = $content.$this->load->view("market/indicators/macd_form", "", true);
		$content = $content.$this->load->view("market/indicators/mar_form", "", true);
		$content = $content.$this->load->view("market/indicators/mfi_form", "", true);
		$content = $content.$this->load->view("market/indicators/mom_form", "", true);
		$content = $content.$this->load->view("market/indicators/pbsar_form", "", true);
		$content = $content.$this->load->view("market/indicators/ppo_form", "", true);
		$content = $content.$this->load->view("market/indicators/pch_form", "", true);
		$content = $content.$this->load->view("market/indicators/rsi_form", "", true);
		$content = $content.$this->load->view("market/indicators/sma_form", "", true);
		$content = $content.$this->load->view("market/indicators/sto_form", "", true);
		$content = $content.$this->load->view("market/indicators/trix_form", "", true);
		$content = $content.$this->load->view("market/indicators/vol_form", "", true);
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msg" => $this->lang->line('success_reset_parameter'), "content" => $content));
	}
	
	public function set_search_box(){
		$companies = $this->company->get_general(null, null, array(array("order_by" => "nemonico", "order" => "asc")));
		
		$result = array();
		foreach($companies as $item) array_push($result, array("label" => $item->nemonico." - ".$item->name, "value" => $item->nemonico));
		
		header('Content-Type: application/json');
		echo json_encode($result);
	}
}
