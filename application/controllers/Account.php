<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		if ($this->session->userdata('is_admin')) redirect("/admin/dashboard");
		else{
			date_default_timezone_set('America/Lima');
			$this->lang->load("system", "spanish");
			$this->lang->load("account", "spanish");
			$this->load->model('account_model','account');
			$this->load->model('favorite_model','favorite');
			$this->load->model('subscription_model','subscription');
			$this->load->model('plan_model','plan');
			$this->load->model('service_model','service');
			$this->load->model('indicator_model','indicator');
			$this->load->model('indicator_plan_model','indicator_plan');
		}
		if (!$this->session->userdata('logged_in')) redirect("/");
	}

	public function index(){
		$account = $this->account->get_by_id($this->session->userdata('aid'));
		$actions = array();
		
		$subscription = $this->subscription->get_valid($account->id, date('Y-m-d'));
		if ($subscription){
			$subscription->limit_text = $this->lang->line('expiration');
			$subscription->is_paid = false;
			if ($subscription->mp_preapproval_id){
				$mp = $this->mercadopago_lib->get_subscription($subscription->mp_preapproval_id);
				switch($mp->status){
					case "pending":
						$subscription->status = $this->lang->line('invalid_payment_method');
						$subscription->status_color = "warning";
						$subscription->init_point = $mp->init_point;
						$subscription->limit_text = $this->lang->line('next_payment');
						$subscription->is_paid = true;
						array_push($actions, "pay");
						break;
					case "cancelled":
						$subscription->status = $this->lang->line('cancelled');
						$subscription->status_color = "danger";
						break;
					default:
						$subscription->status = $this->lang->line('actived');
						$subscription->status_color = "success";
						$subscription->limit_text = $this->lang->line('next_payment');
						$subscription->is_paid = true;
						array_push($actions, "cancel");
				}
			}else{
				$subscription->status = $this->lang->line('actived');
				$subscription->status_color = "success";
			}
			
			$subscription->plan = $this->plan->get_by_id($subscription->plan_id);
			$subscription->to = date("d/m/Y", strtotime($subscription->valid_to));
		}
		
		$data = array(
			"account" => $account,
			"subscription" => $subscription,
			"actions" => $actions,
			"title" => $this->lang->line('my_account'),
			"js_init" => "account/index_init.js",
			"main" => "account/index"
		);
		$this->load->view('layout',$data);
	}
	
	public function update(){
		$account_id = $this->session->userdata('aid');
		$name = $this->input->post("name");
		$status = false;
		if ($name){
			if ($this->account->update($account_id, array("name" => $name))){
				$this->session->set_userdata(array("name" => $name));
				$status = true;
				$msg = $this->lang->line('success_account_update');
			}else $msg = $this->lang->line('error_internal');
		}else $msg = $this->lang->line('error_name');
		
		$response = array("status" => $status, "msg" => $msg, "name" => $name);
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
	public function update_pass(){
		$account = $this->account->get_by_id($this->session->userdata('aid'));
		
		$password = $this->input->post("password");
		$new = $this->input->post("password_new");
		$confirm = $this->input->post("password_confirm");
		
		switch(true){
			case (!$account):
				$msg = $this->lang->line('error_session'); break;
			case (!$password):
				$msg = $this->lang->line('error_password_blank'); break;
			case (!password_verify($password, $account->password)):
				$msg = $this->lang->line('error_password'); break;
			case (!strcmp($password, $new)):
				$msg = $this->lang->line('error_new_password_different'); break;
			case (strlen($new) < 6):
				$msg = $this->lang->line('error_new_password_length'); break;
			case (strcmp($new, $confirm)):
				$msg = $this->lang->line('error_new_password_confirm'); break;
			default: $msg = "";
		}
		
		if ($msg) $response = array("status" => false, "msg" => $msg);
		else{
			if ($this->account->update($account->id, array("password" => password_hash($new, PASSWORD_BCRYPT)))){
				$this->session->sess_destroy();
				$response = array("status" => true);
			}else $response = array("status" => false, "msg" => $msg);
		}
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
	public function remove(){
		$account_id = $this->session->userdata('aid');
		$subscription = $this->subscription->get_last($account_id);
		if ($subscription){
			$this->mercadopago_lib->put("preapproval/".$subscription->mp_preapproval_id, array("status" => "cancelled"));
			$data = array(
				"mp_status" => "cancelled",
				"mp_updated_at" => mdate("%Y-%m-%d %H:%i:%s", time())
			);
			$this->subscription->update($subscription->id, $data);
		}
		
		if ($this->account->update($account_id, array("is_activated" => false))){
			$this->session->sess_destroy();
			$status = true;
			$msg = "";
			$move_to = base_url();
		}else{
			$status = false;
			$msg = $this->lang->line('error_internal');
			$move_to = "";
		}
	
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msg" => $msg, "move_to" => $move_to));
	}
	
	public function favorite_control(){
		$account_id = $this->session->userdata('aid');
		$nemonico = $this->input->post("nemonico");
		$status = false;
		$type = $msg = "";
		if ($this->favorite->get($account_id, $nemonico)){
			if ($this->favorite->delete($account_id, $nemonico)){
				$status = true;
				$type = "remove";
				$msg = $nemonico." ".$this->lang->line('success_favorite_remove');
			}else $msg = $this->lang->line('error_internal');
		}else{
			if ($this->favorite->insert(array("account_id" => $account_id, "nemonico" => $nemonico))){
				$status = true;
				$type = "add";
				$msg = $nemonico." ".$this->lang->line('success_favorite_add');
			}else $msg = $this->lang->line('error_internal');
		}
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msg" => $msg, "type" => $type));
	}
}