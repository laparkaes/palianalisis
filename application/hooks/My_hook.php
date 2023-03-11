<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_hook {
    public function __construct(){
		//$this->CI->load->model('account_admin_model','account_admin');
		//$this->CI->lang->load(array("db_data","layout"), get_cookie("language"));
		
    }
	
	public function pre_constructor(){
        //$this->CI = &get_instance();
	
	}
	
	public function post_constructor(){
        //$this->CI = &get_instance();
		
	}
}
