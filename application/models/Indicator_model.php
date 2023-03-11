<?php

class Indicator_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->tablename = 'indicator';
	}
  
    function get_by_id($id){
		$this->db->where('id', $id);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		if ($result) return $result[0];
		else return null;
	}
  
    function get_by_ids($ids){
		$this->db->where_in('id', $ids);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
  
    function get_by_code($code){
		$this->db->where('code', $code);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		if ($result) return $result[0];
		else return null;
	}
	
	function count_all(){
		$query = $this->db->get($this->tablename);
		return $query->num_rows();
	}
	
	function get_all(){
		$this->db->order_by("description", "asc");
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
