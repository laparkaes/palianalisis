<?php

class Record_today_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->tablename = 'record_today';
	}
	
	function get_all($order_by = "percentageChange", $order = "desc"){
		$this->db->order_by($order_by, $order);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function delete_and_insert($data){
		$this->db->truncate($this->tablename);
		return $this->db->insert_batch($this->tablename, $data);
	}
}
?>
