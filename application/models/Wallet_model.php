<?php

class Wallet_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->tablename = 'wallet';
	}
  
    function get_by_id($id){
		$this->db->where('id', $id);
		$this->db->where('status', 1);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		if ($result) return $result[0];
		else return null;
	}
  
    function get_by_account_id($account_id, $nemonico = null){
		$this->db->where('account_id', $account_id);
		if ($nemonico) $this->db->where('nemonico', $nemonico);
		$this->db->where('status', 1);
		$this->db->order_by("date", "asc");
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function get_general($fields = "", $where = null){
		if ($fields) $this->db->select($fields);
		if ($where) $this->db->where($where);
		$this->db->where('status', 1);
		$this->db->order_by("nemonico", "asc");
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function get_unique_nemonico($account_id){
		$this->db->select("nemonico");
		$this->db->where("account_id", $account_id);
		$this->db->where('status', 1);
		$this->db->order_by("nemonico", "asc");
		$this->db->group_by("nemonico");
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function get_all(){
		$this->db->order_by("description", "asc");
		$this->db->where('status', 1);
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
	
	function delete($id){		
		$this->db->where('id', $id);	
		return $this->db->delete($this->tablename);		
	}
}
?>
