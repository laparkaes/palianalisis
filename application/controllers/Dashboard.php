<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		if ($this->session->userdata('is_admin')) redirect("/admin/dashboard");
		else{	
			date_default_timezone_set('America/Lima');
			$this->lang->load("system", "spanish");
			$this->lang->load("dashboard", "spanish");
			$this->load->model('company_model','company');
			$this->load->model('favorite_model','favorite');
			$this->load->model('record_model','record');
			$this->load->model('record_today_model','record_today');
			$this->load->model('subscription_model','subscription');
		}
	}
	
	public function index(){
		if (!$this->session->userdata('logged_in')) redirect("/");
		
		$nationals = $foreigns = $records = $records_db = $favorites = $favorites_nemonicos = array();
		$my_favorites = $this->favorite->get($this->session->userdata('aid'), null);
		foreach($my_favorites as $item) array_push($favorites_nemonicos, $item->nemonico);
		
		$load_bvl = $this->utility_lib->get_last_from_bvl(null, null, false);
		$all_records = $load_bvl->records;
		foreach($all_records as $item){
			$is_fav = in_array($item->nemonico, $favorites_nemonicos);
			if (($item->percentageChange == 0) and ($item->close and $item->yesterdayClose)){
				$item->percentageChange = ($item->close - $item->yesterdayClose) * 100 / $item->yesterdayClose;
			}
			
			if ($item->open or $item->quantityNegotiated or $is_fav){
				if ($item->open or $item->quantityNegotiated) array_push($records_db, clone $item);
				
				if ($item->percentageChange > 0) $item->color = "success";
				elseif ($item->percentageChange < 0) $item->color = "danger";
				else $item->color = "info";
				
				$d = strlen(substr(strrchr($item->close, "."), 1)); if ($d < 2) $d = 2;
				$c = $item->currencySymbol;
				if ($item->buy) $item->buy_t = $c." ".number_format($item->buy, $d); else $item->buy_t = "";
				if ($item->sell) $item->sell_t = $c." ".number_format($item->sell, $d); else $item->sell_t = "";
				if ($item->close) $item->close_t = $c." ".number_format($item->close, $d); else $item->close_t = "";
				if ($item->quantityNegotiated) $item->negotiated_t = number_format($item->quantityNegotiated); else $item->negotiated_t = "";
				if ($item->amountNegotiated) $item->volume_t = $c." ".$this->utility_lib->shortNumber($item->amountNegotiated);
				else $item->volume_t = "";
				
				if ($item->sectorCode){
					$item->type = $this->lang->line('national');
					if ($item->percentageChange) array_push($nationals, $item);
				}else{
					$item->type = $this->lang->line('foreign');
					if ($item->percentageChange) array_push($foreigns, $item);
				}
				
				if ($is_fav){
					$item->ic_fav = "fa-star text-warning";
					array_push($favorites, clone $item);
				}else $item->ic_fav = "fa-star-o text-muted";
				
				if ($item->open or $item->quantityNegotiated) array_push($records, $item);
			}
		}
		
		if ($favorites){
			foreach($favorites as $item){
				if (!$item->close){
					$d = strlen(substr(strrchr($item->close, "."), 1)); if ($d < 2) $d = 2;
					$item->date = $item->yesterday;
					$item->close_t = $item->currencySymbol." ".number_format($item->yesterdayClose, $d);
					$item->negotiated_t = "";
					$item->volume_t = "";
				}
			}
			
			usort($favorites, function($a, $b) {
				if (strtotime($a->date) < strtotime($b->date)) return true;
				else return $a->percentageChange < $b->percentageChange;
			});
		}
		
		$updated_at = $load_bvl->updated_at;
		if ($records_db) $this->record_today->delete_and_insert($records_db);
		else $records = $this->record_today->get_all();
		
		$headlines = array();
		usort($nationals, function($a, $b) {return $a->percentageChange < $b->percentageChange;});
		if ($nationals){
			if ($nationals[0]->percentageChange > 0)
				array_push($headlines, array("type" => "N", "icon" => "up", "icon_color" => "success", "color" => "#FFAB2D", "stock" => $nationals[0]));
			
			if ($nationals[count($nationals) - 1]->percentageChange < 0)
				array_push($headlines, array("type" => "N", "icon" => "down", "icon_color" => "danger", "color" => "#3693FF", "stock" => $nationals[count($nationals) - 1]));
		}
		
		usort($foreigns, function($a, $b) {return $a->percentageChange < $b->percentageChange;});
		if ($foreigns){
			if ($foreigns[0]->percentageChange > 0)
				array_push($headlines, array("type" => "E", "icon" => "up", "icon_color" => "success", "color" => "#5B5D81", "stock" => $foreigns[0]));
			
			if ($foreigns[count($foreigns) - 1]->percentageChange < 0)
				array_push($headlines, array("type" => "E", "icon" => "down", "icon_color" => "danger", "color" => "#AC4CBC", "stock" => $foreigns[count($foreigns) - 1]));
		}
		
		foreach($headlines as $i => $item){
			$chart_datas = array();
			$stocks = array_reverse($this->record->get_by_limit($item["stock"]->nemonico, true, 30));
			
			if ($stocks) if (strcmp($item["stock"]->yesterday, $stocks[count($stocks)-1]->date)){
				$from = date('Y-m-d', strtotime('+1 day', strtotime($stocks[count($stocks)-1]->date)));
				
				$histories = $this->utility_lib->get_stocks_from_bvl(str_replace("/", "%2F", $item["stock"]->nemonico), $from, $item["stock"]->yesterday);
				foreach($histories as $history){
					/* converting history to record of my db */
					if (!strcmp($history->currencySymbol, $this->lang->line('usd_symbol'))) $history->amountNegotiated = $history->dollarAmountNegotiated;
					else $history->amountNegotiated = $history->solAmountNegotiated;
					
					unset($history->id);
					unset($history->solAmountNegotiated);
					unset($history->dollarAmountNegotiated);
				}
				if ($histories) $this->record->insert_multi($histories);
				
				$stocks = array_reverse($this->record->get_by_limit($item["stock"]->nemonico, true, 30));
			}
			
			foreach($stocks as $s) array_push($chart_datas, array("x" => strtotime($s->date) * 1000, "y" => floatval($s->close)));
			
			/* insert today variation */
			array_push($chart_datas, array("x" => strtotime($item["stock"]->date) * 1000, "y" => floatval($item["stock"]->close)));
			
			$headlines[$i]["chart_datas"] = json_encode($chart_datas);
		}
		
		$checklist = array();
		if ($this->session->userdata('plan')->price){
			$low = array();
			$high = array();
			$checklist_all = $this->record->get_checklist();
			foreach($checklist_all as $item){
				if ($item->last_year_per > 50) array_push($high, $item);
				else array_push($low, $item);
			}
			$checklist["low"] = $low;
			$checklist["high"] = $high;
		}
		
		$arr_companies = array();
		$companies = $this->company->get_general();
		foreach($companies as $item) $arr_companies[$item->nemonico] = $item->name;
		
		if ($this->subscription->get_by_ids($this->session->userdata('aid'))) $has_subscription = true;
		else $has_subscription = false;
		
		$data = array(
			"headlines" => $headlines,
			"records" => $records,
			"favorites" => $favorites,
			"updated_at" => $updated_at,
			"checklist" => $checklist,
			"arr_companies" => $arr_companies,
			"has_subscription" => $has_subscription,
			"title" => $this->lang->line('dashboard'),
			"js_init" => "dashboard_init.js",
			"main" => "dashboard"
		);
		$this->load->view('layout',$data);
	}
}