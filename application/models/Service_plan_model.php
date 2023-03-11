<?php

class Service_plan_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->tablename = 'service_plan';
	}
  
    function get_by_ids($service_id, $plan_id){
		if ($service_id) $this->db->where('service_id', $service_id);
		if ($plan_id) $this->db->where('plan_id', $plan_id);
		if ($service_id or $plan_id){
			$query = $this->db->get($this->tablename);
			$result = $query->result();
			return $result;
		}else return null;
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
	
	function delete_by_ids($service_id, $plan_id){
		if ($service_id) $this->db->where('service_id', $service_id);
		if ($plan_id) $this->db->where('plan_id', $plan_id);
		if ($service_id or $plan_id) return $this->db->delete($this->tablename);
		else return null;
	}
}
?>
