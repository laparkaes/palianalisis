<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Plan extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		if ($this->session->userdata('is_admin')){
			date_default_timezone_set('America/Lima');
			$this->lang->load("system", "spanish");
			$this->lang->load("admin_plan", "spanish");
			$this->load->model('indicator_model','indicator');
			$this->load->model('indicator_plan_model','indicator_plan');
			$this->load->model('plan_model','plan');
			$this->load->model('service_model','service');
			$this->load->model('service_plan_model','service_plan');
		}else redirect("/admin/access/login");
	}
	
	public function index(){
		$plans = $this->plan->get_all();
		$services = $this->service->get_all();
		$indicators = $this->indicator->get_all();
		
		$indicator_plan = $this->indicator_plan->get_all();
		$arr_ind_plan = array();
		foreach($indicator_plan as $item) array_push($arr_ind_plan, $item->indicator_id."/".$item->plan_id);
		
		$service_plan = $this->service_plan->get_all();
		$arr_srv_plan = array();
		foreach($service_plan as $item) array_push($arr_srv_plan, $item->service_id."/".$item->plan_id);
		
		$data = array();
		$data["indicators"] = $indicators;
		$data["plans"] = $plans;
		$data["services"] = $services;
		$data["arr_ind_plan"] = $arr_ind_plan;
		$data["arr_srv_plan"] = $arr_srv_plan;
		$data["title1"] = $this->lang->line('admin');
		$data["title2"] = $this->lang->line('plan');
		$data["title_ctrl"] = $data["title1"]." > ".$data["title2"];
		$data["main"] = "admin/plan/index";
		$this->load->view('admin/layout', $data);
	}
	
	public function add(){
		$desc = $this->input->post("description");
		$price = $this->input->post("price");
		
		$status = true;
		$content = $msg = "";
		
		switch(true){
			case (!$desc):
				$status = false;
				$msg = $this->lang->line('error_plan_description');
				break;
			case (!is_numeric($price)):
				$status = false;
				$msg = $this->lang->line('error_price_format');
				break;
			case ($price < 0):
				$status = false;
				$msg = $this->lang->line('error_price_positive');
				break;
		}
		
		if ($status){
			$result = null;
			if ($price > 0){
				$datas = array(
					"reason" => $desc,
					"auto_recurring" => array(
						"frequency" => 1,
						"frequency_type" => "months",
						//"billing_day" => 15,
						//"billing_day_proportional" => true,
						"transaction_amount" => $price,
						"currency_id" => "PEN"
					),
					"back_url" => base_url()."subscription/confirm"
				);
				$result = $this->mercadopago_lib->post("preapproval_plan", $datas);	
				if (property_exists($result, "message")){
					$status = false; 
					$msg = $result->message;
				}
			}
			
			if ($status){
				if ($result) $mp_plan_id = $result->id;
				else $mp_plan_id = "";
				
				$plan = array(
					"mp_plan_id" => $mp_plan_id,
					"description" => $desc, 
					"price" => $price
				);
				
				if ($this->plan->insert($plan)){
					$content = $this->load->view("admin/plan/rows", array("plans" => $this->plan->get_all()), true);
					$msg = $this->lang->line('success_plan_add');
				}else{$status = false; $msg = $this->lang->line('error_internal');}
			}
		}
		
		$response = array("status" => $status, "msg" => $msg, "content" => $content);
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
	public function remove(){
		$id = $this->input->post("id");
		
		if ($this->plan->delete($id)){
			$content = $this->load->view("admin/plan/rows", array("plans" => $this->plan->get_all()), true);
			$response = array("status" => true, "msg" => $this->lang->line('success_plan_remove'), "content" => $content);
		}else $response = array("status" => false, "msg" => $this->lang->line('error_internal'));
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
	public function service_add(){
		$desc = $this->input->post("description");
		
		switch(true){
			case (!$desc): $msg = $this->lang->line('error_service_description'); break;
			case (!$this->service->insert(array("description" => $desc))):
				$msg = $this->lang->line('error_internal');
				break;
			default: $msg = "";
		}
		
		if ($msg) $response = array("status" => false, "msg" => $msg);
		else{
			$service_plan = $this->service_plan->get_all();
			$arr_srv_plan = array();
			foreach($service_plan as $item) array_push($arr_srv_plan, $item->service_id."/".$item->plan_id);
			
			$data = array();
			$data["arr_srv_plan"] = $arr_srv_plan;
			$data["plans"] = $this->plan->get_all();
			$data["services"] = $this->service->get_all();
			$response = array(
				"status" => true, 
				"srv" => $this->load->view("admin/plan/srv_rows", $data, true), 
				"srv_plan" => $this->load->view("admin/plan/srv_match_rows", $data, true), 
				"msg" => $this->lang->line('success_service_add')
			);
		}
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
	public function service_remove(){
		$id = $this->input->post("id");
		
		if ($this->service->delete($id)){
			$this->service_plan->delete_by_ids($id, "");
			
			$service_plan = $this->service_plan->get_all();
			$arr_srv_plan = array();
			foreach($service_plan as $item) array_push($arr_srv_plan, $item->service_id."/".$item->plan_id);
			
			$data = array();
			$data["arr_srv_plan"] = $arr_srv_plan;
			$data["plans"] = $this->plan->get_all();
			$data["services"] = $this->service->get_all();
			$response = array(
				"status" => true, 
				"srv" => $this->load->view("admin/plan/srv_rows", $data, true), 
				"srv_plan" => $this->load->view("admin/plan/srv_match_rows", $data, true), 
				"msg" => $this->lang->line('success_service_remove')
			);
		}else $response = array("status" => false, "msg" => $this->lang->line('error_internal'));
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
	public function service_control(){
		$is_checked = json_decode($this->input->post("is_checked"));
		$ids = explode("/", $this->input->post("ids"));
		$data = array("service_id" => $ids[0], "plan_id" => $ids[1]);
		
		if ($is_checked){
			$this->service_plan->insert($data);
			echo $this->lang->line('success_service_linked');
		}else{
			$this->service_plan->delete_by_ids($data["service_id"], $data["plan_id"]);
			echo $this->lang->line('success_service_unlinked');
		}
	}
	
	public function indicator_add(){
		$desc = $this->input->post("description");
		$code = $this->input->post("code");
		
		switch(true){
			case (!$desc): $msg = "Ingrese descripcion del indicador."; break;
			case ($this->indicator->get_by_code($code)): 
				$msg = $this->lang->line('error_indicator_duplicate');
				break;
			case (!$this->indicator->insert(array("code" => $code, "description" => $desc))): 
				$msg = $this->lang->line('error_internal');
				break;	
			default: $msg = "";
		}
		
		if ($msg) $response = array("status" => false, "msg" => $msg);
		else{
			$indicator_plan = $this->indicator_plan->get_all();
			$arr_ind_plan = array();
			foreach($indicator_plan as $item) array_push($arr_ind_plan, $item->indicator_id."/".$item->plan_id);
			
			$data = array();
			$data["arr_ind_plan"] = $arr_ind_plan;
			$data["indicators"] = $this->indicator->get_all();
			$data["plans"] = $this->plan->get_all();
			$response = array(
				"status" => true, 
				"ind" => $this->load->view("admin/plan/ind_rows", $data, true), 
				"ind_plan" => $this->load->view("admin/plan/ind_match_rows", $data, true), 
				"msg" => $this->lang->line('success_indicator_add')
			);
		}
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
	public function indicator_remove(){
		$id = $this->input->post("id");
		if ($this->indicator->delete($id)){
			$this->indicator_plan->delete_by_ids($id, "");
			
			$indicator_plan = $this->indicator_plan->get_all();
			$arr_ind_plan = array();
			foreach($indicator_plan as $item) array_push($arr_ind_plan, $item->indicator_id."/".$item->plan_id);
			
			$data = array();
			$data["arr_ind_plan"] = $arr_ind_plan;
			$data["indicators"] = $this->indicator->get_all();
			$data["plans"] = $this->plan->get_all();
			$response = array(
				"status" => true, 
				"ind" => $this->load->view("admin/plan/ind_rows", $data, true), 
				"ind_plan" => $this->load->view("admin/plan/ind_match_rows", $data, true), 
				"msg" => $this->lang->line('success_indicator_remove')
			);
		}else $response = array("status" => false, "msg" => $this->lang->line('error_internal'));
		
		header('Content-Type: application/json');
		echo json_encode($response);
	}
	
	public function indicator_control(){
		$is_checked = json_decode($this->input->post("is_checked"));
		$ids = explode("/", $this->input->post("ids"));
		$data = array("indicator_id" => $ids[0], "plan_id" => $ids[1]);
		
		if ($is_checked){
			$this->indicator_plan->insert($data);
			echo $this->lang->line('success_indicator_linked');
		}else{
			$this->indicator_plan->delete_by_ids($data["indicator_id"], $data["plan_id"]);
			echo $this->lang->line('success_indicator_unlinked');
		}
	}
}