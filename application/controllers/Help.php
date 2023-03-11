<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Help extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
		date_default_timezone_set('America/Lima');
		$this->lang->load("system", "spanish");
		$this->lang->load("welcome", "spanish");
	}
	
	public function terms(){
		$datas["title_ctrl"] = $this->lang->line("title_ctrl");
		$datas["tmp_path"] = base_url()."utility/tmp_f/";
		$datas["main"] = "help/terms";
		$this->load->view('layout_welcome', $datas);
	}
	
	public function privacy(){
		$datas["title_ctrl"] = $this->lang->line("title_ctrl");
		$datas["tmp_path"] = base_url()."utility/tmp_f/";
		$datas["main"] = "help/privacy";
		$this->load->view('layout_welcome', $datas);
	}
}
