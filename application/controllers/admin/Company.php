<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Company extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		if ($this->session->userdata('is_admin')){
			date_default_timezone_set('America/Lima');
			$this->lang->load("system", "spanish");
			$this->lang->load("admin_company", "spanish");
			$this->load->model('company_model','company');
			$this->load->model('record_model','record');
		}else redirect("/admin/access/login");
	}
	
	public function index(){
		if (!$this->session->userdata('is_admin')) redirect("/");
		
		$lasts = $this->utility_lib->get_last_from_bvl()->records;
		$last_records = $this->record->get_last(null, "nemonico", "asc");
		
		$arr_records = $list_records = array();
		foreach($last_records as $item){
			$arr_records[$item->nemonico] = $item;
			array_push($list_records, $item->nemonico);
		}
		
		$arr_update = $arr_new = array();
		foreach($lasts as $item){
			if ($item->close or $item->yesterdayClose){
				if (in_array($item->nemonico, $list_records)){
					if ($item->yesterday){
						if ($item->yesterday != $arr_records[$item->nemonico]->date) array_push($arr_update, $item->nemonico);
						//if (($item->yesterday > $arr_records[$item->nemonico]->date) or (($arr_records[$item->nemonico]->close) and ($arr_records[$item->nemonico]->last_year_per == null))) array_push($arr_update, $item->nemonico);
					}
				}else array_push($arr_new, $item->nemonico);
			}
		}
		
		$data["arr_update"] = $arr_update;
		$data["arr_new"] = $arr_new;
		$data["title1"] = $this->lang->line('admin');
		$data["title2"] = $this->lang->line('company');
		$data["title_ctrl"] = $data["title1"]." > ".$data["title2"];
		$data["main"] = "admin/company/index";
		$this->load->view('admin/layout', $data);
	}
	
	public function load_partial_update(){
		$lasts = $this->utility_lib->get_last_from_bvl()->records;
		$last_records = $this->record->get_last(null, "nemonico", "asc");
		
		$arr_records = $list_records = array();
		foreach($last_records as $item){
			$arr_records[$item->nemonico] = $item;
			array_push($list_records, $item->nemonico);
		}
		
		$arr_update = array();
		foreach($lasts as $item){
			if ($item->close or $item->yesterdayClose){
				if (in_array($item->nemonico, $list_records)){
					if ($item->yesterday){
						if ($item->yesterday != $arr_records[$item->nemonico]->date) array_push($arr_update, $item->nemonico);
						//if (($item->yesterday > $arr_records[$item->nemonico]->date) or (($arr_records[$item->nemonico]->close) and ($arr_records[$item->nemonico]->last_year_per == null))) array_push($arr_update, $item->nemonico);
					}
				}
			}
		}
		
		echo $this->load->view('admin/company/partial', array("arr_update" => $arr_update), true);
	}
	
	public function record_update(){
		$nemonico = $this->input->post("nemonico");
		$is_partial = $this->input->post("is_partial");
		$today = date("Y-m-d");
		
		if ($is_partial){
			$last = $this->record->get_last_single($nemonico);
			if ($last) $from = date('Y-m-d', strtotime('+1 day', strtotime($last->date)));
			else $from = "2011-01-01";
		}else $from = "2011-01-01";	
		
		$msg = "";
		$histories = $this->utility_lib->get_stocks_from_bvl(str_replace("/", "%2F", $nemonico), $from, $today);
		if ($histories){
			$arr_dates = array();
			if ($last){
				$dates = $this->record->get_dates($nemonico, $from, $last->date);
				foreach($dates as $item) array_push($arr_dates, $item->date);	
			}
			
			foreach($histories as $i => $item){
				if (in_array($item->date, $arr_dates)) unset($histories[$i]);
				else{
					/* converting history to record of my db */
					if (!strcmp($item->currencySymbol, $this->lang->line('usd_symbol'))) $item->amountNegotiated = $item->dollarAmountNegotiated;
					else $item->amountNegotiated = $item->solAmountNegotiated;
					
					unset($item->id);
					unset($item->solAmountNegotiated);
					unset($item->dollarAmountNegotiated);	
				}
			}
			if (!$this->record->insert_multi($histories)) $msg = $this->lang->line('error_internal');
		}
		/* calculate last_year_per */
		$records = $this->record->get_by_nemonico($nemonico);
		$data_per = $this->utility_lib->calculate_last_year_per_data($records);
		if ($data_per) $this->record->update_multi($data_per);
		
		if ($msg) $status = false;
		else{
			$status = true;
			$msg = $this->lang->line('msg_update_complete');
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msg" => $msg));
	}
	
	public function update_national_companies(){
		$nemonicos = array();
		$companies = $this->company->get_general();
		foreach($companies as $item) array_push($nemonicos, $item->nemonico);
		
		$companies_new = array();
		/* loading national companies */
		$url = 'https://dataondemand.bvl.com.pe/v1/issuers/search'; //접속할 url 입력
		$datas = array();
		$datas["firstLetter"] = "";
		$datas["sectorCode"] = "";
		$datas["companyName"] = "";
		$res = $this->utility_lib->get_bvl($url, $datas, true);
		if ($res) foreach($res as $item){
			if ($item->stock){
				$stocks = $item->stock;
				foreach($stocks as $nemonico){
					if ($nemonico){
						if (!in_array($nemonico, $nemonicos)){
							$company = new stdClass;
							$company->is_national = true;
							$company->market = "BVL";
							$company->name = $item->companyName;
							$company->nemonico = $nemonico;
							$company->sectorCode = $item->sectorCode;
							$company->sectorDescription = $item->sectorDescription;
							$company->broker = null;
							array_push($companies_new, $company);	
						}
					}
				}
			}
		}
		
		if ($companies_new) echo $this->company->insert_multi($companies_new)." ".$this->lang->line('msg_new_companies');
		else echo $this->lang->line('msg_last_version');
	}
	
	public function update_foreign_companies(){
		ini_set('max_execution_time', 0);
	
		$nemonicos = array();
		$companies = $this->company->get_general();
		foreach($companies as $item) array_push($nemonicos, $item->nemonico);
		
		$companies_new = array();
		
		/* loading foreign companies */
		$url = 'https://dataondemand.bvl.com.pe/v1/securities-foreign/search'; //접속할 url 입력
		$datas = array();
		$datas["businessName"] = "";
		$datas["firstLetter"] = "";
		$datas["securityClass"] = "";
		$datas["status"] = "";
		$res = $this->utility_lib->get_bvl($url, $datas, true);
		if ($res) foreach($res as $item){
			if ($item->mnemonic){
				if (!in_array($item->mnemonic, $nemonicos)){
					$company = new stdClass;
					$company->is_national = false;
					$company->market = $item->tradeMarket;
					$company->nemonico = $item->mnemonic;
					$company->name = $item->businessName;
					$company->sectorCode = null;
					$company->sectorDescription = null;
					$company->broker = $item->broker;
					array_push($companies_new, $company);	
				}
			}
		}
		
		if ($companies_new) echo $this->company->insert_multi($companies_new)." ".$this->lang->line('msg_new_companies');
		else echo $this->lang->line('msg_last_version');
	}
}