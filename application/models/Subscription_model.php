<?php

class Subscription_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->tablename = 'subscription';
	}
  
    function get_by_id($id){
		$this->db->where('id', $id);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		if ($result) return $result[0];
		else return null;
	}
	
	function get_by_ids($account_id = "", $plan_id = ""){
		if ($account_id) $this->db->where('account_id', $account_id);
		if ($plan_id) $this->db->where('plan_id', $plan_id);
		$this->db->order_by("registed_at", "desc");
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function get_by_mp_preapproval_id($mp_preapproval_id){
		$this->db->where('mp_preapproval_id', $mp_preapproval_id);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		if ($result) return $result[0];
		else return null;
	}
	
	function get_last($account_id){
		$this->db->where('account_id', $account_id);
		$this->db->limit(1);
		$this->db->order_by('valid_to', 'desc');
		$this->db->order_by('registed_at', 'desc');
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		if ($result) return $result[0];
		else return null;
	}
	
	function get_valid($account_id, $date){
		$this->db->where('account_id', $account_id);
		$this->db->where('valid_to >=', $date);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		if ($result) return $result[0];
		else return null;
	}
	
	function count_valid($date){
		$this->db->where('valid_to >=', $date);
		$query = $this->db->get($this->tablename);
		return $query->num_rows();
	}
	
	function count_paid($date){
		$this->db->where('mp_preapproval_id !=', null);
		$this->db->where('valid_to >=', $date);
		$query = $this->db->get($this->tablename);
		return $query->num_rows();
	}
	
	function get_all(){
		$this->db->order_by("registed_at", "desc");
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
	
	function update_by_mp_preapproval_id($mp_preapproval_id, $data){ 
		$this->db->where('mp_preapproval_id', $mp_preapproval_id);	
		return $this->db->update($this->tablename, $data);
	}
	
	function update_multi($data){
		$this->db->update_batch($this->tablename, $data, 'id');
	}
	
	function delete($id){		
		$this->db->where('id', $id);	
		return $this->db->delete($this->tablename);		
	}
}
?>
