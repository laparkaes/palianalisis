<?php

class Company_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->tablename = 'company';
	}
  
    function get_by_id($id){
		$this->db->where('id', $id);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		if ($result) return $result[0];
		else return null;
	}
  
    function get_by_nemonico($nemonico){
		$this->db->where('nemonico', $nemonico);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		if ($result) return $result[0];
		else return null;
	}
	
	function get_general($fields = null, $where = null, $orders = null){
		if ($fields) $this->db->select($fields);
		if ($where) $this->db->where($where);
		if ($orders) foreach($orders as $item) $this->db->order_by($item["order_by"], $item["order"]);
		
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function insert($data){ 
		$this->db->insert($this->tablename, $data);
		return $this->db->insert_id();		
	}
	
	function insert_multi($data){
		return $this->db->insert_batch($this->tablename, $data);
	}
	
	function update($id, $data){ 
		$this->db->where('id', $id);	
		return $this->db->update($this->tablename, $data);
	}
	
	function update_multi($data){
		$this->db->update_batch($this->tablename, $data, 'id');
	}
	
	function update_multi_nemonico($data){
		$this->db->update_batch($this->tablename, $data, 'nemonico');
	}
	
	function delete($id){		
		$this->db->where('id', $id);	
		return $this->db->delete($this->tablename);		
	}
}
?>
