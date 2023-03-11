<?php

class Favorite_model extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->tablename = 'favorite';
	}
  
    function get($account_id, $nemonico){
		if ($account_id) $this->db->where('account_id', $account_id);
		if ($nemonico) $this->db->where('nemonico', $nemonico);
		$query = $this->db->get($this->tablename);
		$result = $query->result();
		return $result;
	}
	
	function insert($data){ 
		$this->db->insert($this->tablename, $data);
		return $this->db->insert_id();		
	}
	
	function delete($account_id, $nemonico){
		if ($account_id) $this->db->where('account_id', $account_id);
		if ($nemonico) $this->db->where('nemonico', $nemonico);
		return $this->db->delete($this->tablename);
	}
}
?>
