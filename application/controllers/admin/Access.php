<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Access extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Lima');
		$this->load->model('account_admin_model','account_admin');
		$this->lang->load("system", "spanish");
		
		$this->master_email = "laparkaes@gmail.com";
	}
	
	public function login(){
		if ($this->session->userdata('is_admin')) redirect("/admin/dashboard");
		else $this->session->sess_destroy();
		
		if ($this->account_admin->get_by_email($this->master_email)) $has_master = true;
		else $has_master = false;
		
		$data = array();
		$data["has_master"] = $has_master;
		$data["title1"] = $this->lang->line('admin');
		$data["title2"] = $this->lang->line('plan');
		$data["title_ctrl"] = $data["title1"]." > ".$data["title2"];
		$this->load->view('admin/access/login', $data);
	}
	
	public function login_validation(){
		$email = $this->input->post("email");
		$password = $this->input->post("password");
		$status = false;
		$msg = "";
		
		$account = $this->account_admin->get_by_email($email);
		if ($account){
			if (password_verify($password, $account->password)){
				$session_data = array(
					"aid" => $account->id,
					"email" => $account->email,
					"name" => $account->name,
					"is_admin" => true,
					"is_master" => $account->is_master,
					"logged_in" => true
				);
				$this->session->set_userdata($session_data);
				
				$login_data = array(
					"last_agent" => $this->agent->agent_string(),
					"last_ip" => $this->input->ip_address(),
					"last_logged_at" => mdate("%Y-%m-%d %H:%i:%s", time())
				);
				$this->account_admin->update($account->id, $login_data);
				
				$status = true;
			}else $msg = $this->lang->line('error_password');
		}else $msg = $this->lang->line('error_email');
		
		header('Content-Type: application/json');
		echo json_encode(array("status" => $status, "msg" => $msg));
	}
	
	public function logout(){
		$this->session->sess_destroy();
		redirect('/admin/access/login', 'refresh');
	}
	
	public function generate_master(){
		$email = $this->master_email;
		$password = "123qwe";
		$name = "Jeong Woo Park";
		
		if ($this->account_admin->get_by_email($email)) echo $this->lang->line('error_admin_master_exists');
		else{
			$datas = array(
				"email" => $email,
				"password" => password_hash($password, PASSWORD_BCRYPT),
				"name" => $name,
				"is_activated" => true,
				"is_master" => true,
				"registed_at" => mdate("%Y-%m-%d %H:%i:%s", time())
			);
			
			if ($this->account_admin->insert($datas)){
				$datas["password"] = $password;
				$this->email_lib->send_admin_generated($datas);
				echo $this->lang->line('success_admin_generated');
			}
			else echo $this->lang->line('error_internal');
		}
	}
}