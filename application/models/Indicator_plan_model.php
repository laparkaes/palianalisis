<?php

class Indicator_plan_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->tablename = 'indicator_plan';
	}
  
    function get_by_ids($indicator_id, $plan_id){
		if ($indicator_id) $this->db->where('indicator_id', $indicator_id);
		if ($plan_id) $this->db->where('plan_id', $plan_id);
		if ($indicator_id or $plan_id){
			$query = $this->db->get($this->tablename);
			$result = $query->result();
			return $result;
		}else return null;
	}
	
	function count_by_ids($indicator_id = "", $plan_id = ""){
		if ($indicator_id) $this->db->where('indicator_id', $indicator_id);
		if ($plan_id) $this->db->where('plan_id', $plan_id);
		$query = $this->db->get($this->tablename);
		return $query->num_rows();
	}
	
	function get_all(){
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
	
	function delete_by_ids($indicator_id, $plan_id){
		if ($indicator_id) $this->db->where('indicator_id', $indicator_id);
		if ($plan_id) $this->db->where('plan_id', $plan_id);
		if ($indicator_id or $plan_id) return $this->db->delete($this->tablename);
		else return null;
	}
}
?>
