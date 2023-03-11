<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		if ($this->session->userdata('is_admin')){
			date_default_timezone_set('America/Lima');
			$this->lang->load("system", "spanish");
			$this->lang->load("admin_client", "spanish");
			$this->load->model('account_model','account');
			$this->load->model('subscription_model','subscription');
			$this->load->model('plan_model','plan');
		}else redirect("/admin/access/login");
	}
	
	public function index(){
		if (!$this->session->userdata('is_admin')) redirect("/");
		
		$accounts = $this->account->get_all(30);/* always see 30 lastest accounts */
		$today = date('Y-m-d');
		
		$plans = $this->plan->get_all();
		foreach($plans as $item) if ($item->price) $premium_plan = $item; else $free_plan = $item;
		
		$all_account_qty = $this->account->count_all();
		$premium_all = $this->subscription->count_valid($today);
		$premium_paid = $this->subscription->count_paid($today);
		$premium_gift = $premium_all - $premium_paid;
		$free_all = $all_account_qty - $premium_all;
		
		$premium_plan->color = "success";
		$premium_plan->qty_paid = number_format($premium_paid);
		$premium_plan->qty_gift = number_format($premium_gift);
		$premium_plan->per_paid = number_format($premium_paid * 100 / $all_account_qty, 2)."%";
		$premium_plan->per_gift = number_format($premium_gift * 100 / $all_account_qty, 2)."%";
		
		$free_plan->color = "";
		$free_plan->qty = number_format($free_all);
		$free_plan->per = number_format($free_all * 100 / $all_account_qty, 2)."%";
		
		foreach($accounts as $item){
			$item->registed_at = date("Y-m-d", strtotime($item->registed_at));
			if ($this->subscription->get_valid($item->id, $today)) $item->plan = $premium_plan;
			else $item->plan = $free_plan;
		}
		
		$data["account_qty_text"] = $this->lang->line('of')." ".number_format($all_account_qty)." ".$this->lang->line('in_total');
		$data["accounts"] = $accounts;
		$data["premium_plan"] = $premium_plan;
		$data["free_plan"] = $free_plan;
		$data["title1"] = $this->lang->line('admin');
		$data["title2"] = $this->lang->line('client');
		$data["title_ctrl"] = $data["title1"]." > ".$data["title2"];
		$data["main"] = "admin/client/index";
		$this->load->view('admin/layout', $data);
	}
	
	public function account_filter(){
		$filter = $this->input->post("acc_filter");
		if ($filter) $accounts = $this->account->filter($filter, 30);
		else $accounts = $this->account->get_all(30);
		$today = date('Y-m-d');
		
		$plans = $this->plan->get_all();
		foreach($plans as $item) if ($item->price) $premium_plan = $item; else $free_plan = $item;
		
		$premium_plan->color = "success";
		$free_plan->color = "";
		
		foreach($accounts as $item){
			$item->registed_at = date("Y-m-d", strtotime($item->registed_at));
			if ($this->subscription->get_valid($item->id, $today)) $item->plan = $premium_plan;
			else $item->plan = $free_plan;
		}
		
		$acc_qty = count($accounts);
		$content = $this->load->view('admin/client/account_table', array("accounts" => $accounts), TRUE);
		
		header('Content-Type: application/json');
		echo json_encode(array("acc_qty" => $acc_qty, "content" => $content));
	}
	
	public function load_account_detail(){
		$account = $this->account->get_by_id($this->input->post("aid"));
		$status = false;
		$msg = $content = "";
		
		if ($account){
			$subscription = $this->subscription->get_valid($account->id, date('Y-m-d'));
			if ($subscription){
				$subscription->mp = $this->mercadopago_lib->get_subscription($subscription->mp_preapproval_id);
			}
			
			$datas = array("account" => $account, "subscription" => $subscription);
			$content = $this->load->view('admin/client/detail_modal', $datas, TRUE);
			$status = true;
		}else $msg = $this->lang->line('error_internal');
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => true, "content" => $content, "msg" => $msg));
	}
	
	public function load_add_subscription_form(){
		$account = $this->account->get_by_id($this->input->post("aid"));
		$status = false;
		$msg = $content = "";
		
		if ($account){
			$subscription = $this->subscription->get_valid($account->id, date('Y-m-d'));
			$datas = array(
				"account" => $account, 
				"subscription" => $subscription
			);
			$content = $this->load->view('admin/client/add_subscription_form_modal', $datas, TRUE);
			$status = true;
		}else $msg = $this->lang->line('error_internal');
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => true, "content" => $content, "msg" => $msg));
	}
	
	public function add_subscription_confirm(){
		$account_id = $this->input->post("aid");
		$term = $this->input->post("term");
		
		$status = false;
		if ($term > 0){
			if (!$this->subscription->get_valid($account_id, date('Y-m-d'))){
				$plan = $this->plan->get_premium();
				$subscription_data = array(
					"account_id" => $account_id,
					"plan_id" => $plan->id,
					"valid_to" => date('Y-m-d', strtotime('+'.$term." months", time())),
					"registed_at" => mdate("%Y-%m-%d %H:%i:%s", time())
				);
				
				/* insert new subscription */
				if ($this->subscription->insert($subscription_data)){
					$account = $this->account->get_by_id($account_id);
					$subscription = $this->subscription->get_valid($account_id, date('Y-m-d'));
					$this->email_lib->subscription_new_gift($account, $subscription, $plan);
					
					$status = true;
					$msg = $this->lang->line('success_subscription_add');
				}else $msg = $this->lang->line('error_internal');
			}else $msg = $this->lang->line('error_subscription_valid');
		}else $msg = $this->lang->line('error_subscription_term');
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msg" => $msg));
	}
}