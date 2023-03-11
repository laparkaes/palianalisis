<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Lima');
		$this->lang->load("system", "spanish");
		$this->lang->load("welcome", "spanish");
		$this->load->model('plan_model','plan');
		$this->load->model('service_model','service');
		$this->load->model('service_plan_model','service_plan');
		$this->load->model('indicator_plan_model','indicator_plan');
	}
	
	public function index(){
		//need to set final portfolios text
		$portafolios = array(
			array("file" => "1dashboard1.png", "desc" => ""),
			array("file" => "1dashboard2.png", "desc" => ""),
			array("file" => "1dashboard3.png", "desc" => ""),
			array("file" => "3company1.png", "desc" => ""),
			array("file" => "3company2.png", "desc" => ""),
			array("file" => "3company3.png", "desc" => ""),
			array("file" => "4offers.png", "desc" => ""),
			array("file" => "5wallet1.png", "desc" => ""),
			array("file" => "5wallet2.png", "desc" => "")
		);
		
		$plans = $this->plan->get_all();
		$services = $this->service->get_all();
		
		$service_plan = $this->service_plan->get_all();
		$arr_srv_plan = array();
		foreach($service_plan as $item) array_push($arr_srv_plan, $item->service_id."/".$item->plan_id);
		
		foreach($plans as $i => $p){
			$arr_services = array();
			foreach($services as $s){
				if (in_array($s->id."/".$p->id, $arr_srv_plan)) $i_class = ""; else $i_class = "na";
				array_push($arr_services, array("class" => $i_class, "desc" => $this->lang->line($s->description)));
			}
			$p->services = $arr_services;
			$p->featured = "";
			$p->indicator = $this->indicator_plan->count_by_ids("", $p->id);
		}
		$plans[count($plans)-1]->featured = "featured";
		
		$datas = array();
		$datas["resume"] = $this->utility_lib->get_today_resume();
		$datas["portafolios"] = $portafolios;
		$datas["plans"] = $plans;
		$datas["title_ctrl"] = $this->lang->line("title_ctrl");
		$datas["main"] = "welcome";
		$this->load->view('layout_welcome', $datas);
	}
	
	public function email_test(){
		/*
		$account = $this->account->get_by_email("laparkaes@gmail.com");
		$this->email_lib->account_validation($account);
		$this->email_lib->reset_pass($account);
		$this->email_lib->reset_pass_confirm($account, "123qwe");
		
		$this->load->model('subscription_model','subscription');
		$subscription = $this->subscription->get_valid($account->id, date('Y-m-d'));
		if ($subscription) $plan = $this->plan->get_by_id($subscription->plan_id); else $plan = null;
		$this->email_lib->subscription_new($account, $subscription, $plan);
		$this->email_lib->subscription_new_gift($account, $subscription, $plan);
		$this->email_lib->subscription_cancel($account, $subscription, $plan);
		
		$payment = $this->mercadopago_lib->get_subscription_payment(6171549563); //$aux = print_r($payment, true); $aux = str_replace(" [","<br/> [", $aux); $aux = str_replace(")","<br/>)", $aux); $aux = str_replace("(","<br/>(", $aux); $aux = str_replace(":protected", "", $aux); echo $aux;
		$this->email_lib->subscription_payment($account, $payment);
		
		$mp = $this->mercadopago_lib->get_subscription($subscription->mp_preapproval_id);
		$this->email_lib->subscription_payment_fail($account, $mp->init_point);
		*/
	}
}
