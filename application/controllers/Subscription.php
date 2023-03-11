<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Subscription extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		if ($this->session->userdata('is_admin')) redirect("/admin/dashboard");
		else{
			date_default_timezone_set('America/Lima');
			$this->lang->load("system", "spanish");
			$this->lang->load("subscription", "spanish");
			$this->load->model('account_model','account');
			$this->load->model('subscription_model','subscription');
			$this->load->model('plan_model','plan');
			$this->load->model('indicator_model','indicator');
			$this->load->model('indicator_plan_model','indicator_plan');
			$this->load->model('service_model','service');
			$this->load->model('service_plan_model','service_plan');
		}
	}
	
	public function index(){
		if (!$this->session->userdata('logged_in')) redirect("/");
		
		$subscription = $this->subscription->get_valid($this->session->userdata('aid'), date('Y-m-d'));
		if ($subscription) redirect("/account");
		
		$services = $this->service->get_all();
		$service_plan = $this->service_plan->get_all();
		$arr_srv_plan = array();
		foreach($service_plan as $item) array_push($arr_srv_plan, $item->service_id."/".$item->plan_id);
		
		$plans = $this->plan->get_all(0);
		foreach($plans as $p){
			$arr_services = array();
			foreach($services as $s){
				if (in_array($s->id."/".$p->id, $arr_srv_plan)) $i_class = ""; else $i_class = "na";
				array_push($arr_services, array("class" => $i_class, "desc" => $this->lang->line($s->description)));
			}
			
			$p->services = $arr_services;
			$p->indicator_qty = $this->indicator_plan->count_by_ids("", $p->id);
			$p->featured = "";
			$p->price_sub = "";
			$p->price_currency = $this->lang->line('sol_symbol');
			$p->payment_btn = $this->lang->line('choose');
			
			if ($p->price) $monthly = clone $p;
			else $free = clone $p;
		}
		$weekly = clone $monthly;
		
		$free->price = $this->lang->line('free');
		$free->price_currency = "";
		$free->payment = "#";
		$free->payment_btn = '<i class="fa fa-check" aria-hidden="true"></i>'; //$this->lang->line('current');
		$free->payment_class = "btn btn-lg tp-btn btn-success";
		
		if ($this->subscription->get_by_ids($this->session->userdata('aid'))){
			$weekly_pref = $this->mercadopago_lib->get_weekly_preference();
			$weekly->price = $weekly_pref->items[0]->unit_price;
			$weekly->payment = $weekly_pref->init_point;
		}else{
			$weekly->price = $this->lang->line('free');
			$weekly->price_currency = "";
			$weekly->payment = "/dashboard";
		}
		$weekly->description = "Una Semana";
		$weekly->payment_class = "btn btn-lg btn-outline-primary";
		
		$mp_plan = $this->mercadopago_lib->get_plan($monthly->mp_plan_id);
		$monthly->price_sub = " / ".$this->lang->line('month');
		$monthly->featured = "featured";
		$monthly->payment = $mp_plan->init_point;
		$monthly->payment_class = "btn btn-lg btn-primary";
		
		$data = array(
			"plans" => array($free, $monthly, $weekly),
			"title" => $this->lang->line('subscription'),
			"js_init" => null,
			"main" => "subscription/index"
		);
		$this->load->view('layout',$data);
	}
	
	public function confirm(){
		/*
		return url = https://www.palianalisis.com/subscription/confirm
		subscription datas = ?preapproval_id=2c938084826937dd01827b68183d08f3
		payment datas = ?collection_id=1308004584&collection_status=approved&payment_id=1308004584&status=approved&external_reference=null&payment_type=credit_card&merchant_order_id=5803235120&preference_id=1130632731-30e58feb-0811-46c2-914a-36214d33e7e1&site_id=MPE&processing_mode=aggregator&merchant_account_id=null
		*/
		$preapproval_id = $this->input->get("preapproval_id");
		$payment_id = $this->input->get("payment_id");
		if ($preapproval_id){
			$mp = $this->mercadopago_lib->get_subscription($preapproval_id);
			if ($mp){
				$account = $this->account->get_by_id($this->session->userdata('aid'));
				$plan = $this->plan->get_by_mp_plan_id($mp->preapproval_plan_id);
				$subscription = $this->subscription->get_by_mp_preapproval_id($mp->id);
				
				if ($subscription) $this->subscription->update($subscription->id, array("valid_to" => $mp->next_payment_date));
				else{
					$datas = array(
						"account_id" => $account->id,
						"plan_id" => $plan->id,
						"mp_preapproval_id" => $mp->id,					
						"valid_to" => $mp->next_payment_date,
						"registed_at" => mdate("%Y-%m-%d %H:%i:%s", time())
					);
					$subscription_id = $this->subscription->insert($datas);
					if ($subscription_id){
						$subscription = $this->subscription->get_by_id($subscription_id);
						$this->email_lib->subscription_new($account, $subscription, $plan);
						$this->session->set_userdata("plan", $plan);
					}
				}
			}
		}elseif ($payment_id){
			if (!strcmp("approved", $this->input->get("status"))){
				$account = $this->account->get_by_id($this->session->userdata('aid'));
				$plan = $this->plan->get_premium();
				
				$datas = array(
					"account_id" => $account->id,
					"plan_id" => $plan->id,
					"mp_payment_id" => $payment_id,
					"valid_to" => date('Y-m-d', strtotime('+1 week', time())),
					"registed_at" => mdate("%Y-%m-%d %H:%i:%s", time())
				);
				
				$subscription_id = $this->subscription->insert($datas);
				if ($subscription_id){
					$subscription = $this->subscription->get_by_id($subscription_id);
					$this->email_lib->subscription_new_week($account, $subscription, $plan);
					$this->session->set_userdata("plan", $plan);
				}
			}
		}
		redirect("/account");
	}
	
	public function webhook(){
		//https://www.mercadopago.com.ar/developers/es/docs/subscriptions/additional-content/notifications/webhooks
		$datas = json_decode(file_get_contents('php://input'), TRUE);
		//$this->email_lib->testing(print_r($datas, true));
		switch($datas["type"]){
			case "subscription_preapproval":
				if (!strcmp("updated", $datas["action"])){
					$mp = $this->mercadopago_lib->get_subscription($datas["data"]["id"]);
					if (!strcmp("pending", $mp->status)){
						$subscription = $this->subscription->get_by_mp_preapproval_id($mp->id);
						if ($subscription){/* subscription exists */
							$account = $this->account->get_by_id($subscription->account_id);
							$this->email_lib->subscription_payment_fail($account, $mp->init_point);
						}
					}
				}
			case "subscription_authorized_payment":
				$payment = $this->mercadopago_lib->get_subscription_payment($datas["data"]["id"]);
				if (!strcmp("processed", $payment->status)){
					$subscription = $this->subscription->get_by_mp_preapproval_id($payment->preapproval_id);
					if ($subscription){
						$this->subscription->update($subscription->id, array("valid_to" => $payment->next_retry_date));
						$account = $this->account->get_by_id($subscription->account_id);
						$this->email_lib->subscription_payment($account, $payment);
					}	
				}
				break;
		}
		http_response_code(200);
	}
	
	public function cancel(){
		$status = false;
		$msg = null;
		
		$subscription = $this->subscription->get_last($this->session->userdata('aid'));
		if ($subscription){
			if ($subscription->mp_preapproval_id){
				$error = $this->mercadopago_lib->update_subscription_status($subscription->mp_preapproval_id, "cancelled");
				if ($error) $msg = $error;
				else{
					$account = $this->account->get_by_id($subscription->account_id);
					$plan = $this->plan->get_by_id($subscription->plan_id);
					$this->email_lib->subscription_cancel($account, $subscription, $plan);
					$status = true;
				}
			}else $msg = $this->lang->line('error_admin_subs_cancel');
		}else $msg = $this->lang->line('error_no_subscription');
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msg" => $msg));
	}
	
	public function gift(){
		$account_id = $this->session->userdata('aid');
		$status = false; $msg = "";
		if (!$this->subscription->get_valid($account_id, date('Y-m-d'))){
			$plan = $this->plan->get_premium();
			$subscription_data = array(
				"account_id" => $account_id,
				"plan_id" => $plan->id,
				"valid_to" => date('Y-m-d', strtotime("+1 week", time())),
				"registed_at" => mdate("%Y-%m-%d %H:%i:%s", time())
			);
			
			/* insert new subscription */
			if ($this->subscription->insert($subscription_data)){
				$account = $this->account->get_by_id($account_id);
				$subscription = $this->subscription->get_valid($account_id, date('Y-m-d'));
				$this->email_lib->subscription_new_gift($account, $subscription, $plan);
				$this->session->set_userdata("plan", $plan);
				
				$status = true;
			}else $msg = $this->lang->line('error_internal');
		}else $msg = $this->lang->line('error_subscription_valid');
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msg" => $msg));
	}
	
	public function get_plan_list(){
		//$plans = $this->mercadopago_lib->get_plan("2c93808482ff24d10182ffae7b46006b");
		//print_r($plans);
		$plans = $this->mercadopago_lib->find_plans(array("status" => "active"));
		foreach($plans as $item){
			print_r($item);
			echo "<br/><br/>";
		}
	}
}
