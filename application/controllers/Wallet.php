<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wallet extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		if ($this->session->userdata('is_admin')) redirect("/admin/dashboard");
		else{
			date_default_timezone_set('America/Lima');
			$this->lang->load("system", "spanish");
			$this->lang->load("wallet", "spanish");
			$this->load->model('company_model','company');
			$this->load->model('record_model','record');
			$this->load->model('wallet_model','wallet');
		}
	}
	
	public function index(){
		if (!$this->session->userdata('logged_in')) redirect("/");
		
		$arr_nemonicos = array();
		$w_nemonicos = $this->wallet->get_unique_nemonico($this->session->userdata('aid'));
		foreach($w_nemonicos as $item) array_push($arr_nemonicos, $item->nemonico);
		
		$wallets = $all_operations = array();
		$my_stocks = new stdClass;
		$my_stocks->sol_balance_icon = $my_stocks->dol_balance_icon = "";
		$my_stocks->sol_balance_color = $my_stocks->dol_balance_color = "info";
		$my_stocks->sol_value = $my_stocks->dol_value = number_format(0, 2);
		$my_stocks->sol_balance = $my_stocks->dol_balance = number_format(0, 2);
			
		if ($arr_nemonicos){
			$arr_records = array();
			$records = $this->utility_lib->get_last_from_bvl("", $arr_nemonicos, false)->records;
			if (!$records) $records = $this->record->get_last($arr_nemonicos);
			foreach($records as $item) $arr_records[$item->nemonico] = $item;
			
			/* setting wallets */
			$sol_value = $dol_value = 0;
			foreach($arr_records as $nemonico => $record){
				$currency = $record->currencySymbol;
				
				$total_amount = $invest = $income = $price = $quantity = 0;
				$operations = $this->wallet->get_by_account_id($this->session->userdata('aid'), $nemonico);
				foreach($operations as $item){
					$item->value = $item->price * $item->quantity;
					$item->value_t = $currency." ".$this->utility_lib->shortNumber($item->value);
					if ($item->is_buy){
						$total_amount = $total_amount + $item->value;
						$quantity = $quantity + $item->quantity;
						$price = $total_amount / $quantity;
						$invest = $invest + $item->value;
					}else{
						$total_amount = $total_amount - $item->value;
						$quantity = $quantity - $item->quantity;
						//$price = $total_amount / $quantity;
						$income = $income + $item->value;
					}
					$balance = $income - $invest;
					
					$item->price_t = $currency." ".$this->utility_lib->shortNumber($item->price);
					$item->acc_price = $currency." ".number_format($price, 2);
					$item->acc_quantity = $quantity;
					$item->balance = $balance;
					$item->balance_t = $currency." ".$this->utility_lib->shortNumber(abs($balance));
					if ($balance >= 0){
						$item->balance_icon = "up";
						$item->balance_color = "success";
					}else{
						$item->balance_icon = "down";
						$item->balance_color = "danger";
					}
				}
				
				$operations = array_reverse($operations);
				
				$resume = new stdClass;
				if ($record->close) $resume->price = $record->close;
				elseif ($record->yesterdayClose) $resume->price = $record->yesterdayClose;
				else $resume->price = 0;
				
				$resume->date = $record->date;
				$resume->price_t = $currency." ".$this->utility_lib->shortNumber($resume->price);
				$resume->quantity = $operations[0]->acc_quantity;
				$resume->value = $resume->quantity * $resume->price;
				$resume->value_t = $currency." ".$this->utility_lib->shortNumber($resume->value);
				$resume->balance = $operations[0]->balance + $resume->value;
				$resume->balance_t = $currency." ".$this->utility_lib->shortNumber(abs($resume->balance));
				if ($resume->balance >= 0){
					$resume->balance_icon = "up";
					$resume->balance_color = "success";
				}else{
					$resume->balance_icon = "down";
					$resume->balance_color = "danger";
				}
				
				if (!strcmp($currency, "S/.")) $sol_value += $resume->value;
				else $dol_value += $resume->value;
				
				$datas = array(
					"nemonico" => $nemonico, 
					"name" => $record->companyName,
					"resume" => $resume, 
					"operations" => $operations
				);
				array_push($wallets, $datas);
			}
			
			usort($wallets, function($a, $b) {
				$ar = $a["resume"];
				$br = $b["resume"];
				
				if ($ar->quantity and $br->quantity){
					switch(true){
						case ($ar->balance > $br->balance): $is_change = false; break;
						case ($ar->balance < $br->balance): $is_change = true; break;
						default: $is_change = false;
					}
				}else{
					if (!$ar->quantity) $is_change = true;
					elseif (!$br->quantity) $is_change = false;
					else{
						switch(true){
							case ($ar->balance > $br->balance): $is_change = false; break;
							case ($ar->balance < $br->balance): $is_change = true; break;
							default: $is_change = false;
						}	
					}
				}
				return $is_change;
			});
			
			/* set operations */
			$sol_invest = $sol_income = $sol_balance = $dol_invest = $dol_income = $dol_balance = 0;
			$all_operations = $this->wallet->get_by_account_id($this->session->userdata('aid'));
			foreach($all_operations as $item){
				$currency = $arr_records[$item->nemonico]->currencySymbol;
				
				if (!strcmp($currency, $this->lang->line('sol_symbol'))){
					if ($item->is_buy) $sol_invest += $item->quantity * $item->price;
					else $sol_income += $item->quantity * $item->price;
					
					$sol_balance = $sol_income - $sol_invest;
				}else{
					if ($item->is_buy) $dol_invest += $item->quantity * $item->price;
					else $dol_income += $item->quantity * $item->price;
					
					$dol_balance = $dol_income - $dol_invest;
				}
				
				$item->name = $arr_records[$item->nemonico]->companyName;
				if ($sol_invest) $item->sol_invest = $this->lang->line('sol_symbol')." ".$this->utility_lib->shortNumber($sol_invest); else $item->sol_invest = "-";
				if ($sol_income) $item->sol_income = $this->lang->line('sol_symbol')." ".$this->utility_lib->shortNumber($sol_income); else $item->sol_income = "-";
				if ($sol_balance) $item->sol_balance = $this->lang->line('sol_symbol')." ".$this->utility_lib->shortNumber(abs($sol_balance)); else $item->sol_balance = "-";
				if ($sol_balance > 0){
					$item->sol_balance_icon = "up";
					$item->sol_balance_color = "success";
				}elseif ($sol_balance < 0){
					$item->sol_balance_icon = "down";
					$item->sol_balance_color = "danger";
				}else{
					
					$item->sol_balance_icon = "";
					$item->sol_balance_color = "info";
				}
				
				if ($dol_invest) $item->dol_invest = $this->lang->line('usd_symbol')." ".$this->utility_lib->shortNumber($dol_invest); else $item->dol_invest = "-";
				if ($dol_income) $item->dol_income = $this->lang->line('usd_symbol')." ".$this->utility_lib->shortNumber($dol_income); else $item->dol_income = "-";
				if ($dol_balance) $item->dol_balance = $this->lang->line('usd_symbol')." ".$this->utility_lib->shortNumber(abs($dol_balance));else $item->dol_balance = "-";
				if ($dol_balance > 0){
					$item->dol_balance_icon = "up";
					$item->dol_balance_color = "success";
				}elseif ($dol_balance < 0){
					$item->dol_balance_icon = "down";
					$item->dol_balance_color = "danger";
				}else{
					$item->dol_balance_icon = "";
					$item->dol_balance_color = "info";
				}
			}
			
			$all_operations = array_reverse($all_operations);
			
			$sol_income += $sol_value;
			$sol_balance = $sol_income - $sol_invest;
			if ($sol_balance > 0){
				$my_stocks->sol_balance_icon = "up";
				$my_stocks->sol_balance_color = "success";
			}elseif ($sol_balance < 0){
				$my_stocks->sol_balance_icon = "down";
				$my_stocks->sol_balance_color = "danger";
			}
			$my_stocks->sol_value = number_format($sol_value, 2);
			$my_stocks->sol_balance = number_format($sol_balance, 2);
			
			$dol_income += $dol_value;
			$dol_balance = $dol_income - $dol_invest;
			if ($dol_balance > 0){
				$my_stocks->dol_balance_icon = "up";
				$my_stocks->dol_balance_color = "success";
			}elseif ($dol_balance < 0){
				$my_stocks->dol_balance_icon = "down";
				$my_stocks->dol_balance_color = "danger";
			}
			$my_stocks->dol_value = number_format($dol_value, 2);
			$my_stocks->dol_balance = number_format($dol_balance, 2);
		}
		
		$companies = $this->company->get_general(null, null, array(array("order_by" => "nemonico", "order" => "asc")));
		$result = array();
		foreach($companies as $item) array_push($result, array("label" => $item->nemonico." - ".$item->name, "value" => $item->nemonico));
		
		$data = array(
			"companies" => json_encode($result),
			"wallets" => $wallets,
			"all_operations" => $all_operations,
			"my_stocks" => $my_stocks,
			"title" => $this->lang->line('wallet'),
			"js_init" => "wallet/wallet_init.js",
			"main" => "wallet/index"
		);
		$this->load->view('layout', $data);
	}
	
	public function add(){
		$nemonico = $this->input->post("nemonico");
		$type = $this->input->post("type");
		$date = $this->input->post("date");
		$price = $this->input->post("price");
		$quantity = $this->input->post("quantity");
		$msgs = array();
		$status = false;
		
		if (!$this->session->userdata('logged_in')) array_push($msgs, array("dom_id" => "nw_result_msg", "type" => "error", "msg" => $this->lang->line('error_session')));
		if (!$date) array_push($msgs, array("dom_id" => "nw_date_msg", "type" => "error", "msg" => $this->lang->line('error_select_date')));

		$msg = "";
		if (!$nemonico) $msg = $this->lang->line('error_company');
		elseif (!$this->company->get_by_nemonico($nemonico)) $msg = $this->lang->line('error_company');
		if ($msg) array_push($msgs, array("dom_id" => "nw_nemonico_msg", "type" => "error", "msg" => $msg));
		
		$msg = "";
		if (!$price) $msg = $this->lang->line('error_price');
		elseif (!is_numeric($price)) $msg = $this->lang->line('error_price_format');
		if ($msg) array_push($msgs, array("dom_id" => "nw_price_msg", "type" => "error", "msg" => $msg));
		
		$msg = "";
		if (!$quantity) $msg = $this->lang->line('error_quantity');
		elseif (!is_numeric($quantity)) $msg = $this->lang->line('error_quantity_format');
		if ($msg) array_push($msgs, array("dom_id" => "nw_quantity_msg", "type" => "error", "msg" => $msg));
		
		if (!$msgs){
			$msg = "";
		
			if (!strcmp("buy", $type)) $is_buy = true;
			else{
				$is_buy = false;
				/* stock quantity validation */
				$aux_quantity = 0;
				$operations = $this->wallet->get_by_account_id($this->session->userdata('aid'), $nemonico);
				foreach($operations as $item){
					if ($item->is_buy) $aux_quantity += $item->quantity;
					else $aux_quantity -= $item->quantity;
				}
				
				if ($quantity > $aux_quantity) array_push($msgs, array("dom_id" => "nw_result_msg", "type" => "error", "msg" => $this->lang->line('error_quantity_after')));
			}
			
			if (!$msgs){
				$datas = array(
					"account_id" => $this->session->userdata('aid'),
					"nemonico" => $nemonico,
					"is_buy" => $is_buy,
					"date" => $date,
					"price" => $price,
					"quantity" => $quantity,
					"status" => 1
				);
				
				if ($this->wallet->insert($datas)) $status = true;
				else array_push($msgs, array("dom_id" => "nw_result_msg", "type" => "error", "msg" => $this->lang->line('error_internal')));
			}
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msgs" => $msgs));
	}
	
	public function remove(){
		$wallet = $this->wallet->get_by_id($this->input->post("wid"));
		
		$msg = "";
		$status = false;
		if ($this->session->userdata('logged_in')){
			if ($wallet){
				if ($wallet->account_id == $this->session->userdata('aid')){
					$aux_quantity = 0;
					$quantity_error = false;
					$operations = $this->wallet->get_by_account_id($wallet->account_id, $wallet->nemonico);
					foreach($operations as $o){
						if ($o->id != $wallet->id){
							if ($o->is_buy) $aux_quantity += $o->quantity;
							else $aux_quantity -= $o->quantity;
						}
						
						if ($aux_quantity < 0){
							$quantity_error = true;
							break;
						}
					}
					
					if (!$quantity_error){
						if ($this->wallet->update($wallet->id, array("status" => 0))) $status = true;
						else $msg = $this->lang->line('error_internal');
					}else $msg = $this->lang->line('error_quantity_after');
				}else $msg = $this->lang->line('error_access');
			}else $msg = $this->lang->line('error_operation');
		}else $msg = $this->lang->line('error_session');
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msg" => $msg));
	}
}
