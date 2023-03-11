<?php

class Account_admin_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->tablename = 'account_admin';
	}
  
    function get_by_id($id, $is_activated = true){
		$this->db->where('id', $id);
		if ($is_activated) $this->db->where('is_activated', $is_activated);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		if ($result) return $result[0];
		else return null;
	}
  
    function get_by_email($email, $is_activated = true){
		$this->db->where('email', $email);
		if ($is_activated) $this->db->where('is_activated', $is_activated);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		if ($result) return $result[0];
		else return null;
	}
	
	function filter($filter, $limit = "", $offset = ""){
		$this->db->where('is_activated', true);
		$this->db->like('email', $filter);
		$this->db->or_like('name', $filter);
		$query = $this->db->get($this->tablename, $limit, $offset);
		$result = $query->result();
		return $result;
	}
	
	function count_all(){
		$this->db->where('is_activated', true);
		$query = $this->db->get($this->tablename);
		return $query->num_rows();
	}
	
	function get_all($limit = "", $offset = ""){
		$this->db->where('is_activated', true);
		$this->db->order_by("registed_at", "desc");
		$query = $this->db->get($this->tablename, $limit, $offset);
		$result = $query->result();
		return $result;
	}
	
	function insert($data){ 
		$this->db->insert($this->tablename, $data);
		return $this->db->insert_id();		
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
