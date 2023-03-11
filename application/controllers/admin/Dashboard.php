<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		if ($this->session->userdata('is_admin')){
			date_default_timezone_set('America/Lima');
			$this->lang->load("system", "spanish");	
			$this->lang->load("admin_dashboard", "spanish");
			//$this->load->model('account_admin_model','account_admin');
		}else redirect("/admin/access/login");
	}
	
	public function index(){
		
		$data = array(
			"title1" => $this->lang->line('dashboard'),
			"title_ctrl" => $this->lang->line('dashboard'),
			"main" => "admin/dashboard",
		);
		$this->load->view('admin/layout',$data);
		
		/*
		$data = array();
		$data["title1"] = $this->lang->line('admin');
		$data["title2"] = $this->lang->line('plan');
		$data["title_ctrl"] = $data["title1"]." > ".$data["title2"];
		$this->load->view('admin/access/login', $data);
		*/
	}
}